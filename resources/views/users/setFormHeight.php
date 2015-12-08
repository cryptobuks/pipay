	<script>
		Architekt.event.on('preparing', function() {
			//Get height
			var h = Architekt.device.height;
			h -= 80;	//gnb height
			h -= 143;	//footer height
			
			$('#pi_auth > .pi-container').css('height', h + 'px');
		});
	</script>