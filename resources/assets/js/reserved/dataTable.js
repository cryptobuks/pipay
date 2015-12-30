/****************************************************************************************************
 *
 *      Architekt.module.dataTable: DataTable component module
 *		- options
 *			bool pagenate: show the cursor and trigger paginating events on click. default is false.
 *			bool readOnly: set the cursor to pointer and trigger onitemclick event on click the item. default is true.
 *
 ****************************************************************************************************/

Architekt.module.reserv('DataTable', function(options) {
	return function(options) {
		options = typeof options === 'object' ? options : {};
		var pagenate = typeof options.pagenate !== 'undefined' ? !!options.pagenate : false;
		var readOnly = typeof options.readOnly !== 'undefined' ? !!options.readOnly : true;

		var self = this;

		var _page = 1;
		var _header = [];
		var _columns = [];
		var dom = $('<div></div>').addClass('architekt-dataTable-container');
		var paginator = {
			container: $('<div></div>').addClass('architekt-dataTable-paginator').appendTo(dom),
			left: false,
			right: false,
			//paginator.generate(): Generate new paginator
			generate: function() {
				paginator.left = $('<div></div>').addClass('pi-table-prev sprite-arrow-left').click(function(e) {
					if(isLocked) return;

					e.currentPage = _page;
					self.event.fire('onprevious', e);
				}).appendTo(this.container);

				paginator.right = $('<div></div>').addClass('pi-table-next sprite-arrow-right').click(function(e) {
					if(isLocked) return;

					e.currentPage = _page;
					self.event.fire('onnext', e);
				}).appendTo(this.container);

				return this;
			},
			//paginator.destroy(): Destroy paginator
			destroy: function() {
				if(this.left) this.left.remove();
				if(this.right) this.right.remove();

				return this;
			}
		};
		var tableDom = $('<table></table>').addClass('architekt-dataTable').appendTo(dom);

		var isLocked = false;
		var lockDom = $('<div></div>').hide().addClass('architekt-dataTable-locked').appendTo(dom);
		var loadingDom = $('<div></div>').hide().appendTo(lockDom);


		if(!readOnly) tableDom.addClass('architekt-dataTable-writable');

		//create generator if has pagenate option
		if(pagenate) paginator.generate();


		//events
		this.event = new Architekt.EventEmitter( ['onheaderclick', 'onitemclick', 'onclick', 'onprevious', 'onnext'] );	

		var thead = $('<thead></thead>').appendTo(tableDom);
		var tbody = $('<tbody></tbody>').appendTo(tableDom);

		//onclick
		tableDom.click(function() {
			self.event.fire('onclick');
		});


		//Architekt.module.DataTable.lock(object options): Lock the DataTable
		this.lock = function(options) {
			options = typeof options === 'object' ? options : {};
			var loading = options.loading ? !!options.loading : false;

			if(loading)
				loadingDom.show();
			else
				loadingDom.hide();

			if(paginator.left) {
				var cssObj = {
					'opacity': '0.5',
					'cursor': 'not-allowed',
				};

				paginator.left.css(cssObj);
				paginator.right.css(cssObj);
			}

			isLocked = true;
			lockDom.fadeIn();
			return this;
		};
		//Architekt.module.DataTable.unlock(void): Unlock the DataTable
		this.unlock = function(options) {
			isLocked = false;
			lockDom.fadeOut(200);

			if(paginator.left) {
				var cssObj = {
					'opacity': '1.0',
					'cursor': 'pointer',
				};

				paginator.left.css(cssObj);
				paginator.right.css(cssObj);
			}
			return this;
		};
		//Architekt.module.DataTable.isLocked(void): Returns the value that the DataTable is locked
		this.isLocked = function() {
			return isLocked;
		};
		//Architekt.module.DataTable.resetHeaderColumn(void): Reset header column
		this.resetHeaderColumn = function() {
			_header = [];
			return this;
		}
		//Architekt.module.DataTable.resetColumns(void): Reset item columns
		this.resetColumns = function() {
			_columns = [];
			return this;
		}
		//Architekt.module.DataTable.getCurrentPage(void): Get current page
		this.getCurrentPage = function() {
			return _page;
		};
		//Architekt.module.DataTable.setPage(void): Set page
		this.setPage = function(newPage) {
			_page = +newPage;
			return this;
		};
		//Architekt.module.DataTable.getHeaderColumn(void): Get header column
		this.getHeaderColumn = function() {
			return _header;
		};
		//Architekt.module.DataTable.setHeaderColumn(array headerColumn): Set header column
		this.setHeaderColumn = function(headerColumn) {
			_header = headerColumn;
			return this;
		};
		//Architekt.module.DataTable.getColumn(int index): Get specified index
		this.getColumn = function(index) {
			return _columns[i];
		};
		//Architekt.module.DataTable.getColumns(): Get all item columns
		this.getColumns = function() {
			return _columns;
		};
		//Architekt.module.DataTable.addColumn(array column): Add item column
		this.addColumn = function(column) {
			_columns.push(column);
			return this;
		};
		//Architekt.module.DataTable.addColumns(2ndArray columns): Add item columns
		this.addColumns = function(columns) {
			for(var i = 0, len = columns.length; i < len; i++)
				_columns.push(columns[i]);

			return this;
		};
		//Architekt.module.DataTable.setColumns(2ndArray columns): Set item columns(replace)
		this.setColumns = function(columns) {
			_columns = columns;
			return this;
		};
		//Architekt.module.DataTable.render(renderOptions): Render the DataTable
		this.render = function(renderOptions) {
			renderOptions = typeof renderOptions === 'object' ? renderOptions : {};
			var animate = typeof renderOptions.animate !== 'undefined' ? !!renderOptions.animate : false;

			var animationDuration = typeof renderOptions.animationDuration !== 'undefined' ? +renderOptions.animationDuration : 300;
			var updateHeader = typeof renderOptions.updateHeader !== 'undefined' ? !!renderOptions.updateHeader : true;
			var updateItems = typeof renderOptions.updateItems !== 'undefined' ? !!renderOptions.updateItems : true;


			//resize whole container height to fit
			function resizeContainer() {
				var origHeight = tableDom.height();

				dom.css({
					'height': origHeight + 'px',
					'overflow': 'visible',
				});
			}

			//animate each column(=cell)
			function animateCell(cell, duration) {
				cell.delay(animationDuration).css('opacity', '0.0').animate({
					'opacity': '1.0'
				}, duration);

				//update dom height
				resizeContainer();
			}

			var subAnimDuration = parseInt(animationDuration / 4);
			
			//update header!
			if(updateHeader) {
				thead.empty();

				//render headers
				var tr = $('<tr></tr>').click(function(e) {
					self.event.fire('onheaderclick', e);
				});

				for(var i = 0, len = _header.length; i < len; i++) {
					var th = $('<th></th>').text(_header[i]).appendTo(tr);
					tr.appendTo(thead);
				}

				if(animate) {
					animateCell(tr, subAnimDuration);
				}
			}


			//update items!
			if(updateItems) {
				tbody.empty();

				//render items. note that items are 2d array
				for(var i = 0, len = _columns.length; i < len; i++) {
					(function(i) {
						var tr = $('<tr></tr>').click(function(e) {
							if(!readOnly) {
								e.clickedIndex = i;
								e.column = _columns[i];
								self.event.fire('onitemclick', e);	
							}
						});

						for(var j = 0, jLen = _columns[i].length; j < jLen; j++) {
							var td = $('<td></td>').html(_columns[i][j]).appendTo(tr);
							tr.appendTo(tbody);
						}

						if(animate) {
							animateCell(tr, i * subAnimDuration);
						}
					})(i);
				}	
			}
			
			//create generator if has pagenate option
			if(pagenate) paginator.destroy().generate();
			

			if(!animate) resizeContainer();

			return this;
		};
		//Architekt.module.DataTable.appendTo(object parentDom): Append DataTable to parentDom
		this.appendTo = function(parentDom) {
			dom.appendTo(parentDom);
			return this;
		};
 
	};
});