<?
chdir(dirname(__FILE__));
include_once("../../bootstrap.inc.php");
include_once("plugin.php");

timetable_export();

if (php_sapi_name() == "cli")
  echo date("Y-m-d H:i:s")."\n";
  
?>