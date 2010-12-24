<?php
$location = $options_page; // Form Action URI

function get_blogpath() {
	$document_root = "";
	
	$max_search_levels = 10;
	$search_level = 0;
	while(!is_file($document_root."wp-blog-header.php")) {
		$document_root = "../" . $document_root;
		
		$search_level++;
		if($search_level > $max_search_levels) {
			die();
		}
	}
	
	return realpath($document_root);
}

$border_styles = array('none', 'solid', 'dashed', 'dotted');
function validate_color($input, $default) {
	// hex check
	$hex = preg_match('/^#([0-9A-Fa-f]){6}$/', $input);
	$named = preg_match('/^[a-zA-Z]*$/', $input);
	
	return ($hex || $named) ? $input : $default;
}

function validate_style($input, $default = 'solid') {
	global $border_styles;
	return (in_array($input, $border_styles)) ? $input : $default;
}

$transitions = array('none', 'flash');
function validate_transition($input, $default = 'none') {
	global $transitions;
	return (in_array($input, $transitions)) ? $input : $default;
}

function prepare_uris($exclude) {
	if( empty($exclude) ) return '';
	
	$exclude = strip_tags($exclude);
	if( strpos($exclude, ',') !== false ) {
		$items = explode(',', $exclude);
	} else {
		$items = array($exclude);
	}
	
	$baseURI = get_bloginfo('wpurl');
	$baseURI .= '/';
	$baseURI = str_replace('//', '/', $baseURI);
	
	foreach( $items as $index => $item ) {
		$item = str_replace($baseURI, '/', $item);
		$item = str_replace('/#/', '/', $item);
		$item = ( substr($item, 0, 1) != '/' ) ? '/' . $item : $item;
		$items[$index] = $item;
	}
	
	$output = implode(',', $items);
	return $output;
}

function dir_listing(&$dirlist, $dir, $root) {
	//global $dirlist;
	
	if(!is_array($dirlist)) {
		$dirlist = array();
	}
	
	// exclude the plugin_dir for reading mp3s
	$plugin_dir = ($wp_version >= '2.6.0') ? WP_PLUGIN_DIR : $root . "/wp-content/plugins";
	
	$handle = opendir($dir);
	while(false !== ($file = readdir($handle))) {
		clearstatcache();
		if (filetype($dir."/".$file) === 'dir' && $file != "." && $file != "..") {
			
			// exclude the wp-includes and wp-admin for reading
			if($file != "wp-includes" && $file != "wp-admin" && $dir."/".$file != $plugin_dir) {
				
				// add the found directory to the list
				array_push($dirlist, str_replace($root, "", $dir."/".$file));
				
				// check for sub-directories
				dir_listing($dirlist, $dir."/".$file, $root);
			}
		}
	}	
}

/* Check for admin Options submission and update options*/
if ('process' == $_POST['stage']) {
   update_option('n3rdskwat_autoplay', ($_POST['autoplay'] == "1")?1:0);
   update_option('n3rdskwat_randomize', ($_POST['randomize'] == "1")?1:0);
	update_option('n3rdskwat_repeatall', ($_POST['repeatall'] == "1")?1:0);
	
	update_option('n3rdskwat_playlist', ($_POST['playlist'] == "1")?1:0);
	
	update_option('n3rdskwat_exclude_uris', prepare_uris($_POST['exclude']));
	
	update_option('n3rdskwat_vertical_position', $_POST['vertical_position']);
	update_option('n3rdskwat_horizontal_position', $_POST['horizontal_position']);
	
	update_option('n3rdskwat_border_width', intval($_POST['border_width']));
	update_option('n3rdskwat_border_style', validate_style($_POST['border_style']));
	update_option('n3rdskwat_border_color', validate_color($_POST['border_color'], 'black'));
	
	update_option('n3rdskwat_playlist_text', validate_color($_POST['playlist_color'], 'black'));
	update_option('n3rdskwat_playlist_border_width', intval($_POST['playlist_width']));
	update_option('n3rdskwat_playlist_hover', validate_color($_POST['playlist_hover'], 'black'));
	
	update_option('n3rdskwat_transition', validate_transition($_POST['transition']));
	
	update_option('n3rdskwat_playlist_active_color', validate_color($_POST['playlist_active_color'], 'white'));
	update_option('n3rdskwat_playlist_active_background', validate_color($_POST['playlist_active_background'], 'black'));
	
	update_option('n3rdskwat_background', validate_color($_POST['background'], 'white'));
	
	update_option('n3rdskwat_mp3path', $_POST['path']);
	update_option('n3rdskwat_search_recusive', ($_POST['recursive'] == "1")?1:0);
	
	$opacity = intval($_POST['opacity']);
	$opacity = min(100, $opacity);
	
	update_option('n3rdskwat_opacity', $opacity);
}

/*Get options for form fields*/
$autoplay = get_option('n3rdskwat_autoplay');
$randomize = get_option('n3rdskwat_randomize');
$repeatall = get_option('n3rdskwat_repeatall');

$playlist = get_option('n3rdskwat_playlist');

$horizontal_position = get_option('n3rdskwat_horizontal_position');
$vertical_position = get_option('n3rdskwat_vertical_position');

$border_width = get_option('n3rdskwat_border_width');
$border_style = get_option('n3rdskwat_border_style');
$border_color = get_option('n3rdskwat_border_color');

$playlist_color = get_option('n3rdskwat_playlist_text');
$playlist_width = get_option('n3rdskwat_playlist_border_width');
$playlist_hover = get_option('n3rdskwat_playlist_hover');

$background = get_option('n3rdskwat_background');

$opacity = get_option('n3rdskwat_opacity');
$transition = get_option('n3rdskwat_transition');

$path = get_option('n3rdskwat_mp3path');
$recursive = get_option('n3rdskwat_search_recusive');

$playlist_active_color = get_option('n3rdskwat_playlist_active_color');
$playlist_active_background = get_option('n3rdskwat_playlist_active_background');

$exclude = get_option('n3rdskwat_exclude_uris');


$blog_root = get_blogpath();
dir_listing($dirlist, $blog_root, $blog_root);

?>

<div class="wrap">
  <h2><?php _e('n3rdskwat - mp3player options', 'n3rdskwat_mp3player') ?></h2>
  <form name="form1" method="post" action="<?php echo $location ?>&amp;updated=true">
	<input type="hidden" name="stage" value="process" />
	<table width="100%" cellspacing="2" cellpadding="0" class="form-table">
	<tr>
		<td colspan="3"><?php _e('If you want to add mp3\'s to the playlist, just upload them anywhere on your site, the mp3 script searches for all mp3\'s inside your website.', 'n3rdskwat_mp3player'); ?></td>
	</tr>
	</table>
	
	<table width="100%" class="form-table">
	<tr>
		<td width="50%" valign="top">
			
         <fieldset style="border: 1px outset black; padding: 5px; margin-bottom: 10px;">
         <legend style="font-size: 110%; font-weight: bold;">&nbsp;<?php _e('Player settings', 'n3rdskwat_mp3player') ?>&nbsp;</legend>
			<table width="100%">
			<tr valign="baseline">
				<td width="10"><input type="checkbox" name="autoplay" value="1" id="autoplay"<?php echo ($autoplay)?" checked":"" ?> /></td>
				<td><label for="autoplay"><?php _e('Autoplay mp3\'s', 'n3rdskwat_mp3player') ?></label></td>
			</tr>
			<tr valign="baseline">
				<td width="10"><input type="checkbox" name="randomize" value="1" id="randomize"<?php echo ($randomize)?" checked":"" ?> /></td>
				<td><label for="randomize"><?php _e('Randomize mp3\'s', 'n3rdskwat_mp3player') ?></label></td>
			</tr>
			<tr valign="baseline">
				<td width="10"><input type="checkbox" name="repeatall" value="1" id="repeatall"<?php echo ($repeatall)?" checked":"" ?> /></td>
				<td><label for="repeatall"><?php _e('Repeat all', 'n3rdskwat_mp3player') ?></label></td>
			</tr>
			<tr valign="baseline">
				<td width="10"><input type="checkbox" name="playlist" value="1" id="playlist"<?php echo ($playlist)?" checked":"" ?> /></td>
				<td><label for="playlist"><?php _e('Show playlist', 'n3rdskwat_mp3player') ?></label></td>
			</tr>
			</table>
			</fieldset>
         
         <fieldset style="border: 1px outset black; padding: 5px; margin-bottom: 10px;">
         <legend style="font-size: 110%; font-weight: bold;">&nbsp;<?php _e('Mp3 collection', 'n3rdskwat_player') ?>&nbsp;</legend>
			<table width="100%" cellspacing="0" cellpadding="0">
			<tr valign="baseline">
				<td width="33%"><?php _e('Path to search for mp3\'s', 'n3rdskwat_mp3player') ?></td>
				<td>
					<select name="path" style="width: 200px;">
					<option value="/"<?php echo (($path == "/")?" SELECTED":"") ?>><?php _e('Entire blog', 'n3rdskwat_mp3player') ?></option>
<?php
foreach($dirlist as $dirname) {
	$selected = ($path == $dirname)?" selected='selected'":"";
	echo "<option value=\"".$dirname."\"".$selected.">".$dirname."</option>\n";
}
?>
					</select>	
				</td>
			</tr>
			</table>
			
			<table width="100%">
			<tr valign="baseline">
				<td width="10"><input type="checkbox" name="recursive" value="1" id="recursive"<?php echo ($recursive)?" checked":"" ?> /></td>
				<td><label for="recursive"><?php _e('Recursive searching for mp3\'s', 'n3rdskwat_mp3player') ?></label></td>
			</tr>
			</table>
         </fieldset>
			
         <fieldset style="border: 1px outset black; padding: 5px; margin-bottom: 10px;">
         <legend style="font-size: 110%; font-weight: bold;">&nbsp;<?php _e('Page transitions', 'n3rdskwat_player') ?>&nbsp;</legend>
			<table width="100%" cellspacing="0" cellpadding="0">
			<tr valign="baseline">
				<td width="33%"><?php _e('Transitionstyle', 'n3rdskwat_mp3player') ?></td>
				<td>
					<select name="transition" style="width: 200px;">
					
<?php
foreach($transitions as $transition_option) {
	$selected = ($transition == $transition_option)?" selected='selected'":"";
	echo "<option value=\"".$transition_option."\"".$selected.">".$transition_option."</option>\n";
}
?>
					</select>	
				</td>
			</tr>
			</table>
         </fieldset>
         
         <fieldset style="border: 1px outset black; padding: 5px; margin-bottom: 10px;">
         <legend style="font-size: 110%; font-weight: bold;">&nbsp;<?php _e('Excluded URIs', 'n3rdskwat_player') ?>&nbsp;</legend>
			<table width="100%" cellspacing="0" cellpadding="0">
			<tr valign="baseline">
				<td width="33%" valign="top"><?php _e('Enter the URIs (seperated by commas) of pages where the player should be paused on entry', 'n3rdskwat_mp3player') ?></td>
				<td><textarea name="exclude" style="width: 200px; height: 100px;" cols="30" rows="10"><?php echo $exclude ?></textarea></td>
			</tr>
			</table>
         </fieldset>

		</td>
		<td width="50%" valign="top">
			
         <fieldset style="border: 1px outset black; padding: 5px; margin-bottom: 10px;">
         <legend style="font-size: 110%; font-weight: bold;">&nbsp;<?php _e('Layout settings', 'n3rdskwat_mp3player') ?>&nbsp;</legend>
			<table width="100%" >
			<tr valign="baseline">
				<td width="130"><?php _e('Position on screen', 'n3rdskwat_mp3player') ?></td>
				<td>
					<select name="vertical_position">
						<option value="top"<?php echo ($vertical_position == "top")?" SELECTED":"" ?>><?php _e('top', 'n3rdskwat_mp3player') ?></option>
						<option value="bottom"<?php echo ($vertical_position == "bottom")?" SELECTED":"" ?>><?php _e('bottom', 'n3rdskwat_mp3player') ?></option>
					</select>
					<select name="horizontal_position">
						<option value="left"<?php echo ($horizontal_position == "left")?" SELECTED":"" ?>><?php _e('left', 'n3rdskwat_mp3player') ?></option>
						<option value="center"<?php echo ($horizontal_position == "center")?" SELECTED":"" ?>><?php _e('center', 'n3rdskwat_mp3player') ?></option>
						<option value="right"<?php echo ($horizontal_position == "right")?" SELECTED":"" ?>><?php _e('right', 'n3rdskwat_mp3player') ?></option>
					</select>
				</td>
			</tr>
			<tr valign="baseline">
				<td><?php _e('Background color', 'n3rdskwat_mp3player') ?></td>
				<td>
					<input type="text" name="background" value="<?php echo $background ?>" maxlength="32" size="12" />
				</td>
			</tr>
			<tr valign="baseline">
				<td><?php _e('Opacity', 'n3rdskwat_mp3player') ?></td>
				<td>
					<input type="text" name="opacity" value="<?php echo $opacity ?>" maxlength="3" size="4" dir="RTL" />%
				</td>
			</tr>
			<tr valign="baseline">
				<td><?php _e('Border style', 'n3rdskwat_mp3player') ?></td>
				<td>
					<select name="border_style">
					<?php
					foreach($border_styles as $style) {
						echo "<option value=\"$style\"".(($style == $border_style)?" SELECTED":"").">$style</option>\n";
					}
					?>
					</select>
					<input type="text" name="border_color" value="<?php echo $border_color ?>" maxlength="32" size="12" />
				</td>
			</tr>
			
			<tr>
				<td colspan="2"><h3><?php _e('Playerbar', 'n3rdskwat_player') ?></h3></td>
			</tr>
			<tr valign="baseline">
				<td><?php _e('Border size', 'n3rdskwat_mp3player') ?></td>
				<td>
					<input type="text" name="border_width" value="<?php echo $border_width ?>" maxlength="3" size="4" dir="RTL" />px 
				</td>
			</tr>
			
			<tr>
				<td colspan="2"><h3><?php _e('Playlist', 'n3rdskwat_player') ?></h3></td>
			</tr>
			<tr valign="baseline">
				<td><?php _e('Border size', 'n3rdskwat_mp3player') ?></td>
				<td>
					<input type="text" name="playlist_width" value="<?php echo $playlist_width ?>" maxlength="3" size="4" dir="RTL" />px 
				</td>
			</tr>
			<tr valign="baseline">
				<td><?php _e('Text color', 'n3rdskwat_mp3player') ?></td>
				<td>
					<input type="text" name="playlist_color" value="<?php echo $playlist_color ?>" maxlength="32" size="12" />
				</td>
			</tr>
			<tr valign="baseline">
				<td><?php _e('Hover color', 'n3rdskwat_mp3player') ?></td>
				<td>
					<input type="text" name="playlist_hover" value="<?php echo $playlist_hover ?>" maxlength="32" size="12" />
				</td>
			</tr>
			<tr valign="baseline">
				<td><?php _e('Active song text', 'n3rdskwat_mp3player') ?></td>
				<td>
					<input type="text" name="playlist_active_color" value="<?php echo $playlist_active_color ?>" maxlength="32" size="12" />
				</td>
			</tr>
			<tr valign="baseline">
				<td><?php _e('Active song background', 'n3rdskwat_mp3player') ?></td>
				<td>
					<input type="text" name="playlist_active_background" value="<?php echo $playlist_active_background ?>" maxlength="32" size="12" />
				</td>
			</tr>
			</table>
         </fieldset>
		
		</td>
	</tr>
	</table>
	
	<p class="submit">
	<input type="submit" name="Submit" value="<?php _e('Save Changes', 'n3rdskwat_mp3player') ?>" />
	</p>
  </form>
</div>