<?
/*
Plugin name: Result limit
Description: Limits the number of entries shown for each compo on the beamer
*/

function resultlimit_rendervotes( $data )
{
  if ($data["compo"]->resultlimit)
    $data["results"] = array_slice( $data["results"], -1 * $data["compo"]->resultlimit, NULL, true );
}

add_hook("admin_beamer_prizegiving_rendervotes","resultlimit_rendervotes");

function resultlimit_editform( $data )
{
?>
<tr>
  <td>Number of top entries to show during prizegiving: <small>(0 = all of them)</small></td>
  <td><input id="resultlimit" name="resultlimit" type="text" value="<?=htmlspecialchars($data["compo"]->resultlimit)?>"/></td>
</tr>
<?
}
add_hook("admin_compos_editform","resultlimit_editform");

function resultlimit_editform_data( $data )
{
  $data["data"]["resultlimit"] = (int)$_POST["resultlimit"];
}

add_hook("admin_compos_edit_update","resultlimit_editform_data");
add_hook("admin_compos_edit_insert","resultlimit_editform_data");

function resultlimit_activation()
{
  $r = SQLLib::selectRow("show columns from compos where field = 'resultlimit'");
  if (!$r)
  {
    SQLLib::Query("ALTER TABLE compos ADD `resultlimit` tinyint(4) NOT NULL DEFAULT '0';");
  }
}

add_activation_hook( __FILE__, "resultlimit_activation" );

?>