/****************************************************************************************************
 *
 *                        Architekt.module.Watcher: Error catching module
 *
 ****************************************************************************************************/

 Architekt.module.reserv('watcher', function(options) {
 	options = typeof options === 'object' ? options : {};

 	this.event = new Architekt.EventEmitter(['onerror', 'onwatch']);

 	this.see = function(task) {
 		try {
 			
 		}
 		catch {
 			
 		}
 	};

 	return {
 		see: see
 	}
 });