Architekt.event.on('ready', function() {
	Architekt.module.Printer.setLevel(0);
	Architekt.module.Printer.log('Helloworld!');
	Architekt.module.Printer.warn('You have warning!');
	Architekt.module.Printer.error('You have error');
	Architekt.module.Printer.inspect({
		lorem: 'ipsum',
		dolor: 'sit amet',
		another: {
			lorem: 'ipsum',
			dolor: 'sit',
			andAnother : {
				lorem: 'ipsum',
			},
		},
		method: function() {
			return true;
		},
	});
});