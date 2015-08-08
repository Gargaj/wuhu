<?php
if (!file_exists("database.inc.php")) {
  include_once("config.php");
  exit();
}
include_once("header.inc.php");

function todo_createcompos()     { return SQLLib::selectRow("select * from compos limit 1"); }
function todo_generatevotekeys() { return SQLLib::selectRow("select * from votekeys limit 1"); }
function todo_createtemplate()   { return file_exists( WWW_DIR . "/template.html" ); }
function todo_replaceascii()     { return file_exists( ADMIN_DIR . "/results_header.txt" ); }
function todo_resultxml()        { return file_exists( ADMIN_DIR . "/result.xml" ); }
function todo_slideviewercss()   { return file_exists( ADMIN_DIR . "/slideviewer/custom.css" ); }

$checks = array(
  "todo_createcompos" => "Set up <a href='compos.php'>compos</a>",
  "todo_generatevotekeys" => "Generate <a href='votekeys.php'>votekeys</a> and print them",
  "todo_createtemplate" => "Create a template.html for the party intranet",
  "todo_replaceascii" => "Replace the <a href='results_text.php'>results file header</a>",
  "todo_resultxml" => "Generate an XML for <a href='beamer.php'>the beamer</a>",
  "todo_slideviewercss" => "Create a custom.css for <a href='slideviewer.php'>the slide viewer</a>",
);

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
