/*
    Mad JS libA
    Back to vanilla 2012 with jQuery instead now modish js frameworks
    Author: Andrei Bogarevich
    License:  MIT License
    Site: https://github.com/madeS/mjsa
    v1.2.0.108
    Last Mod: 2016-01-29 20:00
*/

/*
 * @todo 9; rename mthis
  */

var mjsaClass = function ($){
    var self = this;

    this.def = {
        hints: {
            containerClass: 'mjsa_hints_container', //'mjsa_hints_container', undefined for alerts warnings, else need connect mjsa css
            callback: undefined, // application alerts or other action
            mainClass: 'mjsa_hint',
            successClass: 'mjsa_hint_success',
            errorClass: 'mjsa_hint_error',
            simpleClass: 'mjsa_hint_simple',
            closeClass: 'ficon-cancel',
            hintLiveMs: 10000
        },
        registerErrorsUrl: undefined, // Register all ajax error URL
        easilyDefObj: undefined, // obj or func (used for auth in iframe application)
        testing: false,
        bodyAjax: false,
        bodyAjax_inselector: '#body_cont',  //body,
        bodyAjax_timeout: 5000,
        bodyAjaxOnloadFunc: undefined,  // reAttach events for dom and etc.
        bodyAjaxOnunloadFunc: undefined,  // reAttach events for destroy some objects for this page.
        loadingBlock: undefined, // '<img src="/pub/images/15.gif" alt="" />',
        haSaveSelector: '.mjsa_save', // history ajax save forms inputs selector
        htmlInterception: undefined, //  .html interception function(content) if return false, html not processed,
        mform: {
            selector: '.m_form', // mForm Selector
            scrollToIncorrectAttr: 'data-scrolltoincorrect', //
            disableClass: 'disable', // mForm disable class when btn pressed
            inSelector: '.in', // mForm inner selector for collect params
            errorSelector: '.in_error', // mForm error selector to set error text
            incorrectClass: 'm_incorrect', // mForm rror class for error input
            service: '#m_service', // mForm class for executing server JS = def.service
            errorSeparator:'<error_separator/>',
            incorrectSeparator:'<incorrect_separator/>',
            formReplaceSeparator:'<form_replace_separator/>'
        },
        popups: {
            maxWidth: 600,
            maxWidthSpace: 46,
            top: 100,
            padding_hor: 15,
            padding_ver: 15,
            modelName: 'mjsa.popups', // [versionedit]
            mainContainer: '#container',
            loadingBlock: undefined, // Or def.loadingBlock
            closeBtnClass: undefined,
            zindex: 19,
            callOpen:undefined,
            callClose:undefined
        },
        service: '#m_service' // class for executing server JS
    };

    /** Clone the object */
    this.clone = function(obj) {
        return JSON.parse(JSON.stringify(obj));
    };
    /** Get property or call function to get a property */
    this.get = function(obj) {
        var ret = obj;
        if (typeof obj === 'function') ret = obj();
        return ret;
    };
    /** Check is variable in array */
    this.inArray = function(needle, haystack){
        var found = false, key;
        for (key in haystack) {
            if (!haystack.hasOwnProperty(key)) continue;
            if (haystack[key] === needle) {
                found = true;
                break;
            }
        }
        return found;
    };

    /** Debug module */
    this.debug = {};
    /** Return debug text of variable. Like PHP debug param. Used for alert only, recomended use console.log */
    this.debug.print_r = function(arr, level, maxlevel) {
        if (!maxlevel) maxlevel = 3;
        if (level >= maxlevel) return '';
        var print_red_text = "";
        if (!level) level = 0;
        var level_padding = "";
        for (var j=0; j < level + 1; j++) level_padding += "    ";
        if (typeof(arr) === 'object') {
            for (var item in arr) {
                if (!arr.hasOwnProperty(item)) continue;
                var value = arr[item];
                if (typeof(value) === 'object') {
                    print_red_text += level_padding + "'" + item + "' :\n";
                    print_red_text += self.debug.print_r(value, level + 1, maxlevel);
                } else {
                    print_red_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
                }
            }
        } else  print_red_text = "==>"+arr+"<==("+typeof(arr)+")";
        return print_red_text;
    };
    this.debug.alert = function(e) {
        alert(self.debug.print_r(e));
    };
    this.debug.log = function(e) {
        console.log('debugging:', e);
    };

    /** Hint module */
    this.hints = {};
    /** Show Error Message */
    this.hints.printError = function(msg) {
        return self.hints.printHint(msg, self.def.hints.errorClass);
    };
    /** Show Success Message */
    this.hints.printSuccess = function(msg) {
        return self.hints.printHint(msg, self.def.hints.successClass);
    };
    /** Show custom message */
    this.hints.printHint = function(hint, className, opt) {
        opt = opt || {};
        if (self.def.hints.callback && !self.def.hints.callback(hint, className)) {
            return false;
        }
        if (!className) className = self.def.hints.simpleClass;
        var ms = new Date(); // @todo 0; refactor this
        var ms_time = ms.getTime();
        var $hintCont = self.hints._getHintsContainer();
        $hintCont.append(
            '<div class="hintwrap hint' + ms_time + '"><div class="' + self.def.hints.mainClass + ' ' + className+ '">'
            + hint + '<span class="close ' + self.def.hints.closeClass
            + '" onclick="$(this).parents(\'.hintwrap\').remove();" ></span></div></div>'
        );
        $hintCont.find('.hint' + ms_time).animate({height: "show"}, 300);
        if (!opt.permanent) {
            window.setTimeout(function() {
                $('.' + self.def.hints.containerClass).find('.hint' + ms_time + '')
                    .animate({height: "hide"}, {duration: 300, done: function() { $(this).remove(); }});
            }, opt.live || self.def.hints.hintLiveMs);
        }

        return false;
    };
    /** Get Hints container */
    this.hints._getHintsContainer = function(nested) {
        var ret = $('.' + self.def.hints.containerClass);
        if (ret.length === 0 && nested !== true) {
            $('body').append('<div class="' + self.def.hints.containerClass+'"></div>');
            ret = self.hints._getHintsContainer(true);
        }
        return ret;
    };

    /** Interval module */
    this.interval = {};
    this.interval._handlers = [];
    /** Add function to interval */
    this.interval.add = function(func, timer) {
        return self.interval._handlers.push(setInterval(func, timer)) - 1;
    };
    /** Clear all interval functions */
    this.interval.clear = function(index) {
        var count = 1;
        if (!index) {
            index = 0;
            count = self.interval._handlers.length;
        }
        var arr = self.interval._handlers.splice(index, count);
        for (var i in arr) clearInterval(arr[i]);
        return false;
    };

    this.ajax = {};
    this.ajax._repeat = 3;
    /** Default error of ajax. Call it after custom error */
    this.ajax.error = function(jqXHR, textStatus, errorThrown) {
        if (jqXHR.status === 0 && jqXHR.statusText === 'error') jqXHR.statusText = 'Connection error';
        self.hints.printError('Error ' + jqXHR.status + ': ' + jqXHR.statusText);
        if (self.def.registerErrorsUrl) $.post(self.def.registerErrorsUrl, {
            status: jqXHR.status, statusText: jqXHR.statusText, response: jqXHR.responseText, ts: textStatus, et: errorThrown
        });
    };
    /** Send the ajax request */
    this.ajax.send = function(options) {
        var innerOptions = $.extend(self.clone(options), {
            success: function(html, textStatus, XMLHttpRequest) {
                self.ajax._repeat = 3;
                debugger;
                if (options.success !== undefined) options.success(html, textStatus, XMLHttpRequest);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (self.ajax._repeat > 0) {
                    self.ajax._repeat--;
                    options.timeout = undefined; // second try without timeout
                    self.ajax.send(options);
                } else {
                    self.ajax._repeat = 3;
                    if (options.error !== undefined ) options.error(jqXHR, textStatus, errorThrown);
                    else { self.ajax.error(jqXHR, textStatus, errorThrown); }
                }
            }
        });
        $.ajax(innerOptions);
    };

    /** scroll to value in pixels or to selector */
    this.scrollTo = function(value, opt) {
        opt = opt || {};
        if (opt.timer === undefined ) opt.timer = 500;
        var $item =  $("html,body");
        if (value === undefined){
            return $(window).scrollTop();
        }
        if (typeof(value) === "number") {
            $item.animate({scrollTop: value}, opt.timer);
        } else {
            if (!$(value).length) return false;
            var sct = $(value).offset().top;
            if (opt.offset) sct += opt.offset;
            $item.animate({scrollTop: sct},opt.timer);
        }
        return false;
    };
    /** is block in visible part of window */
    this.isInWindow = function(el){
        var scrollTop = $(window).scrollTop();
        var windowHeight = $(window).height();
        var $el = $(el);
        var offset = $el.offset();
        return !!(offset && scrollTop <= offset.top && ($el.height() + offset.top) < (scrollTop + windowHeight));
    };
    /** Unused. GetPosition */
    this.getPosition = function(e){
        var left = 0, top = 0;
        while (e.offsetParent){
            left += e.offsetLeft;
            top  += e.offsetTop;
            e = e.offsetParent;
        }
        left += e.offsetLeft;
        top  += e.offsetTop;
        return {x:left, y:top};
    };
    /** Convert Params to url string or get params from url*/
    this.urlParams = function(params, url) {
        if (params === undefined) {
            if (!location.search) return {};
            var data = {};
            var pairs = location.search.substr(1).split('&');
            for (var i = 0; i < pairs.length; i++) {
                var param = pairs[i].split('=');
                data[param[0]] = decodeURIComponent(param[1]);
            }
            return data;
        } else {
            url = url || '';
            var paramStr = [];
            for (var key in params) {
                if (!params.hasOwnProperty(key)) continue;
                if (params[key] !== ''){
                    paramStr.push(key+'='+encodeURIComponent(params[key]));
                }
            }
            return url + ((url && paramStr) ? '?' : '') + paramStr.join('&');
        }
    };

    /** Collect params */
    this.collectParams = function(selector) {
        if (selector === undefined) return {};
        var ret = {};
        $(selector).each(function(indx, element) {
            var name = $(this).attr('name') || $(this).attr('data-name');
            if (name) {
                if ($(this).is('input[type=checkbox]')) {
                    if ($(this).attr('data-value')) {
                        if ($(this).is(':checked'))
                            ret[name] = ((ret[name]) ? ret[name]+';' : '') + $(this).attr('data-value');
                    } else {
                        ret[name] = ($(this).is(':checked'))?'1':'0';
                    }
                } else if ($(this).is('input[type=radio]')) {
                    if ($(this).is(':checked')) {
                        ret[name] = $(this).val();
                    } else {
                        if (ret[name] === undefined) {
                            ret[name] = '';
                        }
                    }
                } else if ($(this).is('.take_html, [data-take=html]')) {
                    ret[name] = $(this).html();
                } else if ($(this).hasClass('ckeditor')){
                    try {
                        ret[name] = CKEDITOR.instances[$(this).attr('id')].getData();
                    } catch (ex) {
                        console.log('CKEDITOR error - cant get data');
                    }
                } else if ($(this).hasClass('tinymce')){
                    try {
                        ret[name] = tinyMCE.editors[$(this).attr('id')].getContent();
                    } catch (ex) {
                        console.log('TinyMCE error - cant get data');
                    }
                } else {
                    ret[name] = $(this).val();
                }
            }
        });
        return ret;
    };
    /** Insert collected params into form inputs */
    this.loadCollectedParams = function(selector, collected) {
        for(var key in collected){
            if (!collected.hasOwnProperty(key)) continue;
            $el = $(selector + '[name='+key+'],' + selector + '[data-name='+key+']');
            if (($el.attr('type') === 'text') || $el.is('textarea')) $el.val(collected[key]);
            if ($el.is('.take_html, [data-take=html]')) $el.html(collected[key]);
            if ($el.attr('type') === 'radio') $el.filter('[value="'+collected[key]+'"]').prop('checked',true);
            if ($el.attr('type') === 'checkbox') {
                $el.prop('checked',false);
                if ((''+collected[key]).indexOf(';')=== -1){
                    parseInt(collected[key]) && $el.prop('checked',true);
                }else{
                    var vals = (''+collected[key]).split(';');
                    for(var ckey in vals){
                        $el.filter('[data-value="'+vals[ckey]+'"]').prop('checked',true);
                    }
                }
            }
            if ($el.is('select')) $el.find('[value="'+collected[key]+'"]').prop('selected',true);
        }
    };
    // easilyPostAjax
    this.easilyPostAjax = function(url, insertSelector, postObj, postSelector, callback, callBefore, opt){
        opt = opt || {};
        opt.disableClass = self.def.mform.disableClass;
        postObj = self.get(postObj) || {};
        if (self.def.easilyDefObj) {
            var ext = self.get(self.def.easilyDefObj);
            postObj = $.extend(ext, postObj);
        }
        var data = $.extend(postObj, self.collectParams(postSelector),{mjsaAjax:true});
        if (callBefore && !callBefore(data)) return false;
        if (opt.el){
            if ($(opt.el).hasClass(opt.disableClass)) {
                return false;
            } else {
                $(opt.el).addClass(opt.disableClass);
            }
        }
        self.ajax.send({
            url: url,
            type: opt.ajaxtype || 'POST',
            data: data,
            success: function(resp) {
                if (insertSelector !== undefined && (opt.isDoHtml === undefined || self.get(opt.isDoHtml))) {
                    if (self.get(opt.simpleHtml)) {
                        $(insertSelector).html(resp);
                    } else {
                        self.html(insertSelector, resp);
                    }
                }
                if (opt.el) $(opt.el).removeClass(opt.disableClass);
                callback && callback(resp, data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (opt.el) $(opt.el).removeClass(opt.disableClass);
                if (opt.error) {
                    opt.error(jqXHR, textStatus, errorThrown);
                } else  {
                    self.ajax.error(jqXHR, textStatus, errorThrown);
                }
            }
        });
        return false;
    };
    /** Grab response tag @deprecated */
    this.grabResponseTag = function(response, tag, single) {
        if (response.indexOf(tag) === -1) return false;
        var content_separated = response.split(tag);
        var result = [];
        for(i = 1; i < content_separated.length; i++) {
            if (i % 2){
                if (single) return content_separated[i];
                result.push(content_separated[i]);
            }
        }
        return result;
    };
    /** Easily ajax for Form submits */
    this.mFormSubmit = function(el, link, options){
        var $el = $(el);
        var opt = $.extend(self.clone(self.def.mform), options || {});
        if ($el.hasClass(opt.disableClass)) {
            return false;
        } else {
            $el.addClass(opt.disableClass);
        }
        var $form = $el.parents(opt.selector);
        $form.find(opt.errorSelector).html('');
        var paramSelector = $form
            .find('.'+opt.incorrectClass).removeClass(opt.incorrectClass).end()
            .find(opt.inSelector);
        self.easilyPostAjax(
            link,
            opt.service,
            opt.param || {},
            paramSelector,
            function(response, data) {
                var i;
                $el.removeClass(opt.disableClass);
                if (opt.callback && !opt.callback(response,data,el)) return false;
                var errorMsgs = self.grabResponseTag(response,opt.errorSeparator);
                if (errorMsgs) {
                    for (i = 0; i < errorMsgs.length; i++) {
                        $form.find(opt.errorSelector).append(errorMsgs[i]);
                    }
                }
                var incorrects = self.grabResponseTag(response,opt.incorrectSeparator);
                if (incorrects) {
                    for (i = 0; i < incorrects.length; i++) {
                        var $input = $form.find('[name='+incorrects[i]+']').addClass(opt.incorrectClass);
                        if (i === 0  && $form.attr(opt.scrollToIncorrectAttr)){
                            self.scrollTo($input);
                        }
                    }
                }
                var msgs = self.grabResponseTag(response,opt.formReplaceSeparator);
                if (msgs) {
                    for (i = 0; i < msgs.length; i++) {
                        if (i === 0) $form.html('');
                        $form.append(msgs[i]);
                    }
                }
                return false;
            },
            undefined,
            $.extend(
                {
                    error: function(jqXHR, textStatus, errorThrown) {
                        $el.removeClass(opt.disableClass);
                        self.ajax.error(jqXHR, textStatus, errorThrown);
                    }
                },
                opt.easyOpt || {}
            )
        );
        return false;
    };

    this.upload = {};
    this.upload._send = function(filesInfo, opt) {
        if (opt.callBefore && !opt.callBefore(filesInfo)) return false;
        var files = [];
        for (var i = 0; i < filesInfo.length; i++) {
            if (
                opt.maxSize && filesInfo[i].size && filesInfo[i].name && filesInfo[i].size > opt.maxSize
            ) {
                if (opt.maxSizeException) opt.maxSizeException(filesInfo[i]);
                else  self.hints.printError('File '+ filesInfo[i].name + ' is too large. Max file size is '+ parseInt(opt.maxSize /1024) + ' KB.');
            } else if (
                (opt.allowExt && filesInfo[i].name)
                && (!self.inArray( filesInfo[i].name.slice(filesInfo[i].name.lastIndexOf('.')+1).toLowerCase(), opt.allowExt))
            ){
                if (opt.allowExtException) opt.allowExtException(filesInfo[i]);
                else self.hints.printError('File ' + filesInfo[i].name + ' is not allowed');
            } else if (
                opt.maxFiles && files.length >= opt.maxFiles
            ){
                if (opt.maxFilesException) opt.maxFilesException(filesInfo[i]);
                else self.hints.printError('File '+ filesInfo[i].name + ' is not to be upload. Max files to upload is '+opt.maxFiles+' .' );
            } else {
                files.push(filesInfo[i]);
            }
        }
        if (!files.length) return false;
        var http = new XMLHttpRequest();
        if (http.upload && http.upload.addEventListener) {
            http.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable && opt.callProcess) opt.callProcess(e, {loaded:e.loaded, total:e.total});
            }, false);
            http.onreadystatechange = function () {
                if (this.readyState === 4) {
                    if (opt.callAfterPriority) opt.callAfterPriority(this);
                    else opt.callAfter && opt.callAfter(this);
                    if (this.status === 200) opt.callSuccess && opt.callSuccess(this,this.response);
                    else opt.callError && opt.callError(this);
                }
            };
            http.upload.addEventListener('load',function(e) {
                // Событие после которого также можно сообщить о загрузке файлов.// Но ответа с сервера уже не будет.// Можно удалить.
            });
            http.upload.addEventListener('error',function(e) {
                if (opt.callAfterPriority) opt.callAfterPriority(this);
                else opt.callAfter && opt.callAfter(this);
                opt.callError && opt.callError(this);
                self.ajax.error({
                    status: e,
                    statusText: 'mjsa UPLOAD FILE Error'
                }); // Паникуем, если возникла ошибка!
            });
        }
        var form = new FormData();
        //form.append('path', location.href);
        if (opt.params){
            var params = self.get(opt.params);
            for (var key in params) {
                if (!params.hasOwnProperty(key)) continue;
                form.append(key, params[key]);
            }
        }
        if (!opt.name) opt.name = 'thefiles';
        for (var j = 0; j < files.length; j++) {
            form.append(opt.name+((opt.oneFileSimple)?'':'[]'), files[j]);
        }
        if (opt.callPre) opt.callPre(files);
        http.open('POST', opt.url);
        if (opt.headers){
            var headers = self.get(opt.headers);
            for (var headerKey in headers) {
                if (!headers.hasOwnProperty(headerKey)) continue;
                http.setRequestHeader(headerKey, headers[headerKey]);
            }
        }
        http.send(form);
        return http;
    };
    /** HTML 5 upload files: used FormData
    var options = {
        inputFile : '#uploadfile',
        url: '/auth/upload_profile_photo',
        name: 'thefiles',
        params: {},
        callUnsupported: function(){alert('Browser is deprecated and not support');},
        callBefore: function(files){}, // return false to cancel upload
        callPre: function(files){},
        callProcess: function(e,obj){},
        callAfter: function(obj){},
        callSuccess: function(obj,response){},
        callError: function(obj){},
        maxSize: 823000,
        maxSizeException: function(file){},
        oneFileSimple: false,
        maxFiles: 15,
        maxFilesException: function(file){},
        allowExt: ['jpg','jpeg','png','gif'],
        allowExtException: function(file){},
        multirequests: false, // every file in single request
    }; */
    this.upload.send = function(opt){
        // TODO: upload drag and drop
        opt = opt || {};
        var http = null;
        opt.url = opt.url || '/upload';
        if (opt.inputFile === undefined) {
            return http;
        }
        if (opt.action === 'cancel'){
            http = $(opt.inputFile).data('http');
            $(opt.inputFile).val('');
            $(opt.inputFile).data('queue',[]);
            http && http.abort(); // В нек браузерах выхывается error, и соответственно callAfterPriority повторно
            if (opt.callAfterPriority) opt.callAfterPriority();
            else opt.callAfter && opt.callAfter();
            return false;
        }
        $(opt.inputFile).on('change',function(event){
            var filesInfo = $(this)[0].files;
            if (filesInfo === undefined || window.FormData === undefined) {
                if (opt.callUnsupported) opt.callUnsupported();
                else self.hints.printError('Browser is deprecated and not supported');
            }
            if (!filesInfo.length) return false;
            if (opt.multirequests){
                var filesInfo_arr = [];
                for (var i = 0; i < filesInfo.length; i++) {
                    filesInfo_arr.push(filesInfo[i]);
                }
                $(opt.inputFile).data('queue',filesInfo_arr);
                $(opt.inputFile).data('queue_total',filesInfo_arr.length);
                opt.oneFileSimple = true;
                opt.callAfterPriority = function(e) {
                    var queue_filesInfo = $(opt.inputFile).data('queue');
                    var total = $(opt.inputFile).data('queue_total');
                    var done = total - queue_filesInfo.length;
                    if (queue_filesInfo.length){
                        var sub_filesInfo = [];
                        sub_filesInfo.push(queue_filesInfo.shift());
                        $(opt.inputFile).data('queue',queue_filesInfo);

                        http = self.upload._send(sub_filesInfo, opt);
                        $(opt.inputFile).data('http',http);
                    }
                    if (opt.callAfter) {
                        return opt.callAfter(e,{done:done,total:total});
                    }
                    return false;
                };
                opt.callAfterPriority(undefined);
            } else {
                http = self.upload._send(filesInfo,opt);
                $(opt.inputFile).data('http',http);
            }
        });
        return http;
    };


    /*
    opt = {
        url:'',
        name:'',

        langUnsupport:'',
        langFileProcess:'',
        langUploaded:'',

        maxSize: 10000000,
        maxFiles: 1,
        oneFileSimple: true,
        allowExt: ['jpg','jpeg','png','gif'],
    multirequests: false
    };
    */
    this.mFormUpload = function(selector,callback,opt){
        opt = opt || {};
        var def = {
            inputFile : selector+' .mUpload',
            url: '/japi/upload',
            name: 'mFile',
            maxSize: 10000000,
            maxFiles: 1,
            oneFileSimple: true,
            allowExt: ['jpg','jpeg','png','gif'],
            multirequests: false
        };
        var mUploadOpt = $.extend(self.clone(def),{
            callUnsupported: function(){
                self.hints.printError(opt.langUnsupport || 'Ваш браузер устарел и не поддерживает современную технологию загрузки файлов');
            },
            callPre: function(obj){
                $(selector).find('.mUpload').hide();
                $(selector).find('.m_progressbar_container').show().find('.track').css('width','0%');
            },
            callProcess: function(obj,info){
                var percent = parseInt( info.loaded * 100 / info.total);
                $(selector).find('.m_progressbar_container .filetrack').css('width',''+percent+'%');
                if (percent >= 99){
                    $(selector).find('.m_progressbar_container .counter_text .counter_text_percent').html(opt.langFileProcess || 'Обработка файлa(ов)...');
                } else {
                    $(selector).find('.m_progressbar_container .counter_text .counter_text_percent').html((opt.langUploaded || 'Загружено') + ' ' + parseInt(info.loaded / 1024) + ' КB / ' + parseInt(info.total / 1024) + ' KB');
                }
            },
            callAfter: function(obj,doneInfo){
                if (obj === undefined) obj = {};
                if (doneInfo){
                    var percent = parseInt(doneInfo.done * 100 / doneInfo.total);
                    $(selector).find('.m_progressbar_container .totaltrack').css('width',''+percent+'%');
                    $(selector).find('.m_progressbar_container .counter_text .counter_text_files').html(opt.langFileDone || 'Загрузка файлов ('+doneInfo.done+' из '+doneInfo.total+') : ');
                    doneInfo.done && mUploadOpt.multirequestsCallback && mUploadOpt.multirequestsCallback(obj.response)
                }
                if (!doneInfo || doneInfo.done === doneInfo.total){
                    $(selector).find('.mUpload').show().val('');
                    $(selector).find('.m_progressbar_container').hide();
                    if (callback) callback(obj.response);
                    else self.html(self.def.service, obj.response);
                }
            }
        },opt);
        if (opt && opt.cancel) {
            mUploadOpt.action = 'cancel';
            self.upload.send(mUploadOpt);
            return false;
        }
        var html = '<input type="file" class="standart_input mUpload" '+((mUploadOpt.maxFiles > 1)?'multiple':'')+' name="'+mUploadOpt.name+'" />';
        html += '<div class="m_progressbar_container" style="display:none;">';
        if (mUploadOpt.multirequests){
            html    += '<div class="progressbar"><div class="track totaltrack"></div></div>';
        }
        html    += '<div class="progressbar"><div class="track filetrack"></div></div>';
        html    += '<div class="m_cancel'+ ((opt.cancelClass)?' '+opt.cancelClass:'') +'" onclick="return mjsa.mFormUpload(\''+selector+'\',undefined,{cancel:true})">';
        html        += opt.cancelText || 'Cancel';
        html    += '</div><div class="counter_text"><span class="counter_text_files"></span><span class="counter_text_percent"></span></div>';
        html += '</div>';
        $(selector).html(html);
        self.upload.send(mUploadOpt);
        return false;
    };

    this.bodyAjax = {};
    this.bodyAjax._lastLink = '';
    this.bodyAjax._curentPath = '';
    this.bodyAjax.shadow = function(nested) {
        var inner = '';
        if (self.def.loadingBlock) inner += '' + self.def.loadingBlock + '';
        var ret = $('.mjsa_ajax_shadow');
        if (ret.length === 0 && nested !== true) {
            $('body').append('<div class="mjsa_ajax_shadow" onclick="$(this).hide();"><div class="inner"></div>'+inner+'</div>');
            ret = self.bodyAjax.shadow(true);
        }
        return ret;
    };
    this.bodyAjax.init = function(selector){
        if (!selector) selector = 'a';
        self.def.bodyAjax = true; // set on bodyAjax in location
        $(document).on('click', selector, function(){
            if (($(this).attr('href') !== '#'))
                self.bodyLink($(this).attr('href'),{el:this});
            return false;
        });
        self.bodyAjax._curentPath = location.pathname+location.search;
        if ((window.history && history.pushState)){
            window.addEventListener("popstate", function(e) {
                if (location.pathname+location.search !== self.bodyAjax._curentPath){
                    self.bodyLink(location.pathname+location.search,{nopush:true,scrollto:e.state.scroll});
                }
                e.preventDefault();
            }, false);
        }
        return false;
    };
    this.bodyUpdate = function(){
        self.bodyLink(location.pathname + location.search, {nopush:true, callback:function(){}});
    };
    this.bodyLink = function(link, opt) {
        opt = opt || {};
        if (opt.callBefore && !opt.callBefore(link, opt)) return false;
        var noajax = (opt && opt.el) ? $(opt.el).attr('noajax') : false;
        if ((link.indexOf('http') === 0) || !(window.history && history.pushState) || (noajax) || !$(self.def.bodyAjax_inselector).length) {
            if (self.def.testing) {
                console.log('bodyAjax skipped(link: ',link,', history:',window.history,', noajax',noajax, 'in_selector',$(self.def.bodyAjax_inselector).length);
            }
            if (document.location.href === link) { document.location.reload(); return false; }
            document.location.href = link; return false;
        }
        $title = $('title');
        if (opt.pushonly) {
            if (self.bodyAjax._curentPath !== link){
                history.replaceState({url:self.bodyAjax._curentPath, title:$title.html(), scroll:self.scrollTo()}, $title.html(), self.bodyAjax._curentPath);
                history.pushState({url:link,title: $title.html(),scroll:0}, $title.html(), link);
                self.bodyAjax._curentPath = link;
            }
            return false;
        }
        self.bodyAjax.shadow().animate({opacity: "show"},150);
        self.bodyAjax._lastLink = link;
        self.ajax.send({
            url: link, type: 'GET', data: {bodyAjax: 'true'}, timeout: self.def.bodyAjax_timeout,
            success:function(content){
                var collected = self.collectParams(self.def.haSaveSelector);
                var content_separated = undefined;
                if (self.bodyAjax._lastLink !== link) return false;
                if (content.indexOf('<ajaxbody_separator/>') !== -1) {
                    content_separated = content.split('<ajaxbody_separator/>');

                    if (self.bodyAjax._curentPath === link) opt.nopush = true;
                    if(!opt.nopush){
                        history.replaceState({url: self.bodyAjax._curentPath, title: $title.html(), scroll: self.scrollTo()}, $title.html(), self.bodyAjax._curentPath);
                        history.pushState({url:link,title:content_separated[0],scroll:0}, content_separated[0], link);
                        if (!opt.noscroll) self.scrollTo(0);
                    }
                    document.title = content_separated[0];
                    self.bodyAjax._curentPath = link;
                    if (self.def.bodyAjaxOnunloadFunc){
                        self.def.bodyAjaxOnunloadFunc();
                    }
                    self.html(self.def.bodyAjax_inselector, content_separated[1]);
                    self.loadCollectedParams(self.def.haSaveSelector,collected);
                    if (opt.scrollto !== undefined){
                        self.scrollTo(opt.scrollto,{timer:0});
                    }
                    if (self.def.bodyAjaxOnloadFunc){
                        self.def.bodyAjaxOnloadFunc();
                    }
                    if (opt.callback) opt.callback();

                    self.bodyAjax.shadow().queue(function(){$(this).animate({opacity: "hide"},150);$(this).dequeue();});
                } else if (content.indexOf('<mjsa_separator/>') === 0){
                    self.bodyAjax.shadow().queue(function(){$(this).animate({opacity: "hide"},150);$(this).dequeue();});
                    mjsa.html(self.def.service, content);
                } else {
                    location.href = link;
                }
            },
            error:function(jqXHR, textStatus, errorThrown){
                self.bodyAjax.shadow().queue(function(){$(this).animate({opacity: "hide"},150);$(this).dequeue();});
                self.ajax.error(jqXHR, textStatus, errorThrown);
            }
        });
    };

    /** Changing url brawser string, support bodyAjax */
    this.location = function(link){
        if (self.def.bodyAjax) {
            self.bodyLink(link);
        } else {
            document.location.href = link;
        }
        return false;
    };


    // insert html with state look [redirect,alert,stop,html replace append prepand, errors and success hints]
    this.html = function(selector,content){
        var jSel = $(selector),
            i,
            needHtml = true,
            content_separated = undefined,
            par = undefined;
        if (self.def.htmlInterception){
            if (!self.def.htmlInterception(content)) return jSel;
        }
        if (content.substring(0,'<mjsa_separator/>'.length) !== '<mjsa_separator/>') { // quick end
            jSel.html(content);
            return jSel;
        }
        if (content.indexOf('<location_separator/>') !== -1) {
            content_separated = content.split('<location_separator/>');
            if (content_separated.length > 1) {
                self.popups.closeAll();
                self.location(content_separated[1]);
            }
        }
        if (content.indexOf('<error_separator/>') !== -1) {
            content_separated = content.split('<error_separator/>');
            if (content_separated.length > 1) {
                self.hints.printError(content_separated[1]);
            }
        }
        if (content.indexOf('<success_separator/>') !== -1) {
            content_separated = content.split('<success_separator/>');
            if (content_separated.length > 1) {
                self.hints.printSuccess(content_separated[1]);
            }
        }
        if (content.indexOf('<html_replace_separator/>') !== -1) {
            content_separated = content.split('<html_replace_separator/>');
            for(i = 1; i < content_separated.length; i++) {
                if (i%2){
                    par = content_separated[i].split('<html_replace_to/>');
                    if (par.length > 1) $(par[0]).html(par[1]);
                }
            }
        }
        // @todo 8: do easily
        if (content.indexOf('<open_content_popup_separator/>') !== -1) {
            content_separated = content.split('<open_content_popup_separator/>');
            for(i = 1; i < content_separated.length; i++) {
                if (i%2){
                    par = content_separated[i].split('<open_content_data/>');
                    if (par.length > 2) {
                        try{
                            var parOpt = JSON.parse(par[2]);
                            self.popups.openWithContent(par[0],par[1],parOpt);
                        } catch(e){
                            console.log(e);
                        }
                    }
                }
            }
        }
        if (content.indexOf('<html_append_separator/>') !== -1) {
            content_separated = content.split('<html_append_separator/>');
            for(i = 1; i < content_separated.length; i++) {
                if (i%2){
                    par = content_separated[i].split('<html_append_to/>');
                    if (par.length > 1) $(par[0]).append(par[1]);
                }
            }
        }
        if (content.indexOf('<html_prepend_separator/>') !== -1) {
            content_separated = content.split('<html_prepend_separator/>');
            for(i = 1; i < content_separated.length; i++) {
                if (i%2){
                    par = content_separated[i].split('<html_prepend_to/>');
                    if (par.length > 1) $(par[0]).append(par[1]);
                }
            }
        }
        if (content.indexOf('<append_separator/>') !== -1) {
            content_separated = content.split('<append_separator/>');
            if (content_separated.length > 1){
                jSel.append(content_separated[1]);
                needHtml = false;
            }
        }
        if (content.indexOf('<prepend_separator/>') !== -1) {
            content_separated = content.split('<prepend_separator/>');
            if (content_separated.length > 1){
                jSel.append(content_separated[1]);
                needHtml = false;
            }
        }
        if (content.indexOf('<noservice_separator/>') !== -1) {
            content_separated = content.split('<noservice_separator/>');
            if (content_separated.length > 2){
                content = content_separated[0];
                for(i = 1; i < content_separated.length; i++) {
                    if (!(i%2)) content += content_separated[i];
                }
            }
        }
        if (content.indexOf('<stop_separator/>') !== -1) needHtml = false;
        if (needHtml) {
            jSel.html(content);
        }
        return jSel;
    };

    // options = {
    //	timeout: 60000//milliseconds
    //	callNoLocation: function(){} // unavailable get Location
    //	callUnsupported: function(){}
    //	callAccessDenied: function(){} //
    //	callUnavailablePosition: function(){}
    //	callTimeout: function(){}
    //}
    this.geoLocation = function(func, options){
        options = options || {};
        options.timeout = options.timeout || 60000;
        if(!navigator.geolocation){
            options.callUnsupported && options.callUnsupported();
            options.callNoLocation && options.callNoLocation();
            return false;
        }
        navigator.geolocation.getCurrentPosition(
            func, function(err){
                self.def.testing && self.debug.log(err);
                if(err.code === 1) {
                    options.callAccessDenied && options.callAccessDenied();
                }else if(err.code === 2) {
                    options.callUnavailablePosition && options.callUnavailablePosition();
                }else if(err.code === 3) {
                    options.callTimeout && options.callTimeout();
                }
                options.callNoLocation && options.callNoLocation();
            },options
        );
        return false;
    };

    /* LocalStorage and SessionStorage
    opt = {local: true, clear:true, callUnsupported: function(){}} */
    this.webStorage = function(opt,key,value){
        opt = opt || {};
        var storeType = undefined;
        if (opt.local) storeType = window.localStorage;
        else storeType = window.sessionStorage;
        if (!storeType){
            opt.callUnsupported && opt.callUnsupported();
            return false;
        }
        if (opt.clear) {
            storeType.clear();
            return false;
        }
        if (key===undefined) return false;
        if (value===undefined){
            var ret, ret_json_maybe = storeType.getItem(key);
            if (ret_json_maybe){
                try{
                    ret = JSON.parse(ret_json_maybe);
                } catch(e){
                    ret = ret_json_maybe;
                }
            }
            return ret;
        }
        if (value===null){
            storeType.removeItem(key);
            return false;
        }
        if (typeof value === 'object'){
            storeType.setItem(key,JSON.stringify(value));
        } else {
            storeType.setItem(key,value);
        }

        return false;
    };

    this.selection = {};

    /* selected text on window */
    this.selection._get = function(w){
        var ie = false, si = undefined;
        if ( w.getSelection ) {
            si = w.getSelection();
        } else if ( w.document.getSelection ) {
            si = w.document.getSelection();
        } else if ( w.document.selection ) {
            ie = true;
            si = w.document.selection.createRange();
        }
        if(!ie){
            var range = (si.rangeCount)?si.getRangeAt(0):w.document.createRange();
            var d = w.document.createElement('div');
            d.appendChild(range.cloneContents());
            return {text:si.toString(), html:d.innerHTML, range:range,si:si };
        } else {
            return {text:si.text, html:si.htmlText, ieRange:si};
        }
    };
    this.selection._to = function(w, sel, text){
        if(sel.range){
            var root = sel.range.commonAncestorContainer;
            sel.range.deleteContents();
            var d = w.document.createElement('div'); d.innerHTML = text;
            var docFragment = w.document.createDocumentFragment();
            while (d.firstChild) {
                docFragment.appendChild(d.firstChild) ;
            }
            sel.range.collapse(false);
            sel.range.insertNode(docFragment);
            return root;
        } else if(sel.ieRange){
            sel.selectedText.pasteHTML(text); return undefined;
        } else {
            console.log('incorrect selection'); return false;
        }
    };
    this.selection.selected = function(opt){
        opt = opt || {};
        var w = opt.window || window;

        var sel = self.selection._get(w);

        if (opt.replace){
            var root = self.selection._to(w,sel,opt.replace(sel));
            //if (root && !opt.nocorrect) $(root).html($(root).html().replace(/<[^\/>][^>]*>[^<]<\/[^>]+>/gim, ''));
            if (root && !opt.noEmptyCorrect) $(root).parent().parent().find('p,b,i,u,strong,em').filter('*:empty').remove();
            if (root && !opt.noInCorrect) {
                $(root).parent().parent().find('b b,strong strong,i i,em em,u u').each(function(){
                    var $parent = $(this).parent(); $parent.html($parent.text());
                });
            }
            sel = self.selection._get(w);
        }

        return sel;
    };
    this.selection.text = function(selector,opt){ // Thanks http://forum.php.su/topic.php?forum=46&topic=13
        opt = opt || {};
        var $sel = $(selector).filter('input,textarea');
        if (!$sel.length) { console.log('selector not found'); return false; }
        var elem = $sel.get(0);
        var selText = '';
        if (elem.selectionStart !== undefined) {
            selText = elem.value.substring(elem.selectionStart, elem.selectionEnd);
            if (opt.replace && selText !== ''){
                selText = opt.replace(selText);
                elem.value = elem.value.substring(0,elem.selectionStart) + selText + elem.value.substring(elem.selectionEnd);
            }
        } else { // IE
            var textRange = document.selection.createRange ();
            selText = textRange.text;
            if (opt.replace  && selText !== ''){
                selText = opt.replace(selText);
                var rangeParent = textRange.parentElement ();
                if (rangeParent === elem){textRange.text = selText;}
            }
        }
        return selText;
    };

    this.getTimezone = function(){ // Thanks http://paperplane.su/php-timezone/
        var now = new Date();
        var timezone = { offset: 0, dst: 0};
        timezone.offset = now.getTimezoneOffset();
        var d1 = new Date(); var d2 = new Date();
        // Первую дату установим на 1 января текущего года
        d1.setDate(1); d1.setMonth(1);
        // Вторую дату установим на 1 июля текущего года
        d2.setDate(1); d2.setMonth(7);
        // Если смещение часовых поясов совпадают, то поправка на летнее время отсутствует
        if(parseInt(d1.getTimezoneOffset()) === parseInt(d2.getTimezoneOffset())) {
            timezone.dst = 0;
        } else { // если поправка на летнее время существует, то проверим активно ли оно в данный момент
            // Выясним в каком полушарии мы находимся в северном или южном
            // Разница будет положительной для северного и отрицательной для южного
            var hemisphere = parseInt(d1.getTimezoneOffset()) - parseInt(d2.getTimezoneOffset());
            if((hemisphere > 0 && parseInt(d1.getTimezoneOffset()) === parseInt(now.getTimezoneOffset()))
                || (hemisphere < 0 && parseInt(d2.getTimezoneOffset()) === parseInt(now.getTimezoneOffset()))) {
                timezone.dst = 0;
            } else {
                timezone.dst = 1;
            }
        }
        return timezone;
    };



    /*
    opt = {
        once: false,
        toSelector: '#to',
        url: '/ajax/autocomlete'
        params: {},
        collect: '.ac_param',
        minchars: 3
        dataType: 'jsonp'
        callBefore: function(params,el,keyup_event){
            if (params.query.length < 3) {clear(); return false;}
            if (iWantAddParam){
                params.newparam = 'myvalue';
                return params;
            }
            return true;
        }, some user interception
        callAfter: function(param,response)
    }
    */
    this.autocomplete = function(input_selector,options){
        var defOpt = {
            toSelector: '#test',
            itemSelector: '.item',
            queryAttr:'data-value',
            selectedClass: 'selected',
            url: '/default/autocomplete',
            params: undefined,// aditional POST params as default
            collect: undefined, // collect values to params
            minchars: 2,
            once: undefined, // init for one time only
            scrollerSelector: undefined
        };
        var opt = $.extend(self.clone(defOpt), options || {});

        if (opt.crossDomain){
            opt.crossDomain = { crossDomain: true, dataType:'jsonp'};
        } else {
            opt.crossDomain = {};
        }

        self.autocompleteHndlr = null;
        var last_query = '';
        var jDoc = document, jSel = input_selector;
        if (opt && opt.once) { jDoc = input_selector; jSel = undefined;}
        $(jDoc).on('keydown', jSel, function(e){
            // Interception up, down,enter, escape keys
            if (e.keyCode && (e.keyCode===38 || e.keyCode===40 || e.keyCode===13 || e.keyCode===27)){
                if (e.keyCode===38 || e.keyCode===40){ // up key: select previos item
                    var borderSel = ':last';
                    if (e.keyCode===40) borderSel = ':first'; //key down
                    var $selected = $(opt.toSelector).find(opt.itemSelector+'.'+opt.selectedClass).removeClass(opt.selectedClass);
                    if (!$selected.length){
                        $selected = $(opt.toSelector).find(opt.itemSelector+borderSel).addClass(opt.selectedClass);
                        $(this).attr(opt.queryAttr,$(this).val());
                        $(this).val($selected.attr(opt.queryAttr)||$(this).val());
                    } else {
                        $selected = (e.keyCode===40)?$selected.next(opt.itemSelector).addClass(opt.selectedClass)
                            :$selected.prev(opt.itemSelector).addClass(opt.selectedClass);
                        if (!$selected.length) {
                            $(this).val($(this).attr(opt.queryAttr)||$(this).val());
                        } else {
                            $(this).val($selected.attr(opt.queryAttr)||$(this).val());
                        }
                    }
                    opt.callChoose && opt.callChoose($selected,opt);
                    if(opt.callScroller) {
                        opt.callScroller($selected,opt);
                    } else {
                        if (opt.scrollerSelector){
                            var $scroller = $(opt.toSelector).find(opt.scrollerSelector);
                            if ($selected.length && opt.scrollerSelector){
                                var contentHeight = $scroller[0].scrollHeight;
                                var scrollTop = $scroller.scrollTop();
                                var scrollHeight = $scroller.height();
                                var maxScrollTop = contentHeight - scrollHeight;
                                if ($selected[0].offsetTop < scrollTop
                                    || $selected[0].offsetTop+$selected.height() > scrollTop + scrollHeight
                                ){
                                    var scrollTo = $selected[0].offsetTop;
                                    if (scrollTo > maxScrollTop) scrollTo = maxScrollTop;
                                    $scroller.scrollTop(scrollTo);
                                }
                            } else {
                                $scroller.scrollTop(0);
                            }
                        }
                    }
                }
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
        });
        $(jDoc).on('keyup paste', jSel, function(e){
            // Interception up, down,enter, escape keys
            if (e.keyCode && (e.keyCode===38 || e.keyCode===40 || e.keyCode===13 || e.keyCode===27)){
                if (e.keyCode===13){ // enter key: select item, event click, and hide autocomplete
                    $(opt.toSelector).find(opt.itemSelector+'.'+opt.selectedClass).removeClass(opt.selectedClass).click();
                    $(opt.toSelector).html('').hide();
                }
                if (e.keyCode===27){ // escape key, hide autocompleate
                    $(opt.toSelector).html('').hide();
                }
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
            opt.params = self.get(opt.params) || {};
            opt.url = opt.url || '';
            if (self.autocompleteHndlr) clearTimeout(self.autocompleteHndlr);
            self.autocompleteHndlr = setTimeout(function() {
                clearTimeout(self.autocompleteHndlr); //?
                if (opt.collect){
                    opt.params = $.extend(opt.params,self.collectParams(opt.collect));
                }
                opt.params.query = $(self).val();
                if (opt.minchars){
                    if (opt.params.query.length < opt.minchars){
                        $(opt.toSelector).html('').hide();
                        return false;
                    }
                }
                if (opt.callBefore !== undefined) {
                    var ret = opt.callBefore(opt.params,self,e);
                    if (ret === false) return false;
                    if (ret.query !== undefined) opt.params = ret;
                }
                last_query = opt.params.query;
                $(self).attr(opt.queryAttr,last_query);
                $.ajax($.extend({url:opt.url, data:opt.params, type: opt.ajaxType || "POST", dataType: opt.dataType || "html",
                    success:function(data) {
                        try {
                            var obj = (typeof data === 'object') ? data : JSON.parse(data);
                            if (obj.query === last_query) {
                                if (opt.callAfter === undefined || opt.callAfter(opt.params,self,data) !== false) {
                                    $(opt.toSelector).show().html(obj.response);
                                }
                            }
                        } catch(ex) {
                            console.log('search error: response is:',data);
                        }
                        self.autocompleteHndlr = null;
                    }},opt.crossDomain));
            }, 400);
            return true;
        });
        return false;
    };

    this.onClickEnterInit = function(selector,opt){
        var jDoc = document, jSel = selector;
        if (opt && opt.once) { jDoc = selector; jSel = undefined;}
        $(jDoc).on('keypress', jSel, function(e) {
            e = e || window.event;
            if (e.keyCode===13 || e.keyCode===10){
                if (opt && (opt.ctrl === true) && !e.ctrlKey) return true;
                if (opt && opt.callback) opt.callback.call(this);
            }
            return true;
        });
    };
    this.getByteLength = function(str){
        return encodeURIComponent(str).replace(/%../g, 'x').length;
    };

    // TODO 2: drag-and-drop
    // TODO 4: close popup by escape
    this.popups = {};
    this.popups._openedPopups = {};
    this.popups.getOpened = function(){
        for(var key in self.popups._openedPopups){
            if (self.popups._openedPopups[key]) return key;
        }
        return undefined;
    };
    this.popups.closeAll = function() {
        for(var key in self.popups._openedPopups){
            if (self.popups._openedPopups[key]) self.popups.close(key);
        }
    };
    this.popups._getSelector = function(name){
        return '#'+name;
    };
    this.popups.close = function(name) {
        var selector = self.popups._getSelector(name);
        var options = $(selector).data('options');
        if (!options) return false;
        if (!self.popups._openedPopups[name]) return false;
        self.popups._openedPopups[name] = undefined;
        $(selector).find('.toggle_popup_scroll').hide();
        self.popups._loading(name,false);
        self.popups._shadow(name,false);
        if (options.callClose !== undefined) {
            options.callClose();
        }
        //$(selector).find('.popup_scroll_content').html('');
        $(selector).html('');
        return false;
    };
    this.popups.openWithUrl = function(name, url, options) {
        return self.popups._open(name, url, undefined, options);
    };
    this.popups.openWithContent = function(name, content, options) {
        return self.popups._open(name, undefined, content, options);
    };
    this.popups._open = function(name, url, content, options) {
        // Create
        var opt = $.extend(self.clone(self.def.popups), options);
        opt.width = ($(window).width() > opt.maxWidth + opt.maxWidthSpace) ? opt.maxWidth : $(window).width() - opt.maxWidthSpace;
        opt.name = name;
        opt.selector = self.popups._getSelector(name);
        $container = self.popups._getContainer(name);
        if (opt.clearly || !$container.find('.popup_scroll_body').length) {
            $(opt.selector).data('options',opt);
            $container.html(self.popups._getPopupTemplate(opt));
        }
        // Open
        self.popups._shadow(name, true);
        self.popups._loading(name, true);
        self.popups._openedPopups[name] = true;
        if (url !== undefined) {
            self.ajax.send({
                url: url, type: 'GET', data: {}, timeout: self.def.bodyAjax_timeout,
                success:function(data){
                    self.popups._loading(name, false);
                    self.popups._popup(name, true);
                    self.popups._into(selector, data);
                },
                error:function(jqXHR, textStatus, errorThrown){
                    self.popups.close(name);
                    self.ajax.error(jqXHR, textStatus, errorThrown);
                }
            });
        } else if (content !== undefined && content !== '') {
            self.popups._loading(name, false);
            self.popups._popup(name, true);
            self.popups._into(name, content);
            return false;
        }
        return false;
    };
    this.popups.show = function(name) {
        self.popups._shadow(name, true);
        self.popups._showPopup(name);
    };
    this.popups.hide = function(name) {
        self.popups._shadow(name, false);
        self.popups._showPopup(name);
    };
    this.popups._getContainer = function(name, nested){
        var $ret = $(self.popups._getSelector(name));
        if ($ret.length === 0 && nested !== true) {
            $('body').append('<div id="'+name+'" class="scoll_popup_container '+name+'"> </div>');
            $ret = self.popups._getContainer(name, true);
        }
        return $ret;
    };
    this.popups._getPopupTemplate = function(options) {
        // style
        var contSelector = '.scoll_popup_container.'+options.name;
        var closePopupJs = "return " + options.modelName + ".close('" + options.name + "');";
        var html = '<style>';
        html += contSelector+' .popup_scroll_shadow{z-index: '+options.zindex+';}';
        html += contSelector+' .popup_scroll_loading{z-index: '+(options.zindex+1)+';}';
        html += contSelector+' .popup_scroll{width: '+(options.width+options.padding_hor)+'px; top: '+options.top+'px; margin-left: -'+((options.width+options.padding_hor)/2)+'px; z-index: '+(options.zindex+1)+';}';
        html += contSelector+' .popup_scroll_content{padding: '+options.padding_ver+'px '+options.padding_hor+'px; }';
        html += '</style>';
        // shadow
        html += '<div class="popup_scroll_shadow toggle_popup_scroll" onclick="' + closePopupJs + '"></div>';
        // popup container
        html += '<div class="popup_scroll_loading" onclick="' + closePopupJs + '">'+(options.loadingBlock ? options.loadingBlock : self.def.loadingBlock)+'</div>';
        html += '<div class="popup_scroll toggle_popup_scroll">';
        // popup body
        html += '<div class="popup_scroll_body">';
        // popup close botton
        if (options.closeBtnClass !== undefined) {
            html += '<div class="close_popup_scroll' + (options.closeBtnClass ? ' ' + options.closeBtnClass : '') + '" onclick="' + closePopupJs + '"></div>';
        }
        // popup content
        html += '<div class="popup_scroll_content"><br/><br/><br/>What?</div>';
        html += '</div>';
        html += '</div>';
        return html;
    };
    self.popups._shadow = function(name, show){
        var selector = self.popups._getSelector(name);
        var options = $(selector).data('options');
        if (show) {
            $(options.selector+' .popup_scroll_shadow').show();
        } else {
            $(options.selector+' .popup_scroll_shadow').hide();
        }
        if (self.popups.getOpened()) return false;
        if (show) {
            var nowpos = window.pageYOffset || (document.documentElement && document.documentElement.scrollTop) || (document.body && document.body.scrollTop);
            var con_width = $(options.mainContainer).css('width');
            var body_width = $('body').css('width');
            var con_width1 = parseInt(con_width);
            var body_width1 = parseInt(body_width);
            var left_p1 = (body_width1 - con_width1)/2;
            $(options.mainContainer).css('position', 'fixed').css('width','100%');
            $(options.mainContainer).css('top', '-'+nowpos+'px');
            $(options.mainContainer).css('left', ''+left_p1+'px');
            options.nowpos = nowpos;
        } else {
            $(options.mainContainer).css('position', 'relative');
            $(options.mainContainer).css('top', 'auto');
            $(options.mainContainer).css('left', 'auto');
            self.scrollTo(options.nowpos, {timer: 0})
        }
        $(selector).data('options', options);
    };
    self.popups._loading = function(name, show) {
        var selector = self.popups._getSelector(name);
        if (show) {
            $(selector + ' .popup_scroll_loading').show();
        } else {
            $(selector + ' .popup_scroll_loading').hide();
        }
    };
    self.popups._popup = function(name, show) {
        var selector = self.popups._getSelector(name);
        var options = $(selector).data('options');
        if (show) {
            $(options.selector + ' .popup_scroll').show();
            $(options.selector + ' .popup_scroll').css('overflow', 'visible');
            $(options.selector + ' .popup_scroll').css('position', 'absolute');
            $(options.selector + ' .popup_scroll').css('top', options.top + 'px');
        }
    };
    self.popups._into = function(name, data){
        var selector = self.popups._getSelector(name);
        var options = $(selector).data('options');
        self.html($(selector).find('.popup_scroll_content'), data);
        if (options.callOpen !== undefined) {
            options.callOpen();
        }
        return false;
    };

    return this;
};
var mjsa = new mjsaClass(jQuery);





/* 
 * 
**************************************************************
Version History

v1.2.0.108 Beta
Review old code for release v1.3.0


/////////////////////////////////////////////////////
//// FUTURE /////////////////////////////////////////
/////////////////////////////////////////////////////
2) upload ( )
	http://learn.javascript.ru/xhr-onprogress
	http://habrahabr.ru/post/154097/
	http://xdan.ru/Working-with-files-in-JavaScript-Part-1-The-Basics.html
	http://html5demos.com/file-api
5)  * 			$(window).scroll(function(){
				if ($(document).height() - $(window).height() <= $(window).scrollTop() + options.bottomPixels)
				{
*/

