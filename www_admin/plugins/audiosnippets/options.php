<?php
if (!defined("ADMIN_DIR") || !defined("PLUGINOPTIONS"))
  exit();

include_once("functions.inc.php");

?>
<h3>Crontab</h3>
<?php
$log = get_cron_log("audiosnippet_cron");
if ($log)
{
  printf("<p>Audiosnippets cron last ran at <b>%s</b> and said: <i>\"%s\"</i></p>",$log->lastRun,$log->lastOutput);
}
else
{
  printf("<p>Audiosnippets cron hasn't ran yet; check <a href='index.php'>the main page</a> if you've set up crontab correctly.</p>");
}

?>
<form action="<?=$_SERVER["REQUEST_URI"]?>" method="post">
  <input type="submit"/>
</form>
