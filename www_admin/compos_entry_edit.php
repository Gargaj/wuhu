<?php
  include_once("bootstrap.inc.php");

  if ($_GET["download"])
  {
    $entry = SQLLib::selectRow(sprintf_esc("select * from compoentries where id=%d",$_GET["download"]));
    $dirname = get_compoentry_dir_path($entry);
    
    $path = $dirname . $entry->filename;
    $data = file_get_contents($path);
    
    header("Content-type: application/octet-stream");
    header("Content-disposition: attachment; filename=\"".basename($entry->filename)."\"");
    header("Content-length: ".filesize($path));
    echo $data;
    
    exit();
  }
  
  if ($_GET["select"])
  {
    $lock = new OpLock();
    $e = SQLLib::selectRow(sprintf_esc("select * from compoentries where id=%d",$_GET["id"]));

    $a = array();
    $a["filename"] = basename($_GET["select"]);
    SQLLib::updateRow("compoentries",$a,"id=".(int)$_GET["id"]);

    redirect("compos_entry_edit.php?id=".(int)$_GET["id"]);
  }
  include_once("header.inc.php");

  if ($_POST["submit"]=="Move!")
  {
    $lock = new OpLock();
    $e = SQLLib::selectRow(sprintf_esc("select * from compoentries where id=%d",$_POST["id"]));
    if (!$e) die("Error while getting compo entry");

    $maxID = (int)SQLLib::selectRow(sprintf_esc("select max(playingorder) as c from compoentries where compoid=%d",$_POST["targetCompoID"]))->c + 1;

    $oldCompo = get_compo($e->compoid);
    $newCompo = get_compo($_POST["targetCompoID"]);

    $oldDir = get_compoentry_dir_path($e);
    $newDir = get_compo_dir($_POST["targetCompoID"]) . sprintf("%03d",$maxID) . "/";
    if (!$oldDir) die("Error while getting old compo entry dir");
    if (!$newDir) die("Error while getting new compo entry dir");

    @mkdir( get_compo_dir($_POST["targetCompoID"]) );
    @chmod( get_compo_dir($_POST["targetCompoID"]) , 0777);

    @mkdir($newDir);
    @chmod($newDir, 0777);

    $a = glob($oldDir . "*");
    foreach($a as $v) {
      $n = basename($v);
      rename($oldDir . $n, $newDir . $n);
    }

    $a = array();
    $a["compoID"] = $_POST["targetCompoID"];
    $a["filename"] = basename($e->filename);
    $a["playingorder"] = $maxID;
    SQLLib::updateRow("compoentries",$a,"id=".(int)$_POST["id"]);

    printf("<div class='success'>Entry %d moved to compo %s (%s)</div>\n",$_POST["id"],$newCompo->name,$newDir);
    $lock = null;
  }

  if ($_POST["submit"]=="Delete!")
  {
    $lock = new OpLock();
    $entry = SQLLib::selectRow(sprintf_esc("select * from compoentries where id=%d",$_POST["id"]));
    if (!$entry) die("Error while getting compo entry");

    $dirname = get_compoentry_dir_path($entry);
    if (!$dirname) die("Error while getting compo entry dir");

    $a = glob($dirname."*");
    foreach ($a as $v)
      unlink($v);
    rmdir($dirname);

    SQLLib::Query(sprintf_esc("delete from compoentries where id=%d",$_POST["id"]));
    printf("<div class='success'>Entry %d deleted</div>\n",$_POST["id"]);
    $lock = null;
  }

  $id = NULL;
  if ($_REQUEST["id"])
    $id = (int)$_REQUEST["id"];

  if ($_POST["submit"]=="Go!")
  {
    $out = array();
    $data = $_POST;
    $data["id"] = $_REQUEST["id"];
    $data["compoID"] = $_POST["compo"];
    $data["localScreenshotFile"] = $_FILES['screenshot']['tmp_name'];
    $data["localFileName"] = $_FILES['entryfile']['tmp_name'];
    $data["originalFileName"] = $_FILES['entryfile']['name'];
    run_hook("admin_editentry_before_handle",array("data"=>&$data));
    if (handleUploadedRelease($data,$out))
    {
      printf("<div class='success'>Handled <a href='compos_entry_edit.php?id=%d'>%s</a> as %d</div>",$out["entryID"],_html($_POST["title"]),$out["entryID"]);
    }
    else
    {
      printf("<div class='error'>%s</div>",$out["error"]);
    }
  }
  if ($id)
    $entry = SQLLib::selectRow(sprintf_esc("select * from compoentries where id = %d",$id));

?>
<form action="compos_entry_edit.php?id=<?=$id?>" method="post" enctype="multipart/form-data">
<table id="uploadform">
<?php
if ($id) {
?>
<tr>
  <td>Compo:</td>
  <td><?php
  $s = get_compo($entry->compoid);
  printf("<a href='compos_entry_list.php?id=%d'>%s</a>",$s->id,$s->name);
  $dirname = get_compoentry_dir_path($entry);
  ?></td>
</tr>
<?php
} else {
?>
<tr>
  <td>Compo:</td>
  <td><select name="compo">
<?php
$dirname = NULL;
$s = SQLLib::selectRows("select * from compos order by start");
$compoID = NULL;
if ($_GET["compo"])
  $compoID = (int)$_GET["compo"];
if ($_POST["compo"])
  $compoID = (int)$_POST["compo"];
foreach($s as $t) {
  printf("  <option value='%d'%s>%s</option>\n",$t->id,$compoID==$t->id?" selected='selected'":"",$t->name);
}
?>
  </select></td>
</tr>
<?php
}
?>
<tr>
  <td>Product title:</td>
  <td><input name="title" type="text" value="<?=_html($entry->title)?>" class="inputfield"/></td>
</tr>
<tr>
  <td>Author:</td>
  <td><input name="author" type="text" value="<?=_html($entry->author)?>" class="inputfield"/></td>
</tr>
<tr>
  <td>Comment: (this will be shown on the compo slide)</td>
  <td><textarea name="comment"><?=_html($entry->comment)?></textarea></td>
</tr>
<tr>
  <td>Comment for the organizers: (this will NOT be shown anywhere)</td>
  <td><textarea name="orgacomment"><?=_html($entry->orgacomment)?></textarea></td>
</tr>
<?php if ($entry) { ?>
<tr>
  <td>Upload info:</td>
  <td>Uploaded at <i><?=$entry->uploadtime?></i> from <i><?=$entry->uploadip?></i> by <?php
    if ($entry->userid)
    {
      $user = SQLLib::SelectRow(sprintf_esc("select id,nickname from users where id = %d",$entry->userid));
      if ($user)
        printf("<a href='users.php?id=%d'>%s</a>\n",$user->id,_html($user->nickname));
    }
    else
      echo "Admin superuser";
  ?></td>
</tr>
<?php } ?>
<tr>
  <td>Uploaded files:</td>
  <td>
    <ul class='filelist'>
    <?php
    if ($dirname) {
      $a = glob($dirname."*");

      foreach($a as $v)
      {
        $v = basename($v);
        if ($v == $entry->filename)
          printf("<li class='selectedfile'><span><a href='compos_entry_edit.php?download=%d'>%s</a></span> - %d bytes</li>\n",$entry->id,$v,filesize($dirname . $v));
        else
          printf("<li><span>%s</span> - %d bytes [<a href='compos_entry_edit.php?id=%d&amp;select=%s'>select</a>]</li>\n",
            $v,filesize($dirname . $v),$_GET["id"],_html($v));
      }
    }
    ?>
    </ul>
  </td>
</tr>
<tr>
  <td>Upload new file: (max. <?=ini_get("upload_max_filesize")?>)</td>
  <td><input name="entryfile" type="file" class="inputfield"/></td>
</tr>
<tr>
  <td>Screenshot: (JPG, GIF or PNG!)</td>
  <td>
<?php if ($id) { ?>
  <div>
    <img src='screenshot.php?id=<?=(int)$id?>&amp;show=thumb' alt='thumb'/>
  </div>
<?php } ?>
  <input name="screenshot" type="file" class="inputfield" accept="image/*" />
  </td>
</tr>
<?php
run_hook("admin_editentry_editform",array("entry"=>$entry));
?>
<tr>
  <td colspan="2">
  <input type="hidden" value="<?=$id?>" name="id"/>
  <input type="submit" name="submit" class='button-submit' value="Go!" />
  <input type="submit" name="submit" class='button-delete' value="Delete!" id="delentry"/></td>
</tr>
</table>
</form>

<script type="text/javascript">
<!--
document.observe("dom:loaded",function(){
  if ($("delentry")) $("delentry").observe("click",function(e){
    if (!confirm("Are you sure you want to delete this entry?"))
      e.stop();
  });
});
//-->
</script>


<?php if ($id) { ?>
<form action="compos_entry_edit.php?id=<?=$id?>" method="post" enctype="multipart/form-data">
  <h2>Move to compo:</h2>
  <input type="hidden" value="<?=$id?>" name="id"/>
  <select name="targetCompoID">
<?php
$dirname = NULL;
$s = SQLLib::selectRows("select * from compos order by start");
foreach($s as $t) {
  if ($t->id == $entry->compoid) continue;
  printf("  <option value='%d'%s>%s</option>\n",$t->id,max($entry->compoid,$_GET["compo"])==$t->id?" selected='selected'":"",$t->name);
}
?>
  </select>
  <div>
    <input type="submit" name="submit" value="Move!" />
  </div>
</form>
<?php } ?>

<?php
  include_once("footer.inc.php");
?>
