<?php
if (!defined("ADMIN_DIR") || !defined("PLUGINOPTIONS"))
  exit();

if ($_POST)
{
  foreach($_POST as $k=>$v)
    if (strpos($k,"oneliner_")===0)
      update_setting($k,$v);
}

include_once("functions.inc.php");

if(@$_GET["refresh"])
{
  $result = oneliner_generate_slide();
  printf("<div class='success'>Slide regenerated: '%s'</div>\n",$result);
}

$rows = SQLLib::selectRows(
  "select oneliner.datetime, users.nickname, users.id as uid, oneliner.contents from oneliner ".
  "left join users on users.id = oneliner.userid order by datetime desc limit 10");

printf("<a href='./slides/_oneliner.png'>See current slide</a> |\n");
printf("<a href='%s&amp;refresh=1'>Re-generate slide</a>\n",$_SERVER["REQUEST_URI"]);
printf("<table class='minuswiki'>\n");
printf("<tr>\n");
printf("  <th>Time</th>\n");
printf("  <th>User</th>\n");
printf("  <th>Message</th>\n");
printf("</tr>\n");
foreach($rows as $r)
{
  printf("<tr>\n");
  printf("  <td>%s</td>\n",strstr($r->datetime," "));
  printf("  <td><a href='users.php?id=%d'>%s</a></td>\n",$r->uid,$r->nickname);
  printf("  <td>%s</td>\n",htmlspecialchars($r->contents));
  printf("</tr>\n");
}
printf("</table>\n");
?>
<h3>Crontab</h3>
<?php
$log = get_cron_log("oneliner_cron");
if ($log)
{
  printf("<p>Oneliner cron last ran at <b>%s</b> and said: <i>\"%s\"</i></p>",$log->lastRun,$log->lastOutput);
}
else
{
  printf("<p>Oneliner cron hasn't ran yet; check <a href='index.php'>the main page</a> if you've set up crontab correctly.</p>");
}
//printf("<a href='./slides/_twitter.png'>See current slide</a> |\n");
printf("<p><a href='%s&amp;refresh=1'>Re-generate slide manually</a></p>\n",$_SERVER["REQUEST_URI"]);
?>

<form action="<?=$_SERVER["REQUEST_URI"]?>" method="post">
  <h3>HTML rendering options</h3>

  <label for='oneliner_slidecount'>Number of oneliners to show on slide:</label>
  <input type='number' id='oneliner_slidecount' name='oneliner_slidecount' value='<?=get_setting("oneliner_slidecount")?>'/>

<!--
  <h3>PNG rendering options</h3>

  <label for='oneliner_nickcolor'>Nickname color:</label>
  <input type='text' id='oneliner_nickcolor' name='oneliner_nickcolor' value='<?=get_setting("oneliner_nickcolor")?>'/>

  <label for='oneliner_textcolor'>Message color:</label>
  <input type='text' id='oneliner_textcolor' name='oneliner_textcolor' value='<?=get_setting("oneliner_textcolor")?>'/>

  <label for='oneliner_fontsize'>Font size:</label>
  <input type='number' id='oneliner_fontsize' name='oneliner_fontsize' value='<?=(int)get_setting("oneliner_fontsize")?>'/>

  <label for='oneliner_by1'>Top border: (in pixels)</label>
  <input type='number' id='oneliner_by1' name='oneliner_by1' value='<?=(int)get_setting("oneliner_by1")?>'/>

  <label for='oneliner_by2'>Bottom border: (in pixels)</label>
  <input type='number' id='oneliner_by2' name='oneliner_by2' value='<?=(int)get_setting("oneliner_by2")?>'/>

  <label for='oneliner_wordwrap'>Word wrapping: (in characters)</label>
  <input type='number' id='oneliner_wordwrap' name='oneliner_wordwrap' value='<?=(int)get_setting("oneliner_wordwrap")?>'/>

  <label for='oneliner_xsep'>Nick width: (in pixels)</label>
  <input type='number' id='oneliner_xsep' name='oneliner_xsep' value='<?=(int)get_setting("oneliner_xsep")?>'/>

  <label for='oneliner_linespacing'>Line spacing:</label>
  <input type='number' id='oneliner_linespacing' name='oneliner_linespacing' value='<?=(float)get_setting("oneliner_linespacing")?>'/>
-->
  <input type="submit"/>
</form>
