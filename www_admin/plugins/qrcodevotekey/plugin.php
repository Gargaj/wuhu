<?php
/*
Plugin name: QR code votekey
Description: Add ability to scan QR code votekey at register
*/
if (!defined('ADMIN_DIR')) { exit(); }

function qrcodevotekey_inject_javascript() {
?>
<script type="text/javascript">
	(function() {
		try {
			var errorMessage = <?php
				$qrcodevotekey_errormessage = get_setting('qrcodevotekey_errormessage');
				if (!empty($qrcodevotekey_errormessage) && is_string($qrcodevotekey_errormessage)) {
					echo json_encode($qrcodevotekey_errormessage);
				} else {
					echo 'null';
				}
			?>;
			<?php echo file_get_contents(dirname(__FILE__).'/js/jsqrcode.min.js'); ?>
			<?php echo file_get_contents(dirname(__FILE__).'/js/plugin.min.js'); ?>
		} catch (error) {
		}
	})();
</script>
<?php
}
add_hook('register_endform', 'qrcodevotekey_inject_javascript');

function qrcodevotekey_addmenu($data) {
	$data['links']['pluginoptions.php?plugin=qrcodevotekey'] = "QR code votekey";
}
add_hook('admin_menu', 'qrcodevotekey_addmenu');

function qrcodevotekey_activation() {
	if (get_setting('qrcodevotekey_errormessage') === null) {
		update_setting('qrcodevotekey_errormessage', "No QR code found. Please make sure the QR code is within the camera's frame and try again.");
	}
}
add_activation_hook(__FILE__, 'qrcodevotekey_activation');
