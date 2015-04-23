<?
include_once("header.inc.php");

$voter = SpawnVotingSystem();

if (!$voter)
  die("VOTING SYSTEM ERROR");

echo "<h2>Results</h2>";

echo "<p>Text-only version: <a href='results_text.php'>view</a> / <a href='results_text.php?filename=results.txt'>download</a></p>";

$c = SQLLib::selectRows("select * from compos order by start,id");
foreach($c as $compo) 
{
  echo "<h3><a href='compos_entry_list.php?id=".$compo->id."'>".$compo->name."</a></h3>\n";

  $query = new SQLSelect();
  $query->AddTable("compoentries");
  $query->AddWhere(sprintf_esc("compoid=%d",$compo->id));
  $query->AddOrder("playingorder");
  run_hook("admin_results_dbquery",array("query"=>&$query));
  $entries = SQLLib::selectRows( $query->GetQuery() );

  global $results;
  $results = array();
  $results = $voter->CreateResultsFromVotes( $compo, $entries );
  run_hook("voting_resultscreated_presort",array("results"=>&$results));
  arsort($results);
  
  $n = 1;
  echo "<table class='results'>\n";
  $lastPoints = -1;
  foreach($results as $k=>$v) {
    $e = SQLLib::selectRow(sprintf_esc("select * from compoentries where id = %d",$k));
    printf("<tr>\n");
    if ($lastPoints == $v)
      printf("  <td>&nbsp;</td>\n");
    else
      printf("  <td>%d.</td>\n",$n++);
    $lastPoints = $v;
    printf("  <td>%d pts</td>\n",$v);
    printf("  <td>#%d</td>\n",$e->playingorder);
    printf("  <td><a href='compos_entry_edit.php?id=%d'>%s</a></td>\n",$k,htmlspecialchars($e->title));
    printf("  <td>%s</td>\n",htmlspecialchars($e->author));
    printf("</tr>\n");
  }
  echo "</table>\n";
}

include_once("footer.inc.php");
?>
