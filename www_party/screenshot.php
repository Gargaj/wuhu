<?php
session_start();
include_once("database.inc.php");
include_once(ADMIN_DIR . "/bootstrap.inc.php");

$entry = SQLLib::selectRow(sprintf_esc("select * from compoentries where id = %d",@$_GET["id"]));
if(!$entry) exit;
$compo = SQLLib::selectRow(sprintf_esc("select * from compos where id = %d",$entry->compoid));
if (get_user_id() != $entry->userid && $compo->votingopen==0) exit();

include_once(ADMIN_DIR . "/screenshot.php");
?>
