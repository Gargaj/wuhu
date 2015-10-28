<?
/*
Plugin name: Entry status
Description: Enables a variety of statuses for an entry (qualified, disqualified, etc.)
*/

global $ENTRYSTATUSFIELDS;
$ENTRYSTATUSFIELDS = array(
  'new'=>"New",
  'qualified'=>"Qualified! :D",
  'notqualified'=>"Not qualified :(",
  'disqualified'=>"DISQUALIFIED >:(",
);

global $ENTRYSTATUSACCEPTED;
$ENTRYSTATUSACCEPTED = array(
  'qualified'
);

function entrystatus_addheaderfield()
{
  printf("  <th>Entry status</th>\n");
}

add_hook("admin_compo_entrylist_headerrow_end","entrystatus_addheaderfield");

function entrystatus_change()
{
  if ($_GET["newstatus"])
  {
    SQLLib::Query(sprintf_esc("update compoentries set status = '%s' where id = %d",$_GET["newstatus"],$_GET["entry"]));
    header("Location: compos_entry_list.php?id=".$_GET["id"]);
    exit();
  }
}

add_hook("admin_compo_entrylist_preheader","entrystatus_change");

function entrystatus_addhead()
{
?>
<style type="text/css">
.entrystatus {
  display: inline-block;
  width: 16px;
  height: 16px;
  overflow: hidden;
  line-height: 200px;
  border: 1px solid black;
  opacity:0.1;
  border-radius: 5px;
}
.entrystatuscount {
  display: inline-block;
  width: 16px;
  height: 16px;
  opacity: 1.0;
  text-align: center;
  color: black;
}
.entrystatus_new { background: #0ff; }
.entrystatus_qualified { background: #0f0; }
.entrystatus_notqualified { background: #ff0; }
.entrystatus_disqualified { background: #f00; }
.entrystatus:hover { opacity:0.5; }
.entrystatus.selected { opacity:1.0; }
</style>
<?
}

add_hook("admin_head","entrystatus_addhead");

function entrystatus_filterquery( $data )
{
  global $ENTRYSTATUSACCEPTED;
  
  $a = array();
  foreach($ENTRYSTATUSACCEPTED as $v) $a[] = sprintf_esc("status = '%s'",$v);
  $data["query"]->AddWhere("(".implode(" or ",$a).")");
}

add_hook("admin_results_dbquery","entrystatus_filterquery");
add_hook("admin_beamer_generate_compodisplay_dbquery","entrystatus_filterquery");
add_hook("admin_beamer_generate_prizegiving_dbquery","entrystatus_filterquery");
add_hook("admin_compo_entrylist_export_dbquery","entrystatus_filterquery");
add_hook("vote_compo_dbquery","entrystatus_filterquery");

function entrystatus_addfield( $data )
{
  $entry = &$data["entry"];
  global $ENTRYSTATUSFIELDS;
  printf("  <td class='entrystatusrow'>\n");
  foreach($ENTRYSTATUSFIELDS as $k=>$v)
    printf("  <a class='entrystatus entrystatus_%s%s' href='compos_entry_list.php?id=%d&amp;entry=%d&amp;newstatus=%s' title='%s'>%s</a>\n",
      htmlspecialchars($k),$entry->status==$k?' selected':"",$_GET["id"],$entry->id,rawurlencode($k),htmlspecialchars($v),htmlspecialchars($v));
  printf("  </td>\n");
  //printf("  <td class='entrystatus'><input name='entrystatus[%d]' value='%d' style='width:40px'/></td>\n",$entry->id,$entry->entrystatus);
}

add_hook("admin_compo_entrylist_row_end","entrystatus_addfield");

function entrystatus_activation()
{
  $r = SQLLib::selectRow("show columns from compoentries where field = 'status'");
  if (!$r)
  {
    global $ENTRYSTATUSFIELDS;
    $fields = implode(",",array_map(create_function('$a',"return \"'\".\$a.\"'\";"),array_keys($ENTRYSTATUSFIELDS)));
    reset($ENTRYSTATUSFIELDS);
    SQLLib::Query("ALTER TABLE compoentries ADD `status` ENUM(".$fields.") NOT NULL DEFAULT '".key($ENTRYSTATUSFIELDS)."';");
  }
}

add_activation_hook( __FILE__, "entrystatus_activation" );

function entrystatus_userentries($data)
{
  global $ENTRYSTATUSFIELDS;
  printf("<div class='entrystatus entrystatus_%s'>%s</div>",htmlspecialchars($data["entry"]->status),htmlspecialchars($ENTRYSTATUSFIELDS[$data["entry"]->status]));
}

add_hook("editentries_endrow","entrystatus_userentries");

function entrystatus_userentries_header()
{
  printf("<th>Entry status</th>");
}

add_hook("editentries_endheader","entrystatus_userentries_header");

function entrystatus_compos_header()
{
  printf("<th>Entry statuses</th>");
}

add_hook("admin_compolist_headerrow_end","entrystatus_compos_header");

function entrystatus_compos_row( $data )
{
  global $ENTRYSTATUSFIELDS;

  $compo = &$data["compo"];
  $rows = SQLLib::SelectRows(sprintf_esc("select count(*) as c, status from compoentries where compoid = %d group by status",$compo->id));
  $a = array();
  foreach($rows as $r) $a[] = "<span class='entrystatuscount entrystatus_".$r->status."' title='".htmlspecialchars($ENTRYSTATUSFIELDS[$r->status])."'>".$r->c."</span>";
  printf("<td>%s</td>",implode("  ",$a));
}

add_hook("admin_compolist_row_end","entrystatus_compos_row");

function entrystatus_resetstatus( $data )
{
  $id = $data["entryID"];
 
  SQLLib::Query(sprintf_esc("update compoentries set status = 'new' where id = %d",$id));
}

add_hook("admin_common_handleupload_afterdb","entrystatus_resetstatus");
?>