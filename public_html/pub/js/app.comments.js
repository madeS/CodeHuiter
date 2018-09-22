app = (function ($,mjsa){
	var self = this;

	this.commentsAdd = function(el){
		return mjsa.mFormSubmit(el,'/japi_comments/comment_add',{
			callback: function(response,data,el){
				var comments = mjsa.grabResponseTag(response,'<comments/>',true);
				if (comments){ $(el).parents('.comments').html(comments); }
				return true;
			}
		});
	};
	this.commentsEdit = function(el){
		return mjsa.mFormSubmit(el,'/japi_comments/comment_edit',{
			callback: function(response,data,el){
				var comments = mjsa.grabResponseTag(response,'<comments/>',true);
				if (comments){ $(el).parents('.comments').html(comments); }
				return true;
			}
		});
	};
	this.commentsReply = function(el){
		var commentAddHtml = $(el).parents('.comments').find('.comment_add').html();
		var $in = $(el).parents('.comment').find('.comment_add_in').html(commentAddHtml);
		var parentId = $(el).parents('.comment').attr('data-id');
		$in.find('.m_form').prepend('<input type="hidden" class="in" name="parent_id" value="'+parentId+'">');
		$in.find('textarea').focus();
		return false;
	};
	this.commentsEditForm = function(el){
		var commentId = $(el).parents('.comment').attr('data-id');
		var commentStyle = $(el).parents('.comments').find('.comment_add').find('.in[name=comment_style]').val();
		mjsa.easilyPostAjax('/japi_comments/comment_edit_form','#m_service',{comment_id: commentId, comment_style: commentStyle});
		return false;
	};
	
	this.commentsShowParent = function(el){
		var parentId = $(el).attr('data-parent-id');
		var $comment = $(el).parents('.comments').find('.comment[data-id='+parentId+']').css('background','#ddd');
		if (!mjsa.isInWindow($comment)){
			mjsa.scrollTo($comment,{offset:-100});
		}
		setTimeout(function(){
			$comment.css("background","transparent");
		},5000);
		
	};
	
	this.commentsRemove = function(el){
		var commentId = $(el).parents('.comment').attr('data-id');
		var commentStyle = $(el).parents('.comments').find('.comment_add').find('.in[name=comment_style]').val();
		mjsa.easilyPostAjax('/japi_comments/comment_remove','#m_service',{comment_id: commentId, comment_style: commentStyle},undefined,undefined,undefined,{el:el});
		return false;
	};
	
	this.thumbs = function(el){
		var uaction = $(el).attr('data-uaction');
		mjsa.easilyPostAjax('/japi_thumbs/thumbs/'+uaction,'#m_service',
			{
				object_type:$(el).parents('.thumbs').attr('data-type'),
				object_id:$(el).parents('.thumbs').attr('data-id')
			}, undefined,
			function(response){
				var thumbsUp = mjsa.grabResponseTag(response,'<thumbs_up/>',true);
				if (thumbsUp) $(el).parents('.thumbs').find('.thumbs_up .count').html(thumbsUp);
				var thumbsDown = mjsa.grabResponseTag(response,'<thumbs_down/>',true);
				if (thumbsDown) $(el).parents('.thumbs').find('.thumbs_down .count').html(thumbsDown);
				var complain = mjsa.grabResponseTag(response,'<complain/>',true);
				if (complain) $(el).parents('.thumbs').find('.complain .count').html(thumbsUp);
				var pick = mjsa.grabResponseTag(response,'<pick/>',true);
				if (pick!==undefined) $(el).parents('.thumbs').removeClass('pick_thumbs_up pick_thumbs_down pick_complain').addClass(pick);
			},undefined,{el:el}
		);
		return false;
	};
	
	return this;
}).call(app,jQuery,mjsa);