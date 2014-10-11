<?
abstract class Vote {
  abstract public function CreateResultsFromVotes( $compo, $entries ); /* returns associative array (entryid => score) */
  abstract public function GetVoteCount(); /* returns number of votes cast */
  abstract public function SaveVotes(); /* returns false on failure */
  abstract public function PrepareVotes( $compo );
  abstract public function RenderVoteGUI( $compo, $entry );
}

// RANGE VOTING (i.e. rate every entry 0..5)
class VoteRange extends Vote 
{
  function __construct()
  {
    $this->minVote = 0;
    $this->maxVote = 5;
  }
  function CreateResultsFromVotes( $compo, $entries )
  {
    $a = array();
    foreach($entries as $entry) {  
      $v = SQLLib::selectRow(sprintf_esc("select sum(vote) as c from votes_range where entryorderid = %d and compoid=%d",$entry->playingorder,$compo->id))->c;
      $a[$entry->id] = $v;
    }
    return $a;
  }
  function GetVoteCount()
  {
    $v = SQLLib::selectRows(sprintf_esc("select * from votes_range group by userid"));
    return count($v);
  }
  function SaveVotes()
  {
    if (!is_user_logged_in()) return false;
    foreach ($_POST["vote"] as $compoid=>$votes) 
    {
      $set = array();
      foreach ($votes as $entryOrderID=>$voteValue) 
        $set[] = (int)$entryOrderID;
      SQLLib::Query(sprintf_esc("delete from votes_range where compoid=%d and userid=%d and entryorderid in (%s)",$compoid,get_user_id(),implode(",",$set)));  
      
      foreach ($votes as $entryOrderID=>$voteValue) 
      {
        $a = array();
        $a["compoid"] = $compoid;
        $a["userid"] = get_user_id();
        $a["entryorderid"] = $entryOrderID;
        $a["vote"] = min($this->maxVote,max($this->minVote,$voteValue));
        $a["votedate"] = date("Y-m-d H:i:s");
        SQLLib::insertRow("votes_range",$a);
      }
    }
    return true;
  }
  function PrepareVotes( $compo )
  {
    $votes = SQLLib::selectRows(sprintf_esc("select * from votes_range where compoid=%d and userid=%d",$compo->id,get_user_id()));
    $this->votes_a = array();
    foreach($votes as $x) $this->votes_a[$x->entryorderid] = $x->vote;
  }
  function RenderVoteGUI( $compo, $entry )
  {
    if (!isset($this->votes_a[$entry->playingorder])) 
      $this->votes_a[$entry->playingorder] = 0; 
      
    printf("<select name='vote[%d][%d]'>\n",$compo->id,$entry->playingorder);
    for ($x = $this->maxVote; $x >= $this->minVote; $x--) {
      printf("  <option value='%d'%s>%s</option>\n",$x,($x==$this->votes_a[$entry->playingorder])?" selected='selected'":"",$x?sprintf("%d point%s",$x,$x==1?"":"s"):"No vote");
    }
    printf("</select>\n");
  }
}

// RANGE VOTING (i.e. pick top 3)
class VotePreferential extends Vote 
{
  function CreateResultsFromVotes( $compo, $entries )
  {
    $a = array();
    foreach($entries as $entry) {  
      $v = 0;
      $v += 3 * SQLLib::selectRow(sprintf_esc("select count(*) as c from votes_preferential where entry1 = %d and compoid=%d",$entry->playingorder,$compo->id))->c;
      $v += 2 * SQLLib::selectRow(sprintf_esc("select count(*) as c from votes_preferential where entry2 = %d and compoid=%d",$entry->playingorder,$compo->id))->c;
      $v += 1 * SQLLib::selectRow(sprintf_esc("select count(*) as c from votes_preferential where entry3 = %d and compoid=%d",$entry->playingorder,$compo->id))->c;
      $a[$entry->id] = $v;
    }
    return $a;
  }
  function GetVoteCount()
  {
    $v = SQLLib::selectRows(sprintf_esc("select * from votes_preferential group by userid"));
    return count($v);
  }
  function SaveVotes()
  {
    if (!is_user_logged_in()) return false;
    foreach ($_POST["vote"] as $compoid=>$vote) 
    {
      if ($vote[1]==$vote[2]) $vote[2] = 0;
      if ($vote[1]==$vote[3]) $vote[3] = 0;
      if ($vote[2]==$vote[3]) $vote[3] = 0;
      if (!$vote[1]) { $vote[2] = 0; $vote[3] = 0; }
      if (!$vote[2]) { $vote[3] = 0; }
      
      $a = array();
      $a["entry1"]=$vote[1];
      $a["entry2"]=$vote[2];
      $a["entry3"]=$vote[3];
      $a["votedate"] = date("Y-m-d H:i:s");
      $v = SQLLib::selectRow(sprintf_esc("select * from votes_preferential where compoid=%d and userid=%d",$compoid,get_user_id()));  
      if ($v) 
      {
        SQLLib::updateRow("votes_preferential",$a,sprintf_esc("compoid=%d and userid=%d",$compoid,get_user_id()));
      } 
      else 
      {
        $a["compoid"] = $compoid;
        $a["userid"] = get_user_id();
        SQLLib::insertRow("votes_preferential",$a);
      }
    }
    return true;
  }
  function PrepareVotes( $compo )
  {
    $this->vote = SQLLib::selectRow( sprintf_esc("select * from votes_preferential where compoid=%d and userid=%d",$compo->id,get_user_id()) );
  }
  function RenderVoteGUI( $compo, $entry )
  {
    for ($x=1; $x<=3; $x++) 
    {
      $f = "entry".$x;
      printf("<input type='radio' name='vote[%d][%d]' id='vote_%d_%d' value='%d' %s/>\n",$compo->id,$x,$compo->id,$x,$entry->playingorder,($this->vote->$f==$entry->playingorder)?"checked='checked'":"");
      printf("<label for='vote_%d_%d'>%d.</label>\n",$compo->id,$x,$x);
    }
  }
}

function SpawnVotingSystem()
{
  global $settings;
  global $voter;
  $voter = null;
  switch($settings["voting_type"])
  {
    case "range":
      {
        $voter = new VoteRange();
      } break;
    case "preferential":
      {
        $voter = new VotePreferential();
      } break;
  }
  run_hook("vote_spawnvotingsystem",array("voter"=>&$voter));
  
  return $voter;
}
?>