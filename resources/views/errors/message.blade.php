@if (Session::has('flash_notification.message'))
<script>
	Architekt.event.on('ready', function() {

    @if (Session::has('flash_notification.overlay'))
		new Architekt.module.Widget.Notice({
			text: '{!! Session::get('flash_notification.message') !!}',
		});
	@else
		//똑같은걸 왜 또뿌리나요 실장님 이해가 안가는데
		new Architekt.module.Widget.Notice({
			text: '{!! Session::get('flash_notification.message') !!}',
		});
	@endif

	});
</script>
@endif
