<?php
include_once("bootstrap.inc.php");
include_once("qrcode.php");

$qrcode_pixelsize =  $settings["votekeys_qrcode_pixelsize"] ?: 2;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
 <title></title>
 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2" />
<style type="text/css">
body {
  font-family: arial;
  margin: 0px;
  padding: 0px;
}

ul {
  margin: 0px;
  padding: 0px;
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
  margin: 0px;
  padding: 0px;
}
.qr td {
  width: <?=$qrcode_pixelsize?>px;
  height: <?=$qrcode_pixelsize?>px;
}
.qr td.dark {
  background-color: #000;
}
<?=($settings["votekeys_css"] ?: "")?>
</style>
</head>
<body>
<?php
printf("<ul class='votekeys'>");
$n = 1;
$s = SQLLib::selectRows("select * from votekeys");
$format = $settings["votekeys_format"] ?: "{%VOTEKEY%}";
$qrcode_enabled =  $settings["votekeys_qrcode_enabled"] ?: false;
$qrcode_url = $settings["votekeys_qrcode_register_url"] ?: "http://party.lan/index.php?page=Login&votekey={%VOTEKEY%}";
foreach($s as $t) {
  $votekeyhtml = str_replace("{%VOTEKEY%}",$t->votekey,$format);

  if ($qrcode_enabled) {
    $votekeyurl = str_replace("{%VOTEKEY%}",$t->votekey,$qrcode_url);

    $qr = QRCode::getMinimumQRCode($votekeyurl, QR_ERROR_CORRECT_LEVEL_L);

    $qrcodehtml = "<table>";
    for ($r = 0; $r < $qr->getModuleCount(); $r++) {
      $qrcodehtml .= "<tr>";
      for ($c = 0; $c < $qr->getModuleCount(); $c++) {
        $isdark = $qr->isDark($r, $c);
        if ($isdark) {
          $qrcodehtml .= '<td class="dark"></td>';
        } else {
          $qrcodehtml .= '<td></td>';
        }
      }
      $qrcodehtml .= "</tr>";
    }
    $qrcodehtml .= "</table>";

    printf('  <li><span class="qr">%s</span>%s</li>', $qrcodehtml, $votekeyhtml);
  } else {
    printf('  <li>%s</li>', $votekeyhtml);
  }
}
printf("</ul>");

?>
</body>
</html>
