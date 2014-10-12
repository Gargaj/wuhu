<? include("../../bootstrap.inc.php"); ?>
<!DOCTYPE html>
<html>
<head>
  <title>Wuhu vote-o-matic</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <script type="text/javascript" src="http://www.google.com/jsapi"></script>
  <script type="text/javascript">
    google.load('visualization', '1', { packages: ['corechart'] });
  </script>
</head>
<body>
<?
$compos = SQLLib::selectRows("select id,name from compos order by start");
foreach($compos as $compo)
{
//  printf("<h2>%s</h2>",$compo->name);
  $votes   = SQLLib::selectRows(sprintf_esc("select * from votes_range where compoid = %d order by votedate",$compo->id));
  //$entries = SQLLib::selectRows(sprintf_esc("select * from compoentries where compoid = %d and status='qualified' order by playingorder",$compo->id));

  $query = new SQLSelect();
  $query->AddTable("compoentries");
  $query->AddWhere(sprintf_esc("compoid=%d",$compo->id));
  $query->AddOrder("playingorder");
  run_hook("admin_results_dbquery",array("query"=>&$query));
  $entries = SQLLib::selectRows( $query->GetQuery() );
  
?>
<script type="text/javascript">
  function drawVisualization_1() {
  // Create and populate the data table.
  var data = new google.visualization.DataTable();
  data.addColumn('string', 'x');
  data.addColumn({type: 'string', role: 'annotation'});
<?
foreach($entries as $entry)
  printf("data.addColumn('number', '%s - %s');\n",addslashes($entry->title),addslashes($entry->author));

$score = array();
foreach($votes as $vote)
{
  $t = strtotime($vote->votedate);
  $t = (int)($t / 60) * 60;
  $score[ $t ][ $vote->entryorderid ] += $vote->vote;
}
$aggr = array();
foreach($entries as $v) $aggr[$v->playingorder] = 0;
foreach($score as $time => $chunk)
{
  foreach($chunk as $id=>$v) $aggr[$id] += $v;  
  $tstr = date("Y-m-d H:i:s",$time);
  $anno = "null";
  //if (strstr($tstr,"00:00:00")!==false) $anno = "'midnight'";
  //if (strstr($tstr,"00:35:00")!==false) $anno = "'approx. end of compos'";
  
  printf("data.addRow([\"%s\", %s, %s]);\n",$tstr,$anno,implode(",",$aggr));
}
$last = $start;
?>
  // Create and draw the visualization.
  new google.visualization.LineChart(document.getElementById('visualization_<?=$compo->id?>')).
    draw(data, {
      curveType: "function",
      width: screen.width - 200, height: 600,
      vAxis: { title: 'Points', viewWindowMode: 'maximized' },
      hAxis: { textStyle:{ fontSize:10 } },
      legend: { textStyle:{ fontSize:12 } },
      chartArea: { top: 45, left: 75 },
      title: '<?=$compo->name?>',
      annotation: {
        1: {
          style: 'line'
        }
      }
    });
}
google.setOnLoadCallback(drawVisualization_1);
</script>
<div id="visualization_<?=$compo->id?>"></div>
<?  
}
?>
</body>
</html>