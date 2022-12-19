<?php
include_once("bootstrap.inc.php");

loadPlugins();

$compo = get_compo( $_GET["id"] );

function changeShowingNumber($entryID, $from, $to)
{
  error_reporting(E_ALL ^ E_NOTICE);
  global $settings,$compo;
  $s = SQLLib::selectRow(sprintf_esc("select * from compoentries where playingorder = %d and compoid = %d",$from,$entryID));
  if (!$s) return;

  $root = get_compo_dir($compo);

  $olddir = $root . sprintf("%03d",$from);
  $newdir = $root . sprintf("%03d",$to);

  rename($olddir,$newdir);

  SQLLib::Query(sprintf_esc("update compoentries set playingorder=%d where id=%d",$to,$s->id));
}

if ($_GET['direction'])
{
  $lock = new OpLock();
  $s = SQLLib::selectRow(sprintf_esc("select * from compoentries where id = %d",$_GET["pid"]));

  $delta = $_GET['direction']=="up" ? -1 : 1;

  changeShowingNumber( $_GET["id"], $s->playingorder+$delta, -1 );
  changeShowingNumber( $_GET["id"], $s->playingorder, $s->playingorder+$delta );
  changeShowingNumber( $_GET["id"], -1, $s->playingorder );

  redirect("compos_entry_list.php?id=".(int)$_GET["id"]);
}

run_hook("admin_compo_entrylist_preheader");

include_once("header.inc.php");
printf("<h2>%s</h2>\n",$compo->name);

if ($_POST["submit"] == "Export!")
{
  export_compo( $compo );
}

$entries = SQLLib::selectRows(sprintf_esc("select *,compoentries.id as id from compoentries ".
  " left join users on users.id=compoentries.userid ".
  " where compoid = %d order by playingorder",$_GET["id"]));

run_hook("admin_compo_entrylist_start");

?>
<table class='minuswiki' id='compoentrylist'>
<tr>
  <th title='Display order'>Order</th>
  <th title='Entry ID'>#</th>
  <th>Title</th>
  <th>Author</th>
  <th>Uploader</th>
  <th>File name</th>
  <th>File size</th>
<?php
  $compo = get_compo( $_GET["id"] );
  if ($compo->votingopen == 0)
    echo "<th colspan='2'>Re-order</th>\n";
?>
  <th>Upload time</th>
<?php
  run_hook("admin_compo_entrylist_headerrow_end");
?>
</tr>
<?php
$n = 1;
global $entry;
foreach($entries as $entry)
{
  printf("<tr class='entry'>\n");
  printf("  <td%s>%d.</td>\n",$entry->playingorder!=$n?" style='color:red; font-weight:bold;'":"",$entry->playingorder);
  printf("  <td class='entrynumber'>#%d</td>\n",$entry->id);
  printf("  <td><a href='compos_entry_edit.php?id=%d'>%s</a></td>\n",$entry->id,_html($entry->title));
  printf("  <td>%s</td>\n",_html($entry->author));
  if ($entry->userid)
    printf("  <td><a href='users.php?id=%d'>%s</a></td>\n",$entry->userid,_html($entry->nickname));
  else
    printf("  <td>Admin superuser</td>\n");
  printf("  <td><a href='compos_entry_edit.php?download=%d'>%s</a></td>\n",$entry->id,basename($entry->filename));
  @printf("  <td>%s bytes</td>\n",number_format(filesize(get_compoentry_file_path($entry)),0));
  if ($compo->votingopen == 0)
  {
    if ($entry->playingorder > 1)
      printf("  <td class='move moveup'><a href='compos_entry_list.php?pid=%d&amp;id=%d&amp;direction=up'>&uarr;</a></td>\n",$entry->id,$_GET["id"]);
    else
      printf("  <td class='move moveup'>&nbsp;</td>\n");
    if ($n < count($entries))
      printf("  <td class='move movedown'><a href='compos_entry_list.php?pid=%d&amp;id=%d&amp;direction=down'>&darr;</a></td>\n",$entry->id,$_GET["id"]);
    else
      printf("  <td class='move movedown'>&nbsp;</td>\n");
  }
  printf("  <td title='uploaded from %s'>%s</td>\n",$entry->uploadip,date("D H:i:s",strtotime($entry->uploadtime)));
  run_hook("admin_compo_entrylist_row_end",array("entry"=>&$entry));
  printf("</tr>\n");
  $n++;
}
printf("<tr><td colspan='9'><a href='compos_entry_edit.php?compo=%d'>add new entry</a></td></tr>\n",$_GET["id"]);
echo "</table>\n";

run_hook("admin_compo_entrylist_end");

?>
<form action="compos_entry_list.php?id=<?=$_GET["id"]?>" method="post" enctype="multipart/form-data">
  <h2>Export compo stuff to export directory</h2>
  <div>
    <input type="submit" name="submit" value="Export!" />
  </div>
  <small>(Note: whether this is publicly visible or not depends on how you set your server up! The directory is <b><?=_html($settings["public_ftp_dir"])?></b>)</small>
</form>
<?php
include_once("footer.inc.php");
?>
