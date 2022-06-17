<?php
class OpLock
{
  function __construct()
  {
    $this->f = fopen(ADMIN_DIR . "/.oplock","wb");
    flock($this->f,LOCK_EX);
    fwrite($this->f,"open."); // this is to see if the lock gets stuck somewhere.
  }
  function __destruct()
  {
    fwrite($this->f,"close.");
    flock($this->f,LOCK_UN);
    fclose($this->f);
  }
};

function sanitize_filename( &$filename )
{
  $filename = preg_replace("/[^a-zA-Z0-9\._\-]+/","-",$filename);
  $filename = strtolower($filename);
  return true;
}

function sanitize_votekey( $votekey )
{
  return $votekey;
  // todo later
  //return preg_replace("/[^a-zA-Z0-9]+/","",$votekey);
}

function redirect( $uri = null )
{
  header( "Location: " . ($uri ? $uri : $_SERVER["REQUEST_URI"]) );
  exit();
}

function hashPassword( $pwd )
{
  $hash = $pwd;
  for($x=0;$x<10;$x++) $hash = sha1( PASSWORD_SALT . $hash );
  return $hash;
}

function _html( $s )
{
  return htmlspecialchars( $s, ENT_QUOTES );
}

function _js( $s )
{
  return addcslashes( $s, "\x00..\x1f" );
}
/**
 * Multibyte capable wordwrap
 * http://php.net/manual/en/function.wordwrap.php#98724
 *
 * @param string $str
 * @param int $width
 * @param string $break
 * @return string
 */
function mb_wordwrap($str, $width=74, $break="\r\n")
{
  if (!function_exists("mb_substr"))
    return wordwrap($str, $width, $break, 1);

  // Return short or empty strings untouched
  if(empty($str) || mb_strlen($str, 'UTF-8') <= $width)
      return $str;

  $br_width  = mb_strlen($break, 'UTF-8');
  $str_width = mb_strlen($str, 'UTF-8');
  $return = '';
  $last_space = false;

  for($i=0, $count=0; $i < $str_width; $i++, $count++)
  {
      // If we're at a break
      if (mb_substr($str, $i, $br_width, 'UTF-8') == $break)
      {
          $count = 0;
          $return .= mb_substr($str, $i, $br_width, 'UTF-8');
          $i += $br_width - 1;
          continue;
      }

      // Keep a track of the most recent possible break point
      if(mb_substr($str, $i, 1, 'UTF-8') == " ")
      {
          $last_space = $i;
      }

      // It's time to wrap
      if ($count > $width)
      {
          // There are no spaces to break on!  Going to truncate :(
          if(!$last_space)
          {
              $return .= $break;
              $count = 0;
          }
          else
          {
              // Work out how far back the last space was
              $drop = $i - $last_space;

              // Cutting zero chars results in an empty string, so don't do that
              if($drop > 0)
              {
                  $return = mb_substr($return, 0, -$drop);
              }

              // Add a break
              $return .= $break;

              // Update pointers
              $i = $last_space + ($br_width - 1);
              $last_space = false;
              $count = 0;
          }
      }

      // Add character from the input string to the output
      $return .= mb_substr($str, $i, 1, 'UTF-8');
  }
  return $return;
}

function check_menuitem($url)
{
  return !!SQLLib::SelectRow(sprintf_esc("select id from intranet_toc where link='%s'",$url));
}

/*
 * $dataArray members:
 *   id - the ID of the entry, if we're updating; don't set if new entry
 *   compoID - the ID of the compo
 *   userID - the uploader of the entry; if 0 or unset: superuser
 *   localScreenshotFile - the filename of a screenshot on the server
 *   localFileName - the filename of the entry on the server after upload
 *   originalFileName - the original filename of the entry
 *   title       |
 *   author      | optional textfields
 *   comment     | title and author must be speficified for new releases
 *   orgacomment |
 */
function handleUploadedRelease( $dataArray, &$output )
{
  $lock = new OpLock();

  global $settings;

  $output = array();

  $entry = null;
  $id = null;
  if ($dataArray["id"])
  {
    // existing release
    $id = (int)$dataArray["id"];
    $entry = SQLLib::selectRow(sprintf_esc("select * from compoentries where id=%d",$dataArray["id"]));
    if (!$entry)
    {
      $output["error"] = "Entry not found!";
      return false;
    }
  }
  //if (!$entry && (!$dataArray["title"] || !$dataArray["author"]))
  if (!$dataArray["title"] || !$dataArray["author"])
  {
    $output["error"] = "You have to specify a title and an author!";
    return false;
  }
  if (!$entry)
  {
    if (defined("ADMIN_PAGE") && !file_exists($dataArray["localFileName"]))
    {
      $output["error"] = "You have to specify a file!";
      return false;
    }
    if (!defined("ADMIN_PAGE") && !is_uploaded_file($dataArray["localFileName"]))
    {
      $output["error"] = "You have to select a file!";
      return false;
    }
  }

  run_hook("admin_common_handleupload_beforecompocheck",array("dataArray"=>$dataArray,"output"=>&$output));
  if ($output["error"])
  {
    return false;
  }

  $compo = null;
  if ($entry)
  {
    $compo = SQLLib::selectRow(sprintf_esc("select * from compos where id=%d",$entry->compoid));
  }
  else if ($dataArray["compoID"])
  {
    $compo = SQLLib::selectRow(sprintf_esc("select * from compos where id=%d",$dataArray["compoID"]));
  }
  if (!$compo)
  {
    $output["error"] = "Compo not found!";
    return false;
  }

  if ($dataArray["userID"])
  {
    // not a superuser upload: more checks
    if ($entry)
    {
      if ($compo->uploadopen == 0 && $compo->updateopen == 0) {
        $output["error"] = "Sorry, the compo deadline is over!";
        return false;
      }
      if ($entry->userid != $dataArray["userID"]) {
        $output["error"] = "This is NOT your entry!";
        return false;
      }
    }
    else
    {
      if ($compo->uploadopen == 0) {
        $output["error"] = "Sorry, the compo is not open for entries anymore!";
        return false;
      }
    }
  }

  // checks all done, start doing things

  $order = 0;
  if ($entry)
  {
    $order = $entry->playingorder;
  }
  else
  {
    $s = SQLLib::selectRow(sprintf_esc("select max(playingorder) as c from compoentries where compoid=%d",$compo->id));
    $order = $s->c + 1;
  }

  global $sqldata;
  $sqldata = array();

  $meta = array("title","author","comment","orgacomment");
  foreach($meta as $v)
    if (isset($dataArray[$v]))
      $sqldata[$v] = $dataArray[$v];

  // we already checked upload validity above - for admin interfaces, this check is disabled
  if ($dataArray["localFileName"] && file_exists($dataArray["localFileName"]))
  {
    global $filenameBase;
    $filenameBase = $dataArray["originalFileName"];
    run_hook("admin_common_handleupload_beforesanitize",array("filename"=>&$filenameBase,"data"=>$dataArray));
    if (!sanitize_filename( $filenameBase ))
    {
      $output["error"] = "The filename contains invalid characters";
      return false;
    }
    $filenamePath = $settings["private_ftp_dir"] . "/" . $compo->dirname . "/" . sprintf("%03d",$order) . "/" . $filenameBase;
    if (file_exists($filenamePath)) // do not overwrite
    {
      $filenameBase = date("Y_m_d_H_i_s") . "_" . $filenameBase;
      $filenamePath = $settings["private_ftp_dir"] . "/" . $compo->dirname . "/" . sprintf("%03d",$order) . "/" . $filenameBase;
    }

    @mkdir($settings["private_ftp_dir"] . "/" . $compo->dirname . "/" . sprintf("%03d",$order), 0777, True);

    $sqldata["filename"] = $filenameBase;
    $output["filename"] = $filenameBase;
    rename($dataArray["localFileName"], $filenamePath);
    chmod($filenamePath, 0777);
  }

  run_hook("admin_common_handleupload_beforedb",array("sqlData"=>&$sqldata,"output"=>&$output));
  if ($hookError)
  {
    $output["error"] = $hookError;
    return false;
  }

  if ($id)
  {
    SQLLib::UpdateRow("compoentries",$sqldata,"id=".(int)$id);
  }
  else
  {
    $sqldata["playingorder"] = $order;
    $sqldata["userid"] = (int)$dataArray["userID"];
    $sqldata["compoid"] = $compo->id;
    $sqldata["uploadip"] = $_SERVER["REMOTE_ADDR"];
    $sqldata["uploadtime"] = date("Y-m-d H:i:s");
    $id = SQLLib::InsertRow("compoentries",$sqldata);
  }
  run_hook("admin_common_handleupload_afterdb",array("entryID"=>$id));

  if (is_uploaded_file($dataArray["localScreenshotFile"])) {
    list($width,$height,$type) = getimagesize($dataArray["localScreenshotFile"]);
    if ($type==IMAGETYPE_GIF ||
        $type==IMAGETYPE_PNG ||
        $type==IMAGETYPE_JPEG)
    {
      @mkdir( get_screenshot_thumb_path() );

      $a = glob( get_screenshot_thumb_path() . $id . ".*");
      foreach ($a as $v) unlink($v);
      $thumb = get_screenshot_thumb_path() . $id . ".png";

      $a = glob( get_screenshot_path() . $id . ".*");
      foreach ($a as $v) unlink($v);
      $sshot = get_screenshot_path() . $id . image_type_to_extension($type,true);

      thumbnail($dataArray["localScreenshotFile"],$thumb,$settings["screenshot_sizex"],$settings["screenshot_sizey"]);
      move_uploaded_file($dataArray["localScreenshotFile"],$sshot);
      chmod($sshot, 0664);
    }
  }

  $output["entryID"] = $id;
  return true;
}

function export_compo( $compo )
{
  global $settings;
  
  if (!$settings["public_ftp_dir"])
  {
    printf("<div class='error'>Export dir is empty!</div>\n");
    return false;
  }
  
  $lock = new OpLock(); // is this needed? probably not but it can't hurt
  
  $query = new SQLSelect();
  $query->AddTable("compoentries");
  $query->AddWhere(sprintf_esc("compoid=%d",$compo->id));
  $query->AddOrder("playingorder");
  run_hook("admin_compo_entrylist_export_dbquery",array("query"=>&$query));
  $entries = SQLLib::selectRows( $query->GetQuery() );
  
  if (!$entries)
  {
    printf("<div class='warning'>No valid entries for %s compo!</div>\n",_html($compo->name));
    return false;
  }

  @mkdir( get_compo_dir_public( $compo ) );
  @chmod( get_compo_dir_public( $compo ), 0777 );

  foreach ($entries as $entry)
  {
    $oldPath = get_compoentry_file_path($entry);
    $newPath = get_compo_dir_public( $compo ) . basename($oldPath);

    if (!file_exists($newPath))
    {
      copy($oldPath,$newPath);
      printf("<div class='success'>%s exported</div>\n",basename($oldPath));
    }
    else
    {
      printf("<div class='warning'>%s already exists!</div>\n",basename($newPath));
    }
  }
  $lock = null;
  
  return true;
}

///////////////////////////////////////////////////////////
// "plugin api" stuff

$_COMPOCACHE = array();
function _cache_compos() {
  global $_COMPOCACHE;
  $_COMPOCACHE = array();
  $r = SQLLib::SelectRows("select * from compos");
  foreach ($r as $v) $_COMPOCACHE[$v->id] = $v;
}
_cache_compos();

function get_compo($id)
{
  global $_COMPOCACHE;
  if (!$_COMPOCACHE)
    _cache_compos();

  //$compo = SQLLib::selectRow(sprintf_esc("select * from compos where id = %d",$_GET["id"]));
  return $_COMPOCACHE[$id];
}

function get_compos()
{
  global $_COMPOCACHE;
  if (!$_COMPOCACHE)
    _cache_compos();

  return $_COMPOCACHE;
}

function is_user_logged_in() {
  return ($_SESSION["logindata"] && !!$_SESSION["logindata"]->id);
}

function get_user_id()
{
  return (int)$_SESSION["logindata"]->id;
}

function get_current_user_data()
{
  //return $_SESSION["logindata"];
  return SQLLib::selectRow(sprintf_esc("select * from users where id=%d",get_user_id()));
}

function build_url( $page, $query = array() )
{
  return "./index.php?page=".rawurlencode($page).($query ? ("&".http_build_query($query)) : "");
}

function is_admin_page()
{
  return strpos($_SERVER["SCRIPT_FILENAME"],ADMIN_DIR) !== false;
}

function get_page_title()
{
  global $pagetitle;
  return $pagetitle;
}

function get_compo_dir($compo)
{
  global $settings;
  if(is_object($compo))
  {
    return $settings["private_ftp_dir"] . "/" . $compo->dirname . "/";
  }
  else
  {
    $obj = get_compo($compo);
    return $settings["private_ftp_dir"] . "/" . $obj->dirname . "/";
  }
}

function get_compo_dir_public($compo)
{
  global $settings;
  if(is_object($compo))
  {
    return $settings["public_ftp_dir"] . "/" . $compo->dirname . "/";
  }
  else
  {
    $obj = get_compo($compo);
    return $settings["public_ftp_dir"] . "/" . $obj->dirname . "/";
  }
}

function get_compoentry_dir_path( $entry )
{
  global $settings;
  global $_COMPOCACHE;

  if (is_numeric($entry))
  {
    $entry = SQLLib::selectRow(sprintf_esc("select * from compoentries where id=%d",$entry));
  }
  if (!is_object($entry))
    return null;

  return get_compo_dir($entry->compoid) . sprintf("%03d",$entry->playingorder) . "/";
}

function get_compoentry_file_path( $entry )
{
  global $settings;
  global $_COMPOCACHE;

  if (is_numeric($entry))
  {
    $entry = SQLLib::selectRow(sprintf_esc("select * from compoentries where id=%d",$entry));
  }
  if (!is_object($entry))
    return null;

  return get_compo_dir($entry->compoid) . sprintf("%03d",$entry->playingorder) . "/" . basename($entry->filename);
}

function get_screenshot_path( )
{
  global $settings;
  return $settings["screenshot_dir"] . "/";
}

function get_screenshot_thumb_path( )
{
  global $settings;
  return $settings["screenshot_dir"] . "/thumb-".(int)$settings["screenshot_sizex"]."x".(int)$settings["screenshot_sizey"]."/";
}

function get_compoentry_screenshot_path( $entryID )
{
  global $settings;
  $a = glob( get_screenshot_path() . (int)$entryID . ".*" );
  return $a[0];
}

function get_compoentry_screenshot_thumb_path( $entryID )
{
  global $settings;
  $a = glob( get_screenshot_thumb_path() . (int)$entryID . ".*" );
  return $a[0];
}


?>