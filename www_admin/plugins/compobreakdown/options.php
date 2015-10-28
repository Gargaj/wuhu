<?
if (!defined("ADMIN_DIR") || !defined("PLUGINOPTIONS"))
  exit();

$compo = get_compo( $_GET["compoID"] );
printf("  <h2>%s</h2>\n",_html($compo->name));

$query = new SQLSelect();
$query->AddTable("compoentries");
$query->AddWhere(sprintf_esc("compoid=%d",$compo->id));
$query->AddOrder("playingorder");
run_hook("admin_beamer_generate_compodisplay_dbquery",array("query"=>&$query));
$entries = SQLLib::selectRows( $query->GetQuery() );

$dir = get_compo_dir($compo);

printf("  <dl id='breakdown'>\n");
$playingorder = 1;
foreach ($entries as $t) 
{
  $path = substr(get_compoentry_file_path($t),strlen($dir));
  printf("      <dt>#%d. <a href='compos_entry_edit.php?id=%d'>%s</a> - %s <span class='filename'>(%s)</span></dt>\n",$playingorder++,$t->id,_html($t->title),_html($t->author),$path);
  printf("      <dd>\n");
  printf("        <div class='organotes'>%s</div>\n",nl2br(_html($t->organotes)));
  printf("      </dd>\n");
}
printf("  </dl>\n");

?>