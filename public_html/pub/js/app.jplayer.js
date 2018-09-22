app = (function ($,mjsa){
	var self = this;
	// dialogues and notifications
	
	self.initMethods.push('jInit');
	
	// jPlayer Init
	this._isjPlaying = false;
	this.jInit = function(){
		if (!$("#jplayer").jPlayer) return false;
		$("#jplayer").jPlayer({
			swfPath: "/pub/js/jplayer/",
			volume: 1, cssSelectorAncestor:"#jplayer_player",
			ready: function(){ 
				self.jReady();
			}, 
			ended: function(){ self._isjPlaying = false; }
		});
	};
	this.jPlay = function(type,mp3){ 
		if (!self._isjPlaying){
			if(type === 'mp3'){
				$("#jplayer").jPlayer("setMedia", {mp3: '/pub/files/audio/'+mp3});
			} else{
				if(type === 'chicken') $("#jplayer").jPlayer("setMedia", {mp3: '/pub/files/audio/chicken.mp3'});
			}
			self._isjPlaying = true;
			$("#jplayer").jPlayer("play");
			if (self._jplayer_current.name){
				if (!$('#jplayer_player').hasClass('active')){
					$('#jplayer_player').slideDown(function(){
						$('#jplayer_player').addClass('active');
					});
				}
			}
			$('#jplayer_player').find('.jpc-title').html(self._jplayer_current.name);
		}		
	};
	this.jClose = function(el){
		self.jPause();
		if ($('#jplayer_player').hasClass('active')){
			$('#jplayer_player').slideUp(function(){
				$('#jplayer_player').removeClass('active');
			});
		}
	};
	this.jPause = function(){ 
		self._isjPlaying = false;
		$("#jplayer").jPlayer("pause");
	};
	this.jReady = function(){
		
	};
	this._jplayer_current = {id:0,media:null,name:''};
	this.jPlayerSelect = function(el,someobj){
		var obj = {};
		if (someobj){
			obj = {
				id: someobj.id || 0,
				media: someobj.media || null,
				name: someobj.name || ''
			};	
		} else {
			obj = {
				id: $(el).attr('data-id') || 0,
				media: $(el).attr('data-media') || null,
				name: $(el).attr('data-name') || ''
			};	
		}
		if (obj.id === self._jplayer_current.id){
			if (self._isjPlaying){
				self.jPause();
			} else {
				self.jPlay();
			}
			
		} else {
			self.jPause();
			$("#jplayer").jPlayer("setMedia", {mp3: obj.media});
			self._jplayer_current = obj;
			self.jPlay();
		}
	};

	this.sliderMove = function(el){
		var moveTo = $(el).attr('data-move');
		var $slider = $(el).parents('.slider');
		var itemsCount = $slider.find('.slider_item').length;
		var sliderItemWidth = $slider.find('.slider_cont').width() * 1.0 / itemsCount;
		var lefted = parseInt($slider.find('.slider_cont').css('left'));
		var currentIndex = Math.round(0 - (lefted * 1.0 / sliderItemWidth));
		if (moveTo === 'left'){
			if (currentIndex <= 0){ // to last
				currentIndex = itemsCount - 1;
			} else { // shift left
				currentIndex--;
			}
		} else if (moveTo === 'right'){
			if (currentIndex + 1 >= itemsCount){ // to first
				currentIndex = 0;
			} else { // shift right
				currentIndex++;
			}
		}
		lefted = 0 - currentIndex * sliderItemWidth;
		$slider.find('.slider_cont').css('left',lefted);
		var callback = $slider.attr('data-sliderMoveCallback');
		if (callback && self[callback] && typeof self[callback] === 'function'){
			self[callback]($slider, currentIndex, itemsCount);
		}
	};

	
	
	
	
	
	return this;
}).call(app,jQuery,mjsa);