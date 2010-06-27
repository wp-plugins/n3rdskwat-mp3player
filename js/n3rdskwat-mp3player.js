// n3rdskwat - mp3player javascript code

function n3s_initialize_scripts() {
	/*
	 * TODO:
	 *   Find a better way to re-initialize {window.onload} or {document.onload} dependend scripts..
	 *
	 * Alternative:
	 *   Add checks like lightbox to this function...
	 */
	
	// Lightbox support:
	if(typeof initLightbox == 'function') initLightbox();
	
	// NextGen Gallery support:
	if(typeof shutterOnload == 'function') shutterOnload();
}


/*****[ BE AWARE ]*********************************
 **
 **  Modification after this point are at your own risk of breaking functionality
 **
 **/

var n3s_replaced_body = false;
var n3s_showing_error = false;
var n3s_has_scrolled = false;
var n3s_following_url = false;

var n3s_last_url;
var n3s_current_url;

function debug(text) {
	document.body.innerHTML += text;
}

n3s_document_ready = function() {
	if(n3s_replaced_body) return;
	   n3s_replaced_body = true;
	
	n3s_current_url = window.location.toString();
		
	var error_window_code = '<div id="n3s_error"><div id="n3s_error_text"></div><div id="n3s_error_button"><input type="button" value="close" onclick="n3s_close_error();" /></div></div>';
	var playlist_code = (n3s_settings.playlist == '1')?'<div id="n3s_playlist"></div>':'';
	var playerbar_code = '<div id="n3s_mp3player"></div>';
	var body_code = '<div id="n3s_body"></div>';
	
	var playlist_button_code = (n3s_settings.playlist == '1')?'<div id="n3s_playlist_button"><input type="button" value="playlist" onclick="n3s_toggle_playlist();" /></div>':'';
	
	var flash_code = '<OBJECT type="application/x-shockwave-flash" data="'+n3s_settings.path+'/swf/n3rdskwat-mp3player.swf" WIDTH="300" HEIGHT="35" id="n3s_flash"><PARAM NAME=movie VALUE="'+n3s_settings.path+'/swf/n3rdskwat-mp3player.swf"><PARAM NAME=quality VALUE=high><PARAM NAME=wmode VALUE="transparent"><param name="FlashVars" value="randomize='+n3s_settings.randomize+'&autoplay='+n3s_settings.autoplay+'&repeatall='+n3s_settings.repeatall+'&plugin_path='+n3s_settings.path+'" /></OBJECT>';
	
	var html = jQuery(document.body).html();
	jQuery(document.body).html(error_window_code +  playlist_code + playerbar_code + body_code);
	
	if(!n3s_get_current_page()) {
		jQuery('#n3s_body').html(html);
	}
	
	// playlist button on the right -> playlist_button_code first
	// else flash_code first!
	
	jQuery('#n3s_mp3player').css('background', n3s_settings.background);
	
	if(n3s_settings.playlist == 1) {
		jQuery('#n3s_playlist').css('background', n3s_settings.background);
	}
	
	if(n3s_settings.position.indexOf('top') > -1) {
		jQuery('#n3s_mp3player').css('border-top', '0px');
		jQuery('#n3s_mp3player').css('border-bottom', n3s_settings.border + 'px ' + n3s_settings.border_style + ' ' + n3s_settings.border_color);
		jQuery('#n3s_mp3player').css('bottom');
		jQuery('#n3s_mp3player').css('top', '0px');
		
		if(n3s_settings.playlist == 1) {
			jQuery('#n3s_playlist').css('border', n3s_settings.playlist_border + 'px ' + n3s_settings.border_style + ' ' + n3s_settings.border_color);
			jQuery('#n3s_playlist').css('border-top', '0px');
			
			jQuery('#n3s_playlist').css('bottom');
			jQuery('#n3s_playlist').css('top', '35px');
		}
		
		jQuery('#n3s_body').css('margin-bottom', '0px');
		jQuery('#n3s_body').css('margin-top', '50px');
	} else {
		jQuery('#n3s_mp3player').css('border-top', n3s_settings.border + 'px ' + n3s_settings.border_style + ' ' + n3s_settings.border_color);
		jQuery('#n3s_mp3player').css('border-bottom', '0px');
		
		if(n3s_settings.playlist == 1) {
			jQuery('#n3s_playlist').css('border', n3s_settings.playlist_border + 'px ' + n3s_settings.border_style + ' ' + n3s_settings.border_color);
			jQuery('#n3s_playlist').css('border-bottom', '0px');
		}
		
		jQuery('#n3s_body').css('margin-bottom', '50px');
		jQuery('#n3s_body').css('margin-top', '0px');
	}
	
	var player_html = flash_code + playlist_button_code;
	// because of the float:right, the flash and button need to be flipped
	if(n3s_settings.position.indexOf('left') == -1) {
		player_html = playlist_button_code + flash_code;
	}
	
	// add the divs to the player-holder
	jQuery('#n3s_mp3player').html(player_html);
	
	// apply positioning of the flash
	jQuery('#n3s_flash').css('position', 'relative');
	if(n3s_settings.position.indexOf('left') > -1) {
		jQuery('#n3s_flash').css('float', 'left');
		jQuery('#n3s_flash').css('margin-left', '5px');
	} else if(n3s_settings.position.indexOf('center') > 1) {
		jQuery('#n3s_flash').css('position', 'absolute');
		jQuery('#n3s_flash').css('left', '50%');
		jQuery('#n3s_flash').css('margin-left', ((n3s_settings.playlist == '1')?'-190':'-150') + 'px');
		jQuery('#n3s_flash').css('margin-right', '5px');
	} else {
		jQuery('#n3s_flash').css('float', 'right');
		jQuery('#n3s_flash').css('margin-right', '5px');
	}
	
	// apply positioning of the playlist button (if enabled)
	if(n3s_settings.playlist == 1) {
		jQuery('#n3s_playlist_button').css('position', 'relative');
		if(n3s_settings.position.indexOf('left') > -1) {
			jQuery('#n3s_playlist').css('left', '5px');
			jQuery('#n3s_playlist_button').css('float', 'left');
			jQuery('#n3s_playlist_button').css('margin-left', '10px');
		} else if(n3s_settings.position.indexOf('center') > 1) {
			jQuery('#n3s_playlist').css('left', '50%');
			jQuery('#n3s_playlist').css('margin-left', '-183px');
			jQuery('#n3s_playlist_button').css('position', 'absolute');
			jQuery('#n3s_playlist_button').css('left', '50%');
			jQuery('#n3s_playlist_button').css('margin-left', '120px');
		} else {
			jQuery('#n3s_playlist').css('right', '5px');
			jQuery('#n3s_playlist_button').css('float', 'right');
			jQuery('#n3s_playlist_button').css('margin-right', '10px');
		}
	}
	
	// try to access the flash, if this fails it's no use showing the playlist
	var flash = n3s_get_flash_movie('n3s_flash');
	if(!flash) {
		if(n3s_settings.playlist == 1) {
			jQuery('#n3s_playlist_button :input').attr('disabled', 'disabled');
		} else {
			n3s_settings.playlist == 0;
		}
	}
	
	// apply opacity to the player and playlist
	jQuery('#n3s_mp3player').fadeTo(0, n3s_settings.opacity);
	jQuery('#n3s_playlist').fadeTo(0, n3s_settings.opacity);
	
	jQuery('#n3s_playlist').hide();
	
	// change all links + forms to be ajax-controlled
	n3s_replace_links();
	n3s_replace_forms();
	
	// scroll to the top
	try {
		jQuery(window).scrollTo(0, 0);
	} catch(e) {}
}

jQuery(document).ready(n3s_document_ready);


// toggle showing the playlist
var n3s_playlist_shown = false;
function n3s_toggle_playlist() {
	if(n3s_playlist_shown) {
		jQuery('#n3s_playlist').hide();
	} else {
		n3s_populate_playlist();
		jQuery('#n3s_playlist').show();
		try {
			jQuery('#n3s_playlist').scrollTo(jQuery('#n3s_playlist_item_' + n3s_playlist_playing_index));
		} catch(e) {}
	}
	
	n3s_playlist_shown = !n3s_playlist_shown;
}

var n3s_currently_playing = '';
var n3s_playlist_playing_index = -1;
function n3s_highlight_playlist_item(path) {
	if(n3s_settings.playlist == 0) return;
	
	if(n3s_playlist_populated == 0) {
		n3s_currently_playing = path;
		return;
	}
	
	n3s_set_playlist_items_style();
	
	for(var i = 0; i < n3s_playlist.length; i++) {
		if(n3s_playlist[i].dir + '/' + n3s_playlist[i].filename == path) {
			n3s_playlist_playing_index = i;
			
			jQuery('#n3s_playlist_item_' + i).css('color', n3s_settings.playlist_active_text);
			jQuery('#n3s_playlist_item_' + i).css('background', n3s_settings.playlist_active_background);
			jQuery('#n3s_playlist_item_' + i).unbind('mouseenter mouseleave');
			return;
		}
	}
}

// recieve ID3 tag from flash:
var n3s_playlist;
var n3s_playlist_populated = 0;

var n3s_id3_holder = new Array();
function n3s_set_playlist_item(path, title) {
	if(n3s_settings.playlist == 0)  return;
	
	if(n3s_playlist_populated == 0) {
		// update to the n3s_id3_holder
		
		for(var i = 0; i < n3s_id3_holder.length; i++) {
			if(n3s_id3_holder[i].path == path) {
				n3s_id3_holder[i].title = title;
				return;
			}
		}
		
		var song = n3s_id3_holder[n3s_id3_holder.length] = new Object();
		song.path = path;
		song.title = title;
	} else {
		// update to the n3s_playlist
		// update text in '#n3s_playlist_item_<index>'
		for(var i = 0; i < n3s_playlist.length; i++) {
			if(n3s_playlist[i].dir + '/' + n3s_playlist[i].filename == path) {
				n3s_playlist[i].title = title;
				jQuery('#n3s_playlist_item_'+i).text(title);
			}
		}
	}
}

function n3s_update_playlist(index) {
	for(var i = 0; i < n3s_id3_holder.length; i++) {
		if(n3s_playlist[index].dir + '/' + n3s_playlist[index].filename == n3s_id3_holder[i].path) {
			n3s_playlist[index].title = n3s_id3_holder[i].title;
		}
	}
}

// tell flash to load the specified item from the playlist
function n3s_load_item(path) {
	path = unescape(path);
	var flash = n3s_get_flash_movie('n3s_flash');
	try {
		flash.playSong(path);
	} catch(e) {
		// something went wrong with fetching the flash...
	}
}

function n3s_populate_playlist() {
	var date = new Date();
	
	// only once populate the playlist:
	if(n3s_playlist_populated > 0) return;
		n3s_playlist_populated = date.getTime();
	
	jQuery('#n3s_playlist_button :input').attr('disabled', 'disabled');
	
	jQuery('#n3s_playlist').css('height', '200px');
	jQuery('#n3s_playlist').html('<h3>loading...</h3');
	
	jQuery.ajax({
		url: n3s_settings.path + 'n3rdskwat-mp3player-list.php?type=json',
		type: 'GET',
		dataType: 'html',
		success: function(data) {
			n3s_playlist = eval(data);
			
			var mp3player_border;
			if(n3s_settings.position.indexOf('top') == -1) {
				mp3player_border = parseInt(jQuery('#n3s_mp3player').css('border-top-width')) - 1;
			} else {
				mp3player_border = parseInt(jQuery('#n3s_mp3player').css('border-bottom-width')) + 1;
			}
			
			if(!n3s_playlist) {
				jQuery('#n3s_playlist').html('<div class="n3s_playlist_item">No mp3\'s found.</div>');
				jQuery('#n3s_playlist').css('height', jQuery('.n3s_playlist_item').height() + mp3player_border);
				
				// set dummy data for correct height calculations; .length needs to have the value '1'
				data = new Array('empty');
			} else {
				var html = '';
				for(i = 0; i < n3s_playlist.length; i++) {
					if(n3s_id3_holder.length > 0) {
						n3s_update_playlist(i);
					}
					
					html += '<div class="n3s_playlist_item" id="n3s_playlist_item_'+i+'" onclick="n3s_load_item(\''+n3s_playlist[i].dir+'\/'+n3s_playlist[i].filename+'\')">' + n3s_playlist[i].title + '</div>';
				}
				
				jQuery('#n3s_playlist').html(html);
			}
			
			n3s_highlight_playlist_item(n3s_currently_playing);
			
			var item_height = jQuery('.n3s_playlist_item').height();
			var playlist_height = (item_height * n3s_playlist.length + mp3player_border > 200)?200:item_height * n3s_playlist.length + mp3player_border;
			
			jQuery('#n3s_playlist').css('height', playlist_height + 'px')
			try {
				jQuery('#n3s_playlist').scrollTo(jQuery('#n3s_playlist_item_' + n3s_playlist_playing_index));
			} catch(e) {}
			
			jQuery('#n3s_playlist_button :input').attr('disabled', '');
		},
		error: function(req, status, thrown) {
			alert(req.status + req.responseText);
		}
	});
}

function n3s_set_playlist_items_style() {
	jQuery('.n3s_playlist_item').css('color', n3s_settings.playlist_text);
	jQuery('.n3s_playlist_item').css('background', n3s_settings.background);
	jQuery('.n3s_playlist_item').hover(function(){
		jQuery(this).css('background', n3s_settings.playlist_hover);
	}, function() {
		jQuery(this).css('background', n3s_settings.background);
	});
}

function n3s_post_form(form) {
	var action = jQuery(form).attr('action');
	var method = jQuery(form).attr('method').toUpperCase();
	var inputs = jQuery(form).find(':input');
	
	jQuery.ajax({
		url: action,
		type: method,
		data: inputs.serialize(),
		dataType: 'html',
		success: function(html) {
			n3s_replace_body(html);
		},
		error: function(req, status, thrown) {
			if(undefined == jQuery.validationEngine) {
				n3s_show_error(req.responseText);
			}
		}
	});
}

function n3s_follow_url(url, target, SkipSWFAddress) {
	n3s_last_url = url;	
	
	if(url.indexOf('#') == 0) {
		var a_name = url.substr(url.indexOf('#')+1);
		
		try {
			jQuery(window).scrollTo(jQuery('#'+a_name));
		} catch(e) {}
		
		return;
	}
	
	var new_domain = url.replace('http://', '');
	var cur_domain = n3s_current_url.replace('http://', '');
	
	new_domain = (new_domain.indexOf('/') > -1)?new_domain.substr(0, new_domain.indexOf('/')):new_domain;
	cur_domain = (cur_domain.indexOf('/') > -1)?cur_domain.substr(0, cur_domain.indexOf('/')):cur_domain;
	
	// load mp3 if link is an mp3 on the same domain:
	if(url.indexOf(".mp3") > -1) {
		if(url.indexOf('http://') > -1) {
			// dont load cross-domain
			if(new_domain == cur_domain) {
				n3s_load_item(url.replace(n3s_settings.baseurl, ''));
				return;
			}
		}
		else if(url.substr(0, 1) == "/") {
			n3s_load_item(url);
			return; 
		}
	}
	
	// if we are going to another domain: load the page
	if(new_domain != cur_domain) {
		// open links to their respective targets
		if(target == '_blank' || target == '_new') {
			window.open(url, target);
		} else {
			location.href = url;
		}
		return;
	}
	
	if(url.indexOf('/wp-admin/') > -1 || url.indexOf('/wp-login.php') > -1) {
		location.href = url;
		return;
	}
	
	jQuery.ajax({
		url: url,
		cache: false, 
		success: function(html) {
			if(SWFAddress.getValue() != url.replace(n3s_settings.baseurl, '')) {
				SWFAddress.setValue(url.replace(n3s_settings.baseurl, ''));
			}
			n3s_current_url = url;
			n3s_replace_body(html);
			n3s_last_url = '';
		},
		error: function(req, status, bla) {
			alert(req + ' status: ' + status);
		}
	});
}

function n3s_show_error(error) {
	if(typeof error == 'string' && error != "") {
		error = error.substr(error.indexOf('<body'));
		error = error.substr(error.indexOf('>')+1);
		
		jQuery('#n3s_error_text').html(error);
		jQuery('#n3s_error').show();
		jQuery('#n3s_error').css('position', 'fixed');
		jQuery('#n3s_error').css('bottom', 50 + 'px');
		
		n3s_showing_error = true;
	}
}

function n3s_close_error() {
	if(!n3s_showing_error) return;
		n3s_showing_error = false;
	
	jQuery('#n3s_error').hide();
}

function n3s_replace_body(html) {
	// html type will be other then 'string' when loading XML or images
	if(typeof html == 'string') {
		html = html.substr(html.indexOf('<body'));
		var body_tags = html.substr(0, html.indexOf('>'));
		html = html.substr(html.indexOf('>')+1);
		
		jQuery(document.body).removeClass();
		
		var find_class = /class=['"](.*?)['"]/;
		body_class = find_class.exec(body_tags);
		
		if(body_class) {
			if(body_class[1] != '') {
				jQuery(document.body).addClass(body_class[1]);
			}
		}
		
		html = html.replace('</body>', '');
		html = html.replace('</html>', '');
		
		if(document.getElementById) {
			document.getElementById('n3s_body').innerHTML = html;
		} else {
			jQuery('#n3s_body').html(html);
		}
		
		try {
			// if we have a position, scroll to it
			if(n3s_current_url.indexOf('#') > -1) {
				var a_name = n3s_current_url.substr(n3s_current_url.indexOf('#')+1);
				jQuery(window).scrollTo(jQuery('#'+a_name));
			} else {
				jQuery(window).scrollTo(0, 0);
			}
		} catch(e) {}
		
		// don't update current page if it's only a scroll tag!
		if(n3s_current_url.substr(0, 1) != "#") {
			n3s_set_current_page();
		}
		
		// re-initialize script for new content
		n3s_initialize_scripts();
		
		n3s_replace_links();
		n3s_replace_forms();
	} else {
		if(n3s_last_url != '') {
			window.open(n3s_last_url, "_blank");
			n3s_last_url = '';
		}
	}
}

function n3s_replace_links(domEle) {
	jQuery('#n3s_body').find('a').each(function(index, domEle) {
		var href = jQuery(domEle).attr('href');	
		var target = jQuery(domEle).attr('target');
		
		var image = /(.*?)(.gif|.jpeg|.jpg|.png)(.*?)/.test(href);
		var javascript = /^javascript:(.*?)/.test(href);
		
		var onclick = jQuery(domEle).attr('onclick');
		var onmousedown = jQuery(domEle).attr('onmousedown');
		var onmouseup = jQuery(domEle).attr('onmouseup');
		
		// only replace tags if it doesn't contain any javascript
		if(href != "" && !image && !javascript && !onclick && !onmousedown && !onmouseup) {
			jQuery(domEle).bind("click", function(event) {
				// prevent actually following the link
				event.preventDefault();
				// handle the href of the link
				n3s_follow_url(jQuery(this).attr('href'), jQuery(this).attr('target'));
			});
		}
	});
}

function n3s_replace_forms() {
	jQuery("form").unbind("submit");
	jQuery("form").bind("submit", function(event) {
												  
		action = jQuery(this).attr('action');
		
		var new_domain = action.replace('http://', '');
		var cur_domain = n3s_current_url.replace('http://', '');
		
		new_domain = (new_domain.indexOf('/') > -1)?new_domain.substr(0, new_domain.indexOf('/')):new_domain;
		cur_domain = (cur_domain.indexOf('/') > -1)?cur_domain.substr(0, cur_domain.indexOf('/')):cur_domain;
		
		if(new_domain == cur_domain) {
			n3s_post_form(this);
		
			// prevent from following the form's default action
			event.preventDefault();
		}
	});
}

function n3s_get_flash_movie(movie_name) {
	var isIE = (navigator.appName.indexOf("Microsoft") != -1);
	return (isIE)?window[movie_name]:document[movie_name];
}

function n3s_set_current_page() {
	document.cookie = "current_page="+n3s_current_url+"; path=/";
}

function n3s_get_current_page() {
	return; 
	var page = n3s_read_cookie('current_page');
	
	if(page && page != window.location.toString()) {
		n3s_follow_url(page, '');
		return true;
	}
	
	return false;
}

function n3s_read_cookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	
	for(var i=0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') c = c.substring(1, c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
	}
	
	return null;
}


n3s_settings_object = function(blogurl, path, ap, rand, rep, pos, border, border_style, border_color, bg, opacity, pl, pl_text, pl_border, pl_hover, pl_active_text, pl_active_bg) {
	this.baseurl 	= blogurl;
	this.path 		= path;
	this.autoplay 	= ap;
	this.randomize = rand;
	this.repeatall = rep;
	this.position 	= pos;
	this.border 	= border;
	this.border_color 	= border_color;
	this.border_style 	= border_style;
	this.background = bg;
	this.opacity 	= opacity;
	this.playlist 	= pl;
	this.playlist_text 	= pl_text;
	this.playlist_border = pl_border;
	this.playlist_hover 	= pl_hover;
	
	this.playlist_active_text = pl_active_text;
	this.playlist_active_background = pl_active_bg;
}

function handleChange(event) {
	n3s_follow_url(n3s_settings.baseurl + event.value);
}

SWFAddress.addEventListener(SWFAddressEvent.CHANGE, handleChange);