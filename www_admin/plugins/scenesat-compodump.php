<?
/*
Plugin name: Scenesat preliminary compo entry list
*/
if (!defined("ADMIN_DIR")) exit();

function compodump_content( $data )
{
  $content = &$data["content"];
  if (get_page_title() != "Compodump") return;
  $user = get_current_user_data();
  if (!$user || $user->username != "Ziphoid") return;
  
  $c = SQLLib::selectRows("select * from compos order by start,id");
  foreach($c as $compo) {
    $content .= "<h3>".htmlspecialchars($compo->name)."</h3>\n";

    $query = new SQLSelect();
    $query->AddTable("compoentries");
    $query->AddWhere(sprintf_esc("compoid=%d",$compo->id));
    $query->AddOrder("playingorder");
    run_hook("admin_compo_entrylist_export_dbquery",array("query"=>&$query));
    $entries = SQLLib::selectRows( $query->GetQuery() );

    $content .= sprintf("<ol>\n");
    foreach ($entries as $entry)
    {
      $content .= sprintf("<li>%s - %s</li>\n",htmlspecialchars($entry->title),htmlspecialchars($entry->author));
    }
    $content .= sprintf("</ol>\n");
  }
}
add_hook("index_content","compodump_content");

?>