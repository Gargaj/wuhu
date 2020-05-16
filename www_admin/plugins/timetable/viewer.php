<?
include_once("../../bootstrap.inc.php");
include_once("plugin.php");

if ($_POST && $_SERVER['HTTP_X_REQUESTED_WITH'])
{
  header("Content-type: application/json; charset=utf-8");
  die(json_encode(array("timetable"=>get_timetable_content())));
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>timetable viewer</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0;" />
<style type="text/css">
body {
  font-family: "Segoe UI", sans-serif;
}
#clock {
  font-size: 96px;
  text-align: center;
  font-weight: bold;
}
#timetable {
  font-size: 48px;
  width: 60vw;
  margin: 0px auto;
}
#timetable div {
  padding: 10px 20px;
}
#timetable div span.timetable-date {
  width: 30%;
  display: inline-block;
}
#timetable div span.timetable-date small {
  font-size: 20px;
}
#timetable div span.timetable-eventname {
  width: 70%;
  display: inline-block;
  text-align: right;
}
.past {
  color: #ddd;
}
.nextUp {
  background: #080;
  color: white;
}
@media (prefers-color-scheme: light) {
  body {
    background-color: #eee;
    color: #333;
  }
}
@media (prefers-color-scheme: dark) {
  body {
    background-color: #333;
    color: #eee;
  }
  .past {
    color: #555;
  }
}
</style>  
  <script type="text/javascript" src="../../prototype.js"></script>
<script type="text/javascript">
<!--
function padNumberWithTwo(n)
{
  return ("000" + n).slice(-2);
}

// this is where the fun starts!
// http://gargaj.github.io/date-parsing-chrome-ff/
function parseDate(t)
{
  var offset = new Date().getTimezoneOffset() * -1;
  if (offset > 0)
    t += "+" + padNumberWithTwo(offset / 60) + "" + padNumberWithTwo(offset % 60);
  else if (offset < 0)
    t += "-" + padNumberWithTwo(-offset / 60) + "" + padNumberWithTwo(-offset % 60);
  else if (offset == 0)
    t += "+0000";
  return Date.parse( t );
}

function reloadTimetable()
{
  new Ajax.Request("",{
    "method":"post",
    "parameters":{rnd:Math.random()},
    "onException":function(req,ex) { throw ex; },
    "onSuccess":function(transport){
      var data = transport.responseJSON;
      
      $("timetable").update("");

      var nextUp = -1;
      $A(data.timetable).each(function(item,i)
      {
        var date = parseDate(item.date);
        if (date > Date.now())
        {
          nextUp = i;
          throw $break;
        }
      });
         
      var hash = location.search.toQueryParams();
      var beforeCount = parseInt(hash.before || 2, 10);
      var afterCount = parseInt(hash.before || 4, 10);
      $A(data.timetable).each(function(item,i)
      {
        var div = new Element("div");
        if (i < nextUp - beforeCount)
        {
          return;
        }
        else if (i < nextUp)
        {
          div.addClassName("past");
        }
        else if (i == nextUp)
        {
          div.addClassName("nextUp");
        }
        else if (i > nextUp + afterCount)
        {
          return;
        }
        var dateMS = parseDate(item.date);
        var date = new Date(dateMS);
        var hhmm = padNumberWithTwo( date.getHours() ) + ":" + padNumberWithTwo( date.getMinutes() );
        var diffInMin = Math.floor((dateMS - Date.now()) / (1000 * 60));
        if (diffInMin > 0 && diffInMin < 120)
        {
          hhmm += " <small>("+diffInMin+" min.)</small>";
        }
        div.insert(new Element("span",{"class":"timetable-date"}).update(hhmm));
        div.insert(new Element("span",{"class":"timetable-eventname"}).update(item.event));
        $("timetable").insert(div);
      });
    },
  });
}

document.observe("dom:loaded",function(){
  reloadTimetable();
  new PeriodicalExecuter(function(pe) {
    var date = new Date(Date.now());
    $("clock").update(padNumberWithTwo( date.getHours() ) + ":" + padNumberWithTwo( date.getMinutes() ) + ":" + padNumberWithTwo( date.getSeconds() ));
  }, 0.5);
  new PeriodicalExecuter(function(pe) {
    reloadTimetable();
  }, 15);
});
//-->
</script>  
</head>
<body>
  <div id="clock"></div>
  <div id="timetable">
  </div>
</body>
</html>