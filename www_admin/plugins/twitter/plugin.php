<?php
/*
Plugin name: Twitter feed
Description: Standard run-of-the-mill twitter feed plugin.
*/
include_once("functions.inc.php");

function twitter_addmenu( $data )
{
  $data["links"]["pluginoptions.php?plugin=twitter"] = "Twitter";
}

add_hook("admin_menu","twitter_addmenu");

function twitter_contentstart()
{
  if (!is_writable("./slides/")) {
    printf("<div class='error'>Twitter plugin: Unable to write into slides directory!</div>");
  }
  if (!function_exists("curl_init")) {
    printf("<div class='error'>Twitter plugin: cURL needed!</div>");
  }
}
add_hook("admin_content_start","twitter_contentstart");

function twitter_activation()
{
  if (get_setting("twitter_nickcolor") === null)
    update_setting("twitter_nickcolor","#000000");
  if (get_setting("twitter_textcolor") === null)
    update_setting("twitter_textcolor","#FFFFFF");
  if (get_setting("twitter_fontsize") === null)
    update_setting("twitter_fontsize",20);
  if (get_setting("twitter_bx1") === null)
    update_setting("twitter_bx1",0);
  if (get_setting("twitter_by1") === null)
    update_setting("twitter_by1",100);
  if (get_setting("twitter_by2") === null)
    update_setting("twitter_by2",700);
  if (get_setting("twitter_wordwrap") === null)
    update_setting("twitter_wordwrap",60);
  if (get_setting("twitter_xsep") === null)
    update_setting("twitter_xsep",256);
  if (get_setting("twitter_linespacing") === null)
    update_setting("twitter_linespacing",0.9);
  if (get_setting("twitter_slidecount") === null)
    update_setting("twitter_slidecount",10);


}
add_activation_hook( __FILE__, "twitter_activation" );
?>
