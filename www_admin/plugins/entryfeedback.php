<?
/*
Plugin name: Entry feedback
Description: Allows the admin to respond to an entry for the user
*/

function entryfeedback_updatedb( $data )
{
  if (is_admin_page())
    $data["sqlData"]["organizerfeedback"] = $_POST["organizerfeedback"];
  else // if user updates, remove message
    $data["sqlData"]["organizerfeedback"] = "";
}

add_hook("admin_common_handleupload_beforedb","entryfeedback_updatedb");

function entryfeedback_editform( $data )
{
  if (!$data["entry"]) return;
?>
<tr>
  <td>Organizer comment / response / feedback</td>
  <td><textarea id="organizerfeedback" name="organizerfeedback"><?=htmlspecialchars($data["entry"]->organizerfeedback)?></textarea></td>
</tr>
<?
}
add_hook("admin_editentry_editform","entryfeedback_editform");

function entryfeedback_activation()
{
  $r = SQLLib::selectRow("show columns from compoentries where field = 'organizerfeedback'");
  if (!$r)
  {
    SQLLib::Query("ALTER TABLE compoentries ADD `organizerfeedback` text collate utf8_unicode_ci;");
  }
}

add_activation_hook( __FILE__, "entryfeedback_activation" );

function entryfeedback_menu( $data )
{
  $count = count( SQLLib::selectRows(sprintf_esc("select * from compoentries where length(organizerfeedback) > 1 and userid = %d",get_user_id())) );
  if ($count)
  {
    foreach($data["menu"] as &$v)
    {
      if (strstr($v,"EditEntries"))
      {
        $v .= sprintf(" <a href='%s' class='entryfeedback_msg'>New messages</a>",build_url("EditEntries"));
      }
    }
  }
}

add_hook("index_menu_parse","entryfeedback_menu");

function entryfeedback_userentries( $data )
{
  printf("<td>%s</td>",htmlspecialchars($data["entry"]->organizerfeedback));
}

add_hook("editentries_endrow","entryfeedback_userentries");

function entryfeedback_userentries_header()
{
  printf("<th>Organizer comments</th>");
}

add_hook("editentries_endheader","entryfeedback_userentries_header");

?>
