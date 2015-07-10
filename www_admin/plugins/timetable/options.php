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

if ($_POST["export"])
{
  $s = get_timetable_content(6);
  $a = preg_split("/<h3>/ms",$s);
  $n = 1;
  foreach($a as $v)
  {
    if (strstr($v,"</h3>")===false)
      continue;
    $v = "<h3>" . $v;
    $fn = sprintf(ADMIN_DIR . "/slides/timetable-%02d.htm",$n++);
    file_put_contents($fn,$v);
    printf("<div class='success'>%s exported</div>\n",basename($fn));
  }
}
else if ($_POST)
  cmsProcessPost($formdata);

if ($_GET["new"])
  cmsRenderInsertForm($formdata);
else if ($_GET["edit"]) {
  cmsRenderEditForm($formdata,$_GET["edit"]);
} else if ($_GET["del"])
  cmsRenderDeleteForm($formdata,$_GET["del"]);
else
{
  cmsRenderListGrid($formdata);
?>
<form method="post" enctype="multipart/form-data">
  <h2>Export timetable as slides</h2>
  <div>
    <input type="submit" name="export" value="Export!" />
  </div>
</form>
<?  
}


?>