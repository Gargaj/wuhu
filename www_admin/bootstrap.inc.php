<?php
error_reporting(E_ALL ^ E_NOTICE);
if (!defined("ADMIN_DIR"))
  include_once(dirname(__FILE__)."/database.inc.php");

include_once(ADMIN_DIR."/sqllib.inc.php");
include_once(ADMIN_DIR."/setting.inc.php");
include_once(ADMIN_DIR."/thumbnail.inc.php");
include_once(ADMIN_DIR."/common.inc.php");
include_once(ADMIN_DIR."/hooks.inc.php");
include_once(ADMIN_DIR."/cmsgen.inc.php");
include_once(ADMIN_DIR."/csrf.inc.php");
include_once(ADMIN_DIR."/votesystem.inc.php");

loadPlugins();

// Theoretically OPTIONS requests shouldn't need a body;
// since we let OPTIONS requests through, make sure they don't leak data
if ($_SERVER["REQUEST_METHOD"]=="OPTIONS")
{
  die("Of CORS!");
}
?>
