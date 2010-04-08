<?php
/*
Plugin Name: n3rdskwat-mp3player
Plugin URI: http://www.n3rdskwat.com/code/
Description: Places an mp3 player at the bottom of the screen. Ajax-izes the whole site so the music will go on without destroying your SEO structure.
Version: 1.1.12
Author: n3rdskwat-jmf
Author URI: http://www.n3rdskwat.com/
License: GPL2

    Copyright 2010  Jip Moors  (email: j . moors [a t] home.nl)

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


/* use WP_PLUGIN_URL if version of WP >= 2.6.0. If earlier, use wp_url */
if($wp_version >= '2.6.0') {
	$n3rdskwat_mp3player_plugin_prefix = WP_PLUGIN_URL."/n3rdskwat-mp3player/"; /* plugins dir can be anywhere after WP2.6 */
} else {
	$n3rdskwat_mp3player_plugin_prefix = get_bloginfo('wpurl')."/wp-content/plugins/n3rdskwat-mp3player/";
}

/* options page (required for saving prefs)*/
$options_page = get_option('siteurl') . '/wp-admin/admin.php?page=n3rdskwat-mp3player/options.php';

/* Adds our admin options under "Options" */
function n3rdskwat_flashmp3player_options_page() {
	add_options_page('n3rdskwat - mp3player', 'n3rdskwat mp3player', 10, 'n3rdskwat-mp3player/options.php');
}

function n3rdskwat_mp3player_styles() {
	global $n3rdskwat_mp3player_plugin_prefix;
	
	/*Get options for form fields*/
	$autoplay = get_option('n3rdskwat_autoplay');
	$randomize = get_option('n3rdskwat_randomize');
	$repeatall = get_option('n3rdskwat_repeatall');
	
	$horizontal_position = get_option('n3rdskwat_horizontal_position');
	$vertical_position = get_option('n3rdskwat_vertical_position');
	
	$border_width = get_option("n3rdskwat_border_width");
	$border_style = get_option("n3rdskwat_border_style", "solid");
	$border_color = get_option("n3rdskwat_border_color", "black");

	$background = get_option("n3rdskwat_background");
	
	$playlist = get_option("n3rdskwat_playlist");
	$playlist_text = get_option("n3rdskwat_playlist_text");
	$playlist_hover = get_option("n3rdskwat_playlist_hover");
	$playlist_border = get_option("n3rdskwat_playlist_border_width");
	
	$playlist_active_color = get_option("n3rdskwat_playlist_active_color");
	$playlist_active_bg = get_option("n3rdskwat_playlist_active_background");
	
	$opacity = (1/100)*intval(get_option("n3rdskwat_opacity"));
	
    /* The next line figures out where the javascripts and images and CSS are installed,
    relative to your wordpress server's root: */
    $n3rdskwat_mp3player_style_path = $n3rdskwat_mp3player_plugin_prefix."css/";

    /* The xhtml header code needed for lightbox to work: */
	$n3rdskwat_mp3player_script = "
<!-- begin n3rdskwat initialize scripts -->
<script type=\"text/javascript\">
//<![CDATA[
document.write('<link rel=\"stylesheet\" href=\"".$n3rdskwat_mp3player_style_path."n3rdskwat-mp3player.css\" type=\"text/css\" media=\"screen\" />');
var n3s_settings = new n3s_settings_object('".$n3rdskwat_mp3player_plugin_prefix."', ".(($autoplay=='1')?1:0).", ".(($randomize=='1')?1:0).", ".(($repeatall=='1')?1:0).", '$vertical_position $horizontal_position', '$border_width', '$border_style', '$border_color', '$background', $opacity, ".(($playlist=='1')?1:0).", '$playlist_text', '$playlist_border', '$playlist_hover', '$playlist_active_color', '$playlist_active_bg');
//]]>
</script>
<!-- end n3rdskwat initialize scripts -->\n";
	
	/* Output script as text for our web pages: */
	echo($n3rdskwat_mp3player_script);
}

if (!is_admin()) { // if we are *not* viewing an admin page, like writing a post or making a page:
	wp_enqueue_script('jquery', ($n3rdskwat_mp3player_plugin_prefix."js/jquery.js"));
	wp_enqueue_script('jquery-scrollTo', ($n3rdskwat_mp3player_plugin_prefix."js/jquery.scrollTo.js"));
	wp_enqueue_script('n3rdskwat-mp3player', ($n3rdskwat_mp3player_plugin_prefix."js/n3rdskwat-mp3player.js"));
}

/* we want to add the above xhtml to the header of our pages: */
add_action('wp_head', 'n3rdskwat_mp3player_styles');
add_action('admin_menu', 'n3rdskwat_flashmp3player_options_page');
?>
