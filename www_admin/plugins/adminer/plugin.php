<?
/*
Plugin name: Adminer for Wuhu
Description: Adminer (formerly phpMinAdmin) is a full-featured database management tool written in PHP. Conversely to phpMyAdmin, it consist of a single file ready to deploy to the target server. Adminer is available for MySQL, PostgreSQL, SQLite, MS SQL and Oracle.
*/
function adminer_addmenu( $data )
{
  $data["links"]["./plugins/adminer/adminer.php"] = "Adminer";
}

add_hook("admin_menu","adminer_addmenu");
?>