<?
include_once(ADMIN_DIR . "/bootstrap.inc.php");

function twitter_allocate_color_from_setting( $img, $setting )
{
  $color = get_setting($setting);
  list($r,$g,$b) = sscanf($color,"#%02X%02X%02X");
  return imagecolorallocate( $img, $r, $g, $b );
}

function twitter_load_via_curl( $url, $contentArray = array(), $headerArray = array(), $method = "GET" )
{
  $ch = curl_init();
  
  $a = array();
  foreach($headerArray as $k=>$v) $a[] = $k.": ".$v;
  
  $data = http_build_query($contentArray);
  if ($method == "GET")
  {
    $url .= "?" . $data;
  }
  else if ($method == "POST")
  {
    $data = http_build_query($contentArray);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_POST, true);
  }

  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $a);  
    
  $data = curl_exec($ch);
  curl_close($ch);
  
  return $data;
}

function twitter_generate_png( $statuses )
{
  $plugindir = dirname(__FILE__);

  list($ttffile) = glob(ADMIN_DIR . "/shared/*.ttf");
  list($srcfile) = glob(ADMIN_DIR . "/shared/twitter_background.*");
  if (!$srcfile)
    list($srcfile) = glob(ADMIN_DIR . "/shared/background.*");
  $dstfile = ADMIN_DIR . "/slides/_twitter.png";
  
  $img = imagecreatefromstring( file_get_contents($srcfile) );

  $xSep = (int)get_setting("twitter_xsep");
  
  $x = (int)get_setting("twitter_bx1");
  $y = (int)get_setting("twitter_by1");

  $colorNick = twitter_allocate_color_from_setting( $img, "twitter_nickcolor" );
  $colorText = twitter_allocate_color_from_setting( $img, "twitter_textcolor" );
  $size = (int)get_setting("twitter_fontsize");
  
  $lineSpac = (float)get_setting("twitter_linespacing");

  $showAvatars = true;

  $avatars = array();
  foreach($statuses as $status)
  {
    if ($status->retweeted_status) continue;
    if ($y > (int)get_setting("twitter_by2")) break;

    if ($showAvatars && !$avatars[$status->user->profile_image_url_https])
    {
      $avString = twitter_load_via_curl( $status->user->profile_image_url_https );
      $avatars[$status->user->profile_image_url_https] = imagecreatefromstring( $avString );
    }    

    $box = imageftbbox( $size, 0, $ttffile, $status->user->screen_name, array( "linespacing" => $lineSpac ) );

    $width = $box[2] - $box[0];

    $text = $status->text;
    $text = preg_replace("/\s{2,}/"," ",$text);
    $text = mb_wordwrap($text,(int)get_setting("twitter_wordwrap"),"\n",1);

    $inc = ($size + 10) * $lineSpac * (substr_count( $text, "\n" ) + 1) + 10;

    if ($y + max($avHeight + 10,$inc) > (int)get_setting("twitter_by2")) break;
    
    $avHeight = 0;  
    if ($showAvatars)
    {  
      $av = $avatars[$status->user->profile_image_url_https];
      imagecopy($img,$av,$xSep - imagesx($av),$y - $size,0,0,imagesx($av),imagesy($av));
      $avHeight = imagesy($av);  
    }
    
    if (!$showAvatars)
      $box = imagefttext( $img, $size, 0, $xSep - $width - 10, $y, $colorNick, $ttffile, $status->user->screen_name, array( "linespacing" => $lineSpac ) );
    
    $box = imagefttext( $img, $size, 0, $xSep + 10, $y, $colorText, $ttffile, $text, array( "linespacing" => $lineSpac ) );

    $y += max($avHeight + 10,$inc); 
  }

  imagepng($img, $dstfile);
  imagedestroy($img);
}

function twitter_generate_txt( $statuses )
{
  $plugindir = dirname(__FILE__);

  $dstfile = ADMIN_DIR . "/slides/_twitter.txt";

  $tags = explode(",",get_setting("twitter_querystring"));
  $out = "<h3 class='emphasis'>Tweet to us! <span class='tags'>".implode(" ",$tags)."</span></h3>\n";
  $out .= "<ul id='twitter'>\n";
  $n = 0;
  foreach($statuses as $status)
  {
    if ($status->retweeted_status) continue;

    if ($n++ > (int)get_setting("twitter_slidecount"))
      break;

    $out .= "  <li>\n";
    $out .= "    <img class='twitter_avatar' src='".$status->user->profile_image_url_https."'/>\n";
    $out .= "    <span class='twitter_username'>"._html($status->user->screen_name)."</span>\n";
    $out .= "    <span class='twitter_tweet'>".$status->text."</span>\n";
    $out .= "  </li>\n";
  }
  $out .= "</ul>";
  file_put_contents($dstfile,$out);
}

function twitter_generate_slide()
{
  $auth = "Basic " . base64_encode( get_setting("twitter_consumer_key") . ":" . get_setting("twitter_consumer_secret") );
  
  $authTokens = json_decode( twitter_load_via_curl( "https://api.twitter.com/oauth2/token", array("grant_type"=>"client_credentials"), array("Authorization"=>$auth), "POST" ) );
  if (!$authTokens || !$authTokens->access_token)
  {
    //echo "auth failed"; 
    return;
  }
  $auth2 = "Bearer ".$authTokens->access_token;

  // doc @ https://dev.twitter.com/docs/api/1.1/get/search/tweets  

  $statuses = array();
  
  $keys = explode(",",get_setting("twitter_querystring"));
  $n = 0;
  foreach($keys as $key)
  {
    $raw = twitter_load_via_curl( "https://api.twitter.com/1.1/search/tweets.json", array("q"=>$key,"result_type"=>"recent", "count"=>100), array("Authorization"=>$auth2) );
//    file_put_contents( dirname(__FILE__) . "/tweet_".$n.".json" , $raw);
    $data = json_decode( $raw );
    if (!$data || !$data->statuses)
    {
      //echo "loading term '".$key."' failed"; 
      continue;
    }
    $statuses = array_merge($statuses,$data->statuses);
  }
  if (!$statuses) return;

  usort($statuses,function($a,$b) { return strtotime($b->created_at) - strtotime($a->created_at); });

//  twitter_generate_png( $statuses );
  twitter_generate_txt( $statuses );
}
?>