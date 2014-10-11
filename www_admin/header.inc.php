<?
include_once("bootstrap.inc.php");

run_hook("admin_page_start");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <title>wuhu - <?=basename($_SERVER["PHP_SELF"])?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="style.css"/> 
  <script type="text/javascript" src="prototype.js"></script>
<?
run_hook("admin_head");
?>
</head>
<body>

<div id="container">

  <div id="header">
    <h1><span>Wuhu Admin</span></h1>
  </div>

  <div id="csswarning"><i>*** Your browser doesn't support CSS -
  the page was automatically degraded to structure only. ***</i></div>

  <div id="menu">
    <ul>
      <li><a href="index.php">Main page</a> </li>
      <li><hr/></li>
      <li><a href="news.php">News</a> </li>
      <li><a href="pages.php">Pages</a> </li>
      <li><a href="toc.php">Menu</a> </li>
      <li><hr/></li>
      <li><a href="beamer.php">Beamer</a> </li>
      <li><a href="slideviewer.php">Slideviewer</a> </li>
      <li><hr/></li>
      <li><a href="settings.php">Settings</a> </li>
      <li><a href="votekeys.php">Votekeys</a> </li>
      <li><a href="users.php">Users</a> </li>
      <li><a href="compos.php">Compos</a> </li>
      <li><a href="results.php">Results</a> </li>
      <li><hr/></li>
      <li>Plugins (<a href='plugins.php'>edit</a>):</li>
<?
$pluginlinks = array();
run_hook("admin_menu",array("links"=>&$pluginlinks));
if ($pluginlinks)
{
  foreach($pluginlinks as $k=>$v)
    printf("      <li><a href='%s'>%s</a> </li>\n",$k,$v);
}
else
{
    printf("      <li>none</li>\n");
}
?>      
    </ul>

  </div>

  <div id="content">
  <!-- start content -->
<?
run_hook("admin_content_start");
?>