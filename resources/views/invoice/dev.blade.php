<style>
	#payment_beginning_ball {
		@include createPosition(absolute, 50%, unset, unset, 0%);
		margin-top: -50px;
		margin-left: -50px;
		width: 100px;
		height: 100px;
		background-color: #2c6da6;
		@include createBorderRadius(100px);
	}
</style>

<script>
	Architekt.event.on('ready', function() {
		var dom = {
			ball: $('#payment_beginning_ball'),
			moduleBackground: $('#payment_module_background'),
			module: $('#payment_module'),
			navs: $('.payment-nav-item'),
			tabs: $('.payment-tab'),
		};
		
		function attachEvents() {
			//events
			var _is_switching_mode = false;
			
			dom.navs.click(function() {
				if(_is_switching_mode) return;

				_is_switching_mode = true;

				var idx = $(this).index();

				dom.navs.removeClass('on');
				$(this).addClass('on');

				dom.tabs.fadeOut(300, function() {
					dom.tabs.eq(idx).fadeIn(300, function() {
						_is_switching_mode = false;
					});
				});
			});
		}

		//initialize animations
		dom.ball.animate({
			left: '50%'
		}, 600, 'swing', function() {
			dom.ball.animate({
				height: '500px',
				width: '380px',
				'margin-top': '-250px',
				'margin-left': '-190px',
				'border-radius': '5px',
				'-webkit-border-radius': '5px',
				'-moz-border-radius': '5px',
				'-o-border-radius': '5px',
				'-ms-border-radius': '5px',
			}, 600, 'swing', function() {
				dom.moduleBackground.fadeIn(600);

				dom.tabs.eq(0).fadeIn(300, attachEvents);
			});
		});
	});
</script>

<div id="payment_beginning_ball"></div>