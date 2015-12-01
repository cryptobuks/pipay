Architekt.module.reserv('Locale', function(options) {
	var namespace = this;
	var currentLocale = 'ko_kr';
	var localeStrings = {
		"ko_kr": {

		},
		"us_en": {

		},
	};

	return {
		//Architekt.module.Locale.setLocale(string newLocale): Set new locale
		setLocale: function(newLocale) {
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