<?php
if (!defined("ADMIN_DIR")) exit();

if (is_user_logged_in())
{
  header( "Location: ".build_url("News",array("login"=>"alreadyloggedin")) );
  exit();
}

run_hook("login_start");

if ($_POST["login"])
{
  $_SESSION["logindata"] = NULL;

  $userID = SQLLib::selectRow(sprintf_esc("select id from users where `username`='%s' and `password`='%s'",$_POST["login"],hashPassword($_POST["password"])))->id;

  run_hook("login_authenticate",array("userID"=>&$userID));

  if ($userID)
  {
    $_SESSION["logindata"] = SQLLib::selectRow(sprintf_esc("select * from users where id=%d",$userID));
    header( "Location: ".build_url("News",array("login"=>"success")) );
  }
  else
  {
    header( "Location: ".build_url("Login",array("login"=>"failure")) );
  }
  exit();
}
if ($_GET["login"]=="failure")
  echo "<div class='error'>Login failed!</div>";
?>
<form action="<?=build_url("Login")?>" method="post" id='loginForm'>
<div>
  <label for="loginusername">Username:</label>
  <input id="loginusername" name="login" type="text" required='yes' />
</div>
<div>
  <label for="loginpassword">Password:</label>
  <input id="loginpassword" name="password" type="password" required='yes' />
</div>
<div>
  <input type="submit" value="Go!" />
</div>
</form>
<?php
run_hook("login_end");
?>
