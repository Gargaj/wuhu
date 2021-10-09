<?php
/*
Plugin name: Audio snippets
Description: Allow people to upload audio snippets for their entries; requires "sox" and "libsox-fmt-mp3"!
*/
if (!defined("ADMIN_DIR")) exit();

include_once("functions.inc.php");

function audiosnippet_checkandconvert()
{
  $a = glob( audiosnippet_get_tmppath() . "/*" );
  if (count($a) < 1) return "Nothing to do.";

  if (!preg_match("/(\d+)\.[a-zA-Z0-9]+$/",$a[0],$m))
    return "Error fetching filename from ".$a[0];

  $id = (int)$m[0];

  $newPath = audiosnippet_get_path() . "/" . $id . ".ogg";
  audiosnippet_convert( $a[0], $newPath );

  return "New file is ".$newPath.", ".filesize($newPath);
}

add_cron("audiosnippet_cron","audiosnippet_checkandconvert",5 * 60);

function audiosnippet_contentstart()
{
  if (!exec("which sox"))
  {
    printf("<div class='error'>Audio snippet plugin: SoX needed!</div>");
  }
  $formats = audiosnippet_get_formats();
  if (!in_array("mp3",$formats))
  {
    printf("<div class='error'>Audio snippet plugin: libsox-fmt-mp3 needed if you want the users to be able to upload MP3!</div>");
  }
}
add_hook("admin_content_start","audiosnippet_contentstart");

function audiosnippet_uploadform()
{
  if (!get_setting("audiosnippets_allowuserupload"))
  {
    return;
  }
?>
<div class='formrow'>
  <label for='audiosnippet'>Audio snippet: <small>(optional - MP3, OGG or WAV!)</small></label>
  <input id='audiosnippet' name="audiosnippet" type="file" accept="audio/*" />
</div>
<?
}
add_hook("upload_before_submit","audiosnippet_uploadform");
add_hook("editentries_before_submit","audiosnippet_uploadform");

function audiosnippet_uploadform_admin()
{
?>
<tr>
  <td>Audio snippet: <small>(optional - MP3, OGG or WAV!)</small></td>
  <td>
    <input name="audiosnippet" type="file" class="inputfield" accept="audio/*" />
  </td>
</tr>
<?
}
add_hook("admin_editentry_editform","audiosnippet_uploadform_admin");

function audiosnippet_uploadform_data($data)
{
  if (!defined("ADMIN_PAGE") && !get_setting("audiosnippets_allowuserupload"))
  {
    return;
  }
  if (is_uploaded_file($_FILES["audiosnippet"]["tmp_name"]))
  {
    $data["data"]["audiosnippet"] = $_FILES["audiosnippet"]["tmp_name"];
    $ext = strstr($_FILES["audiosnippet"]["name"],".");
    $data["data"]["audiosnippet_ext"] = $ext ? substr($ext,1) : "";
  }
}

add_hook("upload_before_handle","audiosnippet_uploadform_data");
add_hook("editentries_before_handle","audiosnippet_uploadform_data");
add_hook("admin_editentry_before_handle","audiosnippet_uploadform_data");

function audiosnippet_uploadform_stash($data)
{
  if (!$data["dataArray"]["audiosnippet"]) return;

  if (!defined("ADMIN_PAGE") && !get_setting("audiosnippets_allowuserupload"))
  {
    return;
  }

  @mkdir( audiosnippet_get_tmppath() );
  @chmod( audiosnippet_get_tmppath(), 0777 );

  $newPath = audiosnippet_get_tmppath() . "/" . $data["entryID"] . "." . $data["dataArray"]["audiosnippet_ext"];
  move_uploaded_file( $data["dataArray"]["audiosnippet"], $newPath );
}

add_hook("admin_common_handleupload_afterscreenshot","audiosnippet_uploadform_stash");

function audiosnippet_vote_render($data)
{
  $path = audiosnippet_get_snippet_path( $data["entry"]->id );
  if (file_exists($path))
  {
    // we use the video tag here because it allows us to use the poster attribute which is awesome
    $data["html"] = sprintf("<video src='?action=audio&amp;id=%d' controls='true' poster='?action=screenshot&amp;id=%d&amp;show=thumb'></video>",$data["entry"]->id,$data["entry"]->id,$data["entry"]->id);
  }
}

add_hook("vote_render_screenshot","audiosnippet_vote_render");

function audiosnippet_show()
{
  if ($_GET["action"] == "audio")
  {
    $path = audiosnippet_get_snippet_path( $_GET["id"] );
    if (file_exists($path))
    {
      header("Content-type: audio/ogg");
      $s = file_get_contents($path);
      header("Content-length: ".strlen($s));
      echo $s;
    }
    exit();
  }
}

add_hook("index_start","audiosnippet_show");

function audiosnippets_addmenu( $data )
{
  $data["links"]["pluginoptions.php?plugin=audiosnippets"] = "Audiosnippets";
}

add_hook("admin_menu","audiosnippets_addmenu");

function twitter_activation()
{
  if (get_setting("audiosnippets_outputformat") === null)
    update_setting("audiosnippets_outputformat","ogg");
  if (get_setting("audiosnippets_allowuserupload") === null)
    update_setting("audiosnippets_allowuserupload",false);
}

add_activation_hook( __FILE__, "twitter_activation" );
?>