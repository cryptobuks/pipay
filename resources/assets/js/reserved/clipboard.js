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