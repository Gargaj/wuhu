<?php
include_once("header.inc.php");

if ($_POST["votekeys_format"])
{
  update_setting("votekeys_format",$_POST["votekeys_format"]);
}
if ($_POST["votekeys_css"])
{
  update_setting("votekeys_css",$_POST["votekeys_css"]);
}
if ($_POST["votekeys_qrcode_register_url"])
{
  update_setting("votekeys_qrcode_register_url",$_POST["votekeys_qrcode_register_url"]);
}
if ($_POST["votekeys_qrcode_form"])
{
  update_setting("votekeys_qrcode_enabled",$_POST["votekeys_qrcode_enabled"] ? "1" : "0");
}

if ($_POST["amount"])
{
  SQLLib::Query("truncate votekeys");
  $len = (int)$_POST["length"] ? (int)$_POST["length"] : 8;

  $abc = str_split("BCDFGHJKLMNPQRSTVWXYZ");
  for($x=0; $x<$_POST["amount"]; $x++)
  {
    $str = "";
    for ($y=0; $y<$len; $y++)
      $str .= $abc[ array_rand($abc) ];

    $hash = strtoupper($_POST["prefix"].$str);
    SQLLib::InsertRow("votekeys",array("votekey"=>$hash));
  }
}
if ($_POST["mode"] && is_uploaded_file($_FILES["votekeyfile"]["tmp_name"]))
{
  if ($_POST["mode"] == "reset")
  {
    SQLLib::Query("truncate votekeys");
  }

  $f = file( $_FILES["votekeyfile"]["tmp_name"] );
  foreach($f as $v)
  {
    $v = trim($v);
    $v = preg_replace("/\s/","",$v);
    //$v = preg_replace("/[^a-zA-Z0-9]/g","",$v);
    if ($v)
    {
      try{
        SQLLib::InsertRow("votekeys",array("votekey"=>$v));
      } catch(Exception $e) {}
    }
  }

}

?>
<h2>Votekeys</h2>
<h3>Print votekeys</h3>
<a href='votekeys_print.php'>Print votekeys</a>

<form action="votekeys.php" method="post" enctype="multipart/form-data" id='votekeys_print'>
  <label>Votekey format (HTML, <b>{%VOTEKEY%}</b> will be substituted):</label>
  <textarea name="votekeys_format"><?=_html($settings["votekeys_format"] ?: "{%VOTEKEY%}")?></textarea>
  <label>Additional print CSS:</label>
  <textarea name="votekeys_css"><?=_html($settings["votekeys_css"] ?: "")?></textarea>
  <input type="submit" value="Save"/>
</form>

<h3>QR Code</h3>
<form action="votekeys.php" method="post" enctype="multipart/form-data">
  <input type="hidden" name="votekeys_qrcode_form" value="true">
  <label>Register url (Used for QRCode, <b>{%VOTEKEY%}</b> will be substituted):</label>
  <input name="votekeys_qrcode_register_url" type="text" value="<?=_html($settings["votekeys_qrcode_register_url"] ?: "http://party.lan/index.php?page=Login&votekey={%VOTEKEY%}")?>">
  <label>Enable:</label>
  <input type="checkbox" name="votekeys_qrcode_enabled"<?=_html($settings["votekeys_qrcode_enabled"] ? "checked" : "")?>> Enable Include QR Code when printing

  <input type="submit" value="Save"/>
</form>

<h3>Generate votekeys</h3>
<form action="votekeys.php" method="post">
  <label>Amount:</label> <input type="text" name="amount" value="200"/>
  <label>Prefix:</label> <input type="text" name="prefix"/>
  <label>Length:</label> <input type="text" name="length"/>
  <input type="submit" value="Generate new!"/>
</form>
<h3>Load votekeys from text file</h3>
<form action="votekeys.php" method="post" enctype="multipart/form-data">
  <label>File:</label> <input type="file" name="votekeyfile"/>
  <label>Usage:</label>
  <div>
    <input type="radio" name="mode" value="reset" id='load1' /> <label for='load1'>Replace existing</label>
    <input type="radio" name="mode" value="merge" id='load2' checked='checked' /> <label for='load2'>Merge with existing</label>
  </div>
  <input type="submit" value="Upload!"/>
</form>
<h3>Current votekeys</h3>
<?php
printf("<table class='minuswiki' id='votekeys'>");
$n = 1;
$count = SQLLib::selectRow("select count(*) as c from votekeys where userid!=0")->c;
printf("<tr><td colspan='3'>%d votekeys used</td></tr>\n",$count);

$s = SQLLib::selectRows("select * from votekeys");
foreach($s as $t) {
  printf("<tr>");
  printf("  <td>%d.</td>",$n++);
  printf("  <td class='key'>%s</td>",$t->votekey);
  printf("  <td>%s</td>",$t->userid?sprintf("<a href='users.php?id=%d'>used</a>",$t->userid):"");
  printf("</tr>");
}
printf("</table>");


include_once("footer.inc.php");
?>
