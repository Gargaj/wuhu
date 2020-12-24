<?php
include_once("../../database.inc.php");

function adminer_object() 
{
  class AdminerSoftware extends Adminer {
    function __construct() { set_password("server", SQL_HOST, SQL_USERNAME, SQL_PASSWORD); }
    function name() { return "Slengpung"; }
    function credentials() { return array(SQL_HOST, SQL_USERNAME, SQL_PASSWORD); }
    function database() { return SQL_DATABASE; }
    function databasesPrint($d) {}
    function login($login, $password) { return true; }
  }
  return new AdminerSoftware;
}

$_GET["server"] = SQL_HOST;
$_GET["username"] = SQL_USERNAME;
$_GET["db"] = SQL_DATABASE;

$adminerfiles = glob("adminer-*.php");
rsort($adminerfiles);
$adminerfile = reset($adminerfiles);
include_once($adminerfile);
?>
