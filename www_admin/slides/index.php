<?php
$a = array();
if ($_GET["allSlides"])
{
  $a = glob("*");
}
else
{
  $a = array_merge($a,glob("*.{j,J}{p,P}{g,G}",GLOB_BRACE));
  $a = array_merge($a,glob("*.{p,P}{n,N}{g,G}",GLOB_BRACE));
}

header("Content-Type: text/xml; charset=utf-8");
echo "<".'?xml version="1.0" encoding="UTF-8" ?'.">";

list($path) = explode("?",$_SERVER["REQUEST_URI"]);
$dir = ($_SERVER["HTTPS"]=="on"?"https":"http") . "://" . $_SERVER["SERVER_NAME"] . dirname($path . "/dummy.txt");
if ($_GET["allSlides"])
  $dir = "../slides";

echo "<slides>\n";
foreach ($a as $v) {
  if($v == ".") continue;
  if($v == "..") continue;
  if($v == "index.php") continue;
  $url = $dir . "/" . $v;
  printf("\t<slide lastChanged='%d'>%s</slide>\n",filemtime($v),$url);
}
echo "</slides>\n";
?>
