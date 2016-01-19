/****************************************************************************************************
 *
 *      Architekt.module.Client: Client resource provider
 *
 ****************************************************************************************************/

Architekt.module.reserv('Client', function(options) {
	return {
		domain: document.URL,
		host: location.host,
		hostName: location.hostname,
		href: location.href,
		origin: location.origin,
		path: location.path,
		protocol: location.protocol,
		url: (location.protocol + "//" + location.host),
		createUrl: function(sub) {
			var temp = [];
			temp.push(this.url);
			temp.push(sub);

			return temp.join("/");
		}
	};
});
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
 *                        Architekt.module.CustomWidget: Custom UI Widget
 *
 ****************************************************************************************************/

Architekt.module.reserv('CustomWidget', function(options) {
	function CustomWidget(options) {
		var self = this;

		options = options || {};
		this.dom = options.dom;
		this.events = typeof options.events === 'object' ? options.events : {};
		this.data = typeof options.data === 'object' ? options.data : {};
		this.container = this.dom.find('.architekt-widget-container');
		this.close = this.dom.find('.architekt-widget-close');
		this.visible = typeof options.visible !== 'undefined' ? !!options.visible : false;

		//events
		this.event = new Architekt.EventEmitter([ 'show', 'hide', 'destroy' ]);
		
		//hide first
		this.dom.hide();

		//close on click
		if(this.close.length) {
			this.close.click(function() {
				self.hide();
			});
		}
		
		//method registration
		for(var key in options) {
			var val = options[key];
			
			if(options.hasOwnProperty(key) && typeof val === 'function') this[key] = val;
		}

		//custom events processing
		for(var key in this.events) {
			(function (key) {
				//format: "cssSelector eventType": eventHandler (e.g. "#refresh click": "myFunction")
				//_t[0] = css selector (e.g. #refresh)
				//_t[1] = event type (e.g. click)
				//method = event handler. (e.g. myFunction above)
				var _t = key.split(" ");
				var selector = _t[0];
				var eventType = _t[1];
				var method = self.events[key];

				try {
					//try attach custom event each selector
					self.dom.find(selector).on(eventType, function(e) {
						if(typeof self[method] === 'function') {
							self.data.originalEvent = e;
							self[method].call(null, self.data);	//execute event handler with inner data(dataObject)
						}
					});
				}
				catch(message) {
					Architekt.module.Printer.error('Architekt.module.CustomWidget: ' + message);
				}
			})(key);
		}

		//get architekt attributes
		this.attributes = {};
		this.dom.find('[data-architekt-key]').each(function() {
			var key = $(this).attr('data-architekt-key');
			self.attributes[key] = $(this);
		});

		//formats
		this.formats = options.formats || {};

		if(this.visible) this.show();
	}
	//Architekt.module.customWidget.show(object options): Show widget
	//options.verticalCenter: Automatically calculate height and set to the dom element to center (half of height)
	CustomWidget.prototype.show = function(options) {
		var self = this;

		options = typeof options === 'object' ? options : {};
		var verticalCenter = typeof options.verticalCenter !== 'undefined' ? !!options.verticalCenter : true;

		this.dom.show();

		//move the widget to half of the screen
		if(verticalCenter) {
			var height = this.container.height();
			this.container.css('margin-top', '-' + parseInt(height / 2) + 'px');	
		}

		//Fancy scale up animation
		setTimeout(function() {
			self.container.addClass('on');
		}, 25);

		this.event.fire('show');
		return this;
	};
	//Architekt.module.customWidget.hide(void): Hide widget
	CustomWidget.prototype.hide = function() {
		var self = this;

		this.dom.fadeOut(200, function() {
			self.container.removeClass('on');
		});
		
		this.event.fire('hide');
		return this;
	};
	//Architekt.module.customWidget.destroy(void): Destroy widget. means no more Dom element.
	CustomWidget.prototype.destroy = function() {
		if(this.dom != null) {
			this.dom.remove();
			this.dom = null;
		}

		this.event.fire('destroy');
		return this;
	}
	//Architekt.module.customWidget.setData(object dataObject): Set inner data
	CustomWidget.prototype.setData = function(dataObject) {
		dataObject = dataObject || {};
		this.data = dataObject;
		return this;
	};
	//Architekt.module.customWidget.getDat(void): Get inner data
	CustomWidget.prototype.getData = function(dataObject) {
		return this.data;
	};
	//Architekt.module.customWidget.render(options): Render the widget
	CustomWidget.prototype.render = function() {
		var self = this;
		var formats = this.formats;

		//update relative doms
		for(var key in this.attributes) {
			(function(key) {
				var currentDom = self.attributes[key];
				var data = self.data[key];

				//formatting
				var formatList = currentDom.attr('data-architekt-format');

				//has format functions?
				if(formatList && formatList !== '') {
					formatList = formatList.split(" ");

					for(var i = 0, len = formatList.length; i < len; i++) {
						var formatFunc = formats[formatList[i]];

						//execute only if it is function
						if(typeof formatFunc === 'function') {
							var formatArgs = currentDom.attr('data-architekt-format-args');

							//if formatArgs has string, split it by seperator ",". else, let's just give empty array.
							formatArgs = (formatArgs && formatArgs !== "") ? formatArgs.split(",") : [];

							var resultArgs = {};

							for(var j = 0, argLen = formatArgs.length; j < argLen; j++) {
								//each format argument looks like this: key:value,key:value
								var argItem = formatArgs[j].split(":");

								//if the length is smaller than 2, it means it is not like key:value
								if(argItem.length < 2) continue;

								resultArgs[argItem[0]] = argItem[1];	//give as key-value object
							}

							data = formatFunc.call(null, data, resultArgs);	//apply format!
						}
					}
				}
					
				//update dom!
				var _sa = self.attributes[key];

				if(_sa.is('input') || _sa.is('textarea'))
					_sa.val(data);
				else
					_sa.text(data);
			})(key);
		}

		return this;
	};
	//Architekt.module.customWidget.select(string key): Find the specified descendant element
	CustomWidget.prototype.select = function(key) {
		var t = this.attributes[key];

		if(typeof t === 'undefined')
			return false;

		return t;
	};
	//Architekt.module.customWidget.querySelect(string cssSelector):
	CustomWidget.prototype.querySelect = function(cssSelector) {
		return this.dom.find(cssSelector);
	};
	//Architekt.module.customWidget.get(string key): Get the text or form value inside of dom element
	CustomWidget.prototype.get = function(key) {
		var t = this.select(key);
		
		if(t) {
			if(t.is('input') || t.is('textarea'))
				return t.val();
			else
				return t.text();	
		}
		else
			return false;
	};

	return CustomWidget;
});
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
/****************************************************************************************************
 *
 *                        Architekt.module.Formatter: Formatting module
 *
 ****************************************************************************************************/

Architekt.module.reserv('Formatter', function(options) {
	return {
		currency: function(data, options) {
			if(isNaN(data)) return data;

			data = parseFloat(data);

			options = typeof options === 'object' ? options : {};

			var delimiter = typeof options.delimiter !== 'undefined' ? options.delimiter : ',';
			var symbol = typeof options.symbol !== 'undefined' ? options.symbol : '$';
			var symbolPos = typeof options.symbolPos !== 'undefined' ? options.symbolPos : 'right';
			var drop = typeof options.drop !== 'undefined' ? +options.drop : 3;

			switch(symbolPos) {
				case 'left':
				case 'right':
					symbolPos = symbolPos;
					break;
				default:
					symbolPos = 'left';
					break;
			}

			//Check it is float number
			var t = data.toFixed(drop).split(".");	//remember that to Fixed returns string
			var resultNumber = '';

			//makeDot(string numberString): insert dot between each 3 characters
			function makeDot(numberString) {
				var result = '';
				var cnt = 0;    //Count variable for counting each 3 points.
				
				for(var i = numberString.length - 1; i >= 0; i--){
					result += numberString[i];
					
					if(++cnt >= 3 && numberString[i-1]) {
						result += delimiter;
						cnt = 0;
					}
				}
				
				return result.split("").reverse().join("");
			}

			//This is float number
			if(t.length > 1) {
				//The integer
				resultNumber = makeDot(t[0]) + '.'; //Calculate integer part + add point(.)
				cnt = 0;                //Reset counter
				
				//The real
				//resultNumber += makeDotReverse(t[1]);		//Under the zero is not make dots
				resultNumber +=  t[1];
			}
			else {
				resultNumber = makeDot(t[0]);
			}

			//if has symbol,
			if(symbol) {
				if(symbolPos === 'left')
					resultNumber = symbol + ' ' + resultNumber;
				else 
					resultNumber = resultNumber + ' ' + symbol;	
			}
			
			return resultNumber;
		},
	};
});
/****************************************************************************************************
 *
 *      Architekt.module.Guardian: Error catch module
 *
 ****************************************************************************************************/

Architekt.module.reserv('Guardian', function(options) {
	var self = this;
	this.event = new Architekt.EventEmitter(['onerror']);

	return {
		event: this.event,
		catch: function(code) {
			try {
				code();
			}
			catch(err) {
				self.event.fire('onerror', err);
			}
		}
	};
});
/****************************************************************************************************
 *
 *      Architekt.module.Http: Asynchronous HTTP request module
 *
 ****************************************************************************************************/
Architekt.module.reserv({
	name: 'Http',
	deps: ['Printer'],
}, function(options) {
	var printLog = Architekt.module.Printer.log;
	var printWarn = Architekt.module.Printer.warn;
	var printError = Architekt.module.Printer.err;

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
		var delayed = typeof data.delayed === 'function' ? data.delayed : function() { };
		var after = typeof data.after === "function" ? data.after : function () { };
		var timeout = typeof data.timeOut !== 'undefined' ? +data.timeOut : 10000;	//10sec
		
		printLog('Architekt.module.Http: send HTTP request...');
		printLog(method.toUpperCase() + ' ' + url);
		printLog('header: ' + JSON.stringify(headers) + ', data: ' + JSON.stringify(dataObject));

		$.ajax({
			timeout: timeout,
			url: url,
			data: dataObject,
			headers: headers,
			'type': method,
			dataType: dataType,
			success: function (result) {
				printLog('Architekt.module.Http: Http sent success.');
				printLog(method.toUpperCase() + ' ' + url);
				printLog(JSON.stringify(result));
				suc(result);
			},
			error: function (errorObject, textStatus, errorThrown) {
				printError('Architekt.module.Http: error detected');

				textStatus = textStatus || "";
				errorText = errorThrown || "";


				var responseObject = {};	//object for actual throw into the error callback

				responseObject.message = textStatus;
				responseObject.description = errorThrown;
				responseObject.statusCode = errorObject.status;
				responseObject.response = errorObject.responseJSON || {};


				//print variables for debug
				printLog(method.toUpperCase() + ' ' + url);
				printLog(responseObject.statusCode + ' ' + errorText);
				printLog('Sent Header: ' + JSON.stringify(headers));
				printLog('Sent Data: ' + JSON.stringify(dataObject));
				printLog('Status: ' + JSON.stringify(textStatus));
				printLog('Reponse: ' + JSON.stringify(responseObject));
				
				err(responseObject);
			},
			complete: function () {
				_ajax_work = false;
				printLog('Architekt.module.Http: request over.');
				comp();
				after();
			}
		});
		
		//Give notice if processing take too long
		setTimeout(function() {
			if(_ajax_work) {
				printLog('Architekt.module.Http: request taking too long ( >=5000ms )');
				delayed();	//call delayed callback
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
		"ko": { },
		"en": { },
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

				//function for get space between of the depth
				function _getSpaceBetween(depth) {
					var spaceStr = '';
					//Depth * 4 blank spaces
					for(var i = 0; i < ((depth + 1) * spaceBetween); i++) spaceStr += ' ';

					return spaceStr;
				}

				//function that check the key is in the ignore list
				//this use for filtering ignoring properties
				function _checkInIgnoreList(text) {
					for(var i = 0, len = ignoreList.length; i < len; i++)
						if(ignoreList[i].test(text)) return true;

					return false;
				}

				//function for actual inspect object. it recursive until no more sub property.
				function _inspectObject(targetObj, depth) {
					//Make space by depth of the object tree (space = depth * 4)
					var bracketSpace = _getSpaceBetween(depth - 1);
					var propertySpace = _getSpaceBetween(depth);

					//first bracket space
					console.log(bracketSpace + "{");

					for(var key in targetObj) {
						//Make sure that property is not linked in Prototype
						if(targetObj.hasOwnProperty(key) && !_checkInIgnoreList(key)) {
							//If the property is typeof of object, increase depth and search
							if(typeof targetObj[key] === 'object') {
								console.log(propertySpace + '[' + (typeof targetObj[key]) + '] ' + key);

								//check that depth level reached to specified depth
								if(maxDepth === false || (maxDepth && depth < maxDepth))
									_inspectObject(targetObj[key], (depth + 1));	//Recursive with inside of the object
							}
							else {
								//if property is function and display function code flag is unset, just say it is function without code
								if(typeof targetObj[key] === 'function' && !displayFunctionCode) {
									console.log(propertySpace + '[' + (typeof targetObj[key]) + '] ' + key);
								}
								else
									console.log(propertySpace + '[' + (typeof targetObj[key]) + '] ' + key + ": " + targetObj[key]);
							}

						}
					}

					//last bracket space
					console.log(bracketSpace + "}");
				}

				//start inspect!
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
		integer: /^\d+$/,
		real: /^[+-]?\d+(\.\d+)?$/,
		alphabet: /^[a-zA-Z]*$/,
		alphanumeric: /^[a-z0-9]+$/i,
	};

	//equations
	formular.numeric = formular.number = formular.real;

	return {
		//Architekt.module.Validator.is(string type, string string): Validate type of the string
		is: function(string, type) {
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
		//Architekt.module.Validator.check(string type, string string): Alias of Validator.is
		check: function(string, type) {
			return this.is(string, type);
		},
		//Architekt.module.Validator.empty(string string): Returns true if the string is empty or null or undefined
		empty: function(string) {
			if(typeof string === 'undefined' || string === '' || string === null) return true;
			return false;
		},
		//Architekt.module.Valditor.checkIfNotEmpty(string string, string type): Check the string type if it is not empty. Empty just returns true.
		checkIfNotEmpty: function(string, type) {
			if(!this.empty(string)) {
				return this.check(string, type);
			}

			return true;
		},
		//Architekt.module.Validator.length(string string, string comparator, int length): Check the string length with comparator
		length: function(string, comparator, length) {
			var result = false;
			comparator = typeof comparator !== 'undefined' ? comparator : '=';
			length = typeof length !== 'undefined' ? +length : 0;

			switch(comparator) {
				case '>':
					result = (string.length > length) ;
					break;
				case '>=':
					result = (string.length >= length) ;
					break;
				case '<':
					result = (string.length < length) ;
					break;
				case '<=':
					result = (string.length <= length) ;
					break;
				case '=':
					result = (string.length === length) ;
					break;
				default:
					result = false;
					break;
			}

			return result;
		},
		//Architekt.module.Validator.formular(int leftSide, int rightSide, function filter): Create formular
		formular: function(leftSide, rightSide, filter) {
			return filter(parseFloat(leftSide), parseFloat(rightSide));
		},
		//Architekt.module.Validator.equal(int leftSide, int rightSide): Check both sides are same
		equal: function(leftSide, rightSide) {
			return this.formular(leftSide, rightSide, function(a, b) {
				if(a === b)
					return true;

				return false;
			});
		},
		//Architekt.module.Validator.less(int leftSide, int rightSide): Compare that left side is less than right
		less: function(leftSide, rightSide) {
			return this.formular(leftSide, rightSide, function(a, b) {
				if(a < b)
					return true;

				return false;
			});
		},
		//Architekt.module.Validator.lessEqual(int leftSide, int rightSide): Compare that left side is less  than right or equal
		lessEqual: function(leftSide, rightSide) {
			return this.formular(leftSide, rightSide, function(a, b) {
				if(a <= b)
					return true;

				return false;
			});
		},
		//Architekt.module.Validator.greater(int leftSide, int rightSide): Compare that left side is greater than right
		greater: function(leftSide, rightSide) {
			return this.formular(leftSide, rightSide, function(a, b) {
				if(a > b)
					return true;

				return false;
			});
		},
		//Architekt.module.Validator.greaterEqual(int leftSide, int rightSide): Compare that left side is greater than right or equal
		greaterEqual: function(leftSide, rightSide) {
			return this.formular(leftSide, rightSide, function(a, b) {
				if(a >= b)
					return true;

				return false;
			});
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
			var co = this.controlObject;

			co.fadeOut(200, function() {
				co.remove();
				co = null;
			});

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
