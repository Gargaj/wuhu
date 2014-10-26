<?
include_once("bootstrap.inc.php");

$data = json_decode( file_get_contents( "slides/" . $_GET["playlist"] ) );

if ($_POST)
{
  $fn = $_GET["playlist"] ? $_GET["playlist"] : $_POST["playlist"];
  if (substr($fn,-5) != ".json")
    $fn .= ".json";

  $data->name = $_POST["name"];

  if (!$data->settings) $data->settings = new stdClass();

  $data->settings->autoRotate = $_POST["autoRotate"] == "on";
  
  if ($_POST["deleteSlide"]) foreach($_POST["deleteSlide"] as $k => $v)
  {
    unset($data->slides->$k);
  }

  if ($_POST["contents"]) foreach($_POST["contents"] as $k => $v)
  {
    $data->slides->$k->contents = $v;
  }
  if ($_POST["newSlideText"])
  {
    $o = new stdClass();
    $o->type = "text";
    $o->contents = $_POST["newSlideText"];
    $data->slides->{"textSlide-".substr(md5(time()),0,6)} = $o;
  }
  if ($_POST["newSlideHtml"])
  {
    $o = new stdClass();
    $o->type = "html";
    $o->contents = $_POST["newSlideHtml"];
    $data->slides->{"textSlide-".substr(md5(time()),0,6)} = $o;
  }
  if (is_uploaded_file($_FILES["newSlideImage"]["tmp_name"]))
  {
    $s = $_FILES["newSlideImage"]["name"];
    sanitize_filename($s);
    move_uploaded_file($_FILES["newSlideImage"]["tmp_name"], "slides/".$s);

    $o = new stdClass();
    $o->type = "image";
    $o->filename = "../slides/" . $s;
    $data->slides->{$s} = $o;
    var_dump($o);
  }

  file_put_contents( "slides/" . basename($fn), json_encode( $data, JSON_PRETTY_PRINT ) );
  
  header("Location: slideplaylist_editor.php?playlist=".rawurlencode($fn));
  exit();
  //printf("<div class='success'>Playlist saved!</div>\n");
}

include_once("header.inc.php");

?>
<form method="post" enctype="multipart/form-data">
<?
  if (!$_GET["playlist"])
  {
?>
  <label>Playlist filename</label>
  <input type='text' name='playlist' required='yes'/>
<?
  }
  else
    printf("<h2>%s</h2>",$_GET["playlist"]);
?>
<!--
  <label>Playlist name</label>
  <input type='text' name='name' value='<?=_html($data->name)?>'/>
-->
  <label>Settings</label>
  <input type='checkbox' name='autoRotate' <?=_html($data->settings->autoRotate?" checked='checked'":"")?>/> Automatically advance/rotate slides

  <h2>Slides</h2>
  <ul id='slideList'>
<?
if ($data->slides) foreach($data->slides as $k=>$v)
{
  printf("<li class='slide'>\n");
  printf("<span class='name'>%s</span>",_html($k));
  printf("<div class='contents'>\n");
  switch($v->type)
  {
    case "text":
    case "html":
      {
        printf("<textarea name='contents[%s]'>%s</textarea>",_html($k),_html($v->contents));
      } break;
    case "image":
      {
        printf("<img style='width:320px;' src='slides/%s'/>",_html(basename($v->filename)));
      } break;
    default:
      {
        printf("slide type: '%s'",_html($v->type));
      } break;
  }
  printf("</div>\n");
  printf("<input type='submit' name='deleteSlide[%s]' value='Delete slide'>",_html($k));
  printf("</li>\n");
}
?>
    <li class='tab' id='addNewImage'><span class='name'>Add new image:</span> <input type='file' accept='image/*' name='newSlideImage'/></li>
    <li class='tab' id='addNewText'><span class='name'>Add new text slide:</span> <textarea name='newSlideText'></textarea></li>
    <li class='tab' id='addNewHtml'><span class='name'>Add new HTML slide:</span> <textarea name='newSlideHtml'></textarea></li>
  </ul>
  <input type='submit' value='Save changes'/>
</form>

<script type="text/javascript">
<!--
document.observe("dom:loaded",function(){
  var liClicky = new Element("li");
  $("slideList").insertBefore( liClicky, $$("#slideList li.tab").first() );

  $$("#slideList li.tab").each(function(li){
    li.hide();
    var a = new Element("a",{"href":"#"}).update( li.down("span").innerHTML.replace(":","") );
    a.setStyle({"display":"inline-block","margin":"5px 20px"});
    a.observe("click",function(ev){
      $$("#slideList li.tab").invoke("hide");
      li.show();
      ev.stop();
    });
    liClicky.insert(a);
  });

  $$("#slideList li.slide").each(function(li){
    
  });
});
//-->
</script>
<?

include_once("footer.inc.php");

?>