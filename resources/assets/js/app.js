Architekt.event.on('ready', function() {
	Architekt.module.Printer.setLevel(0);

	/* Common tasks */
	//Radio and check box label: on click, simulate click on input
	$('.pi-radio > label').click(function() {
		$(this).prev('input').trigger('click');
	});
	$('.pi-checkbox > label').click(function() {
		$(this).prev('input').trigger('click');
	});
});