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