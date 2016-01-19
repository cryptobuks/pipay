/****************************************************************************************************
 *
 *      Architekt.module.Guardian: Error catch module
 *
 ****************************************************************************************************/

Architekt.module.reserv('Guardian', function(options) {
	var self = this;
	this.event = new Architekt.EventEmitter(['onerror']);

	return {
		event: this.event,
		catch: function(code) {
			try {
				code();
			}
			catch(err) {
				self.event.fire('onerror', err);
			}
		}
	};
});