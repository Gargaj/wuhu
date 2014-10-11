<?
/*
Plugin name: Vote statistics
Description: Over-time voting statistics on pretty looking graphs!
*/
function votestats_addmenu( $data )
{
  $data["links"]["./plugins/votestats/index.php"] = "Vote stats";
}

add_hook("admin_menu","votestats_addmenu");
?>