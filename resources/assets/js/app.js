Architekt.event.on('ready', function() {
	Architekt.module.Printer.setLevel(0);

	Architekt.module.Comparator.start();
	setTimeout(function() {
		Architekt.module.Comparator.check();

		setTimeout(function() {
			Architekt.module.Comparator.check('Custom check string');

			setTimeout(function() {
				var result = Architekt.module.Comparator.stop();
				Architekt.module.Printer.inspect(result);
			}, 750);
		}, 500);
	}, 750);
});