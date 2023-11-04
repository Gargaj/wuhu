<?php
include_once("bootstrap.inc.php");

$cms = new CMSGen();
$cms->formdata = array(
  "table" => "intranet_news",
  "key" => "id",
  "processingfile" => "news.php",
  "class" => "minuswiki",
  "order" => "date desc",
  "fields" => array(
    "id"=>array(
      "sqlfield"=>"id",
      "caption"=>"id",
      "grid"=>false,
    ),
    "date"=>array(
      "sqlfield"=>"date",
      "caption"=>"Date",
      "format"=>"datetime",
      "grid"=>true,
    ),
/*
    "hun_title"=>array(
      "sqlfield"=>"hun_title",
      "caption"=>"Title (Hungarian)",
      "format"=>"text",
      "grid"=>true,
    ),
*/
    "eng_title"=>array(
      "sqlfield"=>"eng_title",
      "caption"=>"Title (English)",
      "format"=>"text",
      "grid"=>true,
    ),
/*
    "hun_body"=>array(
      "sqlfield"=>"hun_body",
      "caption"=>"Body (Hungarian)",
      "format"=>"textarea",
      "grid"=>false,
    ),
*/
    "eng_body"=>array(
      "sqlfield"=>"eng_body",
      "caption"=>"Body (English)",
      "format"=>"textarea",
      "grid"=>false,
    ),
  ),
);

$cms->ProcessPost();

include_once("header.inc.php");

$cms->Render();

include_once("footer.inc.php");
?>
