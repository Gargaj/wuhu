<?php
/*
Plugin name: Validate uploads as RAR/ZIP
Description: Restricts the compo entry uploads to RAR or ZIP
*/
if (!defined("ADMIN_DIR")) exit();

function validatearchive_validate( $params )
{
  if (is_uploaded_file($params["dataArray"]["localFileName"]))
  {
    $f = fopen( $params["dataArray"]["localFileName"], "rb" );
    if ($f)
    {
      $header = fread($f,16);
      if (substr($header,0,2)!="PK"
       && substr($header,0,4)!="Rar!")
        $params["output"]["error"] = "You must upload either a ZIP or RAR!";
      fclose($f);
    }
  }
}

add_hook("admin_common_handleupload_beforecompocheck","validatearchive_validate");

?>
