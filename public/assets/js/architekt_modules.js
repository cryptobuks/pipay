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
//# sourceMappingURL=architekt_modules.js.map
