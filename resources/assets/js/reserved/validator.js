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