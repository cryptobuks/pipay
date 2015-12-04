@if (count($errors) > 0)
<?php
	$errorString = '';

	foreach($errors->all() as $error)
		$errorString .= $error;
?>
	<script>
		var errorString = '<?= $errorString ?>';

		Architekt.event.on('ready', function() {
			new Architekt.module.Widget.Notice({
				text: errorString,
			});
		});
	</script>
@endif