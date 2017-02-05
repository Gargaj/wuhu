<?php
if (!file_exists("database.inc.php"))
{
  include_once("config.php");
  exit();
}
include_once("header.inc.php");

// party status
$voter = SpawnVotingSystem();
printf("<h2>Party status</h2>\n");
printf("<ul>");
$nextCompo = SQLLib::selectRow("select name, start from compos where start > now() order by start limit 1");
if ($nextCompo)
{
  printf("  <li>Next compo: <b>%s (%s)</b></li>\n",_html($nextCompo->name),_html($nextCompo->start));
}
printf("  <li><b>%d</b> compo entries</li>\n",SQLLib::selectRow("select count(*) as c from compoentries")->c);
printf("  <li><b>%d</b> users</li>\n",SQLLib::selectRow("select count(*) as c from users")->c);
printf("  <li><b>%d</b> votes</li>\n",$voter->GetVoteCount());
run_hook("admin_index_status");
printf("</ul>");

// orga checklist

function todo_createcompos()     { return SQLLib::selectRow("select * from compos limit 1"); }
function todo_generatevotekeys() { return SQLLib::selectRow("select * from votekeys limit 1"); }
function todo_createtemplate()   { return file_exists( WWW_DIR . "/template.html" ); }
function todo_replaceascii()     { return file_exists( ADMIN_DIR . "/results_header.txt" ); }
function todo_resultxml()        { return file_exists( ADMIN_DIR . "/result.xml" ); }
function todo_slideviewercss()   { return file_exists( ADMIN_DIR . "/slideviewer/custom.css" ); }
function todo_crontab()          { return SQLLib::selectRow("select * from cron"); }

$checks = array(
  "todo_createcompos" => "Set up <a href='compos.php'>compos</a>",
  "todo_generatevotekeys" => "Generate <a href='votekeys.php'>votekeys</a> and print them",
  "todo_createtemplate" => "Create a template.html for the party intranet",
  "todo_replaceascii" => "Replace the <a href='results_text.php'>results file header</a>",
  "todo_resultxml" => "Generate an XML for <a href='beamer.php'>the beamer</a>",
  "todo_slideviewercss" => "Create a custom.css for <a href='slideviewer.php'>the slide viewer</a>",
  "todo_crontab" => "Put the following line in your crontab to enable background tasks: <pre>*/1 * * * * php ".dirname(__FILE__)."/cron.php > /dev/null</pre>",
);

run_hook("admin_index_checklist",array("checklist"=>&$checks));

printf("<h2>To-do list after setting up</h2>\n");
printf("<ol id='maintodolist'>\n");
foreach($checks as $func=>$description)
{
  if ($func())
    printf("<li><s>%s</s></li>\n",$description);  
  else
    printf("<li>%s</li>\n",$description);  
}
printf("</ol>\n");

include_once("footer.inc.php");
?>
