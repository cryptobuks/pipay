Architekt.event.on('ready', function() {
	var dom = {
		moduleBackground: $('#payment_module_background'),
		module: $('#payment_module'),
		navs: $('.payment-nav-item'),
		tabs: $('.payment-tab'),
	};
	var component = {
		easy: {
			email: $('#email'),
			password: $('#password'),
		}
	}
	var currentTab = null;

	function appInit() {
		attachEvents();
		componentActions();

		component.easy.email.focus();
		currentTab = dom.tabs.eq(0);
	}
	
	function attachEvents() {
		//events
		var _is_switching_mode = false;
		
		dom.navs.click(function() {
			if(_is_switching_mode) return;

			_is_switching_mode = true;

			var idx = $(this).index();

			dom.navs.removeClass('on');
			$(this).addClass('on');


			currentTab.fadeOut(300, function() {
				currentTab = dom.tabs.eq(idx);
				currentTab.fadeIn(300, function() {
					_is_switching_mode = false;
				});
			});
		});
	}

	function componentActions() {
		$('.pi-payment-text > input').focus(function() {
			$(this).parent().addClass('on');
		}).blur(function() {
			$(this).parent().removeClass('on');
		});
	}

	//initialize animations
	dom.module.fadeIn(300, appInit);	
});
//# sourceMappingURL=pi_payment.js.map
