<?php
/*
Plugin name: Print Votekeys with QR code
Description: Add ability to print votekeys with QR code
*/

defined('ADMIN_DIR') || exit();

function qrcodevotekeys_generate_html($url) {
	require_once(__DIR__.'/qrcode.php');

	$qr = QRCode::getMinimumQRCode($url, QR_ERROR_CORRECT_LEVEL_L);

	$html = '<table>';
	for ($r = 0; $r < $qr->getModuleCount(); $r++) {
		$html .= '<tr>';
		for ($c = 0; $c < $qr->getModuleCount(); $c++) {
			$isdark = $qr->isDark($r, $c);
			if ($isdark) {
				$html .= '<td class="dark"></td>';
			} else {
				$html .= '<td></td>';
			}
		}
		$html .= '</tr>';
	}
	$html .= '</table>';

	return $html;
}

function qrcodevotekeys_print() {
	global $settings;

	$register_url = $settings['qrcodevotekeys_register_url'] ?? 'http://party.lan/index.php?page=Login&votekey={%VOTEKEY%}';
	$pixelsize = ((int)($settings['qrcodevotekeys_pixelsize'] ?? 2)) ?? 2;

	$format = $settings['votekeys_format'] ?: '{%VOTEKEY%}';
	$stylesheet = $settings['votekeys_css'] ?? '';
	$rows = SQLLib::selectRows('SELECT * FROM `votekeys`');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2" />
	<style type="text/css">
		body {
			font-family: arial;
			margin: 0;
			padding: 0;
		}
		ul {
			margin: 0;
			padding: 0;
		}
		li {
			list-style: none;
			padding: 15px;
			padding-top: 25px;
			padding-bottom: 25px;
			border: 1px dotted #ccc;
			text-align:center;
			font-size: 130%;
			letter-spacing: 2px;
		}
		.votekeys li {
			float: left;
			width: 25%;
		}
		.qr {
			display: block;
			padding-bottom: 10px;
		}
		.qr table {
			display: inline-block;
		}
		.qr table, .qr tr, .qr td {
			border-style: none;
			border-collapse: collapse;
			margin: 0;
			padding: 0;
		}
		.qr td {
			width: <?php echo $pixelsize; ?>px;
			height: <?php echo $pixelsize; ?>px;
		}
		.qr td.dark {
			background-color: #000;
		}

		<?php echo $stylesheet; ?>
	</style>
</head>
<body>
	<ul class="votekeys">
		<?php foreach($rows as $row): ?>
			<li>
				<span class="qr"><?php echo qrcodevotekeys_generate_html(str_replace('{%VOTEKEY%}', $row->votekey, $register_url)); ?></span>
				<?php echo str_replace('{%VOTEKEY%}', $row->votekey, $format); ?>
			</li>
		<?php endforeach; ?>
	</ul>
</body>
</html>
<?php
}

add_hook('admin_menu', function($data) {
	$data['links']['pluginoptions.php?plugin=qrcodevotekeys'] = 'QR code Votekeys';

});

add_hook('admin_page_start', function() {
	if (!empty($_GET['plugin']) && $_GET['plugin'] === 'qrcodevotekeys' && isset($_GET['print'])) {
		qrcodevotekeys_print();
		exit();
	}
});
