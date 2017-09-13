<?
include_once(ADMIN_DIR . "/bootstrap.inc.php");

function oneliner_allocate_color_from_setting( $img, $setting )
{
  $color = get_setting($setting);
  list($r,$g,$b) = sscanf($color,"#%02X%02X%02X");
  return imagecolorallocate( $img, $r, $g, $b );
}

function oneliner_generate_png( $rows )
{
  $plugindir = dirname(__FILE__);
  
  $openfunc = Array(
    1 =>"imagecreatefromgif",
    2 =>"imagecreatefromjpeg",
    3 =>"imagecreatefrompng",
  );

  list($ttffile) = glob(ADMIN_DIR . "/shared/*.ttf");
  list($srcfile) = glob(ADMIN_DIR . "/shared/oneliner_background.*");
  if (!$srcfile)
    list($srcfile) = glob(ADMIN_DIR . "/shared/background.*");
  $dstfile = ADMIN_DIR . "/slides/_oneliner.png";
  
  $img = imagecreatefromstring( file_get_contents($srcfile) );

  $xSep = (int)get_setting("oneliner_xsep");
  
  //$x = (int)get_setting("oneliner_bx1");
  $y = (int)get_setting("oneliner_by1");

  $colorNick = oneliner_allocate_color_from_setting( $img, "oneliner_nickcolor" );
  $colorText = oneliner_allocate_color_from_setting( $img, "oneliner_textcolor" );
  $size = (int)get_setting("oneliner_fontsize");
  
  $lineSpac = (float)get_setting("oneliner_linespacing");
  foreach($rows as $row)
  {
    if ($y > (int)get_setting("oneliner_by2")) break;
    
    $box = imageftbbox( $size, 0, $ttffile, $row->nickname, array( "linespacing" => $lineSpac ) );
    $width = $box[2] - $box[0];
    
    $box = imagefttext( $img, $size, 0, $xSep - $width - 10, $y, $colorNick, $ttffile, $row->nickname, array( "linespacing" => $lineSpac ) );

    $text = mb_wordwrap($row->contents,(int)get_setting("oneliner_wordwrap"),"\n",1);
    
    $box = imagefttext( $img, $size, 0, $xSep + 10, $y, $colorText, $ttffile, $text, array( "linespacing" => $lineSpac ) );

    $y += ($size + 10) * $lineSpac * (substr_count( $text, "\n" ) + 1) + 5;
  }

  imagepng($img, $dstfile);
  imagedestroy($img);
}

function oneliner_generate_txt( $statuses )
{
  $plugindir = dirname(__FILE__);

  $dstfile = ADMIN_DIR . "/slides/_oneliner.txt";

  $out .= "<ul id='oneliner'>\n";
  $n = 0;
  //foreach($statuses as $status)
  for ($n = 0; $n < (int)get_setting("oneliner_slidecount"); $n++)
  {
    $status = $statuses[$n];
    $out .= "  <li>\n";
    $out .= "    <span class='oneliner_username'>"._html($status->nickname)."</span>\n";
    $out .= "    <span class='oneliner_tweet'>"._html($status->contents)."</span>\n";
    $out .= "  </li>\n";
  }
  $out .= "</ul>";
  return file_put_contents($dstfile,$out);
}

function oneliner_generate_slide()
{
  $rows = SQLLib::selectRows(
    "select oneliner.datetime, users.nickname, oneliner.contents from oneliner ".
    "left join users on users.id = oneliner.userid order by datetime desc limit 20");  
  if (!oneliner_generate_txt( $rows ))
    return "error writing slide file";

  return "success!";
}
add_cron("oneliner_cron","oneliner_generate_slide",5 * 60);
?>