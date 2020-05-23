<?php
/*
Plugin name: Scenesat preliminary compo entry list
*/
if (!defined("ADMIN_DIR")) exit();

function compodump_content( $data )
{
  $content = &$data["content"];
  if (get_page_title() != "Compodump") return;
  $user = get_current_user_data();
  if ( !$user || !$user->compodump ) return;

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

function compodump_addfield( &$data )
{
  if (!$data["user"])
  {
    return;
  }
?>
<hr/>
<form method="post">
  <input type="hidden" name="compodump_toggle_id" value="<?=(int)$data["user"]->id?>"/>
  User <b><?=$data["user"]->compodump?"can":"cannot"?></b> see compo dump
  <input type="submit" name="compodump_toggle" value="Toggle"/>
</form>
<?php
}
add_hook("admin_edituser_beforeactions","compodump_addfield");

function compodump_process()
{
  if ($_POST["compodump_toggle"])
  {
    SQLLib::Query(sprintf_esc("update users set compodump = 1 - compodump where id = %d",$_POST["compodump_toggle_id"]));
    redirect();
  }
}

add_hook("admin_edituser_start","compodump_process");

function compodump_activation()
{
  $r = SQLLib::selectRow("show columns from users where field = 'compodump'");
  if (!$r)
  {
    SQLLib::Query("ALTER TABLE users ADD `compodump` tinyint(4) NOT NULL DEFAULT '0';");
  }
}

add_activation_hook( __FILE__, "compodump_activation" );
?>
