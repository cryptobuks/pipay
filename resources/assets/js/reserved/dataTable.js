/****************************************************************************************************
 *
 *      Architekt.module.dataTable: DataTable component module
 *		- options
 *			bool pagenate: show the cursor and trigger paginating events on click. default is false.
 *
 ****************************************************************************************************/

Architekt.module.reserv('DataTable', function(options) {
	return function(options) {
		options = typeof options === 'object' ? options : {};
		var pagenate = typeof options.pagenate !== 'undefined' ? !!options.pagenate : false;

		var self = this;

		var _page = 0;
		var _header = [];
		var _columns = [];
		var dom = $('<div></div>').addClass('pi-table-container');
		var tableDom = $('<table></table>').addClass('pi-table').appendTo(dom);
		this.event = new Architekt.EventEmitter( ['onheaderclick', 'onitemclick', 'onclick', 'onprevious', 'onnext'] );	

		//onclick
		tableDom.click(function() {
			self.event.fire('onclick');
		});

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
		//Architekt.module.DataTable.render(): Render the DataTable
		this.render = function(options) {
			options = typeof options === 'object' ? options : {};
			var animate = typeof options.animate !== 'undefined' ? !!options.animate : false;
			var animationDuration = typeof options.animationDuration !== 'undefined' ? +options.animationDuration : 300;

			tableDom.empty();

			//make thead and tbody
			var thead = $('<thead></thead>');
			var tbody = $('<tbody></tbody>');

			//render headers
			var tr = $('<tr></tr>').click(function(e) {
				self.event.fire('onheaderclick', e);
			});

			for(var i = 0, len = _header.length; i < len; i++) {
				var th = $('<th></th>').text(_header[i]).appendTo(tr);

				tr.appendTo(thead);
			}

			//render items. note that items are 2d array
			for(var i = 0, len = _columns.length; i < len; i++) {
				(function(i) {
					var tr = $('<tr></tr>').click(function(e) {
						e.clickedIndex = i;
						e.column = _columns[i];
						self.event.fire('onitemclick', e);
					});

					for(var j = 0, jLen = _columns[i].length; j < jLen; j++) {
						var td = $('<td></td>').html(_columns[i][j]).appendTo(tr);

						tr.appendTo(tbody);
					}
				})(i);
			}

			thead.appendTo(tableDom);
			tbody.appendTo(tableDom);

			//draw cursor
			if(showCursor) {
				$('<div></div>').addClass('pi-table-prev sprite-arrow-left').click(function(e) {
					e.currentPage = _page;
					self.event.fire('onprevious', e);
				}).appendTo(dom);

				$('<div></div>').addClass('pi-table-next sprite-arrow-right').click(function(e) {
					e.currentPage = _page;
					self.event.fire('onnext', e);
				}).appendTo(dom);	
			}

			if(animate) tableDom.hide().fadeIn(animationDuration);;
			return this;
		};
		//Architekt.module.DataTable.appendTo(object parentDom): Append DataTable to parentDom
		this.appendTo = function(parentDom) {
			dom.appendTo(parentDom);
			return this;
		};
 
	};
});