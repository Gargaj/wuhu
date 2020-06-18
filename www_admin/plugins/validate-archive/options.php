<?php
if (!defined("ADMIN_DIR") || !defined("PLUGINOPTIONS"))
  exit();

include_once("functions.inc.php");

if ($_POST)
{
  update_setting("validatearchive_type",$_POST["type"]);
  update_setting("validatearchive_rename",$_POST["rename"]);
  if (class_exists("ZipArchive")) 
  {
    update_setting("validatearchive_fileiddiz",$_POST["fileiddiz"]);
  }
}

?>
<h2>Validate uploaded compo entry files</h2>

<form action="<?=$_SERVER["REQUEST_URI"]?>" method="post">
  <h3>Allowed file types</h3>
  <label><input type='radio' name='type' value='all' <?=(get_setting("validatearchive_type")=="all"?" checked='checked'":"")?>/> Any file type</label>
  <label><input type='radio' name='type' value='zip' <?=(get_setting("validatearchive_type")=="zip"?" checked='checked'":"")?>/> ZIP only</label>
  <label><input type='radio' name='type' value='ziprar' <?=(get_setting("validatearchive_type")=="ziprar"?" checked='checked'":"")?>/> ZIP and RAR</label>

  <h3>Rename file to "title by author.ext"</h3>
  <label><input type='radio' name='rename' value='never' <?=(get_setting("validatearchive_rename")=="never"?" checked='checked'":"")?>/> Never</label>
  <label><input type='radio' name='rename' value='check' <?=(get_setting("validatearchive_rename")=="check"?" checked='checked'":"")?>/> If filename is too common (see below)</label>
  <label><input type='radio' name='rename' value='always' <?=(get_setting("validatearchive_rename")=="always"?" checked='checked'":"")?>/> Always</label>
  <p>("Too common" means a filename like these: <?php global $VALIDATEARCHIVE_COMMON; echo implode(", ", array_keys($VALIDATEARCHIVE_COMMON) ) ?>)</p>

<?php if (class_exists("ZipArchive")) {?>
  <h3>file_id.diz requirements (ZIP only!); if missing...</h3>
  <label><input type='radio' name='fileiddiz' value='nothing' <?=(get_setting("validatearchive_fileiddiz")=="nothing"?" checked='checked'":"")?>/> ...do nothing</label>
  <label><input type='radio' name='fileiddiz' value='error' <?=(get_setting("validatearchive_fileiddiz")=="error"?" checked='checked'":"")?>/> ...throw error</label>
  <label><input type='radio' name='fileiddiz' value='generate' <?=(get_setting("validatearchive_fileiddiz")=="generate"?" checked='checked'":"")?>/> ...generate from entry info and add to archive <small>(with accompanying snarky message)</small></label>
<?php }?>

  <input type="submit"/>
</form>
