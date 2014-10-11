document.observe("dom:loaded",function(){

  $$('#compoentrylist tr.entry').each(function(item){
    var n = parseInt( item.select('.entrynumber').first().innerHTML.replace("#",""), 10 );
    item.store("entryNumber",n);
  });

  $$('.movedown').each(function(item){
    Element.remove(item);
  });

  var floater = null;
  $$('.moveup').each(function(item){
    item.setAttribute("colspan","2");
    item.update("&#8661;");
    item.style.textAlign = "center";
    item.style.cursor = "pointer";
    
    var tbody = $(item.parentNode.parentNode);
    item.observe('mousedown',function(e){
      e.stop();
      item.parentNode.style.background = '#444';
      floater = item.parentNode;
    });
  });
  
  $("compoentrylist").insert({"after":"<button id='sendDraggedSort'>Save sorting ordering</button>"});
  $("sendDraggedSort").hide();
  $("sendDraggedSort").observe('click',function(){
    var newOrder = $H();
    var i = 1;
    $$('#compoentrylist tr.entry').each(function(item){
      newOrder.set("order["+(i++)+"]",item.retrieve("entryNumber"));
    });
    var params = window.location.search.toQueryParams();
    newOrder.set("compo",params.id);
    new Ajax.Request("./plugins/draggable_compoentries/ajax_reorder.php",{
      'method': 'post',
      'parameters': newOrder.toQueryString(),
      'onLoaded': function(transport){
        $("sendDraggedSort").hide();
      },
      'onSuccess': function(transport){
        Event.stopObserving(window,'beforeunload');
        if (transport.responseText.indexOf("SUCCESS")!=-1)
          location.reload();
        else
          alert("There was an error:\n\n" + transport.responseText);
      },
    });
  });
  
  document.observe('mouseover',function(e){
    if (floater)
    {
      var above = e.findElement('#compoentrylist tr'); // no .entry here, we want all rows!
      if (above && above != floater)
      {
        var tbody = above.parentNode;
        var idx = tbody.childElements().indexOf(above);
        if (idx > 0)
          tbody.insertBefore(floater,above);
        $("sendDraggedSort").show();
        Event.observe(window,'beforeunload', function(e) {
          e.returnValue = 'Are you sure you want to leave without saving the order?';
        });
      }
    }
  });

  document.observe('mouseup',function(e){
    if (floater)
    {
      e.stop();
      floater.style.background = 'none';
      floater = null;
    }
  });

});