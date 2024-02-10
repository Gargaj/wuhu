<?php
/*
Plugin name: Timetable
Description: Show your party schedule through the intranet
*/
if (!defined("ADMIN_DIR")) exit();

function timetable_ucomp($a,$b)
{
  if ($a->date < $b->date) return -1;
  if ($a->date > $b->date) return  1;
  $s = strcasecmp($a->type,$b->type);
  if ($s) return $s;
  return strcasecmp($a->event,$b->event);
}

function get_timetable_content()
{
  $rows = SQLLib::selectRows("select * from timetable order by `date`");

  $compos = SQLLib::selectRows("select * from compos order by start");
  foreach ($compos as $v)
  {
    $a = new stdClass();
    $a->type = "compo";
    $a->event = $v->name;
    $a->date = $v->start;
    $a->compoID = $v->id;
    $rows[] = $a;
  }
  usort($rows,"timetable_ucomp");
  
  return $rows;
}
  
function get_timetable_content_html( $forceBreak = -1, $skipElapsed = false )
{
  $d = 0;
  $lastdate = -1;
  $lasttime = -1;

  $firstDay = 0;
  $counter = 0;
  
  $rows = get_timetable_content();

  $content = "";
  foreach($rows as $v)
  {
    $elapsed = $v->date < date("Y-m-d H:i:s");
    if ($elapsed && $skipElapsed)
    {
      continue;
    }
    $day = date("l",strtotime($v->date));

    // we don't do the check for the day-switch at midnight
    // instead we check at 4am, because it's visually more practical
    // iow "saturday 4am" still counts as friday
    $effectiveDay = date("l",strtotime($v->date) - 60 * 60 * 4);

    if ($effectiveDay != $lastdate || ($forceBreak != -1 && $counter == $forceBreak))
    {
      if ($d++)
      {
        $content .= sprintf("</tbody>\n");
        $content .= sprintf("</table>\n\n");
      }

      $content .= sprintf("<h3>%s</h3>\n",$day);
      $content .= sprintf("<table class=\"timetable\">\n");
      $content .= sprintf("<thead>\n");
      $content .= sprintf("<tr>\n");
      $content .= sprintf("  <th class='timetabletime'>Time</th>\n");
      $content .= sprintf("  <th class='timetableevent'>Event</th>\n");
      $content .= sprintf("</tr>\n");
      $content .= sprintf("</thead>\n");
      $content .= sprintf("<tbody>\n");
      $counter = 0;
      $lastdate = $effectiveDay;
    }

    $content .= sprintf("<tr%s>\n",$elapsed ? " class='elapsed'" : "");

    if ($lasttime == $v->date)
      $content .= sprintf("  <td class='timetabletime'>&nbsp;</td>\n");
    else
      $content .= sprintf("  <td class='timetabletime'>%s</td>\n",substr($v->date,11,5));

    $lasttime = $v->date;

    $text = $v->event;
    if (@$v->link)
    {
      if (strstr($v->link,"://")!==false)
        $text = sprintf("<a href='%s'>%s</a>",$v->link,$v->event);
      else
        $text = sprintf("<a href='%s'>%s</a>",build_url($v->link),$v->event);
    }

    switch (@$v->type) 
    {
      case "mainevent": {
        $content .= sprintf("  <td class='timetableevent'><span class='timetable_eventtype_mainevent'>%s</span></td>\n",$text);
      } break;
      case "event": {
        $content .= sprintf("  <td class='timetableevent'><span class='timetable_eventtype_event'>%s</span></td>\n",$text);
      } break;
      case "deadline": {
        $content .= sprintf("  <td class='timetableevent'><span class='timetable_eventtype_deadline'>Deadline:</span> %s</td>\n",$text);
      } break;
      case "compo": {
        $content .= sprintf("  <td class='timetableevent'><span class='timetable_eventtype_compo'>Compo:</span> %s</td>\n",$text);
      } break;
      case "seminar": {
        $content .= sprintf("  <td class='timetableevent'><span class='timetable_eventtype_seminar'>Seminar:</span> %s</td>\n",$text);
      } break;
    }
    $content .= sprintf("</tr>\n");
    $counter++;
  }
  $content .= sprintf("</tbody>\n");
  $content .= sprintf("</table>\n");

  return $content;
}

function timetable_export()
{
  $s = get_timetable_content_html((int)get_setting("timetable_perpage") ?: 6,true);
  $a = preg_split("/<h3>/ms",$s);
  $n = 1;
  for ($x=0; $x<10; $x++)
    @unlink( sprintf(ADMIN_DIR . "/slides/timetable-%02d.htm",$x) );

  foreach($a as $v)
  {
    if (strstr($v,"</h3>")===false)
      continue;
    $v = "<h3>" . $v;
    $fn = sprintf(ADMIN_DIR . "/slides/timetable-%02d.htm",$n++);
    file_put_contents($fn,$v);
    printf("<div class='success'>%s exported</div>\n",basename($fn));
  }
}
function timetable_content( $data )
{
  $content = &$data["content"];

  if (get_page_title() != "Timetable") return;
  $content = sprintf("<h2>Timetable</h2>\n");
  $content .= get_timetable_content_html();
}

add_hook("index_content","timetable_content");

function timetable_addmenu( $data )
{
  $data["links"]["pluginoptions.php?plugin=timetable"] = "Timetable";
}

add_hook("admin_menu","timetable_addmenu");

function timetable_activation()
{
  $r = SQLLib::selectRow("show tables where tables_in_".SQL_DATABASE."='timetable'");
  if (!$r)
  {
    SQLLib::Query(" CREATE TABLE `timetable` (".
      "   `id` int(11) NOT NULL auto_increment,".
      "   `date` datetime NOT NULL,".
      "   `type` enum('mainevent','event','deadline','compo','seminar') collate utf8_unicode_ci NOT NULL,".
      "   `event` text collate utf8_unicode_ci NOT NULL,".
      "   `link` text collate utf8_unicode_ci NOT NULL,".
      "   PRIMARY KEY  (`id`)".
      " ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;".
      " ");
  }
  if (get_setting("timetable_perpage") == null)
  {
    update_setting("timetable_perpage", 6);
  }
}

add_activation_hook( __FILE__, "timetable_activation" );

function timetable_toc( $data )
{
  $data["pages"]["Timetable"] = "Timetable";
}
add_hook("admin_toc_pages","timetable_toc");
?>