<?
include_once("header.inc.php");
include_once("cmsgen.inc.php");

if ($_POST["newPlaylistType"])
{
  $out = new stdClass();
  switch($_POST["newPlaylistType"])
  {
    case "compoDisplay":
      {
        $compo = get_compo( $_POST["newPlaylistCompo"] );

        $out->name = "compo-" . $compo->dirname;
        $out->creationTime = date("Y-m-d H:i:s");
        $out->settings = new stdClass();
        $out->settings->autoRotate = false;
        
        $o = new stdClass();
        $o->type = "compoDisplayIntro";
        $o->compoName = $compo->name;
        $out->slides[ $out->name . "-intro" ] = $o;
        
        $query = new SQLSelect();
        $query->AddTable("compoentries");
        $query->AddWhere(sprintf_esc("compoid=%d",$compo->id));
        $query->AddOrder("playingorder");
        run_hook("admin_beamer_generate_compodisplay_dbquery",array("query"=>&$query));
        $entries = SQLLib::selectRows( $query->GetQuery() );
        
        $playingorder = 1;
        foreach ($entries as $t) 
        {
          $o = new stdClass();
          $o->type = "compoDisplaySlide";
          $o->compoName = $compo->name;
          $o->number = $playingorder;
          $o->title = $t->title;
          if ($compo->showauthor)
            $o->author = $t->author;
          $o->comment = $t->comment;
          $out->slides[ $out->name . "-" . $playingorder ] = $o;
          $playingorder++;
        }
        $o = new stdClass();
        $o->type = "compoDisplayOutro";
        $o->compoName = $compo->name;
        $out->slides[ $out->name . "-outro" ] = $o;
      } break;
    case "prizegiving": 
      {
        $voter = SpawnVotingSystem();
        
        if (!$voter)
          die("VOTING SYSTEM ERROR");
      
        $compo = get_compo( $_POST["newPlaylistCompo"] );
      
        $out->name = "results-" . $compo->dirname;
        $out->creationTime = date("Y-m-d H:i:s");
        $out->settings = new stdClass();
        $out->settings->autoRotate = false;
        
        $o = new stdClass();
        $o->type = "prizegivingIntro";
        $o->compoName = $compo->name;
        $out->slides[ $out->name . "-intro" ] = $o;
      
        $query = new SQLSelect();
        $query->AddTable("compoentries");
        $query->AddWhere(sprintf_esc("compoid=%d",$compo->id));
        run_hook("admin_beamer_generate_prizegiving_dbquery",array("query"=>&$query));
        $entries = SQLLib::selectRows( $query->GetQuery() );
      
        global $results;
        $results = array();
        $results = $voter->CreateResultsFromVotes( $compo, $entries );
        run_hook("voting_resultscreated_presort",array("results"=>&$results));
        asort($results);
        $ranks = 0;
        
        run_hook("admin_beamer_prizegiving_rendervotes",array("results"=>&$results,"compo"=>$compo));
  
        $lastpoints = -1;
        foreach($results as $v){
          if ($lastpoints != $v) $ranks++;
          $lastpoints = $v;
        }
        
        $ranks++;
        $lastpoints = -1;
  
        $playingorder = 1;
        foreach ($results as $k=>$t) {
          if ($lastpoints != $t) $ranks--;
          $s = SQLLib::selectRow(sprintf_esc("select * from compoentries where id=%d",$k));
          
          $o = new stdClass();
          $o->type = "prizegivingSlide";
          $o->compoName = $compo->name;
          $o->ranking = $ranks;
          $o->title = $s->title;
          $o->author = $s->author;
          $o->points = $t;
          $out->slides[ $out->name . "-" . $playingorder ] = $o;
          $playingorder++;
                    
          $lastpoints = $t;
        }
        printf("  </results>\n");
      } break;      
  }
  $fn = "slides/" . $out->name . ".json";
  file_put_contents( $fn, json_encode( $out, JSON_PRETTY_PRINT ) );
  printf("<div class='success'>%s created, %d slides</div>\n",$fn,count($out->slides));
}
if(isset($_POST["selectPlaylist"]))
{
  update_setting("beamer_current_playlist",basename($_POST["selectPlaylist"]));
}
?>
<form action="/slideviewer/" method="get" id="frmOpenWindow">
  <label>Native slide size:</label>
  <input type='number' name='width' value='1920' style='width: 70px'/> x
  <input type='number' name='height' value='1080' style='width: 70px'/>
<!--  
  <label>Fullscreen:</label>
  <input type='checkbox' name='fullscreen' checked='checked'/>
-->

  <input type="submit" value='Open viewer'/>
</form>
<noscript>
  <div style='font-size:72px;color:red'>This feature REQUIRES javascript!</div>
</noscript>
<script type="text/javascript">
<!--
document.observe("dom:loaded",function(){
  $("frmOpenWindow").observe("submit",function(ev){
    ev.stop();
    var hash = Form.serialize( $('frmOpenWindow') );
    var wnd = window.open("./slideviewer/?" + hash, '', 'fullscreen=yes');

    if (false) // maybe one day this will not suck
    {    
      var reqFS = [
        "requestFullscreen",
        "webkitRequestFullscreen",
        "webkitRequestFullScreen",
        "mozRequestFullScreen",
        "msRequestFullscreen"
      ];
      var el = wnd.document.body;
      for (var i in reqFS)
      {
        if (el[reqFS[i]])
        {
          el[reqFS[i]]();
          break;
        }
      }
    }
  });
});
//-->
</script>

<form method="POST">
  <h2>Create new playlist from compo</h2>
  Create new 
  <select name='newPlaylistType'>
    <option value='compoDisplay'>compo display</option>
    <option value='prizegiving'>prizegiving</option>
  </select>
  playlist for the 
  <select name='newPlaylistCompo'>
<?
$s = SQLLib::selectRows("select * from compos order by start");
foreach($s as $t)
  printf("  <option value='%d'>%s</option>\n",$t->id,$t->name);
?>  
  </select>
  compo:
  <input type="submit" value='Create!'/>
</form>


<form method="POST">
  <h2>Set playlist as default</h2>
  Set
  <select name='selectPlaylist'>
    <option value="">-- default (contents of the "slides" folder)</option>
<?
$a = glob("slides/*.json");
sort($a);
foreach($a as $v)
  printf("  <option%s>%s</option>\n",basename($v)==get_setting("beamer_current_playlist")?" selected='selected'":"",basename($v));
?>  
  </select> as default playlist:
  <input type="submit" value='Select!'/>
</form>


<form action='slideplaylist_editor.php' method="GET">
  <h2>Edit playlist</h2>
  Edit playlist
  <select name='playlist'>
<?
$a = glob("slides/*.json");
sort($a);
foreach($a as $v)
  printf("  <option>%s</option>\n",basename($v));
?>  
  </select> as default playlist:
  <input type="submit" value='Edit!'/>
</form>

<?
include_once("footer.inc.php");
?>