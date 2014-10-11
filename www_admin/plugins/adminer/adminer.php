<?
include_once("../../database.inc.php");

function adminer_object() 
{
  class AdminerSoftware extends Adminer {
    function name() { return "Wuhu Adminer Plugin"; }
    function credentials() { return array(SQL_HOST, SQL_USERNAME, SQL_PASSWORD); }
    function database() { return SQL_DATABASE; }
    function databasesPrint() {}
  }
  return new AdminerSoftware;
}

$_GET["username"] = SQL_USERNAME;
$_GET["db"] = SQL_DATABASE;

$adminerfiles = glob("adminer-*.php");
rsort($adminerfiles);
$adminerfile = reset($adminerfiles);
include_once($adminerfile);
?>
