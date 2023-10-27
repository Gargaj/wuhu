<?php
/*
Plugin name: SceneSat Compo Playlist Export
Description: Creates playlist for easy import into SceneSat’s admin interface
*/
if (!defined("ADMIN_DIR")) exit();

function scenesat_get_compo_entries_query($compo_id)
{
    $query = new SQLSelect();
    $query->AddTable("compoentries");
    $query->AddWhere(sprintf_esc("compoid=%d",$compo_id));
    $query->AddOrder("playingorder");
    run_hook("admin_compo_entrylist_export_dbquery",array("query"=>&$query));
    return $query->GetQuery();
}

function compodump_content( $data )
{
  $content = &$data["content"];
  if (get_page_title() != "Compodump") return;
  $user = get_current_user_data();
  if ( !$user || !$user->compodump ) return;

  $c = SQLLib::selectRows("select * from compos order by start,id");
  foreach($c as $compo) {
    $content .= "<h3>".htmlspecialchars($compo->name)." <span class='scenesatCompoPlaylistDownloadLink'>";
    $content .= "<a href='".$_SERVER['REQUEST_URI']."&amp;compoid=".$compo->id."'>m3u</a>";
    $content .= "</span></h3>\n";

    $query = scenesat_get_compo_entries_query($compo->id);
    $entries = SQLLib::selectRows($query);

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

function compodump_add_menu_entry(&$data)
{
    $data["menu"][] = "<a href='".build_url("Compodump")."'>SceneSat</a>";
}

add_hook("index_menu_parse", "compodump_add_menu_entry");

function compodump_export_compo_playlist(&$data)
{
    if ((get_page_title() != "Compodump") || !isset($_GET["compoid"])) return;
    $user = get_current_user_data();
    if ( !$user || !$user->compodump ) return;

    $compo_id = $_GET["compoid"];

    $query = new SQLSelect();
    $query->AddTable("compos");
    $query->AddWhere(sprintf_esc("id=%s", $compo_id));
    $query->AddField("name");
    $compo = SQLLib::selectRow($query->GetQuery());

    header("Content-Disposition: attachment; filename=".$compo->name.".m3u");
    header("Content-Type: application/mpegurl");
    print("#EXTM3U\r\n");
    print("#EXTENC: UTF-8\r\n");

    $query = scenesat_get_compo_entries_query($compo_id);
    $entries = SQLLib::selectRows($query);

    foreach ($entries as $entry)
    {
        printf("#EXTINF:0,%s - %s\r\n", $entry->author, $entry->title);
    }

    exit();
}

add_hook("index_template_elements", "compodump_export_compo_playlist");
?>
