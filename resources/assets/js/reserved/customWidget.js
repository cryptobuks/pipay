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
		});;

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

		return this;
	};
	//Architekt.module.customWidget.hide(void): Hide widget
	CustomWidget.prototype.hide = function() {
		var self = this;

		this.dom.fadeOut(200, function() {
			self.container.removeClass('on');
		});
		
		return this;
	};
	//Architekt.module.customWidget.destroy(void): Destroy widget. means no more Dom element.
	CustomWidget.prototype.destroy = function() {
		if(this.dom != null) {
			this.dom.remove();
			this.dom = null;
		}
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
	//Architekt.module.customWidgetget(string key): Get the text or form value inside of dom element
	CustomWidget.prototype.get = function(key) {
		var t = this.attributes[key];

		if(typeof t === 'undefined')
			return false;

		if(t.is('input') || t.is('textarea'))
			return t.val();
		else
			return t.text();
	};

	return CustomWidget;
});