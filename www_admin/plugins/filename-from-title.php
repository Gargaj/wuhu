<?php
/*
Plugin name: Rename files to "title by author.ext"
*/
if (!defined("ADMIN_DIR")) exit();

function filenamefromtitle_rename( $data )
{
  $extension = pathinfo($data["filename"],PATHINFO_EXTENSION);

  $data["filename"] = $data["data"]["title"] . " by " . $data["data"]["author"] . "." . $extension;
}

add_hook("admin_common_handleupload_beforesanitize","filenamefromtitle_rename");

?>
