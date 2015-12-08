<?php
	$errorString = '';

	foreach($errors->all() as $error)
		$errorString .= $error;
?>
	<script>
		Architekt.event.on('ready', function() {
			var errorString = '<?= $errorString ?>';

			new Architekt.module.Widget.Notice({
				text: errorString,
				callback: function() {
					$('#email').focus();
				}
			});
		});
	</script>