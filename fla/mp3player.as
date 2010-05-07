﻿package {	import flash.display.*;	import flash.events.*;	import flash.media.*;	import flash.net.*;		import flash.external.ExternalInterface;		Array.prototype.inArray = function(what:*, recursive:Boolean = false) {	   for( var a = 0; a < this.length; a++ ) {		  if( this[a] == what ) {			 return true;		  }		  else if( recursive && this[a] is Array ) {			 return this[a].inArray( what );		  }	   }	   return false;	}		public class mp3player extends Sprite {		private var currentSong:int = 0;		private var songs:Array = new Array();		private var defaultVolume:Number = 0.6;		private var musicVolume:Number;				private var queuePosition:Number = -1;				private var paused:Boolean = false;		private var randomize:Boolean = false;		private var repeatall:Boolean = false;		private var autoplay:Boolean = false;		private var music:SoundChannel;		private var mp3:Sound;				private var played:Array = new Array();				private var percentageLoaded:Number = 0;				private var sliding:Boolean = false;				public function mp3player():void {			addEventListener(Event.ENTER_FRAME, initialize);		}				private function initialize(event:Event):void {			if(event.target.loaderInfo.bytesTotal == event.target.loaderInfo.bytesLoaded) {				this.removeEventListener(event.type, arguments.callee);								var so:SharedObject = SharedObject.getLocal("settings");				if(so.data.volume) {					musicVolume = Number(so.data.volume);				} else {					musicVolume = defaultVolume;				}								randomize = (root.loaderInfo.parameters.randomize == '1');				repeatall = (root.loaderInfo.parameters.repeatall == '1');								autoplay = (root.loaderInfo.parameters.autoplay == '1');				paused = !autoplay;								// set play button in play mode				start.gotoAndStop(paused?1:2);								SoundMixer.soundTransform = new SoundTransform(musicVolume);				volume.volumeMask.y = 20-(20 * musicVolume);								addEventListener(Event.ENTER_FRAME, updatePosition);								// load XML				var xmlPath:String = root.loaderInfo.parameters.plugin_path;				var xmlLoad:URLLoader = new URLLoader(new URLRequest(xmlPath + "/n3rdskwat-mp3player-list.php"));					xmlLoad.addEventListener(IOErrorEvent.IO_ERROR, xmlError);					xmlLoad.addEventListener(Event.COMPLETE, populate);								createButton(volume);				createButton(volume, MouseEvent.MOUSE_MOVE);				createButton(volume, MouseEvent.MOUSE_UP);			}		}				private function populate(event:Event):void {			// populate the mp3 list from the XML data						played = new Array();						try {				var xml = new XML(event.currentTarget.data);				for each (var track:XML in xml.tracks.track) {					songs.push(track);				}			} catch(e:Error) {				song.songText.text = 'error parsing file listing';				return;			}						if(songs.length > 0) {				ExternalInterface.addCallback("playSong", playSong);			}						// set mouse cursor enabled on buttons			createButton(start);			createButton(previous);			createButton(next);			createButton(song);						loadSong(0);		}				private function xmlError(event:IOErrorEvent):void {			song.songText.text = 'Listing ' + event.text;		}				private function playSong(path:String):String {			// find the song						for(var i:uint = 0; i<songs.length; i++) {				if(songs[i].location == path) {					forcePlay(i, true);					return "";				}			}						return "Could not find the requested song.";		}				private function loadSong(offset:int = 1):void {			start.gotoAndStop(1);						if(songs.length == 0) {				if(song.songText.text != 'unable to load file listing') {					song.songText.text = 'no mp3s found';				}				return;			}						if(played.length == songs.length) {				if(repeatall == false) {					paused = true;				}								played = new Array();			}						if(randomize) {				currentSong = Math.floor(Math.random() * songs.length);				// make sure the song hasn't been played yet:				while(played.inArray(songs[currentSong])) {					currentSong = Math.floor(Math.random() * songs.length);				}			} else {				// loading next / previous song				currentSong += offset;			}						currentSong = (currentSong > songs.length-1)?0:currentSong;			currentSong = (currentSong < 0)?songs.length-1:currentSong;						forcePlay(currentSong);		}				private function forcePlay(index:int, forceUnpause:Boolean = false):void {			currentSong = index;						if(!songs[currentSong]) return;						if(!played.inArray(songs[currentSong])) {				played.push(songs[currentSong]);			}						if(music) {				music.stop();				music = null;			}						if(forceUnpause) {				paused = false;			}						queuePosition = -1;						try {				mp3.close();			} catch(e) {			}						percentageLoaded = -1;						if(!paused) {				// load song + start playing				mp3 = new Sound(new URLRequest(songs[currentSong].location));				mp3.addEventListener(IOErrorEvent.IO_ERROR, loading);				mp3.addEventListener(ProgressEvent.PROGRESS, loading);				mp3.addEventListener(Event.COMPLETE, loading);				mp3.addEventListener(Event.ID3, showSongInfo);								music = mp3.play(1);				music.addEventListener(Event.SOUND_COMPLETE, function(event:*):void { loadSong(1); });				start.gotoAndStop(2);			} else {				music = null;			}						if(ExternalInterface.available) {				ExternalInterface.call("n3s_highlight_playlist_item", songs[currentSong].location.toString());			}						song.songText.text = songs[currentSong].title;			song.songPositionMask.x = 0;		}				private function loading(event:*):void {			switch(event.type) {				case IOErrorEvent.IO_ERROR:					songs = songs.splice(currentSong, 1);					loadSong(0);					break;				case ProgressEvent.PROGRESS:					percentageLoaded = (1 / event.bytesTotal) * event.bytesLoaded;										song.songLoadingMask.x = 280 + (280 * percentageLoaded);										if(queuePosition > -1 && queuePosition <= percentageLoaded) {						music = mp3.play(mp3.length * queuePosition);						music.addEventListener(Event.SOUND_COMPLETE, function(event:*):void { loadSong(1); });												paused = false;						start.gotoAndStop(paused?1:2);												queuePosition = -1;					}					break;				case Event.COMPLETE:					percentageLoaded = 1.0;					if(queuePosition > -1) {						music = mp3.play(mp3.length * queuePosition);						music.addEventListener(Event.SOUND_COMPLETE, function(event:*):void { loadSong(1); });												paused = false;						start.gotoAndStop(paused?1:2);												queuePosition = -1;					}					break;			}		}				private function showSongInfo(event:Event):void {			var id3:ID3Info = mp3.id3;						if(id3.artist != null && id3.songName != null && id3.artist != '' && id3.songName != '') {				var artist:String = id3.artist;				var songName:String = id3.songName;								song.songText.text = artist + ': ' + songName;								// send update info to javascript playlist				if(ExternalInterface.available) {					ExternalInterface.call("n3s_set_playlist_item", songs[currentSong].location.toString(), artist + ': ' + songName);				}			}		}				private function togglePause():void {			var pausePosition:int = 0;						if(music) {				pausePosition = music.position;			}						// resume if paused.			if(paused) {				if(!mp3) {					forcePlay(currentSong, true);					return;				} else {					music = mp3.play(pausePosition);					music.addEventListener(Event.SOUND_COMPLETE, function(event:*):void { loadSong(1); });				}			} else {				music.stop();			}						paused = !paused;			start.gotoAndStop(paused?1:2);		}				private function skipToPosition(event:MouseEvent):void {			if(!mp3) return;						song.songPositionMask.x = event.localX;						var posPercentage:Number = (1 / 280) * event.localX;						if(music) {				music.stop();			}						if(posPercentage <= percentageLoaded || percentageLoaded == -1) {				music = mp3.play(mp3.length * posPercentage);				music.addEventListener(Event.SOUND_COMPLETE, function(event:*):void { loadSong(1); });								paused = false;				start.gotoAndStop(paused?1:2);								queuePosition = -1;			} else {				queuePosition = posPercentage;				paused = true;				start.gotoAndStop(paused?1:2);			}		}				private function changeVolume(event:MouseEvent):void {			musicVolume = (1 / 20) * (20 - event.localY);			SoundMixer.soundTransform = new SoundTransform(musicVolume);						volume.volumeMask.y = event.localY;		}				private function updatePosition(event:Event):void {			if(!mp3) {				song.songPositionMask.x = 0;				return;			}						var newPosition;			if(queuePosition == -1) {				newPosition = (music) ? music.position / mp3.length : 0;			} else {				newPosition = queuePosition;			}						song.songPositionMask.x = 280 * newPosition;		}				private function handleButton(event:MouseEvent):void {			// per button			if(event.type == MouseEvent.CLICK) {				switch(event.target) {					case song:						skipToPosition(event);						break;					case previous:						loadSong(-1);						break;					case next:						loadSong(1);						break;					case start:						togglePause();						break;					case volume:						changeVolume(event);						break;				}			}						if(event.type == MouseEvent.MOUSE_UP) {				if(event.target == volume) {					var so:SharedObject = SharedObject.getLocal("settings");						so.data.volume = musicVolume;						so.flush();				}			}						if(event.type == MouseEvent.MOUSE_MOVE && MouseEvent(event).buttonDown == true) {				changeVolume(event);			}					}				private function createButton(button:*, event:String = MouseEvent.CLICK):void {			button.buttonMode = true;			button.addEventListener(event, handleButton);			button.mouseChildren = false;		}			}}