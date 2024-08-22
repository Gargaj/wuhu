<?php
error_reporting(E_ALL ^ E_NOTICE);
include_once("bootstrap.inc.php");

$encoding = "iso-8859-1";
if (@$_GET["encoding"] == "utf-8")
  $encoding = "utf-8";

function convertEncoding($text)
{
  global $encoding;
  return mb_convert_encoding( $text, $encoding, "utf-8" );
}

if (!@$_GET["suppressHeader"])
{
  header("Content-Type: text/plain; charset=".$encoding);
  if (@$_GET["filename"])
    header("Content-disposition: attachment; filename=".$_GET["filename"]);
}
loadPlugins();

if (file_exists("results_header.txt"))
  include_once("results_header.txt");
else
  echo "The [results_header.txt] file is missing, upload one to include a cool ASCII header!\n\n";

$voter = null;
$results = generate_results($voter, false, true);
foreach($results["compos"] as $compo)
{
  printf("\n\n\n  %s\n\n",strtoupper($compo["name"]));

  $lastRank = -1;
  foreach($compo["results"] as $entry)
  {
    $title = sprintf("%s - %s",convertEncoding(trim($entry["title"])),convertEncoding(trim($entry["author"])));
    $title = wordwrap($title,50,"\n".str_pad(" ",27),1);
    if ($lastRank == $entry["ranking"])
      printf("        #%02d   %3d pts    %s\n",$entry["order"],$entry["points"],$title);
    else
      printf("   %2d.  #%02d   %3d pts    %s\n",$entry["ranking"],$entry["order"],$entry["points"],$title);
    $lastRank = $entry["ranking"];
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
