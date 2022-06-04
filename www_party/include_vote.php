<?php
if (!defined("ADMIN_DIR")) exit();

$voter = SpawnVotingSystem();

if (!$voter)
  die("VOTING SYSTEM ERROR");

$csrf = new CSRFProtect();

if ($_POST["vote"])
{
  $a = array();
  if ($csrf->ValidateToken())
  {
    if ($voter->SaveVotes())
    {
      global $page;
      redirect( build_url($page,array("success"=>time()) ) );
    }
    else
    {
      echo "<div class='failure'>There was an error saving your votes!</div>";
    }
  }
  else
  {
    echo "<div class='failure'>Your CSRF token expired!</div>";
  }
}
if ($_GET["success"])
{
  echo "<div class='success'>Votes saved!</div>";
}

global $query;
$query = new SQLSelect();
$query->AddTable("compos");
$query->AddWhere("votingopen > 0");
$query->AddOrder("start");
run_hook("vote_prepare_dbquery",array("query"=>&$query));
$compos = SQLLib::selectRows( $query->GetQuery() );

if ($compos)
{
  echo "<form id='votingform' action='".$_SERVER['REQUEST_URI']."' method='post' enctype='multipart/form-data'>\n";
  $csrf->PrintToken();

  foreach($compos as $compo)
  {
    global $query;
    $query = new SQLSelect();
    $query->AddTable("compoentries");
    $query->AddWhere(sprintf_esc("compoid=%d",$compo->id));
    $query->AddOrder("playingorder");
    run_hook("vote_compo_dbquery",array("query"=>&$query));
    $entries = SQLLib::selectRows( $query->GetQuery() );

    if ($entries)
    {
      printf("<h3>%s</h3>\n",$compo->name);
      echo "<div class='entrylist votelist'>\n";

      $voter->PrepareVotes( $compo );

      foreach($entries as $entry)
      {
        echo "<div class='entry'>\n";
        printf("<div class='screenshot'><a href='screenshot.php?id=%d' target='_blank'><img src='screenshot.php?id=%d&amp;show=thumb' loading='lazy' alt='screenshot'/></a></div>\n",$entry->id,$entry->id);

        if($compo->showauthor)
          printf("<div class='title'><b>%s</b> - %s</div>\n",_html($entry->title),_html($entry->author));
        else
          printf("<div class='title'><b>%s</b></div>\n",_html($entry->title));

        printf("<div class='vote'>\n");
        $voter->RenderVoteGUI( $compo, $entry );
        printf("</div>\n");
        echo "</div>\n";
      }
      echo "</div>\n";
    }
  }
  echo "<div id='votesubmit'><input type='submit' value='Submit votes!'/></div>\n";
  echo "</form>\n";
}


?>
