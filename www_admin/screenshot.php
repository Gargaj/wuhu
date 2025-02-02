<?php
//error_reporting(E_ALL);
include_once("database.inc.php");
include_once(ADMIN_DIR . "/bootstrap.inc.php");

start_wuhu_session();

$s = SQLLib::selectRow(sprintf_esc("select * from compoentries where id = %d",$_GET["id"]));
if(!$s) exit;

$a = @$_GET["show"]=="thumb" ? get_compoentry_screenshot_thumb_path( $_GET["id"] ) : get_compoentry_screenshot_path( $_GET["id"] );

if ($a && file_exists($a))
{
  list($width,$height,$type) = getimagesize($a);
  header("Content-type: ".image_type_to_mime_type($type));
  echo @file_get_contents($a);
}
else
{
  header("Content-type: image/png");
  if (@$_GET["show"]=="thumb")
  {
    $path = ADMIN_DIR . "/noscreenshot-".(int)$settings["screenshot_sizex"]."x".(int)$settings["screenshot_sizey"].".png";
    if (!file_exists($path))
    {
      thumbnail( ADMIN_DIR . "/noscreenshot.png",$path,$settings["screenshot_sizex"],$settings["screenshot_sizey"]);
    }
    echo file_get_contents($path);
  }
  else
  {
    echo file_get_contents( ADMIN_DIR . "/noscreenshot.png" );
  }
}
?>
