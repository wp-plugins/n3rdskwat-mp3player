=== n3rdskwat-mp3player ===
Contributors: n3rdskwat-jmf
Donate link: http://www.n3rdskwat.com/code/
Tags: mp3, flash, ajax, playlist, adjustable, customizable, music
Requires at least: 2.6.0
Tested up to: 3.0.0
Stable tag: 1.2.1

Places a mp3 player at the bottom of the screen. Ajax-izes the whole site so the music will go on while browsing the blog and keeping your SEO structure intact!

== Description ==

The n3dskwat-mp3player is an idea of n3rdskwat member L.Gero.
Combining our interests in music, video and art n3rdskwat creates.
What we create depends on the situation..
This time I created the mp3player that fitted our needs and *might* fit yours!

The n3rdskwat-mp3player is a very simple player, which allows the visitor to listen to mp3's while browsing the website.
Though most mp3 players stop playback when you follow a link, this one re-codes the blog to load internally. This means you never really leave the homepage, although you have access to the entire blog!

Important Links:

* [Demonstration](http://www.n3rdskwat.com/ "Demonstration")
* [Changelog](http://wordpress.org/extend/plugins/n3rdskwat-mp3player/changelog/ "Changelog")

Features

* Adds a simple mp3 player to your blog
* Scans your whole website or specified directory for mp3's to put in the playlist
* Automatically start playing on entering the blog
* Optional randomization of mp3 playback
* Repeat-all after the entire playlist has been played
* Displaying of the playlist
* Where to search for mp3's
* Easily customizable positioning and layout properties
* Converts your blog to an AJAX controlled environment with keeping the SEO (search engine optimization) structure in tact!
* Uses SWFAddress to ensure you can link your pages to anybody correctly

== Installation ==

1a. Download the plugin through the build-in plugin-search.
1b. Or download it manually and extract it in your plugins directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. If you have other plugins, be sure to test thoroughly after loading a new page on your blog, see the F.A.Q. for more information.
4. When other plugins fail to work properly please contact us at the plugin-website so we can build in a solution!

== Frequently Asked Questions ==

= The plugin screws up my design!? =

If the mp3player rips your design apart, you can try to add a couple of lines to your stylesheet:

#n3s_body { margin: 0px !important; }
#n3s_body { position: relative; }

Are suggestions that have worked on other theme's, I hope it will help you fix your style back to normal!

= The plugin [x] only works on the first page I load =

That's the only big bug that I haven't completely fixed yet.
When you load a new page on your blog, the mp3player handles the request for you.
This means that scripts depending on the 'page is loaded' functionality need to be activated again.
At this point only the 'lightbox' plugin functionality has been added and tested.

If you have plugins that stop working, please let me know so I can add them to the (temporary) plugin compatibility function.

Or, if you know something about javascript, you can add the function-call to the 'n3s_initialize_scripts()' function in the '<n3rdskwat-plugin-directory>/js/n3rdskwat-mp3player.js' file.

= Why doesn't WP Ajax Edit Comments work? =

The WP Ajax Edit Comments plugin uses a strange way on the document.ready functionality. This way I can't reproduce the code when a new page is loaded, which is vital for correct plugin functionality. As long as they don't change the way the plugin works, I cannot make the mp3player compatible with WP Ajax Edit Comments.

== Screenshots ==

1. mp3player in default settings
2. mp3player customized to match the blog
3. playlist disabled

== Changelog ==

= 1.2.1 =
* Minor layout bug fixed

= 1.2.0 =
* Rebuild the javascript for the plugin to be more efficient and easier to read
* Fixed some minor bugs concerning layout and functionality

= 1.1.17 =
* Added SWFAddress functionality
* Fixed a problem where the playlist was not loaded anymore

= 1.1.16 =
* Added some fixes for the form handling, including (basic) validationEngine support

= 1.1.15 =
* Changed the way we handle links to a more transparent form. This way mp3-links will still contain the possibility to download the file by right-clicking.

= 1.1.14 =
* Fixed a bug with the playlist not showing
* Added support for lower versions of PHP (json encoding)

= 1.1.13 =
* Modified path-finding functionality to work in more server environments

= 1.1.12 =
* Changed current-page-cookie to be removed when the browser is closed
* Added functionality to stop catching links that have click or mousedown events on them
* Fixed a bug that removes 'target' attribute from links that aren't caught

= 1.1.11 =
* Added support for NextGen-Gallery
* Optimized code to work better with jQuery and other plugins

= 1.1.10 =
* Small fixes regarding loading mp3-links with full domain urls (on the same domain as the player)

= 1.1.9 =
* Added extra checks for forms that are posted off-domain (like payola)
* Fixed a minor bug for staying at the current page after reloading

= 1.1.8 =
* Sorry something went wrong updating to SVN

= 1.1.7 =
* Updated the flash file to prevent loading when autoplay is disabled
* Changed the 'Path to search for mp3s' to be a dropdown selection of all the directories available
* When you have links on your website to mp3s in the playlist, they will be played when they are clicked

= 1.1.6 =
* Added cookies to maintain the page you are looking at even at hard reload

= 1.1.5 =
* Updated the SWF file to send changes to the playlist
* Updated the SWF for loading/skipping to not-loaded parts
* Updated the playlist code to highlight the currently playing song
* Modified the playlist to accept flash input for ID3 updates

= 1.0.0 =
* Changed the style for the mp3player to position: fixed which keeps it from shaking all over when scrolled

== Upgrade Notice ==

= 1.1.17 =
SWFAddress functionality added, this adds link-ability for external sources

= 1.1.15 =
Small cosmetic / functional update for the link-hack

= 1.1.14 =
Now supporting lower versions of PHP aswel. This fixes a bug when the player works but the playlist remains empty.

= 1.1.13 =
Fixing a bug for blogs that are hosted on servers with more blogs on them. The code now only searches in the current blog directory-pool and no longer search all sites on the server.

= 1.1.11 =
Added Nextgen-Gallery to the supported plugins list

= 1.1.10 =
Fixes in the cookies for taking you back to the currently viewing page while reloading the browser in use with inline linking of mp3s.

= 1.1.7 =
This upgrade saved you a lot of bandwidth if you have autoplay off, the mp3 only gets loaded when somebody starts playing it.

= 1.1.6 =
Cookies need to be enabled to maintain the best browsing experience.

= 1.1.5 =
Playlist has been revised and updates on ID3 tag information, the current song is now highlighted in specified colors.