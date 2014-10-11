WUHU
http://wuhu.function.hu
=========

REQUIREMENTS
  
  Server side:
    Apache 2.x
    PHP 5.x with GDLib
    MySQL 5.x
  
  Beamer side: 
    (desktop)
      Basic hardware acceleration (crossfades, heh)
      Windows XP
      DirectX 9.0c (currently linked to d3dx9_39.dll)
    (browser)
      HTML5 compatible browser (Chrome/Firefox preferred)
      Machine to handle it (any OS)
    

BASIC INSTALLATION

  APACHE

    1. Set up a basic Apache server with two virtual hosts, one for the
       users and one for the admins.
       One convenient way to configure this is
         http://party.lan pointing to /var/www/party
         http://admin.lan pointing to /var/www/admin
       The admin one is recommended to have SSL configured.
         
       It's important to set up a working nameserver too!
    
    2. Set AllowOverride in your Apache configs to All.

  MYSQL 
  
    1. Set up a MySQL server, create a database, and create an account
       that has full read/write access to the database.
       
  MISC UNIX STUFF
     
    1. Create a directory where you will store your compo entries.
       This dir has to be readable and writeable by Apache, and for
       convenience, it's useful if it's the root dir of a password
       protected FTP.  
    
    2. Create another directory, where you will store the screenshots.
       This dir has to be readable and writeable by Apache, but it will
       only serve as storage, it doesn't have to be accessible by
       anything else.
       
    3. Unpack the www_admin dir into your admin dir and
       unpack the www_party dir into your party dir.
  
  DEPLOYMENT 
       
    1. Open your admin interface in a web browser.
       It should bring you to the deployment form.
       
    2. Fill the form accordingly, and remember to use
       absolute paths everywhere.
       
    3. On success, you should be forwarded to the admin interface.
       Note that if you set a user/pass for the interface, you will be
       prompted for it.
       
CONFIGURATION

  Adding compos:
    
    Open the "Compos" link in the admin menu.
    Here you can add and organize competitions - important note is
    that the directory name obviously shouldn't contain weirdo
    characters.
    
  Generating votekeys:
  
    Click the "Votekeys" link.
    Here you can generate votekeys for the attendants - remember
    never to re-generate votekeys after you've printed them out!

  Page contents:
  
    Pages can be dynamically added and removed from the partynet.
    They all use a slightly mutated version of the Wiki-syntax.
    See http://en.wikipedia.org/wiki/Wikipedia:Cheatsheet for
    details.
        
SETTING UP AND USING THE BEAM SYSTEM (DESKTOP)

  NOTE: this is becoming deprecated.

  1. Unpack the exe_beamer directory on the beamer PC.
  
  2. Change the line 
       <sourceurl>http://admin.lan/result.xml</sourceurl>
     so that it corresponds to the (not yet existing) file at the
     admin interface.
     
     If you password protected your admin interface, use the syntax
       https://username:password@host...
  
  3. Edit the config.xml to your needs (this is basically skinning
     and editing colors, etc.)
     
  4. Put your partyslides in the "slides" directory, and
     optionally change the line 
       <slideurl>https://admin.lan/slides/</slideurl>
     so that it corresponds to the "slides" directory at the
     admin interface (see above for syntax).
     
     Remember: if you refresh your slides from the internet,
     it will delete your local ones!
  
  5. Start the executable.

SETTING UP AND USING THE BEAM SYSTEM (BROWSER)
  
  1. Click the "Slideviewer" link in the admin
  
  2. Enter the original slide resolution in which the design was done
  
  3. Press "Open viewer" - most browsers allow you to switch to
     fullscreen with F11.
  
  Both beam systems rely on simple keypresses for operation.
  
  ALT-F4 - quit
  LEFT ARROW - previous slide / minus one minute in countdown mode
  RIGHT ARROW - next slide / plus one minute in countdown mode
  HOME - first slide
  END - last slide
  S - partyslide rotation mode
  R - re-download partyslides from the intranet (not needed for browser)
  SPACE - re-read result.xml (and quit partyslide mode)
    
  This last key essentially means that once you've used the "BEAMER"
  menu on the admin interface, you must press SPACE to refresh the
  data inside (and/or switch to another mode).
  
  
REVISION INFO
  v0.1 (2008. 11. 22.)
    Initial release
    
  v0.2 (2009. 7. 26.)
    BEAMER: Fixed handling of Alt-Tab
    BEAMER: Custom resolutions
    ADMIN:  Various sanity checks to make sure the installation works.
    ADMIN:  Fixed some bugs during install
    ADMIN:  Votekey customization
    PARTY:  Removed RSS (oh well)
    PARTY:  Ability to select and delete files

  v0.3 (2009. 10. 08.)
    ADMIN:  Fixed a bug in the SQL (wtf!)
    BEAMER+ADMIN: Added the feature for intranet-based slides
    BEAMER: Fixed aspect ratio bug    

  v0.4 (2011. 4. 30.)
    ALL: Major overhaul in a lot of things.
    BEAMER: Added SSL support
    ADMIN+PARTY: Added support for updating entries and range-voting.

  v0.5 (2011. 10. 9.)
    BEAMER: Crash on refreshing slides.
    BEAMER: Crash if slide URL not specified.
    BEAMER: Pictures are now pixel correct and bilinear filtered.

  v0.6 (2011. 10. 31.)
    ADMIN: Added support for moving entries between compos.
    ADMIN: Added support for exporting entries to public.

  v0.7 (2012. 07. 07.)
    ADMIN+PARTY: Shared code between the two directories.
    ADMIN+PARTY: Auto-sanitization of filenames.

  v0.8 (2012. 07. 21.)
    ADMIN+PARTY: Fixed a number of bugs
    ADMIN: Added easy buttons to shift around compo times.
    ADMIN: Added colorcoding to number of entries

  v0.9 (2012. 10. 04.)
    Considerable overhaul of a variety of things:
    Wuhu is shifting towards a plugin-based architecture
    which will allow people to add functionality to Wuhu
    without breaking the existing code.
    (I don't think this version was actually released,
    but we used it at some places.)
  
  v1.0 (2013. 04. 20.)
    MAJOR UPDATE: a lot of fixes + plugin based architecture.
  
  v1.1 (2013. 07. 20.)
    BEAMER: Fix crash on Alt-Tab, should also reload slides now
    ADMIN: [draggable_compoentries] Add reminder to save changes
    ADMIN: [entrystatus] Display count next to compos.php
    ADMIN: Better votekeys (uppercase caps only)
    ADMIN: Scenesat plugin (alpha)
    Various other bugfixes
  
  v1.2 (2014. 03. 15.)
    ADMIN+PARTY: Fix security issue
    ADMIN+PARTY: [twitter] Twitter plugin
    ADMIN: Default "no screenshot" pic
    ADMIN: Refactoring into bootstrap
    ADMIN: Votekey loader
    PARTY: 3LN compatibility, more inclusive password policy
    Various other bugfixes
    
  v1.3 (2014. 04. 05.)
    BEAMER: Fix huge memory leak
    PARTY: Fix security issue
    ADMIN: [adminer] Make upgrading easier
    Various other bugfixes
    
  v1.4 (2014. 07. 20.)
    BEAMER: HTML5 slide viewer - now you can technically run
      the whole show with a Linux laptop. The controls are
      pretty much the same.
    PARTY+ADMIN: PM2-style live voting
    ADMIN: Votes are now timestamped
    ADMIN: Add more compos at the same time
    Various other bugfixes and tweaks
    
  v1.5 (2014. 10. 11.)
    BEAMER: Fix countdown timezones in HTML5 slide system
    ADMIN: [oneliner/twitter] Various bugfixes
    ADMIN: Fix plugin enumeration for PHP 5.5
    
    
CREDITS

  Wuhu was created and is maintained by Gargaj / Conspiracy.
  
  Additional effort by:

    Zoom / Conspiracy with the original admin design and QA
    Quarryman / Ogdoad for minor fixes
    lug00ber / Kvasigen for additional QA
    The TG Creativia crew for their immense QA effort

  Acknowledgments for external stuff are available in the License.txt
