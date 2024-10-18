# WUHU
Lightweight party management system
http://wuhu.function.hu

## Requirements

### Server side:
* Apache 2.x (Not tested with other servers but it should probably work fine)
* PHP 5.x (works fine with 7.x, should mostly work with 8.2)
* MySQL 5.x (works fine with MariaDB)
### Beamer side: 
* HTML5 compatible browser (Chrome/Firefox preferred)
* Machine to handle it (any OS)

## Basic installation
Note: there's an installation script available from https://gist.github.com/Gargaj/2a8cb8c015244b6431b9 that can pretty much set most things up on a blank Linux install.

### Apache
1. Set up a basic Apache server with two virtual hosts, one for the users and one for the admins. One convenient way to configure this is
         http://party.lan pointing to /var/www/party
         http://admin.lan pointing to /var/www/admin
       The admin one is recommended to have SSL configured.         
       It's important to set up a working nameserver too!
    
2. Set AllowOverride in your Apache configs to All.

### MySQL 
  
Set up a MySQL server, create a database, and create an account that has full read/write access to the database.
       
### Miscellaneous Unix stuff
1. Create a directory where you will store your compo entries. This dir has to be readable and writeable by Apache, and for convenience, it's useful if it's the root dir of a password protected FTP.  
2. Create another directory, where you will store the screenshots. This dir has to be readable and writeable by Apache, but it will only serve as storage, it doesn't have to be accessible by anything else.
3. Unpack the www_admin dir into your admin dir and unpack the www_party dir into your party dir.
### Deployment
1. Open your admin interface in a web browser. It should bring you to the deployment form.
2. Fill the form accordingly, and remember to use absolute paths everywhere.
3. On success, you should be forwarded to the admin interface. Note that if you set a user/pass for the interface, you will be prompted for it.
       
## Using the beam system
1. Click the "Slideviewer" link in the admin
2. Enter the original slide resolution in which the design was done
3. Press "Open viewer" - most browsers allow you to switch to fullscreen with F11.
  
Both beam systems rely on simple keypresses for operation.
  
* ALT-F4 - quit
* LEFT ARROW - previous slide / minus one minute in countdown mode
* RIGHT ARROW - next slide / plus one minute in countdown mode
* HOME - first slide
* END - last slide
* S - partyslide rotation mode
* T - reload stylesheet (without changing the slide contents) 
* SPACE - re-read beamer.data (and quit partyslide mode)
    
This last key essentially means that once you've used the "BEAMER" menu on the admin interface, you must press SPACE to refresh the data inside (and/or switch to another mode).
  
## Credits
Wuhu was created and is maintained by Gargaj / Conspiracy.

Additional effort by:
* Zoom / Conspiracy with the original admin design and QA
* Quarryman / Ogdoad for minor fixes
* lug00ber / Kvasigen for additional QA
* The TG Creativia crew for their immense QA effort

Acknowledgments for external stuff are available in the license file.
