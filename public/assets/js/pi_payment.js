Architekt.event.on('ready', function() {
	var Printer = Architekt.module.Printer;
	var Notice = Architekt.module.Widget.Notice;
	var Confirm = Architekt.module.Widget.Confirm;

	var paymentComplete = false;	//this variable check for complete payment animation if already completed.
	var dom = {
		moduleBackground: $('#payment_module_background'),
		module: $('#payment_module'),
		navs: $('.payment-nav-item'),
		tabContainer: $('.payment-tab'),
		tabs: {
			easy: $('#tab_easy'),
			pi: $('#tab_pi'),
			sms: $('#tab_sms'),
			payment: $('#tab_payment'),
		},
	};
	var qrCode = new QRCode($('#qrcode').get(0), Architekt.productInfo.address);
	var component = {
		easy: {
			email: $('#email'),
			password: $('#password'),
		},
		sms: {
			form: $('#smsForm'),
			code: $('#authcode'),
			cipher: $('#cipher_id'),
			submit: $('#smsSubmit'),
		},
		pay: {
			pay: $('#pay'),
		}
	};
	var currentTab = null;
	var previousTab = null;
	//socket for check pi-address payment(pi sending)
	var paymentSocket = io('http://devpay.pi-pay.net:8800');

	function _error(text, focus) {
		new Notice({
			text:text,
			callback: function() {
				if(focus) focus.focus();
			}
		});
	}

	function appInit() {
		attachEvents();
		componentActions();

		component.easy.email.focus();
		currentTab = dom.tabs.easy;
	}
	
	function attachEvents() {
		var _is_switching_mode = false; //variable for check navigation switching animation
		
		//navigation click: switch tab and navigation
		dom.navs.click(function() {
			//check if clicked already activated nav, exit
			var idx = $(this).index();
			
			var targetTab = null;

			switch(idx) {
				case 0:
					targetTab = previousTab;
					break;
				case 1:
					targetTab = dom.tabs.pi;
					previousTab = currentTab;
					break;
			}

			if(currentTab === targetTab) return;
			else if(currentTab !== dom.tabs.pi && targetTab === previousTab) return;


			if(_is_switching_mode) return;
			_is_switching_mode = true;


			dom.navs.removeClass('on');
			$(this).addClass('on');

			currentTab.fadeOut(300, function() {
				currentTab = targetTab;
				currentTab.fadeIn(300, function() {
					_is_switching_mode = false;
				});
			});
		});

		//easy payment - login
		dom.tabs.easy.submit(function() {
			dom.tabs.easy.fadeOut(300, function() {
				currentTab = dom.tabs.sms;
				dom.tabs.sms.fadeIn(300);
			});

			return false;


			if(!component.easy.email.val()) {
				_error('이메일을 입력해주세요.', component.easy.email);
				return false;
			}
			else if(!component.easy.password.val()) {
				_error('비밀번호를 입력해주세요.', component.easy.password);
				return false;
			}

			
		});

		//easy payment - sms
		component.sms.form.submit(function() {
			dom.tabs.sms.fadeOut(300, function() {
				currentTab = dom.tabs.payment;
				dom.tabs.payment.fadeIn(300);
			});
			return false;

			if(!component.sms.code.val()) {
				_error('인증 번호를 입력해주세요.', component.sms.code);
				return false;
			}


			return false;
		});

		//easy payment - pay
		component.pay.pay.click(function() {
			paymentComplete = true;

			destroyNav();

			$('#payment_info').animate({
				height: '0',
				opacity: '0.0'
			}, 300, 'swing', function() {
				$(this).remove();
				component.pay.pay.addClass('done').text('결제 완료').off('click');

				$('#payment_complete').delay(100).show().animate({
					height: '80px',
					opacity: '1.0',
					'margin-top': '80px',
				}, 300, 'swing', function() {
					$('#receipt').fadeIn(300);
				});
			});
		});
	}

	function destroyNav() {
		dom.navs.fadeOut(300, function() {
			$(this).remove();
			dom.navs = null;
		});	
	}

	//definition of common actions for components
	function componentActions() {
		$('.pi-payment-text > input').focus(function() {
			$(this).parent().addClass('on');
		}).blur(function() {
			$(this).parent().removeClass('on');
		});
	}

	//execute on end of the pi address payment
	function onAddressPaymentEnd() {
		if(paymentComplete) return;
		paymentComplete = true;

		function _after() {
			var qrwrap = $('#qr_wrap');
			var completeDom = $('#complete');
			var priceDom = $('#tab_pi_price');

			destroyNav();

			qrwrap.animate({
				height: '0',
				opacity: '0.0'
			}, 300, 'swing', function() {
				qrwrap.remove();
				qrwrap = null;

				priceDom.addClass('done');

				completeDom.delay(100).show().animate({
					height: '150px',
					padding: '80px 32px 32px 32px',
					opacity: '1.0',
				}, 500);
			});
		}

		if(currentTab !== dom.tabs.pi) {
			currentTab.fadeOut(150, function() {
				dom.tabs.pi.fadeIn(150, _after);
			});
		}
		else
			_after();
	}

	/********************************************************************************
	 *
	 *
	 *                             Pi address payment
	 *
	 *
	 ********************************************************************************/

	var errorCheck = false;
	paymentSocket.on('invoice-channel.' + Architekt.productInfo.id + ':invoice.payment-start', onAddressPaymentEnd);
	paymentSocket.on('connect_error', function(data) {
		Printer.error('Error with payment socket.');
		Printer.inspect(data);

		if(!errorCheck) {
			errorCheck = true;

			new Confirm({
				text: '오류가 발생하여 파이 주소 결제가 제대로 동작하지 않습니다. [확인] 버튼을 누르면 페이지를 새로 불러옵니다.',
				callback: function() {
					location.reload();
				},
			});
		}
	});
	paymentSocket.on('error', function(data) {
		Printer.error('Error with payment socket.');
		Printer.inspect(data);
	});


	//initialize animations
	dom.module.fadeIn(300, appInit);
});
//# sourceMappingURL=pi_payment.js.map
