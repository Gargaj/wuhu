<?
include_once("header.inc.php");

$rows = SQLLib::selectRows("select title from intranet_minuswiki_pages");
$pages = array(""=>"- none -");
foreach($rows as $row) $pages[$row->title] = $row->title;

run_hook("admin_toc_pages",array("pages"=>&$pages));

$formdata = array(
  "table" => "intranet_toc",
  "key" => "id",
  "processingfile" => "toc.php",
  "class" => "minuswiki",
  "order" => "orderfield",
  "fields" => array(
    "id"=>array(
      "sqlfield"=>"id",
      "caption"=>"id",
      "grid"=>false,
    ),
    "orderfield"=>array(
      "sqlfield"=>"orderfield",
      "caption"=>"Order",
      "format"=>"text",
      "grid"=>true,
    ),
    "title"=>array(
      "sqlfield"=>"title",
      "caption"=>"Title",
      "format"=>"text",
      "grid"=>true,
    ),
    "link"=>array(
      "sqlfield"=>"link",
      "caption"=>"Link",
      "format"=>"select",
      "fields"=>$pages,
      "grid"=>true,
    ),
    "type"=>array(
      "sqlfield"=>"type",
      "caption"=>"type",
      "format"=>"select",
      "fields"=>array(
        'normal'=>"Normal",
        'loggedin'=>"Logged in only",
        'loggedout'=>"Logged out only",
        'separator'=>"SEPARATOR"
      ),
      "grid"=>true,
    ),
  ),
);
if ($_POST) {
  cmsProcessPost($formdata);
}
if ($_GET["new"])
  cmsRenderInsertForm($formdata);
else if ($_GET["edit"])
  cmsRenderEditForm($formdata,$_GET["edit"]);
else if ($_GET["del"])
  cmsRenderDeleteForm($formdata,$_GET["del"]);
else
  cmsRenderListGrid($formdata);

include_once("footer.inc.php");

?>