<?
if (!defined("ADMIN_DIR")) exit();

run_hook("register_start");

function validate() {
  if (strlen($_POST["username"])<3) {
    echo "<div class='error'>This username is too short, must be at least 4 characters!</div>";
    return 0;
  }
  if (strlen($_POST["password"])<4) {
    echo "<div class='error'>This password is too short, must be at least 4 characters!</div>";
    return 0;
  }
  if (!preg_match("/^[a-zA-Z0-9]{3,}$/",$_POST["username"])) {
    echo "<div class='error'>This username contains invalid characters!</div>";
    return 0;
  }
  /*
  if (!preg_match("/^[a-zA-Z0-9]{4,}$/",$_POST["password"])) {
    echo "<div class='error'>This password contains invalid characters!</div>";
    return 0;
  }
  */
    if (strcmp($_POST["password"],$_POST["password2"])!=0) {
    echo "<div class='error'>Passwords don't match!</div>";
    return 0;
  }
  
  $r = SQLLib::selectRows(sprintf_esc("select * from users where `username`='%s'",$_POST["username"]));
  if ($r) {
    echo "<div class='error'>This username is already taken!</div>";
    return 0;
  }
  
  $r = SQLLib::selectRow(sprintf_esc("select * from votekeys where `votekey`='%s'",$_POST["votekey"]));
  if (!$r) {
    echo "<div class='error'>This votekey is invalid!</div>";
    return 0;
  }
  if ($r->userid) {
    echo "<div class='error'>This votekey is already in use!</div>";
    return 0;
  } 
  
  return 1;
}
$success = false;
if ($_POST["username"]) {
  if (validate()) {
    $userdata = array(
      "username"=> ($_POST["username"]),
      "password"=> hashPassword($_POST["password"]),
      "nickname"=> ($_POST["nickname"]),
      "group"=> ($_POST["group"]),
      "regip"=> ($_SERVER["REMOTE_ADDR"]),
      "regtime"=> (date("Y-m-d H:i:s")),
    );
    $error = "";
    run_hook("register_processdata",array("data"=>&$userdata));
    if (!$error)
    {
      SQLLib::InsertRow("users",$userdata);    
      SQLLib::UpdateRow("votekeys",array("userid"=>mysql_insert_id()),sprintf_esc("`votekey`='%s'",$_POST["votekey"]));
      echo "<div class='success'>Registration successful!</div>";
      $success = true;
    } 
    else 
    {
      echo "<div class='failure'>".htmlspecialchars($error)."</div>";
    }
  }
}
if(!$success)
{
?>
<form action="<?=build_url("Login")?>" method="post">
<div>
  <label for="username">Username:</label>
  <input id="username" name="username" type="text" value="<?=htmlspecialchars($_POST["username"])?>"/>
</div>
<div>
  <label for="password">Password:</label>
  <input id="password" name="password" type="password" />
</div>
<div>
  <label for="password2">Password again:</label>
  <input id="password2" name="password2" type="password" />
</div>
<div>
  <label for="votekey">Votekey: <small>(Get one at the infodesk to be able to register!)</small></label>
  <input id="votekey" name="votekey" type="text" value="<?=htmlspecialchars($_POST["votekey"])?>"/>
</div>
<div>
  <label for="nickname">Nick/Handle:</label>
  <input id="nickname" name="nickname" type="text" value="<?=htmlspecialchars($_POST["nickname"])?>"/>
</div>
<div>
  <label for="group">Group: (if any)</label>
  <input id="group" name="group" type="text" value="<?=htmlspecialchars($_POST["group"])?>"/>
</div>
<?
run_hook("register_endform");
?>
<div id='regsubmit'>
  <input type="submit" value="Go!" />
</div>
</form>
<?
}

run_hook("register_end");
?>
