<?php
if (!defined("ADMIN_DIR")) exit();

if ($_POST["nickname"]) {
  global $userdata;
  $userdata = array(
    "group"=> ($_POST["group"]),
  );
  if ($_POST["nickname"])
    $userdata["nickname"] = $_POST["nickname"];
  run_hook("profile_processdata",array("data"=>&$userdata));
  if ($_POST["password"]) {
    if ($_POST["password"]!=$_POST["password2"]) {
      echo "<div class='error'>Passwords don't match!</div>";
    } else {
      $userdata["password"] = hashPassword($_POST["password"]);
    }
  }
  SQLLib::UpdateRow("users",$userdata,sprintf_esc("id='%d'",get_user_id()));
  echo "<div class='success'>Profile editing successful!</div>";
}
global $user;
$user = SQLLib::selectRow(sprintf_esc("select * from users where id='%d'",get_user_id()));
global $page;
?>
<form action="<?=build_url("ProfileEdit")?>" method="post" id='profileForm'>
<div id="profile">
<div>
  <label>Username:</label>
  <b><?=_html($user->username)?></b>
</div>
<div>
  <label for="password">New password: (only if you want to change it)</label>
  <input name="password" type="password" id="password" />
</div>
<div>
  <label for="password2">New password again:</label>
  <input name="password2" type="password" id="password2" />
</div>
<div>
  <label for="nickname">Nick/Handle:</label>
  <input name="nickname" type="text" id="nickname" value="<?=_html($user->nickname)?>" required='yes'/>
</div>
<div>
  <label for="group">Group: (if any)</label>
  <input name="group" type="text" id="group" value="<?=_html($user->group)?>"/>
</div>
<?php
run_hook("profile_endform");
?>
<div id='regsubmit'>
  <input type="submit" value="Go!" />
</div>
</div>
</form>
