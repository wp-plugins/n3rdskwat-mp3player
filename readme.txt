=== n3rdskwat-mp3player ===
Contributors: n3rdskwat-jmf
Donate link: http://www.n3rdskwat.com/code/
Tags: mp3, flash, ajax, playlist, adjustable, customizable
Requires at least: 2.6.0
Tested up to: 2.9.2
Stable tag: 1.1.9

Places a mp3 player at the bottom of the screen. Ajax-izes the whole site so the music will go on while browsing the blog and keeping your SEO structure in tact!

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
* Randomize mp3 playback
* Repeat-all after the entire playlist has been played
* Displaying of the playlist
* Where to search for mp3's
* Easily customizable positioning and layout properties
* Converts your blog to a AJAX controlled environment
* Keeps the SEO (search engine optimization) structure in tact!

== Installation ==

1. Download the plugin through the build-in plugin-search
2. Or download it manually and place it in your plugin directory  
3. Activate the plugin through the 'Plugins' menu in WordPress
4. If you have other plugins, be sure to test thoroughly after loading a new page on your blog, see the F.A.Q. for more information.
5. When other plugins fail to work properly please contact me at the plugin-website so I can build in a solution.

== Frequently Asked Questions ==

= The plugin [x] only works on the first page I load =

That's the only big bug that I haven't completely fixed yet.
When you load a new page on your blog, the mp3player handles the request for you.
This means that scripts depending on the 'page is loaded' functionality need to be activated again.
At this point only the 'lightbox' plugin functionality has been added and tested.

If you have plugins that stop working, please let me know so I can add them to the (temporary) plugin compatibility function.

Or, if you know something about javascript, you can add the function-call to the 'n3s_initialize_scripts()' function in the '<n3rdskwat-plugin-directory>/js/n3rdskwat-mp3player.js' file.

== Screenshots ==

1. mp3player in default settings
2. mp3player customized to match the blog
3. playlist disabled

== Changelog ==

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

= 1.1.7 =
This upgrade saved you a lot of bandwidth if you have autoplay off, the mp3 only gets loaded when somebody starts playing it.

= 1.1.6 =
Cookies need to be enabled to maintain the best browsing experience.

= 1.1.5 =
Playlist has been revised and updates on ID3 tag information, the current song is now highlighted in specified colors.