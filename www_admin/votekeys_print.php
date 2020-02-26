<?php
include_once("bootstrap.inc.php");
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

<?php run_hook("votekeys_print_css"); ?>

<?=($settings["votekeys_css"] ?: "")?>
</style>
</head>
<body>
<?php
printf("<ul class='votekeys'>");
$n = 1;
$s = SQLLib::selectRows("select * from votekeys");
$format = $settings["votekeys_format"] ?: "{%VOTEKEY%}";
foreach($s as $t) {
  print("<li>");
  run_hook("votekeys_print_votekey_before", array("votekey" => $t->votekey));
  printf("%s", str_replace("{%VOTEKEY%}", $t->votekey, $format));
  run_hook("votekeys_print_votekey_after", array("votekey" => $t->votekey));
  print("</li>");
}
printf("</ul>");

?>
</body>
</html>
