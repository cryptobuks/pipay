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