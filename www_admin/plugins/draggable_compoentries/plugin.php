<?php
/*
Plugin name: Draggable compo entries
Description: Allows you to re-order your compo entries by drag-and-drop
*/
function draggableentries_script()
{
  switch(get_setting("draggable_style"))
  {
    case 1:
      printf("<script type='text/javascript' src='./plugins/draggable_compoentries/script_numeric.js'></script>\n");
      break;
    default:
      printf("<script type='text/javascript' src='./plugins/draggable_compoentries/script.js'></script>\n");
      break;
  }
}

add_hook("admin_compo_entrylist_end","draggableentries_script");

function draggableentries_addmenu( $data )
{
  $data["links"]["pluginoptions.php?plugin=draggable_compoentries"] = "DragEntries";
}

add_hook("admin_menu","draggableentries_addmenu");
?>
