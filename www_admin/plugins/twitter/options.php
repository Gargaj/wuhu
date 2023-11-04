<?php
if (!defined("ADMIN_DIR") || !defined("PLUGINOPTIONS"))
  exit();

if ($_POST)
{
  foreach($_POST as $k=>$v)
    if (strpos($k,"twitter_")===0)
      update_setting($k,$v);
}

include_once("functions.inc.php");

if(@$_GET["refresh"])
{
  $result = twitter_generate_slide();
  printf("<div class='success'>Slide regenerated: '%s'</div>\n",$result);
}

?>
<h3>Crontab</h3>
<?php
$log = get_cron_log("twitter_cron");
if ($log)
{
  printf("<p>Twitter cron last ran at <b>%s</b> and said: <i>\"%s\"</i></p>",$log->lastRun,$log->lastOutput);
}
else
{
  printf("<p>Twitter cron hasn't ran yet; check <a href='index.php'>the main page</a> if you've set up crontab correctly.</p>");
}
//printf("<a href='./slides/_twitter.png'>See current slide</a> |\n");
printf("<p><a href='%s&amp;refresh=1'>Re-generate slide manually</a></p>\n",$_SERVER["REQUEST_URI"]);

?>
<form action="<?=$_SERVER["REQUEST_URI"]?>" method="post">
  <h3>Options</h3>

  <label for='twitter_querystring'>Twitter search string:</label>
  <input type='text' id='twitter_querystring' name='twitter_querystring' value='<?=get_setting("twitter_querystring")?>'/>

  <label for='twitter_consumer_key'>Twitter API consumer key: (Get one <a href="https://apps.twitter.com/">here</a>)</label>
  <input type='text' id='twitter_consumer_key' name='twitter_consumer_key' value='<?=get_setting("twitter_consumer_key")?>'/>

  <label for='twitter_consumer_secret'>Twitter API consumer secret:</label>
  <input type='text' id='twitter_consumer_secret' name='twitter_consumer_secret' value='<?=get_setting("twitter_consumer_secret")?>'/>

  <h3>HTML rendering options</h3>

  <label for='twitter_slidecount'>Number of tweets to show on slide:</label>
  <input type='number' id='twitter_slidecount' name='twitter_slidecount' value='<?=get_setting("twitter_slidecount")?>'/>

<!--
  <h3>PNG rendering options</h3>

  <label for='twitter_nickcolor'>Nickname color:</label>
  <input type='text' id='twitter_nickcolor' name='twitter_nickcolor' value='<?=get_setting("twitter_nickcolor")?>'/>

  <label for='twitter_textcolor'>Message color:</label>
  <input type='text' id='twitter_textcolor' name='twitter_textcolor' value='<?=get_setting("twitter_textcolor")?>'/>

  <label for='twitter_fontsize'>Font size:</label>
  <input type='number' id='twitter_fontsize' name='twitter_fontsize' value='<?=(int)get_setting("twitter_fontsize")?>'/>

  <label for='twitter_bx1'>Left border: (in pixels)</label>
  <input type='number' id='twitter_bx1' name='twitter_bx1' value='<?=(int)get_setting("twitter_bx1")?>'/>

  <label for='twitter_by1'>Top border: (in pixels)</label>
  <input type='number' id='twitter_by1' name='twitter_by1' value='<?=(int)get_setting("twitter_by1")?>'/>

  <label for='twitter_by2'>Bottom border: (in pixels)</label>
  <input type='number' id='twitter_by2' name='twitter_by2' value='<?=(int)get_setting("twitter_by2")?>'/>

  <label for='twitter_wordwrap'>Word wrapping: (in characters)</label>
  <input type='number' id='twitter_wordwrap' name='twitter_wordwrap' value='<?=(int)get_setting("twitter_wordwrap")?>'/>

  <label for='twitter_xsep'>Nick width: (in pixels)</label>
  <input type='number' id='twitter_xsep' name='twitter_xsep' value='<?=(int)get_setting("twitter_xsep")?>'/>

  <label for='twitter_linespacing'>Line spacing:</label>
  <input type='number' id='twitter_linespacing' name='twitter_linespacing' value='<?=(float)get_setting("twitter_linespacing")?>'/>
-->

  <input type="submit"/>
</form>
