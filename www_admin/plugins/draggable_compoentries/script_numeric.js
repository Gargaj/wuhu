document.observe("dom:loaded",function(){

  var n = 1;
  $$('#compoentrylist tr.entry').each(function(item){
    var entryID = parseInt( item.select('.entrynumber').first().innerHTML.replace("#",""), 10 );

    item.select('.movedown').first().remove();

    var mov = item.select('.moveup').first();
    mov.setAttribute("colspan","2");
    mov.update("");
    var input = new Element("input",{"class":"entryreorder","value":n,"style":"width:20px"});
    mov.insert(input);
    input.store("entryNumber",entryID);

    input.observe("keyup",function(){ $("sendDraggedSort").show(); });
    n++;
  });
  $("compoentrylist").insert({"after":"<button id='sendDraggedSort'>Save sorting changes</button>"});
  $("sendDraggedSort").hide();
  $("sendDraggedSort").observe('click',function(){
    var newOrder = $H();
    var i = 1;   
    $$('#compoentrylist input.entryreorder').sort(function(a,b){
      return a.value - b.value;
    }).each(function(item){
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
        if (transport.responseText.indexOf("SUCCESS")!=-1)
          location.reload();
        else
          alert("There was an error:\n\n" + transport.responseText);
      },
    });
  });
  
});