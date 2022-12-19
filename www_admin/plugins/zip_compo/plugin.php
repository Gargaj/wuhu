<?php
/*
Plugin name: Zip Compo
Description: Download the compo directory as a zip
*/
if (!defined("ADMIN_DIR")) exit();

function compo_entrylist_show_zip_compo_ui()
{
  $s  = "<p>";
  $s .= "  <h2>Download the compo folder as an archive</h2>";
  $s .= "  <div>";
  $s .= "    <a href='plugins/zip_compo/download.php?id=%s'>Download</a>";
  $s .= "  </div>";
  $s .= "  <small>(Note: the archive will be saved temporarily below <b>%s</b>)</small>";
  $s .= "</p>";

  printf($s, $_GET["id"], get_setting("private_ftp_dir"));
}

add_hook("admin_compo_entrylist_end", "compo_entrylist_show_zip_compo_ui");
?>
