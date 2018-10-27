<?
include_once("../../bootstrap.inc.php");

$lock = new OpLock();

header("Content-type: text/plain");

function _rename($a,$b)
{
  printf("renaming %s to %s\n",$a,$b);
  return rename($a,$b);
}

$entries = SQLLib::selectRows(sprintf_esc("select * from compoentries where compoid = %d",$_POST["compo"]));

// step1: move all entries to temporary folder named by entrynumber

$SUFFIX = ".\$tmp\$/";

$compo = get_compo($_POST["compo"]);
_rename($settings["private_ftp_dir"] . "/" . $compo->dirname, $settings["private_ftp_dir"] . "/" . $compo->dirname . $SUFFIX);  

$list = array();
foreach($entries as $entry)
{
  $root = $settings["private_ftp_dir"] . "/" . $compo->dirname . $SUFFIX;
  $old = $root.sprintf("%03d",$entry->playingorder);
  $new = $root.sprintf("_%03d",$entry->id);
  if (file_exists($old))
  {
    _rename($old,$new);
    $list[$entry->id] = true;
  }
}

// step2: move entries back by correct order 

@mkdir( get_compo_dir( $compo ) );
@chmod( get_compo_dir( $compo ), 0777 );

$newOrder = array();

// take the desired order, add any "new" entries to the end
foreach($_POST["order"] as $entryID)
{
  $newOrder[] = $entryID;
  unset($list[$entryID]);
}
foreach($list as $entryID=>$true)
{
  $newOrder[] = $entryID;
}

$n = 1;
foreach($newOrder as $entryID)
{
  $oldroot = $settings["private_ftp_dir"] . "/" . $compo->dirname . $SUFFIX;
  $newroot = get_compo_dir( $compo );
  $olddir = $oldroot.sprintf("_%03d",$entryID);
  $newdir = $newroot.sprintf("%03d",$n);
  
  // if it was deleted since, just skip it
  if (file_exists($olddir))
  {
    _rename($olddir,$newdir);
    SQLLib::Query(sprintf_esc("update compoentries set playingorder = %d where id = %d",$n,$entryID));
    $n++;
    unset($list[$entryID]);
  }
}

if (!rmdir( $settings["private_ftp_dir"] . "/" . $compo->dirname . $SUFFIX ))
  die("ERROR deleting");

?>SUCCESS