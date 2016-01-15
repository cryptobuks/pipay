/****************************************************************************************************
 *
 *                        Architekt.module.Watcher: Error catching module
 *
 ****************************************************************************************************/

 Architekt.module.reserv('Watcher', function(options) {
 	options = typeof options === 'object' ? options : {};

 	this.event = new Architekt.EventEmitter(['onerror']);

 	function attempt(task) {
 		try {
 			task();
 		}
 		catch(err) {
 			this.event.fire('onerror', err);
 		}
 	};

 	return {
 		attempt: attempt
 	}
 });