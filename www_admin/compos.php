<?php
include_once("bootstrap.inc.php");

if ($_GET['change']) {
  SQLLib::Query(sprintf_esc("update compos set %s=1-%s where id=%d",$_GET['change'],$_GET['change'],$_GET['id']));
  header("Location: compos.php");
  exit();
}
if ($_GET['shiftallcompos']) {
  $sql = sprintf_esc("update compos set start=%s(start,INTERVAL %d MINUTE)",((int)$_GET['shiftallcompos'] < 0 ? "date_sub" : "date_add"),(int)abs($_GET['shiftallcompos']));
  SQLLib::Query($sql);
  header("Location: compos.php");
  exit();
}
if ($_GET['shiftcompo'] && $_GET["shiftid"]) {
  $sql = sprintf_esc("update compos set start=%s(start,'00:%02d:00') where id = %d",
    ((int)$_GET['shiftcompo'] < 0 ? "subtime" : "addtime"),(int)abs($_GET['shiftcompo']),$_GET["shiftid"]);
  //var_dump($sql);
  SQLLib::Query($sql);
  header("Location: compos.php");
  exit();
}
include_once("header.inc.php");

$checkboxen = array(
  "showauthor"=>"Show author on the slide",
  "votingopen"=>"Compo open for voting",
  "uploadopen"=>"Compo open for uploading entries",
  "updateopen"=>"Compo open for updating entries",
);

if ($_POST["delete"]) {
  SQLLib::Query(sprintf_esc("delete from compos where id=%d",$_POST["id"]));
  SQLLib::Query(sprintf_esc("delete from compoentries where compoid=%d",$_POST["id"]));

  // TODO: delete directory, etc.

} else {
  if ($_POST["name"] && $_POST["dirname"])
  {
    if ($_POST["id"])
    {
      $data = array(
        "name" => $_POST["name"],
        "start" => $_POST["compostart_date"]." ".$_POST["compostart_time"],
        "dirname" => $_POST["dirname"],
      );
      foreach($checkboxen as $k=>$v)
        $data[$k] = (int)($_POST[$k] == "on");
      run_hook("admin_compos_edit_update",array("data"=>&$data));
      SQLLib::UpdateRow("compos",$data,"id=".(int)$_POST["id"]);
    }
    else if ($_POST["addnewmulti"])
    {
      foreach($_POST["name"] as $k=>$v)
      {
        if (!$v) continue;
        $data = array(
          "name" => $_POST["name"][$k],
          "start" => $_POST["compostart_date"][$k]." ".$_POST["compostart_time"][$k],
          "dirname" => $_POST["dirname"][$k],
        );
        SQLLib::InsertRow("compos",$data);
      }
    }
    else
    {
      $data = array(
        "name" => $_POST["name"],
        "start" => $_POST["compostart_date"]." ".$_POST["compostart_time"],
        "dirname" => $_POST["dirname"],
      );
      run_hook("admin_compos_edit_insert",array("data"=>&$data));
      SQLLib::InsertRow("compos",$data);
    }
  }
}
if ($_GET['id'])
{
  $compo = SQLLib::selectRow(sprintf_esc("select * from compos where id=%d",(int)$_GET['id']));
?>
<form action="compos.php" method="post">
<table class="minuswiki">
<tr>
  <td>Compo name:</td>
  <td><input id="componame" name="name" type="text" value="<?=htmlspecialchars($compo->name)?>"/></td>
</tr>
<tr>
  <td>Compo start:</td>
  <td><?php
    list($startdate,$starttime) = explode(" ",$compo->start,2);
    printf("<select name='compostart_date'>");
    for ($x = 0; $x<10; $x++)
    {
      $time = strtotime($settings["party_firstday"]) + $x * 60 * 60 * 24;
      $date = date("Y-m-d",$time);
      printf("<option value='%s'%s>Day %d - %s</option>",$date,$startdate==$date?" selected='selected'":"",$x+1,date("M j, D",$time));
    }
    printf("</select>");
    printf("<input name='compostart_time' type='text' value='%s'/>",$starttime?$starttime:"12:00:00");
  ?></td>
</tr>
<tr>
  <td>Directory name:</td>
  <td><input id="dirname" name="dirname" type="text" value="<?=htmlspecialchars($compo->dirname)?>"/></td>
</tr>
<?php
foreach($checkboxen as $k=>$v)
{
?>
<tr>
  <td><?=$v?></td>
  <td><input id="<?=$k?>" name="<?=$k?>" type="checkbox"<?=$compo->$k?' checked="checked"':""?>/></td>
</tr>
<?php
}
run_hook("admin_compos_editform",array("compo"=>$compo));
?>
<tr>
  <td colspan="2">
    <input type="hidden" name="id" value="<?=(int)$_GET['id']?>" />
    <input type="submit" name="edit" value="Go!" />
    <input type="submit" id="delcompo" name="delete" value="Delete compo" />
  </td>
</tr>
</table>
</form>
<script type="text/javascript">
<!--
document.observe("dom:loaded",function(){
  /*
  $("componame").observe("keyup",function(){
    $("dirname").value = $("componame").value.toLowerCase().replace(/[^a-zA-Z0-9]+/g,"_");
  });
  */
  if ($("delcompo")) $("delcompo").observe("click",function(e){
    if (!confirm("Are you sure you want to delete this compo?"))
      e.stop();
  });
});
//-->
</script>
<?php
}
else if ($_GET["new"]=="add")
{
?>
<form action="compos.php" method="post" id='addnewcompo'>
<table class='minuswiki'>
<thead>
  <tr>
    <th>Compo name</th>
    <th>Compo start</th>
    <th>Directory name</th>
  </tr>
</thead>
<tbody>
<tr class='comporow'>
  <td><input id="componame[0]" name="name[0]" class="componame" type="text"/></td>
  <td><?php
    printf("<select class='compostart_day' name='compostart_date[0]'>");
    for ($x = 0; $x<10; $x++)
    {
      $time = strtotime($settings["party_firstday"]) + $x * 60 * 60 * 24;
      $date = date("Y-m-d",$time);
      printf("<option value='%s'>Day %d - %s</option>",$date,$x+1,date("M j, D",$time));
    }
    printf("</select>");
    printf("<input class='compostart_time' name='compostart_time[0]' type='text' value='12:00:00' style='width:75px'/>");
  ?></td>
  <td><input id="dirname[0]" name="dirname[0]" class="dirname" type="text"/></td>
</tr>
</tbody>
</table>
<input type="submit" name="addnewmulti" value="Go!" />
</form>
<script type="text/javascript">
<!--
var original = [];
function insertNewRow()
{
  var count = $("addnewcompo").down("table tbody").select("tr").length;
  var tr = new Element("tr",{class:'comporow'});
  $A(original).each(function(item){
    var td = new Element("td");
    var s = item;
    td.update( s.replace(/\[0\]/g,"[" + count + "]") );
    tr.insert(td);
  });
  tr.down(".compostart_day").selectedIndex = $("addnewcompo").down("table tr.comporow:last-of-type").down(".compostart_day").selectedIndex;
  tr.down(".compostart_time").value = $("addnewcompo").down("table tr.comporow:last-of-type").down(".compostart_time").value;
  $("addnewcompo").down("table tbody").insert(tr);
  instrument();
}
function instrument()
{
  var a = $$("#addnewcompo td .componame");
  a.each(function(item){
    item.stopObserving();
  });
  a.last().observe("keyup",function(){
    if (a.last().value.length > 0)
      insertNewRow();
  });
  $$("#addnewcompo tbody tr").each(function(tr){
    tr.down(".componame").observe("keyup",function(){
      tr.down(".dirname").value = tr.down(".componame").value.toLowerCase().replace(/[^a-zA-Z0-9]+/g,"_");
    });
  });

}
document.observe("dom:loaded",function(){
  $("addnewcompo").select("td").each(function(item){
    original.push( item.innerHTML );
  });
  instrument();
});
//-->
</script>
<?php
}
else
{
  $s = SQLLib::selectRows("select * from compos order by start");
  ?>
  <table class='minuswiki' id='compolist'>
  <tr>
<?php
  run_hook("admin_compolist_headerrow_start");
?>
    <th>Compo</th>
    <th>Edit</th>
    <th>Organize</th>
    <th>Dir</th>
    <th>Start <a href="compos.php?shiftallcompos=-15" title="Shift all compos 15 minutes earlier">-</a>/<a href="compos.php?shiftallcompos=15" title="Shift all compos 15 minutes later">+</a></th>
    <th>Author</th>
    <th>Voting</th>
    <th>Upload</th>
    <th>Editing</th>
<?php
  run_hook("admin_compolist_headerrow_end");
?>
  </tr>
  <?php
  foreach($s as $t) {
    $z = SQLLib::selectRow(sprintf_esc("select count(*) as c from compoentries where compoid=%d",$t->id));

    $class = "enoughentries";
    if ($z->c == 0) $class = "noentries";
    else if ($z->c < 3) $class = "fewentries";
    printf("<tr>\n");
    run_hook("admin_compolist_row_start",array("compo"=>$t));
    printf("  <td><b>%s</b></td>\n",$t->name);
    printf("  <td><a href='compos.php?id=%d'>edit</a></td>\n",$t->id);
    printf("  <td><a href='compos_entry_list.php?id=%d'>organize (<span class='%s'>%d</span>)</a> / <a href='compos_entry_edit.php?compo=%d'>add</a></td>\n",$t->id,$class,$z->c,$t->id);
    printf("  <td>%s</td>\n",$t->dirname);
    printf("  <td><a href='compos.php?shiftid=%d&amp;shiftcompo=-15' title='Shift 15 minutes earlier'>-</a> %s <a href='compos.php?shiftid=%d&amp;shiftcompo=15' title='Shift 15 minutes later'>+</a></td>\n",$t->id,$t->start,$t->id);
    printf("  <td><a href='compos.php?id=%d&amp;change=showauthor'>%s</td>\n",$t->id,$t->showauthor?"shown":"hidden");
    printf("  <td><a href='compos.php?id=%d&amp;change=votingopen'>%s</td>\n",$t->id,$t->votingopen?"open":"closed");
    printf("  <td><a href='compos.php?id=%d&amp;change=uploadopen'>%s</td>\n",$t->id,$t->uploadopen?"open":"closed");
    printf("  <td><a href='compos.php?id=%d&amp;change=updateopen'>%s</td>\n",$t->id,$t->updateopen?"open":"closed");
    run_hook("admin_compolist_row_end",array("compo"=>$t));
    printf("</tr>\n");
  }
  printf("<tr><td colspan='10'><a href='compos.php?new=add'>add new compos</a></td></tr>\n");
  echo "</table>\n";
}
include_once("footer.inc.php");

?>
