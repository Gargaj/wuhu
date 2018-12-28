<?php
if (!defined("ADMIN_DIR")) exit();

$_SESSION["logindata"] = array();
unset($_SESSION["logindata"]);

header("Location: ".build_url("News"));
?>
