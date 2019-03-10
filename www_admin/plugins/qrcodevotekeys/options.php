<?php

defined('ADMIN_DIR') || exit();

if (isset($_POST['qrcodevotekeys_register_url'])) {
	update_setting('qrcodevotekeys_register_url', $_POST['qrcodevotekeys_register_url']);
}

if (isset($_POST['qrcodevotekeys_pixelsize'])) {
	update_setting('qrcodevotekeys_pixelsize', $_POST['qrcodevotekeys_pixelsize']);
}

if (isset($_POST['qrcodevotekeys_generate_type'])) {
	update_setting('qrcodevotekeys_generate_type', $_POST['qrcodevotekeys_generate_type']);
}

$qrcodevotekeys_register_url = $settings['qrcodevotekeys_register_url'] ?? 'http://party.lan/index.php?page=Login&votekey={%VOTEKEY%}';
$qrcodevotekeys_pixelsize = ((int)($settings['qrcodevotekeys_pixelsize'] ?? 2)) ?? 2;
$qrcodevotekeys_generate_type = $settings['qrcodevotekeys_generate_type'] ?? 'image';
?>
<style>
	#qrcodevotekeys_form input[type="text"] { width: 500px; }
</style>

<h2>Votekeys with QR code</h2>

<form action="<?php echo _html($_SERVER['REQUEST_URI']); ?>" method="post" enctype="multipart/form-data" id="qrcodevotekeys_form">
	<label>Register URL (Used for QRCode, <b>{%VOTEKEY%}</b> will be substituted):</label>
	<input name="qrcodevotekeys_register_url" type="text" value="<?php echo _html($qrcodevotekeys_register_url); ?>">

	<label>Size of QR Code pixel:</label>
	<select name="qrcodevotekeys_pixelsize">
		<?php for ($i = 1; $i <= 32; $i++): ?>
			<option value="<?php echo $i; ?>"<?php echo ($qrcodevotekeys_pixelsize === $i ? ' selected' : ''); ?>><?php echo $i; ?> px</option>
		<?php endfor; ?>
	</select>
	<label>QR Code generate type:</label>
	<select name="qrcodevotekeys_generate_type">
		<option value="image"<?php echo ($qrcodevotekeys_generate_type == 'image' ? ' selected' : ''); ?>>Image</option>
		<option value="table"<?php echo ($qrcodevotekeys_generate_type == 'table' ? ' selected' : ''); ?>>Table</option>
		<option value="inlineimage"<?php echo ($qrcodevotekeys_generate_type == 'inlineimage' ? ' selected' : ''); ?>>Inline image</option>
	</select>
	<input type="submit" value="Save"/>
</form>
