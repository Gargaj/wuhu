<?php
include_once("header.inc.php");

if (@$_GET["saveDimensions"])
{
  update_setting("slideviewer_x",$_POST["width"]);
  update_setting("slideviewer_y",$_POST["height"]);
}
?>

<h2>Open slideviewer</h2>

<?php if (file_exists("beamer.data")){ ?>
<form action="/slideviewer/" method="get" id="frm">
  <label>Native slide size:</label>
  <input type='number' name='width' value='<?=(get_setting("slideviewer_x") ?: "1920")?>' style='width: 70px'/> x
  <input type='number' name='height' value='<?=(get_setting("slideviewer_y") ?: "1080")?>' style='width: 70px'/>
<!--
  <label>Fullscreen:</label>
  <input type='checkbox' name='fullscreen' checked='checked'/>
-->
  <label>Prizegiving display style:</label>
  <div><input type='radio' name='prizegivingStyle' value='bars' checked='checked'/> New style (multiple entries are displayed on a single page with a bar displaying the score)</div>
  <div><input type='radio' name='prizegivingStyle' value='pages'/> Old style (each entry gets it's own page)</div>


  <input type="submit" value='Open viewer' style='display:block;margin-top:20px;'/>
</form>

<noscript>
  <div style='font-size:72px;color:red'>This feature REQUIRES javascript!</div>
</noscript>

<h3>Keyboard shortcuts</h3>
<ul>
<li>LEFT ARROW - previous slide</li>
<li>RIGHT ARROW - next slide</li>
<li>HOME - first slide</li>
<li>END - last slide</li>
<li>UP ARROW - plus one minute in countdown mode</li>
<li>DOWN ARROW - minus one minute in countdown mode</li>
<li>S - partyslide rotation mode</li>
<li>P - pause / unpause partyslide rotation</li>
<li>T - reload stylesheet without changing the slide</li>
<li>SPACE - re-read beamer.data (and quit partyslide mode)</li>
</ul>
<?php }else{ ?>
  <p>You haven't set a slide mode yet; go <a href="beamer.php">here</a> to do so.</p>
<?php } ?>

<script type="text/javascript">
<!--
document.observe("dom:loaded",function(){
  if (!$("frm")) return;
  $("frm").observe("submit",function(ev){
    new Ajax.Request('?saveDimensions=1',{
      "method":"post",
      "parameters":Form.serialize( $('frm') )
    });
    ev.stop();
    var hash = Form.serialize( $('frm') );
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
<?php

include_once("footer.inc.php");

?>
