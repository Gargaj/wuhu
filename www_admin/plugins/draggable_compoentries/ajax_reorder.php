<?php
include_once("../../bootstrap.inc.php");

$lock = new OpLock();

header("Content-type: text/plain");

function _rename($a,$b)
{
  printf("renaming %s to %s\n",$a,$b);
  return rename($a,$b);
}

function is_writable_recursive($dir)
{
  if (!is_writable($dir))
  {
    return false;
  }
  $files = glob($dir."/*");
  foreach($files as $v)
  {
    if (basename($v) == ".") continue;
    if (basename($v) == "..") continue;
    if (is_dir($v))
    {
      if (!is_writable_recursive($v))
      {
        return false;
      }
    }
    else
    {
      if (!is_writable($dir))
      {
        return false;
      }
    }
  }
  return true; 
}

$SUFFIX = ".\$tmp\$/";

$entries = SQLLib::selectRows(sprintf_esc("select * from compoentries where compoid = %d",$_POST["compo"]));
$compo = get_compo($_POST["compo"]);

// before we do anything: check if all of our files and directories are writeable
$origDir = $settings["private_ftp_dir"] . "/" . $compo->dirname;
$tempDir = $settings["private_ftp_dir"] . "/" . $compo->dirname . $SUFFIX;

if (!is_writable_recursive($origDir))
{
  die("ERROR: Some of the files in the ".$origDir." directory are not writeable by the webserver! This would cause problems, so reordering won't be possible until you fix that!");
}
if (file_exists($tempDir) && !is_writable_recursive($tempDir))
{
  die("ERROR: The ".$tempDir." directory is present and not writeable! Please delete it before proceeding!");
}

// step1: move all entries to temporary folder named by entrynumber

_rename($origDir, $tempDir);

$list = array();
foreach($entries as $entry)
{
  $root = $tempDir;
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
  $oldroot = $tempDir;
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

if (!rmdir( $tempDir ))
  die("ERROR deleting");

?>SUCCESS
