/****************************************************************************************************
 *
 *      Architekt.module.CustomWidget: Custom UI Widget
 *
 *
 *
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
		if(typeof this.close.click !== 'undefined') this.close.click(function() {
			self.hide();
		});
		
		//custom events processing
		for(var key in this.events) {
			(function (key) {
				var _t = key.split(" ");
				var selector = _t[0];
				var eventType = _t[1];
				var method = self.events[key];

				try {
					self.dom.find(selector).on(eventType, function() {
						if(typeof options[method] === 'function') options[method].call(null, self.data);
					});
				}
				catch(message) {
					Architekt.module.Printer.log('Architekt.module.CustomWidget: ' + message);
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
	CustomWidget.prototype.show = function() {
		var self = this;

		this.dom.show();

		//Fancy scale up animation
		setTimeout(function() {
			self.container.addClass('on');
		}, 25);

		return this;
	};
	CustomWidget.prototype.hide = function() {
		var self = this;

		this.dom.fadeOut();
		this.container.removeClass('on');
		return this;
	};
	CustomWidget.prototype.destroy = function() {
		if(this.dom != null) {
			this.dom.remove();
			this.dom = null;
		}
		return this;
	}
	CustomWidget.prototype.setData = function(dataObject) {
		dataObject = dataObject || {};
		this.data = dataObject;
		return this;
	};
	CustomWidget.prototype.getData = function(dataObject) {
		return this.data;
	};
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
				self.attributes[key].text(data);
			})(key);
		}

		return this;
	};


	return CustomWidget;
});