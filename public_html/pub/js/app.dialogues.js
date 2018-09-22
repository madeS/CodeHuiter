app = (function ($,mjsa){
	var self = this;
	// dialogues and notifications
	
	
		
	this.istackCheckNotifications = function(){
		if (!self.window_focused) {
			// Если окно не в фокусе, происходит торможение частоты коннекта в 4 раза
			if (self.window_focus_ignore_action < 4){
				self.window_focus_ignore_action++;
				return false;
			}
			self.window_focus_ignore_action = 0;
		}
		
		var params = self._getCheckNotificationsParams();
		mjsa.easilyPostAjax('/messages/check','#m_service', params, undefined,function(data){
		}, undefined, {isDoHtml:function(){ return self._isActualNotificationsParams(params);}});	
	};
	this._getCheckNotificationsParams = function(){
		return {
			notifications_count : $('#profile_panel').find('.notifications_params[name=notifications_count]').val(),
			notifications_last : $('#profile_panel').find('.notifications_params[name=notifications_last]').val(),
			dialogues_view: $('#dialogues_view').attr('id'),
			current_room_id : $('#dialogue_messages').attr('data-room-id'),
			last_message_id : $('#dialogue_messages').find('.message_content').last().attr('data-message_id')
		};
	};
	this._isActualNotificationsParams = function(params){
		if (params.notifications_count !== $('#profile_panel').find('.notifications_params[name=notifications_count]').val()) return false;
		if (params.notifications_last !== $('#profile_panel').find('.notifications_params[name=notifications_last]').val()) return false;
		if (params.current_room_id !== $('#dialogue_messages').attr('data-room-id')) return false;
		if (params.last_message_id !== $('#dialogue_messages').find('.message_content').last().attr('data-message_id')) return false;
		return true;
	};
	
	this.showNotifications = function(el){
		$(this).siblings('.pop_cont').show().find('.popcontent').html(
			$(this).siblings('.pop_cont').find('.popcontent_loading').html()
		);
		mjsa.easilyPostAjax('/messages/show_notifications','#m_service');
	};
	
	
	this.dialogueSendSubmit = function(el){
		var params = self._getCheckNotificationsParams();
		mjsa.mFormSubmit(el,'/messages/send',{ 
			easyOpt:{isDoHtml:function(){return self._isActualNotificationsParams(params);}},
			param: params, 
			callback:function(data){
				console.log(data);
			}
		});
		$(el).parents('.m_form').find('textarea').val('');
		return false;
	}; 	
	this.dialogueShowOlder = function(el){
		$(el).parents('.show_more_container').find('.toggleble').toggle();
		var params = self._getCheckNotificationsParams();
		params.first_message_id = $('#dialogue_messages').find('.message_content').first().attr('data-message_id');
		mjsa.easilyPostAjax('/messages/show_older_messages','#m_service', params, undefined,function(data){
			$(el).parents('.show_more_container').remove();
			$('#dialogue_messages').prepend(data);
		}, undefined,{});
		return false;
	}; 	
	this.dialogueRead = function(){
		var params = self._getCheckNotificationsParams();
		mjsa.easilyPostAjax('/messages/readok','#m_service', params, undefined,function(data){});
	};

	
	
	
	
	
	return this;
}).call(app,jQuery,mjsa);