<?php
include_once("header.inc.php");

$formdata = array(
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
if ($_POST)
  cmsProcessPost($formdata);

if ($_GET["new"])
  cmsRenderInsertForm($formdata);
else if ($_GET["edit"]) {
  cmsRenderEditForm($formdata,$_GET["edit"]);
} else if ($_GET["del"])
  cmsRenderDeleteForm($formdata,$_GET["del"]);
else
  cmsRenderListGrid($formdata);

include_once("footer.inc.php");

?>
