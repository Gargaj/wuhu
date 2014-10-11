<?
include_once("header.inc.php");

$voter = SpawnVotingSystem();

if (!$voter)
  die("VOTING SYSTEM ERROR");

$c = SQLLib::selectRows("select * from compos order by start,id");
foreach($c as $compo) {
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
  foreach($results as $k=>$v) {
    $e = SQLLib::selectRow(sprintf_esc("select * from compoentries where id = %d",$k));
    printf("<tr>\n");
    printf("  <td>%d.</td>\n",$n++);
    printf("  <td>%d pts</td>\n",$v);
    printf("  <td>#%d</td>\n",$e->playingorder);
    printf("  <td>%s</td>\n",htmlspecialchars($e->title));
    printf("  <td>%s</td>\n",htmlspecialchars($e->author));
    printf("</tr>\n");
  }
  echo "</table>\n";
}

echo "<a href='results_text.php'>TEXT ONLY VERSION</a>";

include_once("footer.inc.php");
?>
