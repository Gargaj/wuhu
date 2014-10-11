<?
chdir(dirname(__FILE__));
include_once("../../sqllib.inc.php");
include_once("../../common.inc.php");
include_once("functions.inc.php");

oneliner_generate_slide();
if (php_sapi_name() == "cli")
  echo date("Y-m-d H:i:s")."\n";
else
  echo '<img src="../../slides/_oneliner.png"/>';
?>