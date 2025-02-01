<?php
if (version_compare(PHP_VERSION, '5.5.0', '<'))
{
  die("Please use a more recent version of PHP - at least 5.5!");
  exit();
}
define("SQLLIB_SUPPRESSCONNECT",true);
include_once("sqllib.inc.php");
?>
<!DOCTYPE html>
<html>
<head>
 <title>party management system whatever thing config</title>
 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2" />

<style type="text/css">
body {
  background: black;
  color: white;
  font-family: tahoma;
  font-size: 12px;
}
table {
  margin: 20px auto;
  width: 50%;
}
table td {
  border: 1px #444 solid;
  padding: 10px;
}
input {
  width: 320px;
  padding: 5px;
  background: #444;
  border: white 1px solid;
  font-family: tahoma;
  font-size: 12px;
  color: white;
}
input.resolution {
  width: 50px;
}
input[type="submit"] {
  width: auto;
  border-top: #666 1px solid;
  border-left: #666 1px solid;
  border-bottom: #222 1px solid;
  border-right: #222 1px solid;
}
input[type="radio"] {
  display: inline;
  width: 20px;
}
a {
  color: white;
}
.success {
  background: #8f8;
  color: #080;
  border: 2px solid #080;
  margin-bottom: 10px;
  text-align: center;
  padding: 5px;
}
.success a {
  color: #040;
}
.error {
  background: #f88;
  color: #800;
  border: 2px solid #800;
  margin-bottom: 10px;
  text-align: center;
  padding: 5px;
}
.error a {
  color: #400;
}

</style>
</head>
<body>

<?php
function parse_php_size_setting( $str )
{
  $unit = (int)stripos( "bkmgtpezy", substr( $str, -1 ) );
  return (float)$str * pow(1024,$unit);
}

$_POST = clearArray($_POST);
function perform(&$msg) {
  $msg = "";

  if (!function_exists("mysqli_connect")) {
    $msg = "Unable to load MySQLi extension!";
    return 0;
  }

  if (!function_exists("imagecopyresampled")) {
    $msg = "Unable to load GD2 extension!";
    return 0;
  }

  if (!function_exists("mb_convert_encoding")) {
    $msg = "Unable to load Multibyte string extension!";
    return 0;
  }

  clearstatcache();

  if (!is_writable("./")) {
    $msg = "Unable to write into admin directory!";
    return 0;
  }

  if (!is_writable($_POST["main_www_dir"]."/")) {
    $msg = "Unable to write into user-side interface directory!";
    return 0;
  }

  if (file_put_contents($_POST["main_www_dir"]."/database.inc.php","test")===FALSE) {
    $msg = "Unable to create file into user-side interface directory!";
    return 0;
  }

  if (!is_writable($_POST["private_ftp_dir"]."/")) {
    $msg = "Unable to write into compo entry directory!";
    return 0;
  }

  if (disk_free_space($_POST["private_ftp_dir"])<1024*1024*1024) {
    $msg = "Your compo entry dir has less than 1GB space left!";
    return 0;
  }

  if ($_POST["public_ftp_dir"] && !is_writable($_POST["public_ftp_dir"]."/")) {
    $msg = "Unable to write into compo export directory!";
    return 0;
  }

  if (!is_writable($_POST["screenshot_dir"]."/")) {
    $msg = "Unable to write into screenshot directory!";
    return 0;
  }

  // end of checks

  try
  {
    SQLLib::Connect($_POST["mysql_host"],$_POST["mysql_username"],$_POST["mysql_password"],$_POST["mysql_database"]);
  }
  catch(SQLLibException $e)
  {
    $msg = "Unable to connect to MySQL: ".$e->getMessage();
    return 0;
  }

  try
  {
    $f = file_get_contents("initialize.sql");
    if (SQLLib::GetCharacterSet() == "utf8mb4")
    {
      $f = str_replace("CHARSET=utf8 COLLATE=utf8_unicode_ci","CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",$f);
    }
    $commands = explode(";",$f);
    foreach ($commands as $c)
    {
      if ($c = trim($c))
      {
        SQLLib::Query($c);
      }
    }

    $queries = array(
      sprintf_esc("insert into settings (setting,value) values ('private_ftp_dir' ,'%s')",$_POST["private_ftp_dir"]),
      sprintf_esc("insert into settings (setting,value) values ('public_ftp_dir'  ,'%s')",$_POST["public_ftp_dir"]),
      sprintf_esc("insert into settings (setting,value) values ('screenshot_dir'  ,'%s')",$_POST["screenshot_dir"]),
      sprintf_esc("insert into settings (setting,value) values ('screenshot_sizex','%s')",$_POST["screenshot_sizex"]),
      sprintf_esc("insert into settings (setting,value) values ('screenshot_sizey','%s')",$_POST["screenshot_sizey"]),
      sprintf_esc("insert into settings (setting,value) values ('voting_type'     ,'%s')",$_POST["voting_type"]),
      sprintf_esc("insert into settings (setting,value) values ('party_firstday'  ,'%s')",$_POST["party_firstday"]),
      sprintf_esc("insert into settings (setting,value) values ('party_name'      ,'%s')",$_POST["party_name"]),
    );
    foreach ($queries as $q) {
      SQLLib::Query($q);
    }
  }
  catch (Exception $e)
  {
    $msg = "Unable to set up database structure: ".$e->getMessage();
    return 0;
  }

  $salt = "";
  for($x=0;$x<64;$x++) $salt.=chr(rand(0x30,0x7a));
  $db =
  "<"."?php\n".
  "define('SQL_HOST',\"".addslashes($_POST["mysql_host"])."\");\n".
  "define('SQL_USERNAME',\"".addslashes($_POST["mysql_username"])."\");\n".
  "define('SQL_PASSWORD',\"".addslashes($_POST["mysql_password"])."\");\n".
  "define('SQL_DATABASE',\"".addslashes($_POST["mysql_database"])."\");\n".
  "define('WWW_DIR',\"".$_POST["main_www_dir"]."\");\n".
  "define('ADMIN_DIR',\"".dirname($_SERVER["SCRIPT_FILENAME"])."\");\n".
  "define('PASSWORD_SALT',\"".addslashes($salt)."\");\n".
  "?".">\n";

  file_put_contents("database.inc.php",$db);
  file_put_contents($_POST["main_www_dir"]."/database.inc.php",$db);

  if ($_POST["admin_username"] && $_POST["admin_password"] ) 
  {
    $htaccess =
      "AuthUserFile ".dirname($_SERVER["SCRIPT_FILENAME"])."/.htpasswd\n".
      "AuthGroupFile /dev/null\n".
      "AuthName 'Wuhu Virtual Organizer Area - Enter password to continue'\n".
      "AuthType Basic\n".
      "\n".
      "<If \"%{REQUEST_FILENAME} =~ m#beamer\\.php$# || %{REQUEST_FILENAME} =~ m#slides\\.php$#\">\n".
      "  <LimitExcept OPTIONS>\n".
      "    require valid-user\n".
      "  </LimitExcept>\n".
      "</If>\n".
      "<Else>\n".
      "  require valid-user\n".
      "</Else>\n".
      "\n".
      "<IfModule mod_headers.c>\n".
      "  Header always set Access-Control-Allow-Origin \"*\"\n".  // TODO: maybe narrow this?
      "  Header always set Access-Control-Allow-Methods \"*\"\n".
      "  Header always set Access-Control-Allow-Headers \"*\"\n".
      "  Header always set Access-Control-Allow-Credentials \"true\"\n".
      "</IfModule>\n";
    
    file_put_contents(".htaccess",$htaccess);

    $htpasswd = $_POST["admin_username"] . ":" . password_hash($_POST["admin_password"], PASSWORD_DEFAULT);

    file_put_contents(".htpasswd",$htpasswd);
  }

  file_put_contents("activeplugins.serialize",serialize(array()));

  $symlink = array(
    "prototype.js",
  );
  foreach($symlink as $v)
  {
    @symlink(dirname(__FILE__) . "/" . $v,$_POST["main_www_dir"]."/".basename($v));
  }
  //@mkdir($_POST["screenshot_dir"] . "/thumb/");
  //@chmod($_POST["screenshot_dir"] . "/thumb/",0777);

  $msg = "Everything went fine!";
  return 1;
}

if (!empty($_POST["main_www_dir"])) {
  $b = perform($msg);
  if ($b) {
    echo "<div class='success'>".htmlspecialchars($msg)." <a href='./'>Click here to start!</a> </div>";
  } else {
    echo "<div class='error'>".htmlspecialchars($msg)."</div>";
  }
}

if (!function_exists("mysqli_connect")) {
  printf("<div class='error'>mysqli_connect not found - do you have the mysqli extension enabled?</div>\n");
}

if (!function_exists("imagecopyresampled")) {
  printf( "<div class='error'>imagecopyresampled not found - do you have the gd extension enabled?</div>\n");
}

if (parse_php_size_setting(ini_get("post_max_size"))<64*1024*1024) {
  printf( "<div class='error'>post_max_size (%s) is smaller than 64MB - this can cause a problem</div>\n",ini_get("post_max_size"));
}

if (parse_php_size_setting(ini_get("upload_max_filesize"))<64*1024*1024) {
  printf( "<div class='error'>upload_max_filesize (%s) is smaller than 64MB - this can cause a problem</div>\n",ini_get("upload_max_filesize"));
}

if (parse_php_size_setting(ini_get("memory_limit"))<64*1024*1024) {
  printf( "<div class='error'>memory_limit (%s) is smaller than 64MB - this can cause a problem</div>\n",ini_get("memory_limit"));
}

if ((int)ini_get("session.gc_maxlifetime")<60*60*24) {
  printf( "<div class='error'>session.gc_maxlifetime (%s) is smaller than 24 hours - this will be annoying for your visitors</div>\n",ini_get("session.gc_maxlifetime"));
}

if (disk_free_space(sys_get_temp_dir())<64*1024*1024) {
  printf("<div class='error'>'%s' has less than 64MB space left! This will potentially break uploads!</div>\n",sys_get_temp_dir());
}

?>

<form action="config.php" method="post" enctype="multipart/form-data">

<table>

<tr>
<td colspan="2">
Hi. Welcome. Good luck.
</td>
</tr>

<tr>
  <td>Your <b>full party name</b>?</small>
  </td>
  <td>
  <input name="party_name" value="<?=htmlspecialchars(!empty($_POST["party_name"])?$_POST["party_name"]:"Party ".date("Y"))?>"/>
  </td>
</tr>

<tr>
  <td>What is the <b><u>absolute</u></b> path of the directory
  <b>where the partynet's user-side interface is installed to</b>?
  <small>(This should have read/write permissions for PHP (<?=@get_current_user()?>).)</small>
  </td>
  <td>
  <input name="main_www_dir" value="<?=htmlspecialchars(!empty($_POST["main_www_dir"])?$_POST["main_www_dir"]:str_replace("admin","party",getcwd()))?>"/>
  </td>
</tr>

<tr>
  <td>What is the <b><u>absolute</u></b> path of the directory
  on the server where you want to <b>store the compo entries</b>?
  <small>(This should be an organizer-only dir, possibly FTP accessible, with read/write permissions for Apache.)</small>
  </td>
  <td>
  <input name="private_ftp_dir" value="<?=htmlspecialchars(!empty($_POST["private_ftp_dir"])?$_POST["private_ftp_dir"]:"/var/www/entries_private")?>"/>
  </td>
</tr>

<tr>
  <td>What is the <b><u>absolute</u></b> path of the directory
  on the server where you want to <b>export the compo stuff to</b>?
  <small>(<b>Optional</b> - This is a helper directory; you can either use this to
  export all the entries into one directory before the compo, or do it after the compo once you're ready to
  share the files with the visitors or to upload to scene.org. Should have read/write permissions for Apache.)</small>
  </td>
  <td>
  <input name="public_ftp_dir" value="<?=htmlspecialchars(!empty($_POST["public_ftp_dir"])?$_POST["public_ftp_dir"]:"")?>"/>
  </td>
</tr>

<tr>
  <td>What is the <b><u>absolute</u></b> path of the directory
  on the server where you want to <b>store the entry screenshots</b>?
  <small>(This should be a directory with read/write permissions for Apache,
  but it doesn't have to be accessible for anyone else.)</small>
  </td>
  <td>
  <input name="screenshot_dir" value="<?=htmlspecialchars(!empty($_POST["screenshot_dir"])?$_POST["screenshot_dir"]:"/var/www/screenshots")?>"/>
  </td>
</tr>

<tr>
  <td>What <b>pixel size</b> do you want your screenshots to be?
  <small>(This will be used for both width and height.)</small>
  </td>
  <td>
  <input name="screenshot_sizex" class="resolution" value="<?=htmlspecialchars(!empty($_POST["screenshot_sizex"])?$_POST["screenshot_sizex"]:"160")?>"/> x
  <input name="screenshot_sizey" class="resolution" value="<?=htmlspecialchars(!empty($_POST["screenshot_sizey"])?$_POST["screenshot_sizey"]:"90")?>"/>
  </td>
</tr>

<tr>
  <td>Voting type:</td>
  <td>
  <div><input type="radio" name="voting_type" value="range" checked="checked"/> <a href="http://en.wikipedia.org/wiki/Range_voting">Range voting</a> (users assign ratings to each entry - you need this for live voting)</div>
  <div><input type="radio" name="voting_type" value="preferential"/> <a href="http://en.wikipedia.org/wiki/Instant-runoff_voting">Preferential voting</a> (users select their top 3 entries)</div>
  </td>
</tr>

<tr>
  <td>Party starting day:</td>
  <td>
  <input name="party_firstday" value="<?=htmlspecialchars(!empty($_POST["party_firstday"])?$_POST["party_firstday"]:date("Y-m-d"))?>"/>
  </td>
</tr>

<tr>
  <td>MySQL database host:</td>
  <td>
  <input name="mysql_host" value="<?=htmlspecialchars(!empty($_POST["mysql_host"])?$_POST["mysql_host"]:"localhost")?>"/>
  </td>
</tr>

<tr>
  <td>MySQL database name for the party engine:
<?php
$a = glob("plugins/adminer/adminer-*.php");
if ($a) printf("<small>Haven't set one up yet? <a href='%s' target='_blank'>Here's a web interface to help!</a></small>",$a[0]);
?>
  </td>
  <td>
  <input name="mysql_database" value="<?=htmlspecialchars(!empty($_POST["mysql_database"])?$_POST["mysql_database"]:"")?>"/>
  </td>
</tr>

<tr>
  <td>MySQL username for the party engine:</td>
  <td>
  <input name="mysql_username" value="<?=htmlspecialchars(!empty($_POST["mysql_username"])?$_POST["mysql_username"]:"")?>"/>
  </td>
</tr>

<tr>
  <td>MySQL password for the party engine:</td>
  <td>
  <input name="mysql_password" value="<?=htmlspecialchars(!empty($_POST["mysql_password"])?$_POST["mysql_password"]:"")?>" type="password"/>
  </td>
</tr>

<tr>
  <td>Party admin interface username:</td>
  <td>
  <input name="admin_username" value="<?=htmlspecialchars(!empty($_POST["admin_username"])?$_POST["admin_username"]:"")?>"/>
  </td>
</tr>

<tr>
  <td>Party admin interface password:</td>
  <td>
  <input name="admin_password" value="<?=htmlspecialchars(!empty($_POST["admin_password"])?$_POST["admin_password"]:"")?>" type="password"/>
  </td>
</tr>

<tr>
<td colspan="2">
  <input type="submit" value="Deploy!" />
</td>
</tr>

</table>
</form>

</body>
</html>
