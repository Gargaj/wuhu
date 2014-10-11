<?php
if (!file_exists("database.inc.php")) {
  include_once("config.php");
  exit();
}
include_once("header.inc.php");

function todo_createcompos()
{
  return SQLLib::selectRow("select * from compos limit 1");
}
function todo_generatevotekeys()
{
  return SQLLib::selectRow("select * from votekeys limit 1");
}

$checks = array(
  "todo_createcompos" => "Set up <a href='compos.php'>compos</a>",
  "todo_generatevotekeys" => "Generate <a href='votekeys.php'>votekeys</a> and print them",
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