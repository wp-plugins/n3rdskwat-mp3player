<?php
/*
Plugin Name: n3rdskwat-mp3player
Plugin URI: http://www.n3rdskwat.com/code/
Description: Places an mp3 player at the bottom of the screen. Ajax-izes the whole site so the music will go on without destroying your SEO structure.
Version: 1.3.0
Author: n3rdskwat-jmf
Author URI: http://www.n3rdskwat.com/
License: GPL2

    Copyright 2010  Jip Moors  (email : j.moors@home.nl)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

$version = "1.3.0";

add_option("n3rdskwat_autoplay", "0");
add_option("n3rdskwat_randomize", "0");
add_option("n3rdskwat_repeatall", "0");

add_option("n3rdskwat_horizontal_position", "right");
add_option("n3rdskwat_vertical_position", "bottom");

add_option("n3rdskwat_border_width", "1");
add_option("n3rdskwat_border_style", "solid");
add_option("n3rdskwat_border_color", "black");

add_option("n3rdskwat_background", "white");

add_option("n3rdskwat_playlist", "1");
add_option("n3rdskwat_playlist_text", "black");
add_option("n3rdskwat_playlist_hover", "#efefef");
add_option("n3rdskwat_playlist_border_width", "1");

add_option("n3rdskwat_playlist_active_color", "white");
add_option("n3rdskwat_playlist_active_background", "black");

add_option("n3rdskwat_opacity", "80");

add_option("n3rdskwat_mp3path", "/");
add_option("n3rdskwat_search_recusive", "1");


/* use WP_PLUGIN_URL if version of WP >= 2.6.0. If earlier, use wpurl */
if($wp_version >= '2.6.0') {
	$n3rdskwat_mp3player_plugin_prefix = WP_PLUGIN_URL."/n3rdskwat-mp3player/"; /* plugins dir can be anywhere after WP2.6 */
	$options_page = get_bloginfo('wpurl') . '/wp-admin/admin.php?page=n3rdskwat-mp3player/options.php';
} else {
	$n3rdskwat_mp3player_plugin_prefix = get_bloginfo('siteurl')."/wp-content/plugins/n3rdskwat-mp3player/";
	$options_page = get_bloginfo('siteurl') . '/wp-admin/admin.php?page=n3rdskwat-mp3player/options.php';
}

/* Adds our admin options under "Options" */
function n3rdskwat_flashmp3player_options_page() {
	add_options_page('n3rdskwat - mp3player', 'n3rdskwat mp3player', 10, 'n3rdskwat-mp3player/options.php');
}

function n3rdskwat_mp3player_styles() {
	/* What version of WP is running? */
	global $wp_version;
	global $n3rdskwat_mp3player_plugin_prefix;
	
	/*Get options for form fields*/
	$autoplay 					= get_option('n3rdskwat_autoplay');
	$randomize 					= get_option('n3rdskwat_randomize');
	$repeatall 					= get_option('n3rdskwat_repeatall');
	
	$transition 				= get_option('n3rdskwat_transition');
	
	$horizontal_position 	= get_option('n3rdskwat_horizontal_position');
	$vertical_position 		= get_option('n3rdskwat_vertical_position');
	
	$border_width 				= get_option("n3rdskwat_border_width");
	$border_style 				= get_option("n3rdskwat_border_style", "solid");
	$border_color 				= get_option("n3rdskwat_border_color", "black");

	$background 				= get_option("n3rdskwat_background");
	
	$playlist 					= get_option("n3rdskwat_playlist");
	$playlist_text 			= get_option("n3rdskwat_playlist_text");
	$playlist_hover 			= get_option("n3rdskwat_playlist_hover");
	$playlist_border 			= get_option("n3rdskwat_playlist_border_width");
	
	$playlist_active_color	= get_option("n3rdskwat_playlist_active_color");
	$playlist_active_bg 		= get_option("n3rdskwat_playlist_active_background");
	
	$excludeURIs				= get_option('n3rdskwat_exclude_uris');
	
	$opacity = (1/100)*intval(get_option("n3rdskwat_opacity"));
	
    /* The next line figures out where the javascripts and images and CSS are installed,
    relative to your wordpress server's root: */
	 
	 $excludeURIs = explode(',', $excludeURIs);
	 $excludeURIs = json_encode($excludeURIs);

    /* The xhtml header code needed for lightbox to work: */
	$n3rdskwat_mp3player_script = "
<!-- begin n3rdskwat initialize scripts -->
<script type=\"text/javascript\">
//<![CDATA[
n3rdskwat_mp3player_settings = {
	baseurl:'".get_bloginfo('wpurl')."',
	path:'".$n3rdskwat_mp3player_plugin_prefix."',
	exclude:".$excludeURIs.",
	autoplay:".(($autoplay=='1')?1:0).",
	randomize:".(($randomize=='1')?1:0).",
	repeatall:".(($repeatall=='1')?1:0).",
	position:'$vertical_position $horizontal_position',
	border:'$border_width',
	border_style:'$border_style',
	border_color:'$border_color',
	background:'$background',
	opacity:$opacity,
	transition:'$transition',
	playlist:".(($playlist=='1')?1:0).",
	playlist_text:'$playlist_text',
	playlist_border:'$playlist_border',
	playlist_hover:'$playlist_hover',
	playlist_active_text:'$playlist_active_color',
	playlist_active_background:'$playlist_active_bg'
};
//]]>
</script>
<!-- end n3rdskwat initialize scripts -->\n";
	
	/* Output script as text for our web pages: */
	echo($n3rdskwat_mp3player_script);
}

function n3rdskwat_load_translations() {
	load_plugin_textdomain('n3rdskwat_mp3player', false, 'n3rdskwat-mp3player/lang/');
}

$n3rdskwat_mp3player_style_path = ($n3rdskwat_mp3player_plugin_prefix."css/");

if (!is_admin()) { // if we are *not* viewing an admin page, like writing a post or making a page:
	wp_enqueue_style('n3rdskwat-mp3player', ($n3rdskwat_mp3player_style_path."n3rdskwat-mp3player.css"));
	
	wp_enqueue_script('jquery', ($n3rdskwat_mp3player_plugin_prefix."js/jquery-1.4.4.min.js"));
	wp_enqueue_script('jquery-scrollTo', ($n3rdskwat_mp3player_plugin_prefix."js/jquery.scrollTo.js"));
	wp_enqueue_script('swfaddress', ($n3rdskwat_mp3player_plugin_prefix."js/swfaddress-2.4.min.js"));
	wp_enqueue_script('n3rdskwat-mp3player', ($n3rdskwat_mp3player_plugin_prefix."js/n3rdskwat-mp3player.min.js"));
}

/* load the language files at initialization */
add_action('init', 'n3rdskwat_load_translations');
/* we want to add the above xhtml to the header of our pages: */
add_action('wp_head', 'n3rdskwat_mp3player_styles');
add_action('admin_menu', 'n3rdskwat_flashmp3player_options_page');

// Add a version number to the header (code-idea from nextgen-gallery)
add_action('wp_head', create_function('', 'echo "\n<meta name=\"n3rdskwat-mp3player\" content=\"' . $version . '\" />\n";') );

?>
