/*
 * Adapting js for MOP
 */
var app = new (function ($){
    var self = this;

    this.window_focused = true;
    this.window_focus_ignore_action = 0;

    this.loadHtml = '<div class="mjsa_loader"><div class="cssload-loader"><div class="cssload-inner cssload-one"></div><div class="cssload-inner cssload-two"></div><div class="cssload-inner cssload-three"></div></div></div>';

    // ***** for all applications default *****

    this.initMethods = ['checkTimezone'];
    this.onloadMethods = ['correctColums'];
    this.jsLoaded = {};

    this.initActions = function(){

        // Call app function by attr 'data-action' as class 'action'
        $(document).on('click','.action',function(event){
            if ($(this).attr('data-confirm')){
                if (!confirm($(this).attr('data-confirm'))) return false;
            }
            var action = $(this).attr('data-action');
            if (action && self[action] && typeof self[action] === 'function'){
                return self[action].call(this,this);
            } else return true;
        });

        // Init functions
        for(var i in self.initMethods){
            if (self[self.initMethods[i]] && typeof self[self.initMethods[i]] === 'function'){
                self[self.initMethods[i]].call(self);
            }
        }

        // custom application init
        self.initCustom && self.initCustom();
    };

    this.onLoad = function(jsKey,callback){
        if (!self.jsLoaded[jsKey]){
            setTimeout(function(){
                self.onLoad(jsKey,callback);
            },400);
            //console.log('onload waiting [jsKey]...');
            return false;
        }
        callback && callback();
    };


    this.onloadCustom = function(){
        for(var i in self.onloadMethods){
            if (self[self.onloadMethods[i]] && typeof self[self.onloadMethods[i]] === 'function'){
                self[self.onloadMethods[i]].call(self);
            }
        }
    };

    this.popupname = undefined;
    this.popupInit = function(opt){
        if (!opt) opt = {};
        var window_id = opt.name || 'thepopup';
        var width = opt.width || 400;
        width = ($(window).width() > width + 46) ? width : $(window).width() - 46;
        mjsa.scrollPopup.init(window_id,{
            width: width,
            top: opt.top || 100,
            mainContainer: '#body_cont',
            loadingBlock: self.loadHtml,
            closeBtnClass: 'ficon-cancel',
            zindex: 19,
            padding_hor: opt.padding_hor,
            callOpen:undefined,
            callClose:undefined
        });
        return window_id;
    };

    this.shareOpen = function(el){
        //var params = 'scrollbars=yes,resizable=yes,status=no,location=yes,toolbar=no,menubar=no,width=600,height=400';
        var params = 'width=520,height=530,resizable=yes,scrollbars=yes,status=yes';
        window.open($(el).attr('href'), $(el).attr('data-sharetitle'), params);
        return false;
    };

    /*
    $(window).scroll(function(){
        if($(document).height() - $(window).height() <= $(window).scrollTop() + 50){
            loadChronics();
        }
     });
    */

    this.enterEvent = function(el){
        if (el === undefined) el = this;
        if ($(el).attr('data-enteraction')){
            var action = $(el).attr('data-enteraction');
            if (action && self[action] && typeof self[action] === 'function'){
                return self[action](el);
            } else return true;
        }
        $(this).parents('.m_form').find('.m_form_submit').click();
    };

    this.like_a = function(el){
        var link = $(el).attr('href') || $(el).attr('data-href');
        if(link) mjsa.location(link);
        return false;
    };
    this.easyAjax = function(el){
        mjsa.easilyPostAjax(
            $(el).attr('data-uri'),
            mjsa.def.service,
            JSON.parse($(el).attr('data-params') || '{}'),
            undefined, undefined, undefined,
            {el:el}
        );
        return false;
    };
    this.appSubmit = function(el){
        return mjsa.mFormSubmit(el, $(el).attr('data-uri'));
    };

    this.oauthOpen = function(el){
        var params = 'scrollbars=yes,resizable=yes,status=no,location=yes,toolbar=no,menubar=no,' +
             'width=650,height=550';
        window.open($(el).attr('href'), 'oauth', params);
        return false;
    };

    // Timezone sync
    this.checkTimezone = function(){
        var timezoneoffset = $('#body_cont').attr('data-timezoneoffset');
        if (timezoneoffset !== undefined){
            var timezone = mjsa.getTimezone();
            if (timezoneoffset != timezone['offset']){
                $('#body_cont').removeAttr('data-timezoneoffset');
                mjsa.easilyPostAjax('/auth/sync_timezone', '#m_service', timezone);
            }
        }
        return false;
    };

    this.correctColums = function(){
        $('.page_container').each(function(){
            if ($(this).find('.right_container').css('position') === 'relative'){
                $(this).css('height','auto');
                return false;
            }
            var maxHeight = Math.max(
                ($(this).find('.center_container').height() || 0),
                ($(this).find('.left_container').height() || 0),
                ($(this).find('.right_container').height() || 0)
            )
            if ($(this).height() < maxHeight) {
                $(this).css('height',maxHeight);
            }
        });
    };

    // Search filters
    this.querySearchSubmit = function(el){
        var params = mjsa.collectParams($(el).parents('.m_form').find('.in'));
        var saveParams = $(el).parents('.m_form').attr('data-save-params');
        var pathname = $(el).parents('.m_form').attr('data-uri');
        //console.log($(el).parents('.m_form').attr('data-save-params'));
        if (saveParams){
            var currentParams = mjsa.urlParams();
            var saveParamsArr = saveParams.split(',');
            for (var i in saveParamsArr){
                if (saveParamsArr[i] !== '' && currentParams[saveParamsArr[i]]){
                    params[saveParamsArr[i]] = currentParams[saveParamsArr[i]];
                }
            }
        }
        mjsa.location((pathname || location.pathname)+'?'+$.param(params));
        return false;
    };

    this.changeSearchParamSubmit = function(el){
        var params = mjsa.urlParams();
        var paramKeyValue = $(el).attr('data-param').split(':');
        if (paramKeyValue.length > 1){
            params[paramKeyValue[0]] = paramKeyValue[1];
        }
        mjsa.location(location.pathname+'?'+mjsa.urlParams(params));
        return false;
    };

    return this;
})(jQuery);
//////// EVENTS ///////
jQuery(function(){
    // СОстояние активности окна
    $(window).blur(function() { app.window_focused = false; });
    $(window).focus(function() {app.window_focused = true; });

    // data-action subscribe init
    app.initActions();


    mjsa.def.popups.mainContainer = '#body_cont';
    mjsa.def.loadingBlock = app.loadHtml;

    mjsa.bodyAjax.init('a.bodyajax');

    // Default events on clicks and Actions
    $(document).on('click','a',function(){
        if (!$(this).hasClass('bodyajax') && $(this).attr('href') === '#') return false;
        else return true;
    });
    $(document).on('click','.btn.disable',function(event){
        event.preventDefault();
        event.stopImmediatePropagation();
        return false;
    });
    mjsa.onClickEnterInit('.enter_submit',{callback:app.enterEvent});
    mjsa.onClickEnterInit('.ctrlenter_submit',{ctrl:true, callback:app.enterEvent});



    mjsa.def.bodyAjaxOnloadFunc = function(){
        mjsa.interval.clear();
        $('#istack').find('input.istack').each(function(){
            var action = $(this).val();
            var timer = $(this).attr('data-timer');
            if (action && app[action] && typeof app[action] === 'function' && timer){
                mjsa.interval.add(app[action],timer);
            }
        });
        app.onloadCustom && app.onloadCustom();
    };
    mjsa.def.bodyAjaxOnloadFunc();

    //window.onunload = function() { debugger; }
});
