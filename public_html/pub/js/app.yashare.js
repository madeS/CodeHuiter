app = (function ($,mjsa){
	var self = this;

	self.onloadMethods.push('yaShareInit'); // every reload page
	this.yaShareInit = function(){
		if (!self.jsLoaded['yashare']){
			setTimeout(function(){
				self.yaShareInit();
			},400);
			//console.log('fancybox waiting...');
			return false;
		}
		if (!Ya) return false;
		
		$('.yaShare').each(function(){
			var params = {
				element: this,
				theme: $(this).attr('data-theme') || 'default',
				elementStyle: {
					quickServices: ['vkontakte','facebook','twitter','gplus','odnoklassniki'] //,'moimir'
				},
				popupStyle:{
					copyPasteField: true,
					quickServices: ['vkontakte','facebook','twitter','gplus','odnoklassniki'] //,'moimir'

				},
				link: $(this).attr('data-url'),
				title: ' ', // for servise identify
			};
			new Ya.share(params);				
		});
		$('.yaSharePreText').each(function(){
			$(this).html('' + $(this).attr('data-content'));
		});
		
	};
	
	
	
	return this;
}).call(app,jQuery,mjsa);