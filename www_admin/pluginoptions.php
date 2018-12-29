<?php
include_once("header.inc.php");

define("PLUGINOPTIONS",true);

$file = ADMIN_DIR . "/plugins/" . basename($_GET["plugin"]) . "/options.php";
if (file_exists($file))
  include($file);

include_once("footer.inc.php");
?>
