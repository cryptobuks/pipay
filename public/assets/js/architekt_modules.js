/****************************************************************************************************
 *
 *      Architekt.module.Clipboard: Clipboard manipulation module
 *                   No Flash, No HTML5 Clipboard API
 *          Compatible: IE, Google Chrome, Firebox, Opera, Safari
 *
 ****************************************************************************************************/

Architekt.module.reserv('Clipboard', function(options) {
	return {
		//Architekt.module.Clipboard.copy(object targetDom): Copy
		copy: function(targetDom) {
			var success = false;
			var disabled = targetDom.attr('disabled');
			var hasDisabled = false;

			if(typeof disabled !== 'undefined') {
				targetDom.removeAttr('disabled');
				hasDisabled = true;
			}
		
			targetDom.select();
			success = document.execCommand('copy');
			
			if(!success) throw new Error('UnknownErrorException');

			//if dom had disabled attribute, restore it.
			if(hasDisabled) {
				targetDom.attr('disabled', 'disabled');
			}
			
			targetDom.blur();
			return success;
		},
		//Architekt.module.Clipboard.cut(object targetDom): Cut
		cut: function(targetDom) {
			
		},
	};
});
Architekt.module.reserv('Comparator', function(options) {
	var results = [];
	var texts = [];
	var startTime = null;
	var endTime = null;

	return {
		//Architekt.module.Comparator.start(): Begins measuring
		start: function() {
			results = [];
			texts = [];
			startTime = new Date();

			results.push(0);
			texts.push('Begins');
			console.log('Architekt.module.Comparator: Start perfomance measuring at ' + startTime.toGMTString());

			return this;
		},
		//Architekt.module.Comparator.stop(): Stops measuring
		stop: function() {
			endTime = new Date();

			results.push(endTime.getTime() - startTime.getTime());
			texts.push('Ends');
			console.log('Architekt.module.Comparator: Stop perfomance measuring at ' + startTime.toGMTString());

			return this.result();
		},
		//Architekt.module.Comparator.check(): Add checkpoint
		check: function(text) {
			var currentTime = new Date();

			text = typeof text !== 'undefined' ? text : 'Checkpoint';

			results.push(currentTime.getTime() - startTime.getTime());
			texts.push(text);
			console.log('Architekt.module.Comparator: Add checkpoint at ' + startTime.toGMTString());

			return this;
		},
		//Architekt.module.Comparator.result(): Get result
		result: function() {
			var resultObject = [];
			for(var i = 0, len = results.length; i < len; i++)
				resultObject.push({
					time: results[i],
					message: texts[i]
				});

			return resultObject;
		},
	};
});
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

		var _page = 1;
		var _header = [];
		var _columns = [];
		var dom = $('<div></div>').addClass('pi-table-container');
		var tableDom = $('<table></table>').addClass('pi-table').appendTo(dom);
		this.event = new Architekt.EventEmitter( ['onheaderclick', 'onitemclick', 'onclick', 'onprevious', 'onnext'] );	

		var thead = $('<thead></thead>').appendTo(tableDom);
		var tbody = $('<tbody></tbody>').appendTo(tableDom);

		//onclick
		tableDom.click(function() {
			self.event.fire('onclick');
		});


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

			function animateCell(cell, duration) {
				cell.delay(animationDuration).css('opacity', '0.0').animate({
					'opacity': '1.0'
				}, duration);
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
							e.clickedIndex = i;
							e.column = _columns[i];
							self.event.fire('onitemclick', e);
						});

						for(var j = 0, jLen = _columns[i].length; j < jLen; j++) {
							var td = $('<td></td>').html(_columns[i][j]).appendTo(tr);
							tr.appendTo(tbody);
						}

						if(animate) animateCell(tr, i * subAnimDuration);
					})(i);
				}	
			}
			

			//draw cursor only it has pagenate feature
			if(pagenate) {
				$('<div></div>').addClass('pi-table-prev sprite-arrow-left').click(function(e) {
					e.currentPage = _page;
					self.event.fire('onprevious', e);
				}).appendTo(dom);

				$('<div></div>').addClass('pi-table-next sprite-arrow-right').click(function(e) {
					e.currentPage = _page;
					self.event.fire('onnext', e);
				}).appendTo(dom);	
			}


			var origHeight = dom.height();

			dom.css({
				'height': '0',
				'overflow': 'hidden'
			}).animate({
				'height': origHeight + 'px',
			}, animationDuration, 'swing', function() {
				dom.css('overflow', 'visible');
			});

			return this;
		};
		//Architekt.module.DataTable.appendTo(object parentDom): Append DataTable to parentDom
		this.appendTo = function(parentDom) {
			dom.appendTo(parentDom);
			return this;
		};
 
	};
});
/****************************************************************************************************
 *
 *      Architekt.module.Http: Asynchronous HTTP request module
 *
 ****************************************************************************************************/
Architekt.module.reserv('Http', function(options) {
	var log = function() {};
	var warn = function() {};
	var error = function() {};

	Architekt.event.on('ready', function() {
		log = Architekt.module.Printer.log;
		warn = Architekt.module.Printer.warn;
		error = Architekt.module.Printer.error;
	});

	//AJAX REQUEST function
	//Requres console.js
	//ajaxRequest({ dataObject, headers, dataType, url, success, error, complete, after })
	function request(data) {
		if (typeof data !== 'object' || typeof data.url === 'undefined') return false;
		
		var _ajax_work = true;

		var dataObject = typeof data.data === 'object' ? data.data : {};
		var headers = typeof data.headers !== 'undefined' ? data.headers : {};
		var method = typeof data.method !== 'undefined' ? data.method : 'post';
		var dataType = typeof data.dataType !== 'undefined' ? data.dataType : 'json';
		var url = typeof data.url !== 'undefined' ? data.url : '';
		var suc = typeof data.success === "function" ? data.success : function () { };
		var err = typeof data.error === "function" ? data.error : function () { };
		var comp = typeof data.complete === "function" ? data.complete : function () { };
		var after = typeof data.after === "function" ? data.after : function () { };
		
		log('Architekt.module.Http: send HTTP request...');
		log(method.toUpperCase() + ' ' + url);
		log('header: ' + JSON.stringify(headers) + ', data: ' + JSON.stringify(dataObject));

		$.ajax({
			timeout: 20000, //maximum 20seconds to timeout
			url: url,
			data: dataObject,
			headers: headers,
			'type': method,
			dataType: dataType,
			success: function (result) {
				log('Architekt.module.Http: Http sent success.');
				log(method.toUpperCase() + ' ' + url);
				log(JSON.stringify(result));
				suc(result);
			},
			error: function (response, status, error) {
				var responseText = false;
				if(typeof response.responseText !== 'undefined') {
					responseText = JSON.parse(response.responseText);
				}
				else {
					responseText = { message: 'undefined' };
				}
				
				//Check timeout
				if(status === 'timeout') responseText.error = 'timeout';
				
				error('Architekt.module.Http: server sent error');
				log(method.toUpperCase() + ' ' + url);
				log('Sent Header: ' + JSON.stringify(headers));
				log('Sent Data:' + JSON.stringify(dataObject));
				log('Response: ' + JSON.stringify(response));
				log('Code: ' + response.status);
				log('Message: ' + response.responseText);
				log('Error: ' + error);
				err(responseText, response.status)
			},
			complete: function () {
				_ajax_work = false;
				log('Architekt.module.Http: request over.');
				comp();
				after();
			}
		});
		
		//Give notice if processing take too long
		setTimeout(function() {
			if(_ajax_work) {
				log('Architekt.module.Http: request taking too long ( >=5000ms )');
			}
		}, 5000);
	}

	function get(data) {
		data.method = 'get';
		request(data);
	}

	function post(data) {
		data.method = 'post';
		request(data);
	}



	return {
		//Architekt.module.Http.request(): Request HTTP
		request: request,
		//Architekt.module.Http.get(): Request GET
		get: get,
		//Architekt.module.Http.post(): Request POST
		post: post,
	};
});
Architekt.module.reserv('Locale', function(options) {
	var namespace = this;
	var currentLocale = 'ko';
	var localeStrings = {
		"ko": {

		},
		"en": {

		},
	};

	return {
		//Architekt.module.Locale.setLocale(string newLocale): Set new locale
		setLocale: function(newLocale) {
			//If new locale is not supported, use english instead.
			if(typeof localeStrings[newLocale] === 'undefined') {
				currentLocale = 'en';
				console.warn('Architekt.module.Locale: [WARN] Unsupported locale ' + newLocale);
			}
			else
				currentLocale = newLocale;
			
			return this;
		},
		//Architekt.module.Locale.getCurrentLocale(void): Get current locale
		getCurrentLocale: function() {
			return currentLocale;
		},
		//Architekt.module.Locale.getString(string key, object replacements): Get string
		getString: function(key, replacements) {
			var text = localeStrings[currentLocale][key];

			if(typeof text !== 'undefined') {
				//Replace the replacements
				if(typeof replacements === 'object') {
					for(var key in replacements) {
						var replacement = replacements[key];
						text = text.replace(new RegExp("{" + key + "}"), replacement);	//Replace the text that has same replacement character
					}
				}

				return text;
			}
			//If locale string does not exists, just return the key
			else
				return key;
		}
	};
});
/****************************************************************************************************
 *
 *      Architekt.module.Printer: Debugging helper for Architekt
 *      Logging levels:
 *      - 0: Log everything
 *      - 1: Do not show Error
 *      - 2: Do not show Error and Warning
 *      - 3: No log
 *
 ****************************************************************************************************/

Architekt.module.reserv('Printer', function(options) {
	var logLevel = 0;	//Default lvl is 0
	var printDate = false;

	//getDate(): Return date part string
	function getDate(dateObj) {
		return (dateObj.getFullYear() + "-" + ("0" + (dateObj.getMonth() + 1)).substr(0,2) + "-" + ("0" + dateObj.getDate()).substr(0,2));
	}
	//getTime(): Return time part string
	function getTime(dateObj) {
		return (("0" + dateObj.getHours()).substr(0,2) + ":" + ("0" + dateObj.getMinutes()).substr(0,2) + ":" + ("0" + dateObj.getSeconds()).substr(0,2));
	}
	//getFormattedDate(): Return actual formated date string
	function getFormatedDate() {
		var date = new Date();
		var printStr = (printDate ? getDate(date) + " " : "") + getTime(date);

		return printStr;
	}
	//getDebugText(string type, string text): Return printing format [Date Type] Text -> [10:32:20 LOG] Helloworld
	function getDebugText(type, text) {
		return "[" + getFormatedDate() + " " + type + "] " + text;
	}

	return {
		setLevel: function(newLevel) {
			newLevel = +newLevel;

			if(isNaN(newLevel)) {
				console.log('Architekt.module.Printer: Unknown level ' + newLevel);
				newLevel = 0;
			}

			switch(newLevel) {
				case 0:
				case 1:
				case 2:
				case 3:
					newLevel = newLevel;
					break;
				default: 
					console.log('Architekt.module.Printer: Unknown level ' + newLevel);
					break;
			}

			logLevel = newLevel;
			console.log('Architekt.module.Printer: Log level set to ' + newLevel);
			return this;
		},
		getLevel: function() {
			return logLevel;
		},
		setPrintDate: function(print) {
			printDate = !!print;
			return this;
		},
		getPrintDate: function() {
			return printDate;
		},
		//Architekt.module.Printer.log(string text): Log the text
		log: function(text) {
			if(logLevel >= 3) return;
			console.log(getDebugText('LOG', text));
		},
		//Architekt.module.Printer.warn(string text): Log the text with warning
		warn: function(text) {
			if(logLevel >= 2) return;
			console.warn(getDebugText('WARN', text));
		},
		//Architekt.module.Printer.error(string text): Log the text with error
		error: function(text) {
			if(logLevel >= 1) return;
			console.error(getDebugText('ERR', text));
		},
		//Architekt.module.Printer.inspect(object obj, options): Inspect object properties
		//options:
		//int depth: Max tree level for search
		//array ignoreRegex: Array of the strings that ignore searching it.
		inspect: function(obj, options) {
			if(logLevel >= 3) return;

			if(typeof obj !== 'object') {
				console.log(getDebugText('INSPECT', 'Parameter obj is not a object.'));
			}
			else {
				console.log(getDebugText('INSPECT', obj.constructor.name));

				options = typeof options === 'object' ? options : {};

				var maxDepth = typeof options.maxDepth !== 'undefined' ? +options.maxDepth : false;
				var ignoreRegex = typeof options.ignoreRegex !== 'undefined' ? options.ignoreRegex : false;
				var displayFunctionCode = typeof options.displayFunctionCode !== 'undefined' ? !!options.displayFunctionCode : false;
				var spaceBetween = 4;
				var treeDepth = 0;

				//To increase perfomance, make regexp array in here
				var ignoreList = [];
				for(var i = 0, len = ignoreRegex.length; i < len; i++) ignoreList.push(new RegExp(ignoreRegex[i]));

				//function for get between space of the depth
				function _getSpaceBetween(depth) {
					var spaceStr = '';
					//Depth * 4 blank spaces
					for(var i = 0; i < ((depth + 1) * spaceBetween); i++) spaceStr += ' ';

					return spaceStr;
				}

				//function that check the key is in the ignore list
				function _checkInIgnoreList(text) {
					for(var i = 0, len = ignoreList.length; i < len; i++)
						if(ignoreList[i].test(text)) return true;

					return false;
				}

				//function for actual inspect object. it is recursive until property exists.
				function _inspectObject(targetObj, depth) {
					//Make space by depth of the object tree (space = depth * 4)
					var bracketSpace = _getSpaceBetween(depth - 1);
					var propertySpace = _getSpaceBetween(depth);

					console.log(bracketSpace + "{");

					for(var key in targetObj) {
						//Make sure that property is not linked in Prototype
						if(targetObj.hasOwnProperty(key) && !_checkInIgnoreList(key)) {
							//If the property is typeof of object, increase depth and search
							if(typeof targetObj[key] === 'object') {
								console.log(propertySpace + '[' + (typeof targetObj[key]) + '] ' + key);

								if(maxDepth === false || (maxDepth && depth < maxDepth))
									_inspectObject(targetObj[key], (depth+1));	//Recursive with inside of the object
							}
							else {
								if(typeof targetObj[key] === 'function' && !displayFunctionCode) {
									console.log(propertySpace + '[' + (typeof targetObj[key]) + '] ' + key);
								}
								else
									console.log(propertySpace + '[' + (typeof targetObj[key]) + '] ' + key + ": " + targetObj[key]);
							}

						}
					}

					console.log(bracketSpace + "}");
				}

				_inspectObject(obj, 0);
			}
		}
	}
});
/****************************************************************************************************
 *
 *      Architekt.module.Validator: Validation module
 *
 ****************************************************************************************************/

Architekt.module.reserv('Validator', function(options) {
	//regex objects container
	var formular = {
		email: /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i,
		url: /https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,4}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/,
		number: /^\d+$/,
		alphabet: /^[a-zA-Z]*$/,
		alphanumeric: /^[a-z0-9]+$/i,
	};

	//equations
	formular.numeric = formular.number;

	return {
		//Architekt.module.Validator.check(string type, string string): Validate string
		check: function(type, string) {
			var noneSupported = false;

			switch(type) {
				case 'email':
				case 'url':
				case 'number':
				case 'numeric':
				case 'alphabet':
				case 'alphanumeric':
					type = type;
					break;
				default:
					noneSupported = true;
					break;
			}

			if(noneSupported)
				throw new Error('Architekt.module.Validator: unsupported validation type ' + type);
			

			var result = formular[type].test(string);

			if(result) return true;
			else return false;
		},
		//Architekt.module.Validator.empty(string string): Returns true if the string is empty or null or undefined
		empty: function(string) {
			if(typeof string === 'undefined' || string === '' || string === null) return true;
			return false;
		},
		//Architekt.module.Vaditor.checkIfNotEmpty(string type, string string): Check the string is validate if it is not empty. If it is empty, returns true.
		checkIfNotEmpty: function(type, string) {
			if(!this.empty(string)) {
				return this.check(type, string);
			}

			return true;
		},
	};
});
/* Widget Module */
Architekt.module.reserv('Widget', function(options) {
	var body = $('body');

	var defaultText = {
		ok: 'Ok',
		confirm: 'Confirm',
		close: 'Close',
		cancel: 'Cancel',
	};

	//widgetBase constructor
	function widgetBase(options) {
		options = typeof options === 'object' ? options : {};
		this.text = typeof options.text !== 'undefined' ? options.text : '';
		this.controlObject = null;
		this.callback = typeof options.callback === 'function' ? options.callback : function() {};
		this.noCallback = typeof options.noCallback === 'function' ? options.noCallback : function() {};
		this.okText = typeof options.okText !== 'undefined' ? options.okText : defaultText.ok;
		this.confirmText = typeof options.confirmText !== 'undefined' ? options.confirmText : defaultText.confirm;
		this.closeText = typeof options.closeText !== 'undefined' ? options.closeText : defaultText.close;
		this.cancelText = typeof options.cancelText !== 'undefined' ? options.cancelText : defaultText.cancel;

		this.destruct = function() {
			this.controlObject.remove();
			this.controlObject = null;
			return this;	
		};
	}

	//Architekt.module.Widget.Notice(): Create a Notice widget
	function Notice(options) {
		Architekt.object.link(widgetBase, this, options);	//Link widgetBase with argument options

		var self = this;

		this.controlObject = $('<div></div>').addClass('architekt-widget-background');

		var container = $('<div></div>').addClass('architekt-widget-container').appendTo(this.controlObject);	
		var textObject = $('<p></p>').text(this.text).appendTo(container);
		var buttonContainer = $('<div></div>').addClass('architekt-widget-buttonContainer').appendTo(container);

		$('<button></button>').addClass('architekt-widget-button architekt-theme-confirm').click(function() {
			self.callback();
			self.destruct();
		}).text(this.okText).appendTo(buttonContainer);

		//Append to body
		this.controlObject.appendTo(body);

		//Fancy scale up animation
		setTimeout(function() {
			container.addClass('on');
		}, 25);
	}

	//Architekt.module.Widget.Confirm(): Create a Confirm widget
	function Confirm(options) {
		Architekt.object.link(widgetBase, this, options);	//Link widgetBase with argument options

		var self = this;

		this.controlObject = $('<div></div>').addClass('architekt-widget-background');

		var container = $('<div></div>').addClass('architekt-widget-container').appendTo(this.controlObject);	
		var textObject = $('<p></p>').text(this.text).appendTo(container);
		var buttonContainer = $('<div></div>').addClass('architekt-widget-buttonContainer').appendTo(container);

		$('<button></button>').addClass('architekt-widget-button').click(function() {
			self.noCallback();
			self.destruct();
		}).text(this.closeText).appendTo(buttonContainer);

		$('<button></button>').addClass('architekt-widget-button architekt-theme-confirm').click(function() {
			self.callback();
			self.destruct();
		}).text(this.confirmText).appendTo(buttonContainer);

		//Append to body
		this.controlObject.appendTo(body);

		//Fancy scale up animation
		setTimeout(function() {
			container.addClass('on');
		}, 25);
	}

	return {
		Notice: Notice,
		Confirm: Confirm,
		setDefaultText: function(newTexts) {
			if(typeof newTexts.ok !== 'undefined') defaultText.ok = newTexts.ok;
			if(typeof newTexts.confirm !== 'undefined') defaultText.confirm = newTexts.confirm;
			if(typeof newTexts.close !== 'undefined') defaultText.close = newTexts.close;
			if(typeof newTexts.cancel !== 'undefined') defaultText.cancel = newTexts.cancel;
		}
	};
});
//# sourceMappingURL=architekt_modules.js.map
