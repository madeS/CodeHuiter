app = (function ($,mjsa){
	var self = this;

	self.onloadMethods.push('fancyInit'); // every reload page
	this.fancyInit = function(){
		if (!self.jsLoaded['fancybox']){
			setTimeout(function(){
				self.fancyInit();
			},400);
			//console.log('fancybox waiting...');
			return false;
		}
		if ($.fancybox){
			$(".fancybox").fancybox({
				padding: 0,
				prevEffect	: 'fade',
				nextEffect	: 'fade'
			});
		}
	};
	this.fancyboxOpen = function(){
		if ($.fancybox){
			$.fancybox($(this),{
				padding: 0,
				prevEffect	: 'fade',
				nextEffect	: 'fade'
			});
		}
		return false;
	};
	
	self.initMethods.push('paralaxInit'); // one time
	self.onloadMethods.push('paralaxUpdate');
	this.paralaxInit = function(){
		$(document).on('scroll',function(){
			var topOffset = mjsa.scrollTo();
			$('.backgroundlayer').css('top', parseInt(-mjsa.scrollTo()*1.5) );
			return true;
		});
	};
	this.paralaxUpdate = function(){
		$(document).trigger('scroll');
	};
	
	self.onloadMethods.push('tinyInit'); // every reload page
	this.tinyCss = '/pub/compressor/compressed_dev.css';
	this.tinyInit = function(){
		var opt = {};
		if (!self.jsLoaded['tinymce']){
			setTimeout(function(){
				self.tinyInit();
			},400);
			//console.log('fancybox waiting...');
			return false;
		}
		tinymce.init({
			content_css : [self.tinyCss, '/pub/css/app.tinymce.css'],
			relative_urls: true,
			document_base_url: 'http://mop.bogarevich.com/',
			plugins:["contextmenu table paste searchreplace image link code media visualchars nonbreaking moxiecut"],
			forced_root_block : false,
			force_br_newlines : (opt.newline == 'p') ? false : true,
			force_p_newlines : (opt.newline == 'p') ? true : false,
			selector: "textarea.tinymce"
		});
		//$('.tinymce').removeClass('tinymce');

	};
	
	
	// select2
	//self.onloadMethods.push('select2Init'); // every reload page
	this.select2Init = function(selector, callback, opt1, opt2){
		if (!self.jsLoaded['select2']){
			setTimeout(function(){
				self.select2Init(selector, callback, opt1, opt2);
			},400);
			//console.log('fancybox waiting...');
			return false;
		}
		var ret = undefined;
		if (opt1 === undefined){
			ret = $(selector).select2();
		} else {
			if (opt2 === undefined){
				ret = $(selector).select2(opt1);
			} else {
				ret = $(selector).select2(opt1,opt2);
			}
		}
		if (callback){
			callback(ret);
		}
	};

	self.initMethods.push('fixedHeaderInit'); // one time
	self.onloadMethods.push('fixedHeaderUpdate');
	this.$fixedHeader = null;
	this.fixedHeaderHeight = 0;
	this.fixedHeaderInit = function(){
		if ($('.header .logo img').height() < 30){
			setTimeout(function(){
				self.fixedHeaderInit();
			},400);
			//console.log('fancybox waiting...');
			return false;
		}
		
		self.fixedHeaderParams();
		$(document).on('scroll',function(){
			if ($(document).scrollTop() > self.fixedHeaderHeight){
				self.$fixedHeader.addClass('fixed');
			} else {
				self.$fixedHeader.removeClass('fixed');
			}
			self.$fixedHeader.removeClass('menu_active');
		});
		
		
		
		$(window).on('scroll',function(){
			
		});
		$(window).trigger('scroll');

		$('.menu_btn').on('click',function(){
			header_inner.toggleClass('menu_active');
		});
	};
	this.fixedHeaderUpdate = function(){
		self.fixedHeaderParams();
		$(document).trigger('scroll');
	};
	this.fixedHeaderParams = function(){
		self.$fixedHeader = $('.header');
		self.fixedHeaderHeight = self.$fixedHeader.height() - 50;
		$('.header > .bg').css('height',self.$fixedHeader.height());
	}

	return this;
}).call(app,jQuery,mjsa);