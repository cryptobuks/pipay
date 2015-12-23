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
		var Validator = Architekt.module.Validator;

		//validations
		var itemDesc = $('#item_desc');
		var orderId = $('#order_id');
		var amount = $('#amount');
		var email = $('#email');
		var redirectUrl = $('#redirect');
		var ipn = $('#ipn');

		function _error(text, focus, reset) {
			new Notice({
				text: text,
				callback: function() {
					focus.focus();

					if(reset) reset.val('');
				}
			});
		}

		/* check empty */
		if(!itemDesc.val()) {
			_error('상품명을 입력해주세요.', itemDesc);
			return false;
		}
		else if(!amount.val()) {
			_error('상품 가격을 입력해주세요.', amount);
			return false;
		}

		/* data format */
		if(itemDesc.val().length < 2) {
			_error('상품명은 2글자 이상으로 입력해주세요.', itemDesc);
			return false;
		}
		else if(!Validator.checkIfNotEmpty('alphanumeric', orderId.val())) {
			_error('상품번호는 영문자와 숫자의 조합만 사용가능합니다.', orderId);
			return false;
		}
		else if(!Validator.check('numeric', amount.val())) {
			_error('상품가격은 숫자로 입력해주세요.', amount);
			return false;
		}
		else if(!Validator.checkIfNotEmpty('email', email.val())) {
			_error('이메일은 이메일 형식으로 입력해주세요!', email);
			return false;
		}
		else if(!Validator.checkIfNotEmpty('url', redirectUrl.val())) {
			_error('결제 후 연결할 URL주소는 URL 형식으로 입력해주세요!', redirectUrl);
			return false;
		}
		else if(!Validator.checkIfNotEmpty('url', ipn.val())) {
			_error('IPN은 URL 형식으로 입력해주세요!', ipn);
			return false;
		}

		_isSubmittingCreateProduct = true;

		//Send POST request
		Architekt.module.Http.post({
			url: url,
			data: {
				'item_desc': itemDesc.val(),
				'order_id': orderId.val(),
				amount: amount.val(),
				email: email.val(),
				url: redirectUrl.val(),
				ipn: ipn.val(),
			},
			success: function(data) {
				var cipher = data.crypt;

				$('#pi_product_generate').fadeOut(600, function() {

					$('#pi_product_generated').fadeIn(600);
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