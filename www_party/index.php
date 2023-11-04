<?php
session_start();
if (!file_exists("database.inc.php")) {
  die("The system is not yet configured - please go to the admin panel to do so.");
}

/////////////////////////////////////////////////
// bootstrap

include_once("database.inc.php");
include_once(ADMIN_DIR . "/bootstrap.inc.php");
include_once("minuswiki.inc.php");

loadPlugins();

run_hook("index_start");

/////////////////////////////////////////////////
// basic init

$year = "intranet";

$wiki = new MinusWiki();
$wiki->TableName = "intranet_minuswiki_pages";

$page = "News";
if(@$_GET["page"]) $page = $_GET["page"];

if (strstr($page,":")!==false)
  list($lang,$pagetitle) = explode(":",$page);
else
  $pagetitle = $page;

/////////////////////////////////////////////////
// fetch contents

$content = "";

run_hook("index_content",array("content"=>&$content));

if (!$content)
{
  $row = SQLLib::selectRow(sprintf_esc("select type from intranet_toc where link='%s'",$pagetitle));
  if ($row && $row->type=='loggedin' && (!$_SESSION["logindata"] || !$_SESSION["logindata"]->id)) {
    $content = "UNAUTHORIZED REQUEST!";
  } else
    $content = $wiki->GetPage( $page );
}

/////////////////////////////////////////////////
// menu

$menuArray = array();

run_hook("index_menu_start");

$rows = SQLLib::selectRows("select id, type, link, title from intranet_toc order by orderfield");
foreach($rows as $r) {
  if ($r->type=='separator') {
    $menuArray[] = "<hr/>\n";
    continue;
  }
  if ( @$_SESSION["logindata"] && $r->type=='loggedout') continue;
  if (!@$_SESSION["logindata"] && $r->type=='loggedin') continue;
  $menuArray[] = "<a href='".build_url( $r->link )."'>".$r->title."</a>";
}

run_hook("index_menu_parse",array("menu"=>&$menuArray));

$menu = "<ul>\n";
foreach($menuArray as $v)
  $menu .= "<li>".$v."</li>\n";
$menu .= "</ul>\n";

run_hook("index_menu_end");

/////////////////////////////////////////////////
// templating

$f = @file_get_contents("template.html");
if (!$f) $f = "Please create your own template.html - you can use template.html.dist as an example!";

$TEMPLATE = array();
$TEMPLATE["{%MENU%}"] = $menu;
$TEMPLATE["{%CONTENT%}"] = $content;

run_hook("index_template_elements",array("template"=>&$TEMPLATE));

/////////////////////////////////////////////////
// render

echo str_replace(array_keys($TEMPLATE),array_values($TEMPLATE),$f);
?>
