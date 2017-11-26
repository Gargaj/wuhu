<?
error_reporting(E_ALL ^ E_NOTICE);
include_once("bootstrap.inc.php");

$encoding = "iso-8859-1";
if ($_GET["encoding"] == "utf-8")
  $encoding = "utf-8";

function convertEncoding($text)
{
  global $encoding;
  return mb_convert_encoding( $text, $encoding, "utf-8" );
}

if (!$_GET["suppressHeader"])
{
  header("Content-Type: text/plain; charset=".$encoding);
  if ($_GET["filename"])
    header("Content-disposition: attachment; filename=".$_GET["filename"]);
}
loadPlugins();

$voter = SpawnVotingSystem();

if (!$voter)
  die("VOTING SYSTEM ERROR");

if (file_exists("results_header.txt"))
  include_once("results_header.txt");
else
  echo "The [results_header.txt] file is missing, upload one to include a cool ASCII header!\n\n";
  
$c = SQLLib::selectRows("select * from compos order by start,id");
foreach($c as $compo) 
{
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
  
  
  foreach($results as $k=>$v) 
  {
    $e = SQLLib::selectRow(sprintf_esc("select * from compoentries where id = %d",$k));
    $title = sprintf("%s - %s",convertEncoding(trim($e->title)),convertEncoding(trim($e->author)));
    $title = wordwrap($title,50,"\n".str_pad(" ",27),1);
    if ($lastpoints==$v)
      printf("        #%02d   %3d pts    %s\n",$e->playingorder,$v,$title);
    else
      printf("   %2d.  #%02d   %3d pts    %s\n",$n,$e->playingorder,$v,$title);
    $lastpoints=$v;
    $n++;
  }
}
$users = SQLLib::selectRow("select count(*) as c from users")->c;
printf("\n\n\n\n===============================================================================\n\n");
printf("        %d votes were cast by %d registered voters.\n",$voter->GetVoteCount(),$users);
printf("\n");
printf("        Made possible by Wuhu - http://wuhu.function.hu\n");
if (file_exists("results_footer.txt"))
  include_once("results_footer.txt");
?>