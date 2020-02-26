<?php
require_once(__DIR__.'/qrcode.php');

if (isset($_GET['url'])) {
	$qr = QRCode::getMinimumQRCode($_GET['url'], QR_ERROR_CORRECT_LEVEL_L);
	$pixelsize = ((int)($_GET['size'] ?? 2)) ?? 2;

	$im = $qr->createImage($pixelsize, $pixelsize);
	header('Content-type: image/gif');
	imagegif($im);
	imagedestroy($im);
}
