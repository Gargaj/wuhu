<?php
/*
Plugin name: Single page voting
Description: Separates the voting page by compos instead of one big list with all of them
*/
if (!defined("ADMIN_DIR")) exit();

function singlepagevoting_dbquery( $data )
{
  $query = &$data["query"];

  $compos = SQLLib::selectRows( $query->GetQuery() );
  if ($compos)
  {
    printf("<ul id='votecompolist'>\n");
    foreach($compos as $compo)
    {
      printf("  <li><a href='%s'>%s</a></li>\n",build_url("Vote",array("compo"=>$compo->id)),_html($compo->name));
    }
    printf("</ul>\n");
  }
  else
  {
    printf("No compos found open for voting!");
  }

  if (@$_GET["compo"])
    $query->AddWhere(sprintf_esc("id = %d",$_GET["compo"]));
  else
    $query->AddWhere("1=3");
}

add_hook("vote_prepare_dbquery","singlepagevoting_dbquery");

?>
