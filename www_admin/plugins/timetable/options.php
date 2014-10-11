<?
if (!defined("PLUGINOPTIONS")) exit();

$formdata = array(
  "table" => "timetable",
  "key" => "id",
  "processingfile" => $_SERVER["REQUEST_URI"],
  "class" => "minuswiki",
  "sort" => "date",
  "fields" => array(
    "id"=>array(
      "sqlfield"=>"id",
      "caption"=>"id",
      "format"=>"static",
      "dontinsert"=>true,
      "grid"=>true,
    ),
    "date"=>array(
      "sqlfield"=>"date",
      "caption"=>"date",
      "format"=>"datetime",
      "grid"=>true,
    ),
    "type"=>array(
      "sqlfield"=>"type",
      "caption"=>"type",
      "format"=>"select",
      "grid"=>true,
      "fields"=>array(
        'mainevent'=>"main event", 
        'event'=>"event", 
        'deadline'=>"deadline", 
        'compo'=>"compo", 
        'seminar'=>"seminar"
      ),
    ),
    "event"=>array(
      "sqlfield"=>"event",
      "caption"=>"event",
      "format"=>"text",
      "grid"=>true,
    ),
    "link"=>array(
      "sqlfield"=>"link",
      "caption"=>"link",
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

?>