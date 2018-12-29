<?php
/*
Plugin name: Visitor list
Description: Show registered visitors in the admin
*/
if (!defined("ADMIN_DIR")) exit();

function visitorlist_content( $data )
{
  $content = &$data["content"];
  if (get_page_title() != "Visitors") return;

  $content = "";
  $content .= sprintf("<h2>Visitors of the party</h2>\n");
  $content .= sprintf("<table id='visitors'>\n");
  $num = 1;
  $rows = SQLLib::selectRows("select * from users where visible > 0 order by regtime");
  foreach($rows as $v) {
    $content .= sprintf("<tr>\n");
    $content .= sprintf("  <td>%d.</td>\n",$num++);
    $content .= sprintf("  <td><b>%s</b></td>\n",htmlspecialchars($v->nickname));
    $content .= sprintf("  <td>%s</td>\n",htmlspecialchars($v->group));
    $content .= sprintf("</tr>\n");
  }
  $content .= sprintf("</table>\n");
}
add_hook("index_content","visitorlist_content");

function visitorlist_processfield( $data )
{
  $data["data"]["visible"] = ($_POST["public"] == "on") ? 1 : 0;
}
add_hook("profile_processdata","visitorlist_processfield");
add_hook("register_processdata","visitorlist_processfield");

function visitorlist_addfield( )
{
  $checked = true;
  if (is_user_logged_in())
    $checked = get_current_user_data()->visible;
?>
<div>
  <label for="public">Do you want to appear on the visitors listing?</label>
  <input id="public" name="public" type="checkbox"<?=($checked?' checked="checked"':'')?>/>
</div>
<?php
}
add_hook("profile_endform","visitorlist_addfield");
add_hook("register_endform","visitorlist_addfield");

function visitorlist_activation()
{
  $r = SQLLib::selectRow("show columns from users where field = 'visible'");
  if (!$r)
  {
    SQLLib::Query("ALTER TABLE users ADD `visible` tinyint(4) NOT NULL DEFAULT '0';");
  }
}

add_activation_hook( __FILE__, "visitorlist_activation" );

function visitors_toc( $data )
{
  $data["pages"]["Visitors"] = "Visitors";
}
add_hook("admin_toc_pages","visitors_toc");

?>
