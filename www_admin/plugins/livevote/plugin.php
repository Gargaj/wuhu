<?
/*
Plugin name: Live voting
*/
if (!defined("ADMIN_DIR")) exit();

function livevote_content( $data )
{
  $content = &$data["content"];
  
  if (get_page_title() != "LiveVote") return;
  if (!is_user_logged_in()) return;
  if (get_setting("voting_type") != "range") { $content = "Livevoting only works with ranged voting!"; return; }

  $voter = SpawnVotingSystem();
  $csrf = new CSRFProtect();

  if ($_POST["vote"])
  {
    header("Content-type: application/json; charset=utf-8");
    if (!$csrf->ValidateToken())
    {
      $a = array("error" => "csrferror");
    }
    else
    {
      if ($voter->SaveVotes())
      {
        $a = array("success" => "true");
        $a["csrf"] = $csrf->GenerateTokens();
      }
      else
      {
        $a = array("error" => "true");
      }
    }
    die( json_encode($a) );
  }  
  if ($_POST["listCompo"])
  {
    header("Content-type: application/json; charset=utf-8");

    $a = array();
    $compo = get_compo( (int)get_setting("livevote_compo") );
    if (!$compo)
    {
      $a = array("error" => "No compo open for live voting");
    }
    else
    {
      $a["compoName"] = $compo->name;
      $a["compoID"] = $compo->id;
      $a["csrf"] = $csrf->GenerateTokens();
      
      $s = new SQLSelect();
      $s->AddField("compoentries.id");
      $s->AddField("compoentries.title");
      $s->AddField("compoentries.author");
      $s->AddField("compoentries.playingorder");
      $s->AddTable("compoentries");
      $s->AddField("votes_range.vote");
      $s->AddJoin("left","votes_range",sprintf_esc("votes_range.compoid = %d and votes_range.userid = %d and votes_range.entryorderid = compoentries.playingorder",$compo->id,get_user_id()));
      $s->AddWhere(sprintf_esc("compoentries.compoid = %d",$compo->id));
      $s->AddWhere("livevote_enabled = 1");
      $s->AddOrder("playingorder desc");
      $a["entries"] = SQLLib::selectRows($s->GetQuery());
    }
    
    die( json_encode($a) );
  }
  $content = "<div id='livevoteContainer'></div>";
  ob_start();
?>
<script type="text/javascript" src="prototype.js"></script>
<script type="text/javascript">
<!--
var csrfName = "";
var csrfToken = "";
var lastCompo = 0;
function reloadVotes()
{
  new Ajax.Request(location.href,{
    "method":"POST",
    "parameters":{
      "listCompo": true,
      "rnd":Math.random(),
    },
    onSuccess:function(transport){
      if (!transport.responseJSON)
        return;
      
      if (transport.responseJSON.error)
      {
        $("compoEntries").update("");
        $("compoName").update( transport.responseJSON.error );
        lastCompo = -1;
        return;
      }
      csrfName  = transport.responseJSON.csrf.name;
      csrfToken = transport.responseJSON.csrf.token;
      if (lastCompo != transport.responseJSON.compoID)
      {
        lastCompo = transport.responseJSON.compoID;
        $("compoEntries").update("");
      }
      $("compoName").update( transport.responseJSON.compoName );
      var playingorder = transport.responseJSON.entries.length;
      $A(transport.responseJSON.entries).each(function(entry){
        var liEntry = $$("#compoEntries li[data-entryid="+entry.id+"]").first();
        if (!liEntry)
        {
          liEntry = new Element("li",{"data-entryid":entry.id,"data-playingorder":entry.playingorder});
          liEntry.insert( new Element("h3") );
          liEntry.insert( new Element("ul",{"class":"votes"}) );
          for(var i = <?=(int)$voter->minVote?>; i <= <?=(int)$voter->maxVote?>; i++)
          {
            var vote = new Element("li",{class:"vote","data-votevalue":i});
            vote.observe("click",function(ev){
              var p = {};
              p[ "vote["+transport.responseJSON.compoID+"]["+entry.playingorder+"]" ] = ev.element().getAttribute("data-votevalue");
              p[ "ProtName" ] = csrfName;
              p[ "ProtValue" ] = csrfToken;
              
              ev.element().addClassName("loading");
              new Ajax.Request(location.href,{
                "method":"POST",
                "parameters":p,
                onSuccess:function(transVote){
                  ev.element().removeClassName("loading");
                  if (!transVote.responseJSON)
                    return;
                  if (transVote.responseJSON.success)
                  {
                    csrfName  = transVote.responseJSON.csrf.name;
                    csrfToken = transVote.responseJSON.csrf.token;
                    ev.element().up("ul").select("li.vote").invoke("removeClassName","selected");
                    ev.element().addClassName("selected");
                  }
                },
              });            
            });
            liEntry.down(".votes").insert( vote.update(i) );
          }
          $("compoEntries").insert( liEntry );
        }
        liEntry.select("ul.votes li.vote").invoke("removeClassName","selected");
        if (entry.vote)
          liEntry.down("ul.votes li.vote[data-votevalue="+entry.vote+"]").addClassName("selected");
        var s = "";
        s += "#" + playingorder--;
        s += " - ";
        s += entry.title;
        if (entry.author) s += " - " + entry.author;
        liEntry.down("h3").update(s);
      });
      var e = $$("#compoEntries > li").sortBy(function(item){ return -parseInt(item.getAttribute("data-playingorder"),10); });
      $$("#compoEntries > li").invoke("remove");
      e.each(function(item){ $("compoEntries").insert(item); });
    }
  });
}
document.observe("dom:loaded",function(){
  var container = $("livevoteContainer");
  if (!container) return;
  
  container.insert( new Element("h2",{"id":"compoName"}) );
  container.insert( new Element("ul",{"id":"compoEntries"}) );
  
  reloadVotes();
  new PeriodicalExecuter(function(pe){ reloadVotes(); },15);
});
//-->
</script>
<noscript>Live voting requires JavaScript! (AMIGAAAAA!)</noscript>
<?  
  $content .= ob_get_clean();
}
add_hook("index_content","livevote_content");

function livevote_toc( $data )
{
  $data["pages"]["LiveVote"] = "LiveVote";
}
add_hook("admin_toc_pages","livevote_toc");

function livevote_addmenu( $data )
{
  $data["links"]["pluginoptions.php?plugin=livevote"] = "Live voting";
}
add_hook("admin_menu","livevote_addmenu");

function livevote_activation()
{
  $r = SQLLib::selectRow("show columns from compoentries where field = 'livevote_enabled'");
  if (!$r)
  {
    SQLLib::Query("ALTER TABLE compoentries ADD `livevote_enabled` tinyint NOT NULL DEFAULT '0';");
  }
}

add_activation_hook( __FILE__, "livevote_activation" );
?>
