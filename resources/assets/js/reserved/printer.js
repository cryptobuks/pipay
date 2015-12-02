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

	function getDate(dateObj) {
		return (dateObj.getFullYear() + "-" + ("0" + (dateObj.getMonth() + 1)).substr(0,2) + "-" + ("0" + dateObj.getDate()).substr(0,2));
	}
	function getTime(dateObj) {
		return (("0" + dateObj.getHours()).substr(0,2) + ":" + ("0" + dateObj.getMinutes()).substr(0,2) + ":" + ("0" + dateObj.getSeconds()).substr(0,2));
	}
	function getFormatedDate() {
		var date = new Date();
		var printStr = (printDate ? getDate(date) + " " : "") + getTime(date);

		return printStr;
	}
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
		//Architekt.module.Printer.inspect(object obj): Inspect object properties
		inspect: function(obj) {
			if(logLevel >= 3) return;

			if(typeof obj !== 'object') {
				console.log(getDebugText('INSPECT', 'Parameter obj is not a object.'));
			}
			else {
				console.log(getDebugText('INSPECT', obj.constructor.name));

				var spaceBetween = 4;
				var treeDepth = 0;

				//function for get between space of the depth
				function _getSpaceBetween(depth) {
					var spaceStr = '';
					//Depth * 4 blank spaces
					for(var i = 0; i < ((depth + 1) * spaceBetween); i++) spaceStr += ' ';

					return spaceStr;
				}

				//function for actual inspect object. it is recursive until property exists.
				function _inspectObject(targetObj, depth) {
					//Make spaces by depth of the object tree
					var bracketSpace = _getSpaceBetween(depth - 1);
					var propertySpace = _getSpaceBetween(depth);

					console.log(bracketSpace + "{");

					for(var key in targetObj) {
						//Make sure that property is not linked in Prototype
						if(targetObj.hasOwnProperty(key)) {
							console.log(propertySpace + '[' + (typeof targetObj[key]) + '] ' + key);

							//If the property is typeof of object, increase depth and search
							if(typeof targetObj[key] === 'object') {
								_inspectObject(targetObj[key], (depth+1));	//Recursive with inside of the object
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