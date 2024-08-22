<?php
include_once("header.inc.php");
if (@$_POST["mode"])
{
  ob_start();

  echo "<".'?xml version="1.0" encoding="utf-8"?'.">\n";
  printf("<result>\n");
  printf("  <mode>%s</mode>\n",$_POST["mode"]);

  switch ($_POST["mode"]) 
  {
    case "announcement":
    {
      $isHTML = $_POST["isHTML"] == "on" ? "true" : "false";
      printf("  <announcementtext isHTML='%s'>%s</announcementtext>\n",$isHTML,_html($_POST["announcement"]));
    } break;
    case "compocountdown":
    {
      if ($_POST["compo"])
      {
        $s = get_compo( $_POST["compo"] );
        printf("  <componame>%s</componame>\n",_html($s->name));
        printf("  <compostart>%s</compostart>\n",_html( $s->start ));
      }
      if ($_POST["eventname"])
      {
        printf("  <eventname>%s</eventname>\n",_html( $_POST["eventname"] ));
        printf("  <compostart>%s</compostart>\n",_html( $_POST["eventtime"] ));
      }

    } break;
    case "compodisplay":
    {
      $compo = get_compo( $_POST["compo"] );
      printf("  <componame>%s</componame>\n",_html($compo->name));
      printf("  <entries>\n");

      $query = new SQLSelect();
      $query->AddTable("compoentries");
      $query->AddWhere(sprintf_esc("compoid=%d",$_POST["compo"]));
      $query->AddOrder("playingorder");
      run_hook("admin_beamer_generate_compodisplay_dbquery",array("query"=>&$query));
      $entries = SQLLib::selectRows( $query->GetQuery() );

      $playingorder = 1;
      foreach ($entries as $t)
      {
        printf("    <entry>\n");
        printf("      <number>%d</number>\n",$playingorder++);
        printf("      <title>%s</title>\n",_html($t->title));
        if ($compo->showauthor)
          printf("      <author>%s</author>\n",_html($t->author));
        printf("      <comment>%s</comment>\n",_html($t->comment));
        printf("    </entry>\n");
      }
      printf("  </entries>\n");
    } break;
    case "prizegiving":
    {
      $voter = null;
      $compo = get_compo( $_POST["compo"] );
      $results = generate_results($voter, $compo->id, false);
      $compoResults = $results["compos"][0]["results"];
      run_hook("admin_beamer_prizegiving_rendervotes",array("results"=>&$compoResults,"compo"=>$compo));

      printf("  <componame>%s</componame>\n",_html($compo->name));
      printf("  <results>\n");
      $out = "";
      foreach ($compoResults as $entry) 
      {
        $tag =  sprintf("    <entry>\n");
        $tag .= sprintf("      <ranking>%d</ranking>\n",$entry["ranking"]);
        $tag .= sprintf("      <points>%d</points>\n",_html($entry["points"]));
        $tag .= sprintf("      <title>%s</title>\n",_html($entry["title"]));
        $tag .= sprintf("      <author>%s</author>\n",_html($entry["author"]));
        $tag .= sprintf("    </entry>\n");
        $out = $tag . $out; // reverse order
      }
      echo $out;
      printf("  </results>\n");
    } break;
  }
  printf("</result>\n");
  file_put_contents("result.xml",ob_get_clean());
  @chmod("result.xml",0755);
}
printf("<h2>Change beamer setting</h2>\n");

$f = @file_get_contents("result.xml");
preg_match("|\\<mode\\>(.*)\\</mode\\>|m",$f,$m);

$s = SQLLib::selectRows("select * from compos order by start");

printf("Current mode: <a href='result.xml'>%s</a>",@$m[1]);
//if ($m[0]=="announcement") {
  preg_match("/<announcementtext isHTML='(.*)'>(.*)<\/announcementtext>/sm",$f,$ann);
//  var_dump($ann);
//}
?>

<div class='beamermode'>
<h3>Announcement</h3>
<form action="beamer.php" method="post" enctype="multipart/form-data">
  <textarea name="announcement"><?=trim(@$ann[2]?:"")?></textarea><br/>
  <input type="checkbox" name="isHTML" id="isHTML" style='display:inline-block'<?=(@$ann[1]=="true"?" checked='checked'":"")?>/> <label for='isHTML'>Use HTML</label>
  <input type="hidden" name="mode" value="announcement"/>
  <input type="submit" value="Switch to Announcement mode."/>
</form>
</div>

<div class='beamermode'>
<h3>Compo countdown</h3>
<form action="beamer.php" method="post" enctype="multipart/form-data">
<select name="compo">
<?php
foreach($s as $t)
  printf("  <option value='%d'>%s</option>\n",$t->id,$t->name);
?>
</select><br/>
  <input type="hidden" name="mode" value="compocountdown"/>
  <input type="submit" value="Switch to Compo Countdown mode."/>
</form>
</div>

<div class='beamermode'>
<h3>Event countdown</h3>
<form action="beamer.php" method="post" enctype="multipart/form-data">
  <label for="eventname">Event name:</label>
  <input type="text" id="eventname" name="eventname" value="" required='yes'/>
  <label for="eventtime">Event time:</label>
  <input type="text" id="eventtime" name="eventtime" value="<?=date("Y-m-d H:i:s")?>"/>
  <input type="hidden" name="mode" value="compocountdown"/>
  <input type="submit" value="Switch to Event Countdown mode."/>
</form>
</div>

<div class='beamermode'>
<h3>Compo display</h3>
<form action="beamer.php" method="post" enctype="multipart/form-data">
<select name="compo">
<?php
foreach($s as $t)
  printf("  <option value='%d'>%s</option>\n",$t->id,$t->name);
?>
</select><br/>
  <input type="hidden" name="mode" value="compodisplay"/>
  <input type="submit" value="Switch to Compo Display mode."/>
</form>
</div>

<div class='beamermode'>
<h3>Prizegiving</h3>
<form action="beamer.php" method="post" enctype="multipart/form-data">
<select name="compo">
<?php
foreach($s as $t)
  printf("  <option value='%d'>%s</option>\n",$t->id,$t->name);
?>
</select><br/>
  <input type="hidden" name="mode" value="prizegiving"/>
  <input type="submit" value="Switch to Prizegiving mode."/>
</form>
</div>
<?php
include_once("footer.inc.php");
?>
