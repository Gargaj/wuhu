var WuhuSlideSystem = Class.create({

  reLayout:function()
  {
    $$('.reveal .slides>section').each(function(item){
      var container = item.down("div.container");
	  if (container) 
	  {
	    var h = this.revealOptions.height;
	    var containerHeight = container.getLayout().get("height");
        container.style.top = Math.floor((h - containerHeight) / 2) + 'px';
	  }
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

  reloadStylesheets:function() {
    var queryString = '?reload=' + new Date().getTime();
    $$('link[rel="stylesheet"]').each(function(item) {
      item.href = item.href.replace(/\?.*|$/, queryString);
    });
  },
  deleteAllSlides:function()
  {
    this.slideContainer.update("");
  },
  reloadSlideRotation:function()
  {
    var currentURL = null;
    if (currentSlide = Reveal.getCurrentSlide())
      currentURL = currentSlide.getAttribute("data-slideurl");
    
    if (this.options.countdownOverlay)
    {
      var revealContainer = $$(".reveal").first();
      if ($$(".countdownSlide").length && !$("pip-countdown"))
      {
        revealContainer.insert( new Element("div",{"id":"pip-countdown"}) );
        $$(".countdownSlide .container").first().childElements().each(function(i){
          $("pip-countdown").insert( i );
        });
      }
    }
    else
    {
      $$("#pip-countdown").invoke("remove");
    }

    this.deleteAllSlides();
    $A(this.slides).each(function(slide){
      var sec = this.slideContainer.down("section[data-slideurl='" + slide.url + "']");
      if (sec)
      {
        sec.down("div.container").update("");
      }
      else
      {
        sec = this.insertSlide({
          "data-slideurl": slide.url,
          "class": "rotationSlide",
        });
      }
      var cont = sec.down("div.container");
      var ext = slide.url.split('.').pop().toLowerCase();
      switch (ext)
      {
        case "jpg":
        case "gif":
        case "png":
        case "jpeg":
          {
            sec.addClassName( "image" );
            var img = new Element("img",{src:slide.url + "?" + slide.lastUpdate});
            cont.insert( img );
            var wuhu = this;
            img.observe("load",(function(){ this.reLayout(); }).bind(this));
          } break;
        case "txt":
        case "htm":
        case "html":
          {
            new Ajax.Request(slide.url + "?" + Math.random(),{
              "method":"GET",
              onException:function(req,ex) { throw ex; },
              onSuccess:function(transport){
                sec.addClassName( "text" );
                cont.update( transport.responseText );
                this.reLayout();
              }
            });
          } break;
        case "ogv":
        case "mp4":
          {
            var video = new Element("video",{"muted":true});
            video.insert( new Element("source",{src:slide.url}) );
            video.observe("load",(function(){ this.reLayout(); }).bind(this));
            video.observe("loadedmetadata",(function(){ this.reLayout(); }).bind(this));
            sec.addClassName( "video" );
            cont.insert( video );
          } break;
        default:
          {
            sec.addClassName( "unknown" );
            cont.update( "Unknown file type: " + slide.url );
          } break;
      }
    },this);
    this.revealOptions.loop = true;
    Reveal.initialize( this.revealOptions );

    var fixed = false;
    if (currentURL)
    {
      //console.log("[wuhu] navigating to " + current);
      //Reveal.slide( current );
      var n = 0;
      this.slideContainer.select("section").each(function(item){
        if (currentURL == item.getAttribute("data-slideurl"))
        {
          Reveal.slide( n );
          fixed = true;
        }
        n++;
      });
    }
    if (!fixed)
      Reveal.slide(0);
    this.reLayout();
  },

  fetchSlideRotation:function()
  {
    new Ajax.Request("../slides/?allSlides=1",{
      "method":"GET",
      onException:function(req,ex) { throw ex; },
      onSuccess:(function(transport){
    		if (transport.responseText.length <= 0)
          return;
        var e = new Element("root").update( transport.responseText );
    		if (Element.select(e,"slides").length <= 0)
          return;
        this.slides = [];
        Element.select(e,"slide").each((function(slide){
          var o = {};
          o.url = slide.innerHTML;
          o.lastUpdate = slide.getAttribute("lastChanged");
          this.slides.push( o );
        }).bind(this));
        Reveal.resumeAutoSlide();
        this.reloadSlideRotation();
        this.regenerateTransitions();
      }).bind(this)
    });
  },

  updateCountdownTimer:function()
  {
    var timer = $$(".countdownTimer");
    
    if (!timer.length) return;

    // date.now / date.gettime? http://wholemeal.co.nz/blog/2011/09/09/chrome-firefox-javascript-date-differences/
    var sec = Math.floor( (this.countdownTimeStamp - Date.now()) / 1000 );
    if (sec < 0)
    {
      $$(".isStartingIn").invoke("update","will start");
      timer.invoke("update","soon");
      return;
    }
    $$(".isStartingIn").invoke("update","will start in");

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

    timer.invoke("update", s );
  },
  fetchSlideEvents:function()
  {
    new Ajax.Request("../result.xml?" + Math.random(),{
      "method":"GET",
      onException:function(req,ex) { throw ex; },
      onSuccess:(function(transport){
        var e = new Element("root").update( transport.responseText );

        $$("#pip-countdown").invoke("remove");
        this.deleteAllSlides();

        this.prizinator = null;
        
        var mode = Element.down(e,"result > mode").innerHTML;
        switch(mode)
        {
          case "announcement":
            {
              var sec = this.insertSlide({"class":"announcementSlide"});
              var cont = sec.down("div.container");
              var text = Element.down(e,"result > announcementtext").innerHTML;
              var useHTML = Element.down(e,"result > announcementtext").getAttribute("isHTML") == "true";
              cont.update( useHTML ? text.unescapeHTML() : text.replace(/(?:\r\n|\r|\n)/g, '<br />') );
            } break;
          case "compocountdown":
            {
              var sec = this.insertSlide({"class":"countdownSlide"});
              var cont = sec.down("div.container");

              var openingText = "";
              if (Element.down(e,"result > componame"))
                openingText = "The " + Element.down(e,"result > componame").innerHTML + " compo";
              if (Element.down(e,"result > eventname"))
                openingText = Element.down(e,"result > eventname").innerHTML;

              var t = Element.down(e,"result > compostart").innerHTML;
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
              this.countdownTimeStamp = Date.parse( t );

              cont.insert( new Element("div",{"class":"eventName"}).update(openingText) );
              cont.insert( new Element("div",{"class":"isStartingIn"}).update("will start in") );
              cont.insert( new Element("div",{"class":"countdownTimer"}).update("0") );
              this.updateCountdownTimer();

            } break;
          case "compodisplay":
            {
              this.revealOptions.loop = false;

              var compoName = "";
              var compoNameFull = "";
              if (Element.down(e,"result > componame"))
              {
                compoName = Element.down(e,"result > componame").innerHTML;
                compoNameFull = "The " + compoName + " compo";
              }
              if (Element.down(e,"result > eventname"))
              {
                compoName = Element.down(e,"result > eventname").innerHTML;
                compoNameFull = compoName;
              }

              // slide 1: introduction
              var sec = this.insertSlide({"class":"compoDisplaySlide intro"});
              var cont = sec.down("div.container");
              cont.insert( new Element("div",{"class":"eventName"}).update(compoNameFull) );
              cont.insert( new Element("div",{"class":"willStart"}).update("will start") );
              cont.insert( new Element("div",{"class":"now"}).update("now!") );

              // slide 2..n: entries

              Element.select(e,"result > entries entry").each(function(entry){
                var sec = this.insertSlide({"class":"compoDisplaySlide entry"});
                sec.insert( new Element("div",{"class":"eventName"}).update(compoName) );
                var cont = sec.down("div.container");
                var fields = ["number","title","author","comment"];
                fields.each(function(field){
                  if ( Element.down(entry,field) )
                  {
                    var s = Element.down(entry,field).innerHTML;
                    if (field == "comment")
                      s = s.replace(/(?:\r\n|\r|\n)/g, '<br />');
                    cont.insert( new Element("div",{"class":field}).update( s ) );
                  }
                },this);

              },this);

              // slide n+1: end of compo
              var sec = this.insertSlide({"class":"compoDisplaySlide outro"});
              var cont = sec.down("div.container");
              cont.insert( new Element("div",{"class":"eventName"}).update(compoNameFull) );
              cont.insert( new Element("div",{"class":"is"}).update("is") );
              cont.insert( new Element("div",{"class":"over"}).update("over!") );

            } break;
          case "prizegiving":
            {
              this.revealOptions.loop = false;

              var compoName = Element.down(e,"result > componame").innerHTML;
              var compoNameFull = "The " + compoName + " compo";

              // slide 1: introduction
              var sec = this.insertSlide({"class":"prizegivingSlide intro"});
              var cont = sec.down("div.container");
              cont.insert( new Element("div",{"class":"header"}).update("Results") );
              cont.insert( new Element("div",{"class":"eventName"}).update(compoName) );

              // slide 2..n: entries
              var results = [];
              var maxPts = 0;
              Element.select(e,"result > results entry").each(function(entry){
                var fields = ["ranking","title","author","points"];
                var o = {};
                fields.each(function(field){
                  if ( Element.down(entry,field) )
                  {
                    var s = Element.down(entry,field).innerHTML;
                    o[field] = s;
                  }
                },this);
                maxPts = Math.max( maxPts, parseInt(o["points"],10) );
                results.push(o);
              },this);
              
              var sec = this.insertSlide({"class":"prizegivingSlide prizinator"});
              sec.insert( new Element("div",{"class":"eventName"}).update(compoName) );
              var cont = sec.down("div.container");
              this.prizinator = new WuhuPrizinator({"parent":cont,"maxPoints":maxPts,"results":results});
              /*
              Element.select(e,"result > results entry").each(function(entry){
                var sec = this.insertSlide({"class":"prizegivingSlide entry"});
                sec.insert( new Element("div",{"class":"eventName"}).update(compoName) );
                var cont = sec.down("div.container");
                var fields = ["ranking","title","author","points"];
                fields.each(function(field){
                  if ( Element.down(entry,field) )
                  {
                    var s = Element.down(entry,field).innerHTML;
                    if (field == "points") s += (s == 1) ? " pt" : " pts";
                    cont.insert( new Element("div",{"class":field}).update( s ) );
                  }
                },this);

              },this);
              */

            } break;
        }
        Reveal.initialize( this.revealOptions );
        Reveal.slide( 0 );
        Reveal.pauseAutoSlide();
        $$('.reveal .slides > section').each((function(item){
          item.setAttribute("data-transition",this.options.defaultTransition);
        }).bind(this));
        this.reLayout();
      }).bind(this)
    });
  },
  regenerateTransitions:function()
  {
    var transitions = this.options.transitions.split("/");
    var randomTransition = transitions[ Math.floor(Math.random() * transitions.length) ];
    $$('.reveal .slides > section.rotationSlide').each(function(item){
      item.setAttribute("data-transition",randomTransition);
    });
  },
  initialize:function( opt )
  {
    this.options = {
      showHours: false,
      width: screen.width,
      height: screen.height,
      countdownOverlay: true,
      transitions: "cube/page/concave/zoom/linear/fade",
      defaultTransition: "cube",
      newPrizegiving: false,
    };
    Object.extend(this.options, opt || {} );

    this.slides = [];
    this.prizinator = null;

    this.MODE_ROTATION = 1;
    this.MODE_EVENT = 2;

    this.slideMode = this.MODE_EVENT;

    this.countdownTimeStamp = null;

    this.slideContainer = $$(".reveal .slides").first();

    this.revealOptions =
    {
      controls: false,
      progress: false,
      history: true,
      center: true,
      keyboard: false, // we disable Reveal's keyboard handling and use our own

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


    if (this.slideMode == this.MODE_ROTATION)
      this.fetchSlideRotation();
    else
      this.fetchSlideEvents();

    var wuhu = this;
    new PeriodicalExecuter((function(pe) {
      if (this.slideMode == this.MODE_ROTATION)
      {
        this.fetchSlideRotation();
      }
    }).bind(this), 60);
    new PeriodicalExecuter((function(pe) {
      //if (this.slideMode == this.MODE_EVENT)
        this.updateCountdownTimer();
      this.reLayout();
    }).bind(this), 0.5);
    document.observe("keyup",(function(ev){
      if (ev.keyCode == ' '.charCodeAt(0))
      {
        this.slideMode = this.MODE_EVENT;
        this.fetchSlideEvents();
        ev.stop();
      }
      if (ev.keyCode == 'S'.charCodeAt(0))
      {
        this.slideMode = this.MODE_ROTATION;
        this.fetchSlideRotation();
        ev.stop();
      }
      if (ev.keyCode == 'P'.charCodeAt(0))
      {
        if (!Reveal.autoSlidePaused)
          Reveal.pauseAutoSlide();
        else
          Reveal.resumeAutoSlide();
      }
      if (ev.keyCode == 'T'.charCodeAt(0))
      {
        this.reloadStylesheets();
        ev.stop();
      }
      if ($$(".countdownTimer").length)
      {
        if (ev.keyCode == Event.KEY_DOWN)
        {
          this.countdownTimeStamp -= 60 * 1000;
          this.updateCountdownTimer();
          ev.stop();
          return;
        }
        if (ev.keyCode == Event.KEY_UP)
        {
          this.countdownTimeStamp += 60 * 1000;
          this.updateCountdownTimer();
          ev.stop();
          return;
        }
      }

      // default reveal stuff we disabled
      switch( ev.keyCode ) {
        case Event.KEY_PAGEUP: 
        case Event.KEY_LEFT: { if (this.prizinator && !Reveal.isFirstSlide()) { if(this.prizinator.previous()) break; } Reveal.navigateLeft(); ev.stop(); } break;
        case Event.KEY_PAGEDOWN: 
        case Event.KEY_RIGHT: { if (this.prizinator && !Reveal.isFirstSlide()) { if(this.prizinator.next()) break; } Reveal.navigateRight(); ev.stop(); } break;
        case Event.KEY_HOME: Reveal.slide( 0 ); ev.stop(); break;
        case Event.KEY_END: Reveal.slide( $$('.reveal .slides>section').length - 1 ); ev.stop(); break;
        case Event.KEY_ESC: { ev.stop(); Reveal.toggleOverview(); } break;
        case Event.KEY_RETURN: { ev.stop(); if (Reveal.isOverview()) Reveal.toggleOverview(); } break;
      }

    }).bind(this));

    document.observe("slidechanged",(function(ev){
      setTimeout((function(){
        this.regenerateTransitions();
      }).bind(this),this.revealOptions.autoSlide / 2);
      $$('.reveal .slides>section.rotationSlide').each(function(item){
        var video = ev.currentSlide.down("video");
        if (video) video.play();
      });
      this.reLayout();
    }).bind(this));
    Event.observe(window, 'resize', (function() { this.reLayout(); }).bind(this));
  },
});

var WuhuSlideSystemCanvas = Class.create(WuhuSlideSystem,{
  deleteAllSlides:function( $super )
  {
    $super();
    this.canvases = [];
    this.contexts = [];
  },
  insertSlide:function( $super, options )
  {
    var section = $super(options);
    //section.setStyle({"background":"none"});
    var canvas = new Element("canvas",{ width: this.revealOptions.width, height: this.revealOptions.height });
    canvas.setStyle({
      width: this.revealOptions.width + "px",
      height: this.revealOptions.height + "px",
    });
    this.canvases.push( canvas );
    this.contexts.push( canvas.getContext('2d') );
    section.insertBefore( canvas, section.down(".container") );
    return section;
  },
  animate:function()
  {
    // TODO: only update canvases that are visible
    $A(this.contexts).each((function(item){
      item.drawImage(this.sourceCanvas, 0, 0);
    }).bind(this));

    requestAnimationFrame( (function(){ this.animate(); }).bind(this) );
    //setTimeout( (function(){ this.animate(); }).bind(this), 10 );
  },
  initialize:function( $super, options )
  {
    $super(options);
    this.canvases = [];
    this.contexts = [];
    this.sourceCanvas = new Element("canvas",{width:options.width,height:options.height,style:"display: none;"});
    document.body.insert(this.sourceCanvas);
    this.animate();
  }
});

var WuhuPrizinator = Class.create({
  initialize:function( opt )
  {
    this.list = new Element("ul",{"class":"prizinator"});
    opt.parent.insert(this.list);
    $A(opt.results).sortBy(function(i){return i.ranking;}).each((function(entry){
      var s = "";
      s += "<span class='bar' style='width:"+(entry.points/opt.maxPoints*100).toFixed(2)+"%'>&nbsp;</span>\n";
      s += "<div class='info'>\n";
      s += "<span class='ranking'>" + entry.ranking + ".</span>\n";
      s += "<span class='title'>" + entry.title + "</span>\n";
      s += "<span class='author'>" + entry.author + "</span>\n";
      s += "<span class='points'>" + (entry.points==1 ? entry.points+" pt" : entry.points+" pts") + ".</span>\n";
      s += "</div>\n";
      var li = new Element("li",{"class":"hidden","data-ranking":entry.ranking});
      li.update(s)
      this.list.insert( li );
    }).bind(this));
  },
  previous:function()
  {
    var items = this.list.select("li:not(.hidden)").sortBy(function(i){ return i.getAttribute("data-ranking"); });
    if (items.length == 0) return false;
    var prevRank = items.first().getAttribute("data-ranking");
    this.list.select("li[data-ranking="+prevRank+"]").invoke("addClassName","hidden");
    return true;
  },
  next:function()
  {
    var items = this.list.select("li.hidden").sortBy(function(i){ return -i.getAttribute("data-ranking"); });
    if (items.length == 0) return false;
    var nextRank = items.first().getAttribute("data-ranking");
    this.list.select("li[data-ranking="+nextRank+"]").invoke("removeClassName","hidden");
    return true;
  },
});

var WuhuAudioMonitor = Class.create({
  setup:function(stream)
  {
    window.AudioContext = window.AudioContext || window.webkitAudioContext;
    this.context = new AudioContext();

    this.mic = this.context.createMediaStreamSource( stream );

    this.analyser = this.context.createAnalyser();
    this.analyser.smoothingTimeConstant = this.options.smooth;
    this.analyser.fftSize = this.options.fftSize;

    this.processor = this.context.createScriptProcessor(this.analyser.fftSize, 1, 1);
    this.processor.onaudioprocess = (function() {
      var array = new Uint8Array(this.analyser.frequencyBinCount);
      this.analyser.getByteFrequencyData(array);
      for (var i = 0; i<this.analyser.frequencyBinCount; i++)
        this.fft[i] = array[i] / 255.0;
    }).bind(this);

    this.mic.connect(this.analyser);
    this.analyser.connect(this.processor);
    this.processor.connect(this.context.destination);
  },
  initialize:function( opt )
  {
    this.options = {
      fftSize: 512,
      smooth: 0.7,
    };
    Object.extend(this.options, opt || {} );

    this.fft = new Array(this.options.fftSize / 2);

    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia)
    {
      var p = navigator.mediaDevices.getUserMedia({audio:true});
      p.then((function(stream) {
        this.setup(stream);
      }).bind(this));
      return;
    }
    navigator.getUserMedia = navigator.getUserMedia
      || navigator.webkitGetUserMedia
      || function(){ console.warn("getUserMedia not found") };
    navigator.getUserMedia( {audio:true}, (function(stream) {
      this.setup(stream);
    }).bind(this), function (){console.warn("Error getting audio stream from getUserMedia")} );
  },
  getFFTValue:function(v)
  {
    return this.fft[v] || 0;
  },
  getFFTValueNormalized:function(v)
  {
    return this.fft[ Math.floor(v * this.options.fftSize / 2) ] || 0;
  },
});