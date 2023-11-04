<?php
include_once("bootstrap.inc.php");

$cms = new CMSGen();
$cms->formdata = array(
  "table" => "intranet_minuswiki_pages",
  "key" => "title",
  "processingfile" => "pages.php",
  "class" => "minuswiki",
  "order" => "title",
  "stayonform" => true,
  "fields" => array(
    "id"=>array(
      "sqlfield"=>"id",
      "caption"=>"id",
      "format"=>"static",
      "grid"=>true,
      "dontinsert"=>true,
    ),
    "title"=>array(
      "sqlfield"=>"title",
      "caption"=>"Title",
      "format"=>"text",
      "grid"=>true,
    ),
    "content"=>array(
      "sqlfield"=>"content",
      "caption"=>"Page Contents",
      "format"=>"textarea",
    ),
  ),
);

$cms->ProcessPost();

include_once("header.inc.php");

$cms->Render();

include_once("footer.inc.php");
?>