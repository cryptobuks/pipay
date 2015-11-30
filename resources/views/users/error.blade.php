<?php
	$errorString = '';

	foreach($errors->all() as $error)
		$errorString .= $error;
?>
	<script>
		var errorString = '<?= $errorString ?>';

		window.addEventListener('load', function(){
			new Widget.Notice({
				text: errorString,
				headText: 'Pi-Pay',
				callback: function(){
					$('#email').focus();
				},
				error: true
			});
		});
	</script>