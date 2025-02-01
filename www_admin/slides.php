<?php
$files = glob("slides/*");
$output = array(
  "root" => "../",
  "slides" => array()
);
foreach ($files as $v)
{
  if($v == ".") continue;
  if($v == "..") continue;
  if($v == "index.php") continue;
  $output["slides"][] = array(
    "url" => $v,
    "lastChanged" => filemtime($v),
  );
}

switch(@$_GET["format"])
{
  case "jsonp":
    {
      header("Content-type: application/javascript");
      printf("%s(%s);",@$_GET["callback"]?:"wuhuJSONPCallback",json_encode($output));
    }
    break;
  case "json":
  default:
    {
      header("Content-type: application/json");
      echo json_encode($output);
    }
    break;
}

?>