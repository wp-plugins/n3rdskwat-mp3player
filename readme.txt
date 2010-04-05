=== n3rdskwat-mp3player ===
Contributors: n3rdskwat-jmf
Donate link: http://www.n3rdskwat.com/
Tags: mp3, flash, ajax, playlist, adjustable, customizable
Requires at least: 2.6.0
Tested up to: 2.9.2
Stable tag: 1.1.6

Places a mp3 player at the bottom of the screen. Ajax-izes the whole site so the music will go on while browsing the blog.

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
* Converts your blog to a AJAX controlled environment
* Scans your whole website or specified directory for mp3's to put in the playlist
* Automatically start playing on entering the blog
* Randomize mp3 playback
* Repeat-all after the entire playlist has been played
* Displaying of the playlist
* Where to search for mp3's
* Easily customizable positioning and layout properties

== Installation ==

1. Activate the plugin through the 'Plugins' menu in WordPress
2. If you have other plugins, be sure to test thoroughly after loading a new page on your blog, see the F.A.Q. for more information.

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

= 1.1.6 =
Cookies need to be enabled to maintain the best browsing experience.
= 1.1.5 =
Playlist has been revised and updates on ID3 tag information, the current song is now highlighted in specified colors.