<?php
/*
Plugin name: Oneliner
Description: Standard run-off-the-mill oneliner plugin. Note: use the {%ONELINER%} substitution token in your template!
*/
include_once("functions.inc.php");

function oneliner_parsepost( $data )
{
  if ($_POST["onelinerText"] && is_user_logged_in())
  {
    $valid = true;

    //$row = SQLLib::selectRow(sprintf_esc("select id from compoentries where userID = %d limit 1",get_user_id()));
    //if (!$row) $valid = false;

    $row = SQLLib::selectRow(sprintf_esc("select userID from oneliner order by datetime desc"));
    if ($row->userID == get_user_id())
      $valid = false;

    if ($valid)
    {
      $a = array();
      $a["userID"] = get_user_id();
      $a["contents"] = trim($_POST["onelinerText"]);
      $a["datetime"] = date("Y-m-d H:i:s");
      SQLLib::InsertRow("oneliner",$a);

      oneliner_generate_slide();
    }

    redirect();
  }
}

function oneliner_add_template_element( $data )
{
  if ($data["title"] != "News") {
    //$TEMPLATE["{%ONELINER%}"] = "";
    //return;
  }

  $rows = SQLLib::selectRows(
    "select oneliner.datetime, users.nickname, oneliner.contents from oneliner ".
    "left join users on users.id = oneliner.userid order by datetime desc limit 10");

  $s = "<div id='onelinercontainer'><h4>Funliner</h4>\n";
  if ($rows)
  {
    $s .= "<ul>\n";
    foreach($rows as $row)
    {
      $text = wordwrap($row->contents,30,"\n",1);
      $s .= sprintf("  <li><span class='onelinertime'>[%s]</span> <span class='onelinernick'>&lt;%s&gt;</span> %s</li>\n",substr($row->datetime,-8),_html($row->nickname),_html($text));
    }
    $s .= "</ul>\n";
  }
  if(is_user_logged_in())
  {
    //$row = SQLLib::selectRow(sprintf_esc("select id from compoentries where userID = %d limit 1",get_user_id()));
    $row = true;
    if ($row)
    {
      $s .= "<form action='".$_SERVER["REQUEST_URI"]."' method='post' enctype='multipart/form-data'>\n";
      $s .= "  <input type='text' name='onelinerText' class='form-control oneliner-input' />\n";
      $s .= "  <input type='submit' value='Go!' class='btn btn-default oneliner-button' />\n";
      $s .= "</form>\n";
    } else {
      $s .= "<span class='not4u'>No releases, no oneliner!</span>\n";
    }
  } else {
    $s .= "<span class='not4u'>Log in to post!</span>\n";
  }
  $s .= "</div>\n";

  $data["template"]["{%ONELINER%}"] = $s;
}
add_hook("index_template_elements","oneliner_parsepost");
add_hook("index_template_elements","oneliner_add_template_element");

function oneliner_addmenu( $data )
{
  $data["links"]["pluginoptions.php?plugin=oneliner"] = "Oneliner";
}

add_hook("admin_menu","oneliner_addmenu");

function oneliner_contentstart()
{
  if (!is_writable("./slides/")) {
    printf("<div class='error'>Oneliner plugin: Unable to write into slides directory!</div>");
  }
}
add_hook("admin_content_start","oneliner_contentstart");

function oneliner_activation()
{
  $r = SQLLib::selectRow("show tables where tables_in_".SQL_DATABASE."='oneliner'");
  if (!$r)
  {
    SQLLib::Query("CREATE TABLE `oneliner` (".
    "  `id` int(11) NOT NULL AUTO_INCREMENT,".
    "  `userID` int(11) NOT NULL,".
    "  `contents` varchar(64) collate utf8_unicode_ci NOT NULL,".
    "  `datetime` datetime NOT NULL,".
    "  PRIMARY KEY (`id`)".
    ") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
  }

  if (get_setting("oneliner_nickcolor") === null)
    update_setting("oneliner_nickcolor","#000000");
  if (get_setting("oneliner_textcolor") === null)
    update_setting("oneliner_textcolor","#FFFFFF");
  if (get_setting("oneliner_fontsize") === null)
    update_setting("oneliner_fontsize",20);
  if (get_setting("oneliner_by1") === null)
    update_setting("oneliner_by1",100);
  if (get_setting("oneliner_by2") === null)
    update_setting("oneliner_by2",700);
  if (get_setting("oneliner_wordwrap") === null)
    update_setting("oneliner_wordwrap",60);
  if (get_setting("oneliner_xsep") === null)
    update_setting("oneliner_xsep",256);
  if (get_setting("oneliner_linespacing") === null)
    update_setting("oneliner_linespacing",0.9);
  if (get_setting("oneliner_slidecount") === null)
    update_setting("oneliner_slidecount",10);

}
add_activation_hook( __FILE__, "oneliner_activation" );
?>
