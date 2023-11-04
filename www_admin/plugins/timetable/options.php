<?php
if (!defined("PLUGINOPTIONS")) exit();

$cms = new CMSGen();
$cms->formdata = array(
  "table" => "timetable",
  "key" => "id",
  "processingfile" => "pluginoptions.php?plugin=timetable",
  "class" => "minuswiki",
  "order" => "date",
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
      "format"=>"datetime_easy",
      "firstday"=>$settings["party_firstday"],
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

if (@$_POST["export"])
{
  timetable_export();
}
else if (@$_POST["timetable_perpage"])
{
  update_setting("timetable_perpage",(int)$_POST["timetable_perpage"]);
}
else if ($_POST)
{
  $cms->ProcessPost();
}

if (@$_GET["new"])
{
  $cms->RenderInsertForm();
}
else if (@$_GET["edit"]) 
{
  $cms->RenderEditForm($_GET["edit"]);
} 
else if (@$_GET["del"])
{
  $cms->RenderDeleteForm($_GET["del"]);
}
else
{
  if (!check_menuitem("Timetable"))
  {
    printf("<div class='warning'>You should probably also <a href='toc.php'>add the Timetable to the partynet menu</a>.</div>\n");
  }
  
  $events = get_timetable_content();
  echo "<table class='minuswiki'>\n";
  echo "<tr>\n";
  echo "  <th>Date/time</th>\n";
  echo "  <th>Event type</th>\n";
  echo "  <th>Event name</th>\n";
  echo "  <th>&nbsp;</th>\n";
  echo "</tr>\n";
  foreach($events as $ev)
  {
    echo "<tr>\n";
    printf("  <td>%s</td>\n",$ev->date);
    printf("  <td>%s</td>\n",$ev->type);
    printf("  <td>%s</td>\n",$ev->event);
    if ($ev->id)
    {
      printf("  <td><a href='?plugin=timetable&amp;edit=%d'>edit</a> / <a href='?plugin=timetable&amp;del=%d'>del</a></td>\n",$ev->id,$ev->id);
    }
    else if ($ev->compoID)
    {
      printf("  <td><a href='compos.php?id=%d'>edit</a> / <a href='compos.php?id=%d'>organize</a></td>\n",$ev->compoID,$ev->compoID);
    }
    else
    {
      printf("  <td>&nbsp;</td>\n");
    }
    echo "</tr>\n";
  }
  echo "  <td colspan='5'><a href='?plugin=timetable&amp;new=add'>Add new item</a></td>\n";
  echo "</table>\n";
//  $cms->RenderListGrid($formdata);
?>
<h2>Countdown viewer</h2>
<form action="plugins/timetable/viewer.php" method="get">

  <label>Number of previous events shown:</label>
  <input type='number' name='before' value='2'/>

  <label>Number of upcoming events:</label>
  <input type='number' name='after' value='4'/>
  
  <input type="submit" value="Open"/>
</form>
  
<form method="post" enctype="multipart/form-data">
  <h2>Options</h2>

  <label for='twitter_querystring'>Number of entries per slide:</label>
  <input type='number' id='timetable_perpage' name='timetable_perpage' value='<?=get_setting("timetable_perpage")?>'/>

  <div>
    <input type="submit" name="save" value="Save" />
  </div>

  <h2>Export timetable as slides</h2>
  <div>
    <input type="submit" name="export" value="Export!" />
  </div>
</form>
<?php
}


?>