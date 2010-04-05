<?php
$location = $options_page; // Form Action URI


$border_styles = array('none', 'solid', 'dashed', 'dotted');

function validate_color($input, $default) {
	// hex check
	$hex = preg_match('/^#([0-9A-Fa-f]){6}$/', $input);
	$named = preg_match('/^[a-zA-Z]*$/', $input);
	
	if(!$hex && !$named) {
		return $default;
	}	
	return $input;
}

function validate_style($input, $default = 'solid') {
	global $border_styles;
	
	if(in_array($input, $border_styles)) {
		return $input;
	}
	
	return $default;
}

function validate_path($input) {
	
	while($input != urldecode($input)) {
		$input = urldecode($input);
	}
	
	$input = trim($input);
	if($input == "") {
		return "/";
	}
	
	$chars_allowed = preg_match('/^[a-zA-Z0-9\/%\s]*$/', $input);
	
	if($chars_allowed) {
		return $input;
	}
	
	return "/";
}

/* Check for admin Options submission and update options*/
if ('process' == $_POST['stage']) {
   update_option('n3rdskwat_autoplay', ($_POST['autoplay'] == "1")?1:0);
   update_option('n3rdskwat_randomize', ($_POST['randomize'] == "1")?1:0);
	update_option('n3rdskwat_repeatall', ($_POST['repeatall'] == "1")?1:0);
	
	update_option('n3rdskwat_playlist', ($_POST['playlist'] == "1")?1:0);
	
	update_option('n3rdskwat_vertical_position', $_POST['vertical_position']);
	update_option('n3rdskwat_horizontal_position', $_POST['horizontal_position']);
	
	update_option('n3rdskwat_border_width', intval($_POST['border_width']));
	update_option('n3rdskwat_border_style', validate_style($_POST['border_style']));
	update_option('n3rdskwat_border_color', validate_color($_POST['border_color'], 'black'));
	
	update_option('n3rdskwat_playlist_text', validate_color($_POST['playlist_color'], 'black'));
	update_option('n3rdskwat_playlist_border_width', intval($_POST['playlist_width']));
	update_option('n3rdskwat_playlist_hover', validate_color($_POST['playlist_hover'], 'black'));
	
	
	update_option('n3rdskwat_playlist_active_color', validate_color($_POST['playlist_active_color'], 'white'));
	update_option('n3rdskwat_playlist_active_background', validate_color($_POST['playlist_active_background'], 'black'));
	
	update_option('n3rdskwat_background', validate_color($_POST['background'], 'white'));
	
	update_option('n3rdskwat_mp3path', validate_path($_POST['path']));
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

$path = get_option('n3rdskwat_mp3path');
$recursive = get_option('n3rdskwat_search_recusive');

$playlist_active_color = get_option('n3rdskwat_playlist_active_color');
$playlist_active_background = get_option('n3rdskwat_playlist_active_background');

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
			
			<h3><?php _e('Player settings', 'n3rdskwat_mp3player') ?></h3>
			<table width="100%">
			<tr valign="baseline">
				<td width="10"><input type="checkbox" name="autoplay" value="1" id="autoplay"<?= ($autoplay)?" checked":"" ?> /></td>
				<td><label for="autoplay"><?php _e('Autoplay mp3\'s', 'n3rdskwat_mp3player') ?></label></td>
			</tr>
			<tr valign="baseline">
				<td width="10"><input type="checkbox" name="randomize" value="1" id="randomize"<?= ($randomize)?" checked":"" ?> /></td>
				<td><label for="randomize"><?php _e('Randomize mp3\'s', 'n3rdskwat_mp3player') ?></label></td>
			</tr>
			<tr valign="baseline">
				<td width="10"><input type="checkbox" name="repeatall" value="1" id="repeatall"<?= ($repeatall)?" checked":"" ?> /></td>
				<td><label for="repeatall"><?php _e('Repeat all', 'n3rdskwat_mp3player') ?></label></td>
			</tr>
			<tr valign="baseline">
				<td width="10"><input type="checkbox" name="playlist" value="1" id="playlist"<?= ($playlist)?" checked":"" ?> /></td>
				<td><label for="playlist"><?php _e('Show playlist', 'n3rdskwat_mp3player') ?></label></td>
			</tr>
			</table>
			
			<h3><?php _e('Mp3 collection', 'n3rdskwat_player') ?></h3>
			<table width="100%" cellspacing="0" cellpadding="0">
			<tr valign="baseline">
				<td width="33%"><?php _e('Path to search for mp3\'s', 'n3rdskwat_mp3player') ?></td>
				<td><input type="text" name="path" value="<?= $path ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><small><?php _e('Leave blank to scan the entire website for mp3\'s', 'n3rdskwat_mp3player') ?></small></td>
			</tr>
			</table>
			
			<table width="100%">
			<tr valign="baseline">
				<td width="10"><input type="checkbox" name="recursive" value="1" id="recursive"<?= ($recursive)?" checked":"" ?> /></td>
				<td><label for="recursive"><?php _e('Recursive searching for mp3\'s', 'n3rdskwat_mp3player') ?></label></td>
			</tr>
			</table>

		</td>
		<td width="50%" valign="top">
			
			<h3><?php _e('Layout settings', 'n3rdskwat_mp3player') ?></h3>
			<table width="100%" >
			<tr valign="baseline">
				<td width="130"><?php _e('Position on screen', 'n3rdskwat_mp3player') ?></td>
				<td>
					<select name="vertical_position">
						<option value="top"<?= ($vertical_position == "top")?" SELECTED":"" ?>><?php _e('top', 'n3rdskwat_mp3player') ?></option>
						<option value="bottom"<?= ($vertical_position == "bottom")?" SELECTED":"" ?>><?php _e('bottom', 'n3rdskwat_mp3player') ?></option>
					</select>
					<select name="horizontal_position">
						<option value="left"<?= ($horizontal_position == "left")?" SELECTED":"" ?>><?php _e('left', 'n3rdskwat_mp3player') ?></option>
						<option value="center"<?= ($horizontal_position == "center")?" SELECTED":"" ?>><?php _e('center', 'n3rdskwat_mp3player') ?></option>
						<option value="right"<?= ($horizontal_position == "right")?" SELECTED":"" ?>><?php _e('right', 'n3rdskwat_mp3player') ?></option>
					</select>
				</td>
			</tr>
			<tr valign="baseline">
				<td><?php _e('Background color', 'n3rdskwat_mp3player') ?></td>
				<td>
					<input type="text" name="background" value="<?= $background ?>" maxlength="32" size="12" />
				</td>
			</tr>
			<tr valign="baseline">
				<td><?php _e('Opacity', 'n3rdskwat_mp3player') ?></td>
				<td>
					<input type="text" name="opacity" value="<?= $opacity ?>" maxlength="3" size="4" dir="RTL" />%
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
					
					<input type="text" name="border_color" value="<?= $border_color ?>" maxlength="32" size="12" />
				</td>
			</tr>
			
			<tr>
				<td colspan="2"><h3><?php _e('Playerbar', 'n3rdskwat_player') ?></h3></td>
			</tr>
			<tr valign="baseline">
				<td><?php _e('Border size', 'n3rdskwat_mp3player') ?></td>
				<td>
					<input type="text" name="border_width" value="<?= $border_width ?>" maxlength="3" size="4" dir="RTL" />px 
				</td>
			</tr>
			
			<tr>
				<td colspan="2"><h3><?php _e('Playlist', 'n3rdskwat_player') ?></h3></td>
			</tr>
			<tr valign="baseline">
				<td><?php _e('Border size', 'n3rdskwat_mp3player') ?></td>
				<td>
					<input type="text" name="playlist_width" value="<?= $playlist_width ?>" maxlength="3" size="4" dir="RTL" />px 
				</td>
			</tr>
			<tr valign="baseline">
				<td><?php _e('Text color', 'n3rdskwat_mp3player') ?></td>
				<td>
					<input type="text" name="playlist_color" value="<?= $playlist_color ?>" maxlength="32" size="12" />
				</td>
			</tr>
			<tr valign="baseline">
				<td><?php _e('Hover color', 'n3rdskwat_mp3player') ?></td>
				<td>
					<input type="text" name="playlist_hover" value="<?= $playlist_hover ?>" maxlength="32" size="12" />
				</td>
			</tr>
			<tr valign="baseline">
				<td><?php _e('Active song text', 'n3rdskwat_mp3player') ?></td>
				<td>
					<input type="text" name="playlist_active_color" value="<?= $playlist_active_color ?>" maxlength="32" size="12" />
				</td>
			</tr>
			<tr valign="baseline">
				<td><?php _e('Active song background', 'n3rdskwat_mp3player') ?></td>
				<td>
					<input type="text" name="playlist_active_background" value="<?= $playlist_active_background ?>" maxlength="32" size="12" />
				</td>
			</tr>
			
			</table>
		
		</td>
	</tr>
	</table>
	
	<p class="submit">
	<input type="submit" name="Submit" value="<?php _e('Save Changes', 'n3rdskwat_mp3player') ?>" />
	</p>
  </form>
</div>