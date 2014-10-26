<?
include_once("header.inc.php");
if ($_POST["mode"]) 
{
  ob_start();

  echo "<".'?xml version="1.0" encoding="utf-8"?'.">\n";
  printf("<result>\n");
  printf("  <mode>%s</mode>\n",$_POST["mode"]);

  switch ($_POST["mode"]) {
    case "announcement": 
    {
      $isHTML = $_POST["isHTML"] == "on" ? "true" : "false";
      printf("  <announcementtext isHTML='%s'>%s</announcementtext>\n",$isHTML,htmlspecialchars($_POST["announcement"]));
    } break;
    case "compocountdown": 
    {
      if ($_POST["compo"])
      {
        $s = get_compo( $_POST["compo"] );
        printf("  <componame>%s</componame>\n",htmlspecialchars($s->name));
        printf("  <compostart>%s</compostart>\n",$s->start);
      }
      if ($_POST["eventname"])
      {
        printf("  <eventname>%s</eventname>\n",htmlspecialchars( $_POST["eventname"] ));
        printf("  <compostart>%s</compostart>\n",htmlspecialchars( $_POST["eventtime"] ));
      }
      
    } break;
    case "compodisplay": 
    {
      $compo = get_compo( $_POST["compo"] );
      printf("  <componame>%s</componame>\n",htmlspecialchars($compo->name));
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
        printf("      <title>%s</title>\n",htmlspecialchars($t->title));
        if ($compo->showauthor)
          printf("      <author>%s</author>\n",htmlspecialchars($t->author));
        printf("      <comment>%s</comment>\n",htmlspecialchars($t->comment));
        printf("    </entry>\n");
      }
      printf("  </entries>\n");
    } break;
    case "prizegiving": 
    {
      $voter = SpawnVotingSystem();
      
      if (!$voter)
        die("VOTING SYSTEM ERROR");
    
      $compo = get_compo( $_POST["compo"] );
    
      $query = new SQLSelect();
      $query->AddTable("compoentries");
      $query->AddWhere(sprintf_esc("compoid=%d",$_POST["compo"]));
      run_hook("admin_beamer_generate_prizegiving_dbquery",array("query"=>&$query));
      $entries = SQLLib::selectRows( $query->GetQuery() );
    
      global $results;
      $results = array();
      $results = $voter->CreateResultsFromVotes( $compo, $entries );
      run_hook("voting_resultscreated_presort",array("results"=>&$results));
      asort($results);
      $ranks = 0;

      run_hook("admin_beamer_prizegiving_rendervotes",array("results"=>&$results,"compo"=>$compo));

      $lastpoints = -1;
      foreach($results as $v){
        if ($lastpoints != $v) $ranks++;
        $lastpoints = $v;
      }
      
      $ranks++;
      $lastpoints = -1;

      printf("  <componame>%s</componame>\n",htmlspecialchars($compo->name));
      printf("  <results>\n");
      foreach ($results as $k=>$t) {
        if ($lastpoints != $t) $ranks--;
        $s = SQLLib::selectRow(sprintf_esc("select * from compoentries where id=%d",$k));
        printf("    <entry>\n");
        printf("      <ranking>%d</ranking>\n",$ranks);
        printf("      <points>%d</points>\n",htmlspecialchars($t));
        printf("      <title>%s</title>\n",htmlspecialchars($s->title));
        printf("      <author>%s</author>\n",htmlspecialchars($s->author));
        printf("    </entry>\n");
        $lastpoints = $t;
      }
      printf("  </results>\n");
    } break;
  }
  printf("</result>\n");
  file_put_contents("result.xml",ob_get_clean());
  @chmod("result.xml",0755);
}
?>
<div class='error'>WARNING: everything here has been deprecated - it will still work with the old beamer but no development is done!</div>
<?
printf("<h2>Change beamer setting</h2>\n");

$f = file_get_contents("result.xml");
preg_match("|\\<mode\\>(.*)\\</mode\\>|m",$f,$m);

$s = SQLLib::selectRows("select * from compos order by start");

printf("Current mode: <a href='result.xml'>%s</a>",$m[1]);
//if ($m[0]=="announcement") {
  preg_match("/<announcementtext isHTML='(.*)'>(.*)<\/announcementtext>/sm",$f,$ann);
//  var_dump($ann);
//}
?>

<div class='beamermode'>
<h3>Announcement</h3>
<form action="beamer.php" method="post" enctype="multipart/form-data">
  <textarea name="announcement"><?=trim($ann[2])?></textarea><br/>
  <input type="checkbox" name="isHTML" style='display:inline-block'<?=($ann[1]=="true"?" checked='checked'":"")?>/> Use HTML  
  <input type="hidden" name="mode" value="announcement"/>
  <input type="submit" value="Switch to Announcement mode."/>
</form>
</div>

<div class='beamermode'>
<h3>Compo countdown</h3>
<form action="beamer.php" method="post" enctype="multipart/form-data">
<select name="compo">
<?
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
  <input type="text" id="eventname" name="eventname" value=""/>
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
<?
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
<?
foreach($s as $t)
  printf("  <option value='%d'>%s</option>\n",$t->id,$t->name);
?>  
</select><br/>
  <input type="hidden" name="mode" value="prizegiving"/>
  <input type="submit" value="Switch to Prizegiving mode."/>
</form>
</div>
<?
include_once("footer.inc.php");
?>
