<?php
include_once("header.inc.php");

if (is_uploaded_file($_FILES["newSlideFile"]["tmp_name"]))
{
  $fn = $_FILES["newSlideFile"]["name"];
  sanitize_filename($fn);
  if ($fn != "index.php")
  {
    move_uploaded_file($_FILES["newSlideFile"]["tmp_name"],"slides/".$fn);
  }

  redirect();
}
else if ($_POST["newTextSlideContents"] && $_POST["newTextSlideFilename"])
{
  $fn = $_POST["newTextSlideFilename"];
  sanitize_filename($fn);
  file_put_contents("slides/".$fn,$_POST["newTextSlideContents"]);

  redirect();
}
else if ($_POST["editSlideContents"] && $_POST["editSlideFilename"])
{
  file_put_contents("slides/".$_POST["editSlideFilename"],$_POST["editSlideContents"]);

  redirect();
}
else if ($_GET["delete"])
{
  unlink("slides/".basename($_GET["delete"]));

  redirect();
}
else if ($_GET["edit"])
{
  $v = basename($_GET["edit"]);
  echo "<div id='slideedit'>";
  printf("<h3>%s</h3>\n",_html($v));
  switch(substr(strtolower($v),-4))
  {
    case ".png":
    case ".jpg":
    case "jpeg":
    case ".gif":
      printf("<img src='slides/%s'/>",$v);
      break;
    case ".mp4":
    case ".ogv":
    case ".avi":
      printf("<video controls='yes'><source src='slides/%s'/></video>",$v);
      break;
    case ".txt":
    case ".htm":
    case "html":
      echo "<form method='post' enctype='multipart/form-data'>\n";
      printf("<textarea name='editSlideContents'>%s</textarea>",_html(file_get_contents("slides/".$v)));
      printf("<input type='hidden' name='editSlideFilename' value='%s' />",_html($v));
      echo "<input type='submit' value='Save' />";
      echo "</form>\n";
      break;
  }
  echo "</div>";
}
else
{
  $a = glob("slides/*");

  echo "<h2>Current slides</h2>\n";
  echo "<ul id='slides'>\n";
  foreach($a as $v)
  {
    $v = basename($v);
    if ($v == ".") continue;
    if ($v == "..") continue;
    if ($v == "index.php") continue;

    echo "<li>\n";
    printf("<h3>%s</h3>\n",_html($v));
    printf("<div class='contents'>\n");
    switch(substr(strtolower($v),-4))
    {
      case ".png":
      case ".jpg":
      case "jpeg":
      case ".gif":
        printf("<img src='slides/%s'/>",$v);
        break;
      case ".mp4":
      case ".ogv":
      case ".avi":
        printf("<video><source src='slides/%s'/></video>",$v);
        break;
      case ".txt":
      case ".htm":
      case "html":
        printf("<pre>%s</pre>",_html(file_get_contents("slides/".$v)));
        break;
    }
    echo "</div>\n";
    printf("<a href='?edit=%s'>Edit</a> | ",rawurlencode($v));
    printf("<a href='?delete=%s' class='del'>Delete</a>",rawurlencode($v));
    echo "</li>";
  }
  echo "</ul>\n";

  echo "<h2>Add new slides</h2>\n";

  echo "<form method='post' enctype='multipart/form-data'>\n";
  printf("<h3>New text slide</h3>\n");
  echo "<label>Slide contents</label>\n";
  echo "<p><b>Warning:</b> All text will be treated as HTML!</p>";
  echo "<textarea name='newTextSlideContents' required='yes'></textarea>";
  echo "<label>Slide filename</label>\n";
  echo "<input name='newTextSlideFilename' required='yes' type='text'/>";
  echo "<input type='submit' value='Save file' />";
  echo "</form>\n";

  echo "<form method='post' enctype='multipart/form-data'>\n";
  printf("<h3>Upload new slide</h3>\n");
  echo "<input type='file' name='newSlideFile' required='yes' />";
  echo "<input type='submit' value='Start upload' />";

  echo "</form>\n";
  ?>
  <script type="text/javascript">
  <!--
  document.observe("dom:loaded",function(){
    $$(".del").invoke("observe","click",function(ev){
      if (!confirm("Are you sure?")) ev.stop();
    });
  });
  //-->
  </script>
  <?php
}
include_once("footer.inc.php");
?>
