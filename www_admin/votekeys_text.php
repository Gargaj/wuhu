<?php
error_reporting(E_ALL ^ E_NOTICE);
include_once("bootstrap.inc.php");

$encoding = "iso-8859-1";
if (!$_GET["suppressHeader"])
{
  header("Content-Type: text/plain; charset=".$encoding);
  if ($_GET["filename"])
    header("Content-disposition: attachment; filename=".$_GET["filename"]);
}

$s = SQLLib::selectRows("select * from votekeys");

$format = $_GET["format"] ?? "text";
if ($format == "json")
{
  $json = [ "votekeys" => [] ];
  foreach($s as $t) {
    $json["votekeys"][] = $t->votekey;
  }
  echo json_encode($json);
}
else
{
  foreach($s as $t) {
    printf("%s\n",$t->votekey);
  }
}
