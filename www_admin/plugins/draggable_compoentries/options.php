<?
if (!defined("ADMIN_DIR") || !defined("PLUGINOPTIONS"))
  exit();

if ($_POST)
{
  update_setting("draggable_style",$_POST["style"]);
}

?>
<form action="<?=$_SERVER["REQUEST_URI"]?>" method="post">
  <input type='radio' name='style' value='0' id='style1'<?=(get_setting("draggable_style")==0?" checked='checked'":"")?>/> <label for='style1'>Draggable rows</label>
  <input type='radio' name='style' value='1' id='style2'<?=(get_setting("draggable_style")==1?" checked='checked'":"")?>/> <label for='style2'>Numeric input</label>
  <input type="submit"/>
</form>