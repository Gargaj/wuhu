<?
include_once("header.inc.php");
include_once("cmsgen.inc.php");

$formdata = array(
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