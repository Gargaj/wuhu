<?php
if (!defined("ADMIN_DIR")) exit();

function audiosnippet_get_tmppath()
{
  global $settings;
  return $settings["screenshot_dir"] . "/audiotmp/";
}

function audiosnippet_get_path()
{
  global $settings;
  return $settings["screenshot_dir"] . "/audio/";
}

function audiosnippet_get_snippet_path($id)
{
  global $settings;
  $ext = get_setting("audiosnippets_outputformat") ?: "ogg";
  return audiosnippet_get_path().(int)$id.".".$ext;
}

function audiosnippet_get_formats()
{
  $data = shell_exec(exec("which sox"));
  preg_match("/AUDIO FILE FORMATS: (.*)$/m",$data,$m);
  return explode(" ",$m[1]);
}

function audiosnippet_convert( $from, $to )
{
  @mkdir( dirname($to) );
  @chmod( dirname($to), 0777 );
  
  $soxi = exec("which soxi");
  if (!$soxi) die("SoXi not found!");
  
  $cmd = array();
  $cmd[] = $soxi;
  $cmd[] = "-V0 -D";
  $cmd[] = addslashes($from);
  $data = shell_exec(implode(" ",$cmd));

  $duration = (float)$data;
  
  $snippetStart = 0;
  $snippetDuration = $duration;
  if ($duration > 30)
  {
    $snippetStart = (int)($duration * 0.33 + rand(-10,10));
    $snippetDuration = 30;
  }
  $fadeLength = 5;

  $sox = exec("which sox");
  if (!$sox) die("SoX not found!");
  
  $cmd = array();
  $cmd[] = $sox;
  $cmd[] = addslashes($from);
  $cmd[] = addslashes($to);

  $cmd[] = "trim";
  $cmd[] = $snippetStart;
  $cmd[] = $snippetDuration;
  
  $cmd[] = "fade";
  $cmd[] = "t";
  $cmd[] = $fadeLength;
  $cmd[] = $snippetDuration;
  $cmd[] = $fadeLength;

  $data = shell_exec(implode(" ",$cmd));
  
  if (file_exists($to))
  {
    unlink($from);
  }
}

?>