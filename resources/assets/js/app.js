Architekt.event.on('ready', function() {
	new Architekt.module.Widget.Confirm({
		text: 'Press OK to see Notice widget.',
		confirmText: 'OK',
		callback: function() {
			new Architekt.module.Widget.Notice({
				text: 'I am Notice widget!',
			});
		}
	});
});