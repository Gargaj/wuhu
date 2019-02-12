<?php
if (!defined('ADMIN_DIR') || !defined('PLUGINOPTIONS')) { exit(); }

if (!empty($_POST['qrcodevotekey_errormessage'])) {
	update_setting('qrcodevotekey_errormessage', $_POST['qrcodevotekey_errormessage']);
}
?>
<form action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="post">
	<h3>Options</h3>

	<label for="qrcodevotekey_errormessage">Error message string:</label>
	<textarea id="qrcodevotekey_errormessage" name="qrcodevotekey_errormessage" rows="4" style="height: 4em;"><?php echo htmlentities(get_setting('qrcodevotekey_errormessage')); ?></textarea>

	<input type="submit" />
</form>
