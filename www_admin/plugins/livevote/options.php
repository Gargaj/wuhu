<?php
if (!defined("ADMIN_DIR") || !defined("PLUGINOPTIONS"))
  exit();

if (isset($_POST["livevoteCompo"]))
{
  update_setting("livevote_compo", (int)$_POST["livevoteCompo"]);
  SQLLib::Query("update compoentries set livevote_enabled = 0 where compoid=".(int)$_POST["livevoteCompo"]);
}
if (isset($_POST["livevoteEntries"]))
{
  //update_setting("livevote_compo", (int)$_POST["livevoteCompo"]);
  foreach($_POST["livevoteEntries"] as $k=>$v)
  {
    SQLLib::updateRow("compoentries",array("livevote_enabled"=>($v=="on")),"id=".(int)$k);
  }
}

$opencompos = SQLLib::selectRows("select * from compos where uploadopen = 0 and updateopen = 0 order by start");

$compos = array(0=>"- none -");
foreach($opencompos as $v) $compos[$v->id] = $v->name;

echo "<form method='post'>";
echo "<label for='livevoteCompo'>Select compo for livevoting: (only compos with closed uploads are visible:)</label>";
echo "<select name='livevoteCompo'>";
foreach($compos as $k=>$v)
  echo "<option value='".$k."'".(get_setting("livevote_compo") == $k ? " selected='selected'" : "").">"._html($v)."</option>";
echo "</select>";
echo "<input type='submit' value='Set'/>";

$compo = get_compo( (int) get_setting("livevote_compo") );
if ($compo)
{
  $query = new SQLSelect();
  $query->AddTable("compoentries");
  $query->AddWhere(sprintf_esc("compoid=%d",get_setting("livevote_compo")));
  $query->AddOrder("playingorder");
  run_hook("admin_beamer_generate_compodisplay_dbquery",array("query"=>&$query));
  $entries = SQLLib::selectRows( $query->GetQuery() );

  echo "<h2>"._html($compo->name)."</h2>";
//  echo "<label>Select the entries to enable voting for them:</label>";
  echo "<ol id='entries'>";
  foreach($entries as $v)
  {
    echo "<li>";
    printf("<input type='checkbox' name='livevoteEntries[%d]' id='livevoteEntries[%d]'%s>\n",$v->id,$v->id,$v->livevote_enabled ? " checked='checked'" : "");
    printf("<label for='livevoteEntries[%d]'>%s</label>",$v->id,_html($v->title));
    echo "</li>";
  }
  echo "</ol>";
  echo "<p id='loading'></p>";
  echo "<input type='submit' value='Set' id='saveEntries'/>";
}

echo "</form>";
?>
<script type="text/javascript">
<!--
document.observe("dom:loaded",function(){
  $("saveEntries").hide();
  $$("#entries li").each(function(item){
    item.down("input").setStyle({margin:"5px"});
    item.down("input").observe("change",function(ev){
      var p = {};
      p[ ev.element().name ] = ev.element().checked ? "on" : "";
      $("loading").update("Saving...");
      new Ajax.Request("",{
        method:"post",
        parameters:p,
        onSuccess:function(){ $("loading").update("") },
      });
    });
  });
});
//-->
</script>
<?php if($compo && !$compo->votingopen) { ?>
<p>Click <a href='./compos.php?id=<?=$compo->id?>&change=votingopen'>here</a> to enable normal voting for this compo.</p>
<?php } ?>
