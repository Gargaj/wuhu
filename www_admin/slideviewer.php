<?
include_once("header.inc.php");

if ($_GET["saveDimensions"])
{
  update_setting("slideviewer_x",$_POST["width"]);
  update_setting("slideviewer_y",$_POST["height"]);
}
?>
<form action="/slideviewer/" method="get" id="frm">
  <label>Native slide size:</label>
  <input type='number' name='width' value='<?=(get_setting("slideviewer_x") ?: "1920")?>' style='width: 70px'/> x
  <input type='number' name='height' value='<?=(get_setting("slideviewer_y") ?: "1080")?>' style='width: 70px'/>
<!--  
  <label>Fullscreen:</label>
  <input type='checkbox' name='fullscreen' checked='checked'/>
-->
  <label>Prizegiving display style:</label>
  <div><input type='radio' name='newPrizegiving' value='true'/> New style (multiple entries are displayed on a page with a bar displaying the score <b>BETA, use at your own risk</b>)</div>
  <div><input type='radio' name='newPrizegiving' value='false' checked='checked'/> Old style (each entry gets it's own page</div>
  
  
  <input type="submit" value='Open viewer' style='display:block;margin-top:20px;'/>
</form>

<noscript>
  <div style='font-size:72px;color:red'>This feature REQUIRES javascript!</div>
</noscript>

<script type="text/javascript">
<!--
document.observe("dom:loaded",function(){
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
<?

include_once("footer.inc.php");

?>