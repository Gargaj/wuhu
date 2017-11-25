<?
/*
Plugin name: Image gallery
*/
if (!defined("ADMIN_DIR")) exit();

function ig_replace($m)
{
  return str_replace("Vote","ImageGallery",$m[0]);
}

function imagegallery_content( $data )
{
  $content = &$data["content"];
  
  if (get_page_title() != "ImageGallery") return;

  ob_start();
  include_once(WWW_DIR . "/include_vote.php");
  $content = ob_get_clean();
  
  $content = "<h2>Image Gallery</h2>\n\n".$content;
  $content = preg_replace_callback("/href=['\"].*Vote.*['\"]/i","ig_replace",$content);
  $content = preg_replace("/<div id='votesubmit'>.*<\/div>/i","",$content);
}
add_hook("index_content","imagegallery_content");

class DummyVoteSystem
{
  public function CreateResultsFromVotes( $compo, $entries ) {}
  public function GetVoteCount() {}
  public function SaveVotes() {}
  public function PrepareVotes( $compo ) {}
  public function RenderVoteGUI( $compo, $entry ) {}
}

function imagegallery_dummyvoter( $data )
{
  if (get_page_title() != "ImageGallery") return;

  $data["voter"] = new DummyVoteSystem();
}

add_hook("vote_spawnvotingsystem","imagegallery_dummyvoter");

function imagegallery_toc( $data )
{
  $data["pages"]["ImageGallery"] = "ImageGallery";
}
add_hook("admin_toc_pages","imagegallery_toc");
?>