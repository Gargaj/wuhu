<?php
include_once("bootstrap.inc.php");

$cms = new CMSGen();
$cms->formdata = array(
  "table" => "settings",
  "key" => "id",
  "processingfile" => "settings.php",
  "class" => "minuswiki",
  "fields" => array(
    "id"=>array(
      "sqlfield"=>"id",
      "caption"=>"id",
      "format"=>"static",
      "dontinsert"=>true,
      "grid"=>true,
    ),
    "setting"=>array(
      "sqlfield"=>"setting",
      "caption"=>"setting",
      "format"=>"text",
      "grid"=>true,
    ),
    "value"=>array(
      "sqlfield"=>"value",
      "caption"=>"value",
      "format"=>"text",
      "grid"=>true,
    ),
  ),
);

$cms->ProcessPost();

include_once("header.inc.php");

$cms->Render();

include_once("footer.inc.php");
?>
