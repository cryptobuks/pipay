Architekt.event.on('ready', function() {
	var Client = Architekt.module.Client;
	var Http = Architekt.module.Http;
	var Printer = Architekt.module.Printer;
	var Validator = Architekt.module.Validator;
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
		ucp: {
			shown: false,
			container: $('#payment_module_ucp'),
			email: $('#ucp_email'),
			logout: $('#ucp_logout'),
			show: function() {
				dom.ucp.shown = true;

				dom.ucp.container.stop(true, true).css({
					height: 0,
					padding: 0,
				}).animate({
					height: '32px',
					padding: '8px',
				}, 300, 'swing');

				//resize whole module
				dom.module.css({
					height: '532px',
					'margin-top': '-266px',
				});

				return this;
			},
			hide: function() {
				dom.ucp.shown = false;

				dom.ucp.container.stop(true, true).css({
					height: '32px',
					padding: '8px',
				}).animate({
					height: 0,
					padding: 0
				}, 300, 'swing');

				//resize whole module
				dom.module.css({
					height: '500px',
					'margin-top': '-250px',
				});

				return this;
			},
		},
	};
	var qrCode = new QRCode($('#qrcode').get(0), Architekt.productInfo.address);
	var component = {
		easy: {
			email: $('#email'),
			password: $('#password'),
			submit: $('#submitLogin'),
			reset: function() {
				$('#email').val('');
				$('#password').val('');
			},
		},
		sms: {
			form: $('#smsForm'),
			code: $('#authcode'),
			cipher: $('#cipher_id'),
			submit: $('#submitSms'),
			reset: function() {
				$('#authcode').val('');
				$('#cipher_id').val('');
			},
		},
		pay: {
			submit: $('#pay'),
		}
	};
	var currentTab = null;
	var previousTab = null;	//remember the preivous tab on switch the tab

	//socket for check pi-address payment(pi sending)
	var paymentSocket = io('http://devpay.pi-pay.net:8800');


	//common error handler for validations
	function _error(text, focus, reset) {
		new Notice({
			text:text,
			callback: function() {
				if(focus) focus.focus();
				if(reset) reset.val('');
			}
		});
	}

	function appInit() {
		//module settings here:
		Printer.setLevel(0);

		
		attachEvents();
		componentActions();


		//if user already logged in, display the user info and move to payment tab
		if(typeof Architekt.userInfo.email !== 'undefined') {
			currentTab = dom.tabs.payment;
			dom.ucp.email.text(Architekt.userInfo.email);
			dom.ucp.show();

			$('#payment_balance > h1').text(Architekt.userInfo.name + '님의 잔고');
			$('#payment_balance > p').text(parseFloat(Architekt.userInfo.balance).toFixed(1) + ' Pi');
		}
		else {
			currentTab = dom.tabs.easy;

			setTimeout(function() {
				component.easy.email.focus();
			}, 100);
		}

		currentTab.fadeIn(200);
	}

	/*****************************************************************************************
	 *
	 *
	 * 								Animation functions
	 *
	 *
	 *****************************************************************************************/

	var animation = {
		submitLogin: {
			isPlaying: false,
			play: function() {
				if(this.isPlaying) return;
				this.isPlaying = true;

				component.easy.submit.addClass('locked');
				component.easy.email.attr('readonly', true);
				component.easy.password.attr('readonly', true);

				component.easy.submit.parent().addClass('loading');
			},
			stop: function() {
				if(!this.isPlaying) return;
				this.isPlaying = false;

				component.easy.submit.removeClass('locked');
				component.easy.email.removeAttr('readonly', true);
				component.easy.password.removeAttr('readonly', true);

				component.easy.submit.parent().removeClass('loading');
			},
		},
		submitSms: {
			isPlaying: false,
			play: function() {
				if(this.isPlaying) return;
				this.isPlaying = true;

				component.sms.submit.addClass('locked');
				component.sms.code.attr('readonly', true);

				component.sms.submit.parent().addClass('loading');
			},
			stop: function() {
				if(!this.isPlaying) return;
				this.isPlaying = false;

				component.sms.submit.removeClass('locked');
				component.sms.code.removeAttr('readonly', true);

				component.sms.submit.parent().removeClass('loading');
			},
		},
		pay: {
			isPlaying: false,
			play: function() {
				if(this.isPlaying) return;
				this.isPlaying = true;

				component.pay.submit.addClass('locked');

				component.pay.submit.parent().addClass('loading');
			},
			stop: function() {
				if(!this.isPlaying) return;
				this.isPlaying = false;

				component.pay.submit.removeClass('locked');

				component.pay.submit.parent().removeClass('loading');
			},
		},
	};



	 /*****************************************************************************************
	 *
	 *
	 * 							Application manipulation functions
	 *
	 *
	 *****************************************************************************************/

	function attachEvents() {
		var _isSubmit = false;	//check for form submit, prevent event duplication
		var _is_switching_mode = false; //variable for check navigation switching animation
		
		// exit button
		$('#payment_module_exit').click(function() {
			window.close();
		});

		// 	navigation click: switch tab and navigation
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
		var _isSubmit = false;
		dom.tabs.easy.submit(function() {
			if(_isSubmit) return;

			var email = component.easy.email;
			var password = component.easy.password;

			if(!email.val()) {
				_error('이메일을 입력해주세요.', email);
				return false;
			}
			else if(!password.val()) {
				_error('비밀번호를 입력해주세요.', password);
				return false;
			}

			//validation
			if(!Validator.is(email.val(), 'email') ) {
				_error('이메일은 이메일 형식에 맞춰 입력해주세요.', email);
				return false;
			}
			else if(Validator.length(password.val(), '<', 6)) {
				_error('비밀번호는 6자리 이상을 입력해주세요.', password);
				return false;
			}


			_isSubmit = true;
			animation.submitLogin.play();

			Http.post({
				url: '/oauth/loginOnce',
				data: {
					email: component.easy.email.val(),
					password: component.easy.password.val(),
				},
				success: function(data) {
					var cipher = data.id;

					component.sms.reset();
					component.sms.cipher.val(cipher);

					component.easy.reset();

					dom.tabs.easy.fadeOut(300, function() {
						currentTab = dom.tabs.sms;

						dom.tabs.sms.fadeIn(300, function() {
							component.sms.code.focus();
						});
					});
				},
				error: function(err) {
					var errText = '서버에 오류가 발생하였습니다. 관리자에게 문의해주세요.';
					var errorMessage = err.response.status;
					
					if(errorMessage === 'failed') {
						errText = '잘못된 이메일이나 비밀번호입니다.';
					}
					else if(errorMessage === 'invalid_email') {
						errText = '유효하지 않은 이메일 주소입니다.';
					}
					else if(errorMessage === 'invalid_password') {
						errText = '유효하지 않은 비밀번호입니다.';
					}
					
					new Notice({
						text: errText,
						callback: function() {
							email.focus();
						}
					});

					Printer.inspect(err);
				},
				complete: function() {
					_isSubmit = false;
					animation.submitLogin.stop();
				}
			});			

			//don't forget to no submit!
			return false;
		});

		//easy payment - sms
		component.sms.form.submit(function() {
			if(_isSubmit) return false;

			var code = component.sms.code;
			var cipher = component.sms.cipher;

			if(!code.val()) {
				_error('인증 번호를 입력해주세요.', component.sms.code);
				return false;
			}

			//validation
			if(!Validator.check(code.val(), 'numeric')) {
				_error('인증 번호는 숫자로 입력해주세요.', code, code);
				return false;
			}
			else if(Validator.length(code.val(), '<', 5)) {
				_error('인증 번호는 5자리 입니다.', code);
				return false;
			}


			_isSubmit = true;
			animation.submitSms.play();

			Http.post({
				url: '/oauth/smsAuth',
				data: {
					'authcode': code.val(),
					'cipher_id': cipher.val(),
				},
				success: function(data) {
					var balance = data.balance;
					var username = data.username;
					var email = data.email;

					component.sms.reset();

					//update user info dom
					dom.ucp.email.text(email);
					dom.ucp.show();

					$('#payment_balance > h1').text(username + '님의 잔고');
					$('#payment_balance > p').text(parseFloat(balance).toFixed(1) + ' Pi');

					dom.tabs.sms.fadeOut(300, function() {
						currentTab = dom.tabs.payment;

						dom.tabs.payment.fadeIn(300);
					});
				},
				error: function(err) {
					var errText = '서버에 오류가 발생하였습니다. 관리자에게 문의해주세요.';
					var errorMessage = err.response.status;

					if(errorMessage === 'failed') {
						errText = '잘못된 인증코드입니다. 다시 입력해주세요.';
						code.val('');
					}

					new Notice({
						text: errText,
						callback: function() {
							code.focus();
						}
					});

					Printer.inspect(err);
				},
				complete: function() {
					_isSubmit = false;
					animation.submitSms.stop();
				}
			});

			return false;
		});

		//easy payment - pay: remember that pay tab is not a form
		component.pay.submit.click(function() {
			if(_isSubmit) return;


			_isSubmit = true;
			animation.pay.play();

			Http.post({
				url: '/invoice/payment',
				data: {
					token: Architekt.productInfo.token,
				},
				success: function(data) {
					paymentComplete = true;

					destroyNav();
					detachEvents();

					$('#payment_info').animate({
						height: '0',
						opacity: '0.0'
					}, 300, 'swing', function() {
						$(this).remove();
						component.pay.submit.addClass('done').text('결제완료').off('click');

						$('#payment_complete').delay(100).show().animate({
							height: '80px',
							opacity: '1.0',
							'margin-top': '80px',
						}, 300, 'swing', function() {
							$('#receipt').fadeIn(300);
						});
					});
				},
				error: function(err) {
					var errText = '서버에 오류가 발생하였습니다. 관리자에게 문의해주세요.';
					var errorMessage = err.response.status;
					
					new Notice({
						text: errText,
						callback: function() {
							email.focus();
						}
					});

					Printer.inspect(err);
				},
				complete: function() {
					_isSubmit = false;
					animation.pay.stop();
				}
			});
		});

		//ucp logout
		dom.ucp.logout.click(function() {
			Http.get({
				url: '/oauth/logout'
			});

			dom.ucp.hide();

			currentTab.fadeOut(300, function() {
				currentTab = dom.tabs.easy;

				dom.tabs.easy.fadeIn(300);
			});

			return false;
		});
	}

	function detachEvents() {
		dom.ucp.logout.off('click');
	}

	function destroyNav() {
		dom.navs.fadeOut(300, function() {
			$(this).remove();
			dom.navs = null;
		});	
	}

	//definition of common actions for components
	function componentActions() {
		//give focus effect on input
		$('.pi-payment-text > input').focus(function() {
			$(this).parent().addClass('on');
		}).blur(function() {
			$(this).parent().removeClass('on');
		});

		//auto select input
		$('div.pi-payment-text').click(function() {
			$(this).find('input').focus();
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
			detachEvents();

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