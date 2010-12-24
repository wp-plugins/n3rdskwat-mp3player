// JavaScript Document

if(typeof(n3rdskwat) === "undefined") {
	var n3rdskwat = {};
}


(function($) { 

if(typeof(n3rdskwat.mp3player) === "undefined") { 
	var TRUE	= true;
	var FALSE = false;
	
	var initialized = FALSE;
	var first_load	= TRUE;
	
	var settings = {};
	
	var div_error;
	var div_playlist;
	var div_bar;
	var div_body;
	var div_playlist_button;
	var object_flash;
	
	var playlist_shown = FALSE;
	var playlist_playing_index = 0;
	var currently_playing = '';
	
	var path;
	var id3_holder = new Array();
	var playlist_data = new Array();
	var playlist_populated = 0;
	 
	var last_url = '';
	var current_url = window.location.toString();
	 
	n3rdskwat.mp3player = {
		
		loadScript: function( src ) {
			try {
				var fileref = document.createElement('script');
					 fileref.setAttribute("type","text/javascript");
					 fileref.setAttribute("src", src);
					 document.body.appendChild(fileref);
			} catch(e) {}
		},
		
		initialize_scripts: function() {
			/*
			 * Extra javascript execution to try to be compatible with more plugins
			 */
			
			if(typeof(this.scripts) !== "undefined") {
				// load javascript links
				for(var index in this.scripts) {
					this.loadScript(this.scripts[index]);
				}
			}
			
			if(typeof(this.js_codeblocks) !== "undefined") {
				// load javascript code
				for(var index in this.js_codeblocks) {
					try {
						eval(this.js_codeblocks[index]);
					} catch(e) {}
				}
			} 
			
			/*
			 * TODO:
			 * Find a better way to re-initialize {window.onload} or {document.onload} dependend scripts..
			 *
			 * Alternative:
			 * Add checks like lightbox to this function...
			 */
			if(typeof(initLightbox) == 'function') initLightbox();
			
			
			// NextGen Gallery support:
			if(typeof(shutterOnload) == 'function') shutterOnload();
			
			if(typeof(sfHover) == 'function') sfHover();
		},
		
		redirect: function() {
			settings = n3rdskwat_mp3player_settings;
			var baseurl = (settings.baseurl.substr(-1) != "/") ? settings.baseurl + '/' : settings.baseurl;
			if(current_url != baseurl && current_url.indexOf('#/') == -1 && current_url.indexOf('#') < current_url.length-1) {
				// make cookie for
				var redirect = current_url.replace(baseurl, '');
				window.location = baseurl + '#/' + redirect;
				return;
			}
		},
		
		initialize: function() {
			if(initialized == TRUE) return;
				initialized = TRUE;
			
			settings = n3rdskwat_mp3player_settings;
			
			//alert(document.body.onload);
			
			this.build_ui();
			
			// grab current HTML Body contents
			var html = $(document.body).html();
			
			// apply our own overlay HTML Body contents
			$(document.body).html(div_error + div_playlist + div_bar + div_body);
			
			var swflocation = SWFAddress.getValue();
			if(swflocation == "/" || swflocation == "") {
				$('#n3s_body').html(html);
			}
			
			// because of the float:right, the flash and button need to be flipped
			if(settings.position.indexOf('left') == -1) {
				$('#n3s_mp3player').html(div_playlist_button + object_flash);
			} else {
				$('#n3s_mp3player').html(object_flash + div_playlist_button);
			}
			
			this.apply_formatting();
			
			// apply opacity to the player and playlist
			$('#n3s_mp3player').fadeTo(0, settings.opacity);
			$('#n3s_playlist').fadeTo(0, settings.opacity);
			$('#n3s_playlist').hide();
			
			// change all links + forms to be ajax-controlled
			this.replace_links();
			this.replace_forms();
			
			SWFAddress.addEventListener(SWFAddressEvent.CHANGE, this.handleChange);
		},
		
		build_ui: function() {
			// objects to be added to the page
			div_error    = '<div id="n3s_error"><div id="n3s_error_text"></div><div id="n3s_error_button"><input type="button" value="close" onclick="n3rdskwat.mp3player.close_error();" /></div></div>';
			div_playlist = (settings.playlist == '1')?'<div id="n3s_playlist"></div>':'';
			div_bar      = '<div id="n3s_mp3player"></div>';
			div_body     = '<div id="n3s_body"></div>';
			div_playlist_button = (settings.playlist == '1') ? '<div id="n3s_playlist_button"><input type="button" value="playlist" onclick="n3rdskwat.mp3player.toggle_playlist();" /></div>' : '';
			
			object_flash = '<object type="application/x-shockwave-flash" data="'+settings.path+'/swf/n3rdskwat-mp3player.swf" width="300" height="35" id="n3s_flash"><param name=movie value="'+settings.path+'/swf/n3rdskwat-mp3player.swf"><param name=quality VALUE=high><param name=wmode value="transparent"><param name="FlashVars" value="randomize='+settings.randomize+'&autoplay='+settings.autoplay+'&repeatall='+settings.repeatall+'&plugin_path='+settings.path+'" /></object>';
		},
		
		apply_formatting: function() {
			var mp3player	= $('#n3s_mp3player');
			var playlist 	= $('#n3s_playlist');
			var flash 		= $('#n3s_flash');
			var body 		= $('#n3s_body');
			var playlist_button = $('#n3s_playlist_button');
			
			mp3player.css('background', settings.background);
			
			if(settings.playlist == 1) {
				playlist.css('background', settings.background);
			}
					 
			if(settings.position.indexOf('top') != -1) {
				mp3player.css('border-top', '0px');
				mp3player.css('border-bottom', settings.border + 'px ' + settings.border_style + ' ' + settings.border_color);
				mp3player.css('bottom');
				mp3player.css('top', '0px');
				
				if(settings.playlist == 1) {
					playlist.css('border', settings.playlist_border + 'px ' + settings.border_style + ' ' + settings.border_color);
					playlist.css('border-top', '0px');
					
					playlist.css('bottom');
					playlist.css('top', '35px');
				}
				
				body.css('margin-bottom', '0px');
				body.css('margin-top', '50px');
			} else {
				mp3player.css('border-top', settings.border + 'px ' + settings.border_style + ' ' + settings.border_color);
				mp3player.css('border-bottom', '0px');
				
				if(settings.playlist == 1) {
					playlist.css('border', settings.playlist_border + 'px ' + settings.border_style + ' ' + settings.border_color);
					playlist.css('border-bottom', '0px');
				}
				
				body.css('margin-bottom', '50px');
				body.css('margin-top', '0px');
			}
			
			// apply positioning of the flash
			flash.css('position', 'relative');
			if(settings.position.indexOf('left') > -1) {
				flash.css('float', 'left');
				flash.css('margin-left', '5px');
			} else if(settings.position.indexOf('center') > -1) {
				flash.css('position', 'absolute');
				flash.css('left', '50%');
				flash.css('margin-left', ((settings.playlist == '1')?'-190':'-150') + 'px');
				flash.css('margin-right', '5px');
			} else {
				flash.css('float', 'right');
				flash.css('margin-right', '5px');
			}
			
			// apply positioning of the playlist button (if enabled)
			if(settings.playlist == 1) {
				playlist_button.css('position', 'relative');
				if(settings.position.indexOf('left') > -1) {
					playlist.css('left', '5px');
					playlist_button.css('float', 'left');
					playlist_button.css('margin-left', '10px');
				} else if(settings.position.indexOf('center') > 1) {
					playlist.css('left', '50%');
					playlist.css('margin-left', '-183px');
					playlist_button.css('position', 'absolute');
					playlist_button.css('left', '50%');
					playlist_button.css('margin-left', '120px');
				} else {
					
					playlist.css('right', '5px');
					playlist_button.css('float', 'right');
					playlist_button.css('margin-right', '10px');
				}
			}
		},
		
		replace_body: function(html) {
			// html type will be other then 'string' when loading XML or images
			if(typeof html == 'string') {
				/* find all the <script *** src="<file>"></script> tags */
				// do this for every page:
				//if(typeof(this.scripts) === 'undefined') {
					this.scripts = new Array();
					
					var find_js_scripts = /<script\s(.*?)src=['"](.*?)['"](.*?)><\/script>/i;
					while(find_js_scripts.test(html)) {
						js_scripts = find_js_scripts.exec(html);
					 	
						if( js_scripts[0].indexOf('n3rdskwat') == -1 ) {
							this.scripts.push(js_scripts[2]);
						}
						
						html = html.replace(find_js_scripts, '');
					}
				//}
				
				this.js_codeblocks = new Array();
					
				var find_js_code = /<script(\s*)(.*?)>((.|\r|\n)*?)<\/script>/i;
				while(find_js_code.test(html)) {
					js_code = find_js_code.exec(html);
						
					if( js_code[0].indexOf('n3rdskwat') == -1 ) {
						code = js_code[0];
							
						if(code != '') {
							this.js_codeblocks.push(code);
						}
					}
						
					html = html.replace(find_js_code, '');
				}
					
				var exclude_body = /<body(.*?)>((.|\r|\n)*)<\/body>/i;
				var body = exclude_body.exec(html);
                body = body[0];
				
				// extract the body-class for this page
				var body_class = '';
				var get_class = /<body(.*?)\sclass=("(.*?)"|'(.*?)')/i;
				if(get_class.test(body)) {
					var get_class = /class=("(.*?)"|'(.*?)')/i;
					body_class = get_class.exec(body);
					body_class = (body_class.length > 0) ? body_class[1] : '';
				}
				
				// extract the body onload script, if any
				var body_onload = '';
				var get_onload = /<body(.*?)\sonload=("(.*?)"|'(.*?)')/i;
				if(get_onload.test(body)) {
					var get_onload = /onload=("(.*?)"|'(.*?)')/i;
					body_onload = get_onload.exec(body);
					body_onload = (body_onload.length > 0) ? body_onload[2] : '';
				}
				
				// update body class
				$(document.body).removeClass();
				if(body_class != '') {
					$(document.body).addClass(body_class);
				}
				
				// remove body tags
				body = body.replace(/<body(.*?)>/i, '');
				body = body.replace(/<\/body>/i, '');
				
				if(document.getElementById) {
					document.getElementById('n3s_body').innerHTML = body;
				} else {
					$('#n3s_body').html(body);
				}
				
				// execute body onload script
				try {
					eval(body_onload);
				} catch(e) {}
				
				if(!first_load) {
					if(settings.transition == 'flash') {
						$('#n3s_body').fadeIn('fast');
					}
				}
				
				first_load = FALSE;
				
				try {
					// if we have a position, scroll to it
					if(current_url.indexOf('#') > -1) { 
						var a_name = current_url.substr(current_url.lastIndexOf('#')+1);
						$(window).scrollTo(jQuery('#'+a_name));
					} else {
						$(window).scrollTo(0, 0);
					}
				} catch(e) {}
				
				// re-initialize script for new content
				this.initialize_scripts();
				
				this.replace_links();
				this.replace_forms();
			} else {
				if(last_url != '') {
					window.open(last_url, "_blank");
					last_url = '';
				}
			}
		},
		
		replace_links: function(domEle) {
			$('#n3s_body').find('a').each(function(index, domEle) {
				var href = $(domEle).attr('href');
				var target = $(domEle).attr('target');
				
				var image = /(.*?)(.gif|.jpeg|.jpg|.png)(.*?)/.test(href);
				var javascript = /^javascript:(.*?)/.test(href);
				var pageAnchor = (href.substr(0,1) == '#');
				
				var onclick = $(domEle).attr('onclick');
				var onmousedown = $(domEle).attr('onmousedown');
				var onmouseup = $(domEle).attr('onmouseup');
				
				// only replace tags if it doesn't contain any javascript
				if(href != "" && !image && !javascript && !pageAnchor && !onclick && !onmousedown && !onmouseup) {
					$(domEle).bind("click", function(event) {
						// prevent actually following the link
						event.preventDefault();
						// handle the href of the link
						n3rdskwat.mp3player.follow_url($(this).attr('href'), $(this).attr('target'));
					});
				}
			});
		},
		
		replace_forms: function() {
			/* dont strip functionality for other plugins? */
			//$("form").unbind("submit");
			
			$("form").bind("submit", function(event) {
				
				action = $(this).attr('action');
				
				var new_domain = action.replace('http://', '');
				var cur_domain = current_url.replace('http://', '');
				
				new_domain = (new_domain.indexOf('/') > -1)?new_domain.substr(0, new_domain.indexOf('/')):new_domain;
				cur_domain = (cur_domain.indexOf('/') > -1)?cur_domain.substr(0, cur_domain.indexOf('/')):cur_domain;
				
				if(new_domain == cur_domain) {
					n3rdskwat.mp3player.post_form(this);
					// prevent from following the form's default action
					event.preventDefault();
				}
			});
		},
		
		follow_url: function(url, target) {
			last_url = url;
			
			if(current_url == url) return;
			
			// see where we are at and where we are trying to go to
			var new_domain = url.replace('http://', '');
			var cur_domain = current_url.replace('http://', '');
			
			new_domain = (new_domain.indexOf('/') > -1)?new_domain.substr(0, new_domain.indexOf('/')):new_domain;
			cur_domain = (cur_domain.indexOf('/') > -1)?cur_domain.substr(0, cur_domain.indexOf('/')):cur_domain;
			
			// load mp3 if link is an mp3 on the same domain:
			if(url.indexOf(".mp3") > -1) {
				if(url.indexOf('http://') > -1) {
					// dont load cross-domain
					if(new_domain == cur_domain) {
						this.load_item(url.replace(settings.baseurl, ''));
						return;
					}
				}
				else if(url.substr(0, 1) == "/") {
					this.load_item(url);
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
			
			// don't redirect / handle WP specific urls
			if(url.indexOf('/wp-admin/') > -1 || url.indexOf('/wp-login.php') > -1) {
				location.href = url;
				return;
			}
			
			// apply transitions
			if(!first_load) {
				if(settings.transition == 'flash') {
					$('#n3s_body').fadeOut('fast');
				}
			}
			
			// apply SWFAddress functionality
			var baseurl = (settings.baseurl.substr(-1, 1) == "/") ? settings.baseurl.substr(0, -1) : settings.baseurl;
			if(SWFAddress.getValue() != url.replace(baseurl, '')) {
				SWFAddress.setValue(url.replace(baseurl, ''));
				return;
			}
			
			// make the request!
			$.ajax({
				url: url,
				cache: false,
				success: function(html) {
					n3rdskwat.mp3player.replace_body(html);
					current_url = url;
					last_url = '';
				},
				error: function(xhr, error) {
					if(xhr.status == 404) {
						url_parts = current_url.split('/');
						url_parts.pop();
						new_url = url_parts.join('/');

						n3rdskwat.mp3player.follow_url(new_url, target);
					}
				}
			});
		},
		
		post_form: function(form) {
			var action = $(form).attr('action');
			var method = $(form).attr('method').toUpperCase();
			var inputs = $(form).find(':input');
			
			if(method == 'GET') {
				if(action.indexOf('?') == -1) {
					action += '?' + inputs.serialize();
				} else {
					action += '&' + inputs.serialize();
				}
			} else {
				postData = inputs.serialize();
			}
			
			$.ajax({
				url: action,
				type: method,
				data: postData,
				dataType: 'html',
				success: function(html) {
					n3rdskwat.mp3player.replace_body(html);
				},
				error: function(req, status, thrown) {
					if(undefined == $.validationEngine) {
						this.show_error(req.responseText);
					}
				}
			});
		},
		
		populate_playlist: function() {
			var date = new Date();
			
			// only once populate the playlist:
			if(playlist_populated > 0) return;
				playlist_populated = date.getTime();
			
			$('#n3s_playlist_button :input').attr('disabled', 'disabled');
			
			$('#n3s_playlist').css('height', '200px');
			$('#n3s_playlist').html('<h3>loading...</h3');
			
			$.ajax({
				url: settings.path + 'n3rdskwat-mp3player-list.php?type=json',
				type: 'GET',
				dataType: 'html',
				success: function(data) {
					playlist_data = eval(data);
					
					var mp3player_border;
					if(settings.position.indexOf('top') == -1) {
						mp3player_border = parseInt($('#n3s_mp3player').css('border-top-width')) + 1;
					} else {
						mp3player_border = parseInt($('#n3s_mp3player').css('border-bottom-width')) - 1;
					}
					
					if(!playlist_data) {
						$('#n3s_playlist').html('<div class="n3s_playlist_item">No mp3\'s found.</div>');
						$('#n3s_playlist').css('height', $('.n3s_playlist_item').height() + mp3player_border);
						
						// set dummy data for correct height calculations;.length needs to have the value '1'
						data = new Array('empty');
					} else {
						var html = '';
						for(i = 0;i < playlist_data.length;i++) {
							if(id3_holder.length > 0) {
								n3rdskwat.mp3player.update_playlist(i);
							}
							
							html += '<div class="n3s_playlist_item" id="n3s_playlist_item_'+i+'" onclick="n3rdskwat.mp3player.load_item(\''+playlist_data[i].dir+'\/'+playlist_data[i].filename+'\')">' + playlist_data[i].title + '</div>';
						}
						
						$('#n3s_playlist').html(html);
					}
					
					n3rdskwat.mp3player.highlight_playlist_item(currently_playing);
					
					var item_height = $('.n3s_playlist_item').height();
					var playlist_height = (item_height * playlist_data.length + mp3player_border > 200) ? 200 : item_height * playlist_data.length + mp3player_border;
					
					$('#n3s_playlist').css('height', playlist_height + 'px');
					try {
						$('#n3s_playlist').scrollTo($('#n3s_playlist_item_' + playlist_playing_index));
					} catch(e) {}
					
					$('#n3s_playlist_button :input').attr('disabled', '');
				},
				error: function(req, status, thrown) {
					alert(req.status + req.responseText);
				}
			});
		},
		
		update_playlist: function(index) {
			for(var i = 0;i < id3_holder.length;i++) {
				if(playlist_data[index].dir + '/' + playlist_data[index].filename == id3_holder[i].path) {
					playlist_data[index].title = id3_holder[i].title;
				}
			}
		},
		
		set_playlist_item: function(path, title) {
			if(0 == settings.playlist) return;
			if(0 == playlist_populated) {
				// update to the id3_holder
				for(var i = 0;i < id3_holder.length;i++) {
					if(id3_holder[i].path == path) {
						id3_holder[i].title = title;
						return;
					}
				}
				
				var song = id3_holder[id3_holder.length] = new Object();
					 song.path = path;
					 song.title = title;
			} else {
				// update to the playlist_data
				// update text in '#n3s_playlist_item_<index>'
				for(var i = 0;i < playlist_data.length;i++) {
					if(playlist_data[i].dir + '/' + playlist_data[i].filename == path) {
						playlist_data[i].title = title;
						$('#n3s_playlist_item_'+i).text(title);
					}
				}
			}
		},
			 
		toggle_playlist: function() {
			if(playlist_shown) {
				$('#n3s_playlist').hide();
			} else {
				this.populate_playlist();
				jQuery('#n3s_playlist').show();
				try {
					$('#n3s_playlist').scrollTo($('#n3s_playlist_item_' + playlist_playing_index));
				} catch(e) {}
			}
	 		
			playlist_shown = !playlist_shown;
		},
		
		highlight_playlist_item: function(path) {
			if(0 == settings.playlist) return;
			if(0 == playlist_populated) {
				currently_playing = path;
				return;
			}
			
			$('.n3s_playlist_item').css('color', settings.playlist_text);
			$('.n3s_playlist_item').css('background', settings.background);
			$('.n3s_playlist_item').hover(function(){
				$(this).css('background', settings.playlist_hover);
			}, function() {
				$(this).css('background', settings.background);
			});
			
			for(var i = 0;i < playlist_data.length;i++) {
				if(playlist_data[i].dir + '/' + playlist_data[i].filename == path) {
					playlist_playing_index = i;
					
					$('#n3s_playlist_item_' + i).css('color', settings.playlist_active_text);
					$('#n3s_playlist_item_' + i).css('background', settings.playlist_active_background);
					$('#n3s_playlist_item_' + i).unbind('mouseenter mouseleave');
					return;
				}
			}
		},
		
		load_item: function(path) {
			path = unescape(path);
			var flash = this.get_flash_movie('n3s_flash');
			try {
				flash.playSong(path);
			} catch(e) {
				// something went wrong with fetching the flash...
			}
		},
		
		handleChange: function(event) {
			n3rdskwat.mp3player.follow_url(settings.baseurl + event.value);
		},
		
		show_error: function(error) {
			if(typeof(error) === 'string' && error != "") {
				error = error.substr(error.indexOf('<body'));
				error = error.substr(error.indexOf('>')+1);
				
				$('#n3s_error_text').html(error);
				$('#n3s_error').show();
				$('#n3s_error').css('position', 'fixed');
				$('#n3s_error').css('bottom', 50 + 'px');
				
				showing_error = TRUE;
			}
		},
		
		close_error: function() {
			if(!showing_error) return;
				 showing_error = FALSE;
			
			$('#n3s_error').hide();
		},
				
		get_flash_movie: function(movie_name) {
			return (navigator.appName.indexOf("Microsoft") != -1) ? window[movie_name] : document[movie_name];
		}
	}
}
})(jQuery);

 
jQuery(document.body).ready(function() {
	n3rdskwat.mp3player.initialize();
});