<?
/*
Plugin name: Lorem Ipsum
Description: The ability to fill your database with junk compos / entries / etc. for testing.
*/
if (!defined("ADMIN_DIR")) exit();

function lipsum_addmenu( $data )
{
  $data["links"]["pluginoptions.php?plugin=lipsum"] = "Lipsum";
}

add_hook("admin_menu","lipsum_addmenu");
?>