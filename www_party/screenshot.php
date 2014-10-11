<?
session_start();
include_once("database.inc.php");
include_once(ADMIN_DIR . "/bootstrap.inc.php");

$s = SQLLib::selectRow(sprintf_esc("select * from compoentries where id = %d",$_GET["id"]));
if(!$s) exit;
$c = SQLLib::selectRow(sprintf_esc("select * from compos where id = %d",$s->compoid));
if ($_SESSION["logindata"]->id != $s->userid && $c->votingopen==0) exit();

include_once(ADMIN_DIR . "/screenshot.php");
?>