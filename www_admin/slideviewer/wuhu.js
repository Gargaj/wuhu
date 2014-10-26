var WuhuSlideSystem = Class.create({

  reLayout:function()
  {
    $$('.reveal .slides>section').each(function(item){
      var cont = item.down("div.container");
      if (cont) cont.style.top = ((this.revealOptions.height - cont.getLayout().get("height")) / 2) + 'px';
    },this);
  },
  
  insertSlide:function( options )
  {
    var sec = new Element("section",options).update("<div class='container'></div>");
    sec.setStyle({
      width: this.revealOptions.width + "px",
      height: this.revealOptions.height + "px",
    });
    this.slideContainer.insert(sec);
    return sec;
  },

  reloadStylesheets:function() 
  {
    // TODO: replace this with loading the CSS with ajax in the background and stuffing it into a <style> tag
    var queryString = '?reload=' + new Date().getTime();
    $$('link[rel="stylesheet"]').each(function(item) {
      item.href = item.href.replace(/\?.*|$/, queryString);
    });
  },
    
  reloadSlideRotation:function()
  {
    // todo: if slide exist, replace. if not, create.
    var current = Reveal.getCurrentSlide() ? Reveal.getIndices( Reveal.getCurrentSlide() ).h : -1;
    
    var wuhu = this;
    
    this.slideContainer.update("");
    $H(this.slides).each(function(slideKVP){
      var sec = this.slideContainer.down("section[data-slideimg='" + slideKVP.key + "']");
      if (sec)
      {
        sec.down("div.container").update("");
      }
      else
      {
        sec = this.insertSlide({
          "data-slideimg": slideKVP.key,
        });
      }
      var cont = sec.down("div.container");
      var slide = slideKVP.value;
      switch (slide.type)
      {
        case "image":
          {
            sec.addClassName( "image" );
            var img = new Element("img",{src:slide.filename});
            cont.insert( img );
            img.observe("load",function(){ wuhu.reLayout(); });
          } break;
        case "text":
          {
            sec.addClassName( "text" );
            cont.update( slide.contents.escapeHTML() );
            this.reLayout();
          } break;
        case "html":
          {
            sec.addClassName( "text" );
            cont.update( slide.contents );
            this.reLayout();
          } break;
        case "video":
          {
            var video = new Element("video",{"muted":true});
            video.insert( new Element("source",{src:slide.filename}) );
            video.observe("load",function(){ wuhu.reLayout(); });
            video.observe("loadedmetadata",function(){ wuhu.reLayout(); });
            sec.addClassName( "video" );
            cont.insert( video );
          } break;

        case "countdown":
          {
            wuhu.revealOptions.keyboard = false; // we're overriding left/right!
            
            sec.addClassName("countdownSlide");
            var cont = sec.down("div.container");

            var openingText = "";
            if (slide.compoName)
              openingText = "The " + slide.compoName + " compo";
            if (slide.eventName)
              openingText = slide.eventName;

            var t = slide.timeStart;
            t = t.split(" ").join("T");
            
            function padNumberWithTwo(n)
            {
              return ("000" + n).slice(-2);
            }
            
            // this is where the fun starts!
            // http://gargaj.github.io/date-parsing-chrome-ff/
            
            var offset = new Date().getTimezoneOffset() * -1;
            if (offset > 0)
              t += "+" + padNumberWithTwo(offset / 60) + "" + padNumberWithTwo(offset % 60);
            else if (offset < 0)
              t += "-" + padNumberWithTwo(-offset / 60) + "" + padNumberWithTwo(-offset % 60);
            else if (offset == 0)
              t += "+0000";
            //console.log(t);
            wuhu.countdownTimeStamp = Date.parse( t );

            cont.insert( new Element("div",{"class":"eventName"}).update(openingText) );
            cont.insert( new Element("div",{"class":"isStartingIn"}).update("will start in") );
            cont.insert( new Element("div",{"class":"countdownTimer"}).update("0") );
            wuhu.updateCountdownTimer();

          } break;

        case "compoDisplayIntro":
          {
            var compoName = slide.compoName;
            var compoNameFull = "The " + compoName + " compo";

            sec.addClassName("compoDisplaySlide");
            sec.addClassName("intro");
            var cont = sec.down("div.container");

            cont.insert( new Element("div",{"class":"eventName"}).update(compoNameFull) );
            cont.insert( new Element("div",{"class":"willStart"}).update("will start") );
            cont.insert( new Element("div",{"class":"now"}).update("now!") );
          } break;
        case "compoDisplaySlide":
          {
            var compoName = slide.compoName;
            var compoNameFull = "The " + compoName + " compo";

            // slide 2..n: entries
            sec.addClassName("compoDisplaySlide");
            sec.addClassName("entry");
            sec.insert( new Element("div",{"class":"eventName"}).update(compoName) );
            var cont = sec.down("div.container");
            var fields = ["number","title","author","comment"];
            fields.each(function(field){
              if ( slide[field] )
                cont.insert( new Element("div",{"class":field}).update( slide[field] ) );
            });
          } break;
        case "compoDisplayOutro":
          {
            var compoName = slide.compoName;
            var compoNameFull = "The " + compoName + " compo";

            sec.addClassName("compoDisplaySlide");
            sec.addClassName("outro");
            var cont = sec.down("div.container");
            cont.insert( new Element("div",{"class":"eventName"}).update(compoNameFull) );
            cont.insert( new Element("div",{"class":"is"}).update("is") );
            cont.insert( new Element("div",{"class":"over"}).update("over!") );
          } break;
        case "prizegivingIntro":
          {
            var compoName = slide.compoName;

            sec.addClassName("prizegivingSlide");
            sec.addClassName("intro");
            var cont = sec.down("div.container");

            cont.insert( new Element("div",{"class":"header"}).update("Results") );
            cont.insert( new Element("div",{"class":"eventName"}).update(compoName) );
          } break;
        case "prizegivingSlide":
          {
            var compoName = slide.compoName;

            // slide 2..n: entries
            sec.addClassName("prizegivingSlide");
            sec.addClassName("entry");
            sec.insert( new Element("div",{"class":"eventName"}).update(compoName) );
            var cont = sec.down("div.container");
            var fields = ["ranking","title","author","points"];
            fields.each(function(field){
              if ( slide[field] )
              {
                var s = slide[field];
                if (field == "points") s += (s == 1) ? " pt" : " pts";
                cont.insert( new Element("div",{"class":field}).update( s ) );
              }
            });
          } break;
      }
    },this);

    if (current >= 0)
    {
      console.log("[wuhu] navigating to " + current);
      Reveal.slide( current );
    }
    this.reLayout();
  },
  
  fetchPlaylist:function(url)
  {
    var wuhu = this;
    new Ajax.Request(url + (url.indexOf("?") ? "&" : "?") + "rnd=" + new Date().getTime(),{
      "method":"GET",
      onSuccess:function(transport){
        var data = transport.responseJSON;
        if (data === null || data === undefined)
          return; // error
        
        wuhu.slides = {};
        $H(data.slides).each(function(slide){
          wuhu.slides[slide.key] = Object.clone(slide.value);
        });
        
        if (data.settings)
        {
          wuhu.revealOptions.loop = !!data.settings.autoRotate;
        }
        else
        {
          wuhu.revealOptions.loop = false;
        }
        wuhu.revealOptions.keyboard = true;
        wuhu.reloadSlideRotation();

        Reveal.initialize( wuhu.revealOptions );
        if (wuhu.revealOptions.loop)
        {
          Reveal.resumeAutoSlide();
        }
        else
        {
          Reveal.slide( 0 );
          Reveal.pauseAutoSlide();
        }

        wuhu.reLayout();
      }
    });
  },
  fetchSlideRotation:function()
  {
    var wuhu = this;
    wuhu.fetchPlaylist("../slides/?html5=1");
  },
  
  updateCountdownTimer:function()
  {
    if (!$$(".countdownTimer").length) return;
  
    var timer = $$(".countdownTimer").first();
  
    var sec = Math.floor( (this.countdownTimeStamp - Date.now()) / 1000 );
    if (sec < 0)
    {
      $$(".isStartingIn").first().update("will start");
      timer.update("soon");
      return;
    }
    $$(".isStartingIn").first().update( "will start in");
  
    var s = "";
    if (this.options.showHours) 
    {
      s = ("000" + (sec % 60)).slice(-2); sec = Math.floor(sec / 60);
      s = ("000" + (sec % 60)).slice(-2) + ":" + s; sec = Math.floor(sec / 60);
      s = ("" + (sec)) + ":" + s;
    }
    else
    {
      s = ("000" + (sec % 60)).slice(-2); sec = Math.floor(sec / 60);
      s = ("" + (sec)) + ":" + s;
    }
  
    timer.update( s );
  },
  initialize:function( opt )
  {
    this.options = {
      showHours: false,
      width: screen.width,
      height: screen.height,
    };
    Object.extend(this.options, opt || {} );

    this.slides = {};
  
    this.countdownTimeStamp = null;
  
    this.slideContainer = $$(".reveal .slides").first();
    
    this.revealOptions = 
    {
      controls: false,
      progress: false,
      history: true,
      center: true,
    
      loop: true,
    
      autoSlide: 10000,
      autoSlideStoppable: false,
    
      width: this.options.width,
      height: this.options.height,
    
      margin: 0,
    
      transition: 'default',
      transitionSpeed: 'slow',
    
      // Optional libraries used to extend on reveal.js
      dependencies: []
    };
    
    
    this.fetchSlideRotation();
      
    var wuhu = this;
    new PeriodicalExecuter(function(pe) {
      if (wuhu.revealOptions.loop)
        wuhu.fetchSlideRotation();
    }, 60);
    new PeriodicalExecuter(function(pe) {
      wuhu.updateCountdownTimer();
      wuhu.reLayout();
    }, 0.5);
    new PeriodicalExecuter(function(pe) {
      if ("WebSocket" in window)
      {
        if (!wuhu.sock)
        {
          wuhu.sock = new WebSocket("ws://dinnye:31337");
          wuhu.sock.onopen = function(event){ console.log("sock.onopen"); };
          wuhu.sock.onerror = function(event){ console.log("sock.onerror"); wuhu.sock.close(); wuhu.sock = null; };
          wuhu.sock.onclose = function(event){ console.log("sock.onclose"); wuhu.sock = null; };
          wuhu.sock.onmessage = function(event)
          { 
            console.log("sock.onmessage"); 
            var data = JSON.parse(event.data); 
            if (data == null || data == undefined)
              return;
            if (!data.command)
              return;
            switch(data.command)
            {
              case "slideNext": { Reveal.navigateRight(); } break;
              case "slidePrev": { Reveal.navigateLeft(); } break;
            }
          };
        }
      }
    }, 5);
    document.observe("keyup",function(ev){
      if (ev.keyCode == ' '.charCodeAt(0))
      {
        this.fetchSlideRotation();
        ev.stop();
      }
      if (ev.keyCode == 'S'.charCodeAt(0))
      {
        wuhu.fetchSlideRotation();
        ev.stop();
      }
      if (ev.keyCode == 'T'.charCodeAt(0))
      {
        wuhu.reloadStylesheets();
        ev.stop();
      }
      if ($$(".countdownTimer").length)
      {
        if (ev.keyCode == Event.KEY_LEFT)
        {
          wuhu.countdownTimeStamp -= 60 * 1000;
          wuhu.updateCountdownTimer();
          ev.stop();
        }
        if (ev.keyCode == Event.KEY_RIGHT)
        {
          wuhu.countdownTimeStamp += 60 * 1000;
          wuhu.updateCountdownTimer();
          ev.stop();
        }
      }
    });
    
    document.observe("slidechanged",function(ev){
      if (wuhu.revealOptions.loop)
      {
        setTimeout(function(){
          var trans = "cube/page/concave/zoom/linear/fade".split("/");
          var t = trans[Math.floor(Math.random()*trans.length)];
          $$('.reveal .slides>section').each(function(item){
            item.setAttribute("data-transition",t);
          });
        },1200);
      }
      var video = ev.currentSlide.down("video");
      if (video) video.play();
      wuhu.reLayout();
    });
    Event.observe(window, 'resize', function() { wuhu.reLayout(); });
  },
});
