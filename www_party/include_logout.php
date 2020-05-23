<?php
if (!defined("ADMIN_DIR")) exit();

$_SESSION["logindata"] = array();
unset($_SESSION["logindata"]);

redirect( build_url("News") );
?>
