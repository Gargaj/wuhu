<?
error_reporting(E_ALL ^ E_NOTICE);
include_once("sqllib.inc.php");
include_once("setting.inc.php");
include_once("thumbnail.inc.php");
include_once("common.inc.php");
include_once("hooks.inc.php");
include_once("cmsgen.inc.php");
include_once("votesystem.inc.php");
header("Content-Type: text/plain; charset=iso-8859-1");

loadPlugins();

$voter = SpawnVotingSystem();

if (!$voter)
  die("VOTING SYSTEM ERROR");

include_once("results_header.txt");

$c = SQLLib::selectRows("select * from compos order by start,id");
foreach($c as $compo) {
//  echo "<h3><a href='entries.php?id=".$compo->id."'>".$compo->name."</a></h3>\n";
  printf("\n\n\n  %s\n\n",strtoupper($compo->name));

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
  $lastpoints = -1;
  
  
  foreach($results as $k=>$v) {
    $e = SQLLib::selectRow(sprintf_esc("select * from compoentries where id = %d",$k));
    $title = sprintf("%s - %s",utf8_decode(trim($e->title)),utf8_decode(trim($e->author)));
    $title = wordwrap($title,50,"\n".str_pad(" ",27),1);
    if ($lastpoints==$v)
      printf("        #%02d   %3d pts    %s\n",$e->playingorder,$v,$title);
    else
      printf("   %2d.  #%02d   %3d pts    %s\n",$n,$e->playingorder,$v,$title);
    $lastpoints=$v;
    $n++;
  }
}
printf("\n\n\n\n===============================================================================\n\n");
printf("        %d votes were cast.\n",$voter->GetVoteCount());
?>

        Made possible by Wuhu - http://wuhu.function.hu