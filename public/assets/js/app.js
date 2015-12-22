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


	/* Create product */
	var _isSubmittingCreateProduct = false;
	$('#createProductForm').submit(function() {
		if(_isSubmittingCreateProduct) return;

		var url = $(this).attr('action');
		var Notice = Architekt.module.Widget.Notice;

		//validations
		var itemDesc = $('#item_desc');
		var orderId = $('#order_id');
		var amount = $('#amount');
		var email = $('#email');
		var redirectUrl = $('#redirect');
		var ipn = $('#ipn');

		console.log('item_desc: ' + itemDesc.val());


		_isSubmittingCreateProduct = true;

		//Send POST request
		Architekt.module.Http.post({
			url: url,
			data: {
				'item_desc': itemDesc.val(),
				'order_id': orderId.val(),
				amount: amount.val(),
				email: email.val(),
				redirect: redirectUrl.val(),
				ipn: ipn.val(),
			},
			success: function(data) {
				new Notice({
					text: data,
				});
			},
			error: function(text, status) {
				new Notice({
					text: (text + status).join(", "),
				});
			},
			complete: function() {
				_isSubmittingCreateProduct = false;
			}
		});

		return false;
	});
});
//# sourceMappingURL=app.js.map
