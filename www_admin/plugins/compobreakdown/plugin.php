<?
/*
Plugin name: Compo breakdown
Description: Allows the organizer to add notes to self to entries, and view the compo as a "breakdown list" (which files are the correct ones, what the notes are)
*/

function compobreakdown_updatedb( $data )
{
  if (is_admin_page())
    $data["sqlData"]["organotes"] = $_POST["organotes"];
  //else
  //  $data["sqlData"]["organotes"] = "";
}

add_hook("admin_common_handleupload_beforedb","compobreakdown_updatedb");

function compobreakdown_editform( $data )
{
  if (!$data["entry"]) return;
?>
<tr>
  <td>Organizer notes to self<br/><small>(typical examples of this would include
    "which platform", "which emulator", "this one is long", "leave running after fadeout", etc.)</small></td>
  <td><textarea id="organotes" name="organotes"><?=htmlspecialchars($data["entry"]->organotes)?></textarea></td>
</tr>
<?
}
add_hook("admin_editentry_editform","compobreakdown_editform");

function compobreakdown_link()
{
  printf("<a href='pluginoptions.php?plugin=compobreakdown&compoID=%d'>Go to compo breakdown</a>",$_GET["id"]);
}

add_hook("admin_compo_entrylist_end","compobreakdown_link");

function compobreakdown_activation()
{
  $r = SQLLib::selectRow("show columns from compoentries where field = 'organotes'");
  if (!$r)
  {
    SQLLib::Query("ALTER TABLE compoentries ADD `organotes` TEXT NOT NULL collate utf8_unicode_ci;");
  }
}

add_activation_hook( __FILE__, "compobreakdown_activation" );
?>