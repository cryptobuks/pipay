@if (Session::has('flash_notification.message'))
    @if (Session::has('flash_notification.overlay'))
	<script>
		window.addEventListener('load', function(){
			new Widget.Notice({
				text: '{!! Session::get('flash_notification.message') !!}',
				headText: 'Pi-Payment'
			});
		});
	</script>
	@else
	<script>
		window.addEventListener('load', function(){
			new Widget.Notice({
				text: '{!! Session::get('flash_notification.message')  !!}',
				headText: 'Pi-Payment'
			});
		});
	</script>
	@endif
@endif
