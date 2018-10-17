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

if (count($entries) != count($_POST["order"]))
{
  die("WARNING: the number of releases changed in this compo while you were reordering them; please reload the page and try again!");
}

// step1: move all entries to temporary folder named by entrynumber

$SUFFIX = ".\$tmp\$/";

$compo = get_compo($_POST["compo"]);
_rename($settings["private_ftp_dir"] . "/" . $compo->dirname, $settings["private_ftp_dir"] . "/" . $compo->dirname . $SUFFIX);  

foreach($entries as $entry)
{
  $root = $settings["private_ftp_dir"] . "/" . $compo->dirname . $SUFFIX;
  _rename($root.sprintf("%03d",$entry->playingorder),$root.sprintf("_%03d",$entry->id));
}

// step2: move entries back by correct order 

@mkdir( get_compo_dir( $compo ) );
@chmod( get_compo_dir( $compo ), 0777 );

$n = 1;
foreach($_POST["order"] as $v)
{
  $oldroot = $settings["private_ftp_dir"] . "/" . $compo->dirname . $SUFFIX;
  $newroot = get_compo_dir( $compo );
  _rename($oldroot.sprintf("_%03d",$v),$newroot.sprintf("%03d",$n));
  SQLLib::Query(sprintf_esc("update compoentries set playingorder = %d where id = %d",$n,$v));
  $n++;
}

if (!rmdir( $settings["private_ftp_dir"] . "/" . $compo->dirname . $SUFFIX ))
  die("ERROR deleting");

?>SUCCESS