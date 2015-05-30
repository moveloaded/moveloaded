((function(){

    if (typeof this.rokbox != 'undefined') return;
    if (typeof this.RokBox == 'undefined') this.RokBox = {};

    var RokBox = this.RokBox;

    var isWebkit = navigator.userAgent.match(/Webkit/i),
        isSafari6 = navigator.userAgent.match(/Version\/6.+Safari/i),
        isIE8 = navigator.userAgent.match(/MSIE\s8.0.+Trident/i),
        isiOS = ( navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false );

    String.implement({
        htmlEncode: function(){
            return this.replace(/&[^(#\d+;|a-z+;)]/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        },

        htmlDecode: function(){
            return this.replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"');
        }
    });

    this.RokBox.Class = new Class({

        Implements: [Options, Events],

        options:{
            data: 'rokbox'
        },

        initialize: function(options){
            this.setOptions(options, RokBoxSettings || {});
            Browser.Features.Touch = (function(){ return !!('ontouchstart' in window); })();

            this.bound = {
                resize: null
            };

            this._build();
            this.attach();
            this.isFitting = false;
            this.isTouch = Browser.Features.Touch;
            if (this.isTouch) this.wrapper.addClass('touch-device');
            this.media = new RokBox.Media();
        },

        attach: function(){
            if (document.retrieve(this.options.data + ':attached', false)) return;

            var touchMove = 0;
            var open = document.retrieve(this.options.data + ':open', function(event, element){
                    if (event && (event.shift || event.meta || event.rightClick)) return true;
                    if (event) event.preventDefault();

                    this.open(element);
                }.bind(this)),

                close = document.retrieve(this.options.data + ':close', function(event, element){
                    if ((!this.isOpen && !this.isOpening) || (event && event.rightClick)) return true;
                    if (event.target && !event.target.get('data-rokboxclose') && (event.target == this.container || this.container.contains(event.target))) return true;
                    if (event.target && !event.target.get('data-rokboxclose') && (
                        (event.target == this.header || this.header.contains(event.target)) ||
                        (event.target == this.footer || this.footer.contains(event.target))
                    )) return true;

                    this.close();
                    event.stop();
                }.bind(this)),

                fitscreen = document.retrieve(this.options.data + ':fitscreen', function(event, element){
                    if (event && event.rightClick) return true;
                    this.fitscreen(element);
                }.bind(this)),

                unfitscreen = document.retrieve(this.options.data + ':unfitscreen', function(event, element){
                    if (event && event.rightClick) return true;
                    this.unfitscreen(element);
                }.bind(this)),

                toggleFitscreen = document.retrieve(this.options.data + ':toggleFitscreen', function(event, element){
                    if (event && event.rightClick) return true;
                    this[(this.isFitting) ? 'unfitscreen' : 'fitscreen'](element);
                }.bind(this)),

                navigation = document.retrieve(this.options.data + ':navigation', function(event, element){
                    var item;
                    if (event && event.rightClick) return true;
                    if (!element && (event.key == 'left' || event.key == 'right')){
                        element = document.getElement('[data-rokbox' + (event.key == 'left' ? 'previous' : 'next') + ']');
                    }
                    if (!element && (event.direction == 'left' || event.direction == 'right')){
                        element = document.getElement('[data-rokbox' + (event.direction == 'left' ? 'previous' : 'next') + ']');
                    }

                    item = document.getElement('[href=' + element.get('data-rokboxnavigation') + '][data-rokbox-album=' + element.get('data-rokboxnavigation-album') + ']');
                    if (item) this.load(item);
                }.bind(this));

            if (Browser.Features.Touch && !(Browser.Platform.win && Browser.chrome)){
                document.addEvent('touchstart:relay([data-rokbox])', function(event){
                    if (event.touches.length > 1) return;
                    var touch = event.touches[0];
                    touchMove = {x: touch.pageX, y: touch.pageY};
                });
                document.addEvent('touchend:relay([data-rokbox])', function(event){
                    if (event.changedTouches && event.changedTouches.length > 1) return;
                    var touch = event.changedTouches[0], newTouchMove;
                    newTouchMove = {x: touch.pageX, y: touch.pageY};

                    if (newTouchMove.x == touchMove.x && newTouchMove.y == touchMove.y){
                        if (event.target.get('data-rokbox') == null) event.target = event.target.getParent('[data-rokbox]');
                        return open(event, event.target);
                    } else return true;
                });
                //document.addEvent('click:relay([data-rokbox])', open);

                // fix for menus - not needed for WP
                /*var menuTouchFix;
                document.getElements('.gf-menu li a').addEvent('touchstart', function(e){
                    if (this.getParent('.gf-menu-device-wrapper')) return;
                    if (!this.getParent('li').hasClass('parent')){
                        this.click();
                        e.stop();
                    }
                    if (menuTouchFix === this || this.getParent('li').hasClass('grouped') || this.getParent('li').hasClass('modules')) return this.click();
                    menuTouchFix = this;
                    return true;
                });
                document.getElements('.gf-menu li a').addEvent('touchend', function(e){
                    if (this.getParent('.gf-menu-device-wrapper')) return;
                    return false;
                });*/

                document.addEvents({
                    'keyup:keys(esc)': close,
                    'touchend:relay([data-rokboxwrapper])': close,
                    'keydown:keys(f)': toggleFitscreen,
                    'touchend:relay([data-rokboxfitscreen])': fitscreen,
                    'touchend:relay([data-rokboxunfitscreen])': unfitscreen,
                    'touchend:relay([data-rokboxprevious], [data-rokboxnext])': navigation,
                    'keydown:keys(right)': navigation,
                    'keydown:keys(left)': navigation,
                    'swipe': navigation
                });

            } else {
                document.addEvent('click:relay([data-rokbox])', open);
                document.addEvents({
                    'keyup:keys(esc)': close,
                    'click:relay([data-rokboxwrapper])': close,
                    'keydown:keys(f)': toggleFitscreen,
                    'click:relay([data-rokboxfitscreen])': fitscreen,
                    'click:relay([data-rokboxunfitscreen])': unfitscreen,
                    'click:relay([data-rokboxprevious], [data-rokboxnext])': navigation,
                    'keydown:keys(right)': navigation,
                    'keydown:keys(left)': navigation,
                    'swipe': navigation
                });
            }

            document.store(this.options.data + ':attached', true);
            document.store('swipe:cancelVertical', true);

            $$('[data-rokbox]').addEvent(this.isTouch ? 'touchstart' : 'click', function(e){
                if (e) e.preventDefault();
            });

        },

        detach: function(){
            if (!document.retrieve(this.options.data + ':attached', true)) return;

            var open = document.retrieve(this.options.data + ':open'),
                close = document.retrieve(this.options.data + ':close'),
                fitscreen = document.retrieve(this.options.data + ':fitscreen'),
                unfitscreen = document.retrieve(this.options.data + ':unfitscreen'),
                navigation = document.retrieve(this.options.data + ':navigation');

            document.removeEvents({
                'click:relay([data-rokbox])': open,
                'keyup:keys(esc)': close,
                'click:relay([data-rokboxwrapper])': close,
                'click:relay([data-rokboxfitscreen])': fitscreen,
                'click:relay([data-rokboxunfitscreen])': unfitscreen,
                'click:relay([data-rokboxprevious], [data-rokboxnext])': navigation
            });
            document.store(this.options.data + ':attached', false);
        },

        open: function(element){
            if (this.isOpening) return this;
            if (this.isOpen) return this.load(element);

            this.isOpening = true;

            this._openAndFixJump();

            moofx(this.wrapper).style({display: 'block'}).animate({opacity: 1}, {duration: 300});
            this.containerCaption.set('html', '').addClass('rokbox-hidden');
            this.footer.setStyle('display', 'none');
            moofx(this.container).style({top: '-50%', opacity: 0}).animate({top: 0, opacity: 1}, {
                duration: 300,
                callback: function(){

                    /*['-webkit-', '-moz-', '-ms-', '-o-', ''].each(function(pfx){
                        moofx(document.body.getElements('> *:not([class=rokbox-wrapper])')).style(pfx + 'filter', 'blur(5px)');
                    });*/

                    this.load(element);
                }.bind(this)
            });
        },

        close: function(element){
            moofx(this.wrapper).animate({opacity: 0}, {
                duration: 300,
                callback: function(){
                    window.removeEvent('resize', this.bound.resize);

                    /*['-webkit-', '-moz-', '-ms-', '-o-', ''].each(function(pfx){
                        moofx(document.body.getElements('> *:not([class=rokbox-wrapper])')).style(pfx + 'filter', 'none');
                    });*/

                    this.wrapper.setStyle('display', 'none');
                    this.container.setStyles({maxWidth: null, maxHeight: null});
                    if (!this.isTouch) this.containerControls.setStyle('display', 'none');
                    this.containerContent.setStyles({maxWidth: null, maxHeight: null, width: null, height: null}).empty();
                    this.object = null;
                    this.isOpen = false;
                    this.isOpening = false;
                    document.body.setStyle('margin-right', 0).removeClass('rokbox-opened');
                }.bind(this)
            });
        },

        load: function(element){
            if (!element) return;

            var url = element.get('href'),
                href = this.media.format(url),
                type = this.media.getType(url),
                aspect = this.media.getAspect(url),
                object, attr, data = {}, params, size, waitLoad = true, overrideSize;

            window.removeEvent('resize', this.bound.resize);

            this.setNavigation(element);
            this.showSpinner();
            //this.setType(type);
            this.setFitting();

            if (element.get('data-rokbox-element')) type = 'element';
            size = element.get('data-rokbox-size') ? element.get('data-rokbox-size').split(' ') : false;

            data = {
                href: href,
                element: element,
                type: type,
                aspect: aspect,
                size: size
            };

            switch(type){

                case 'image':
                    object = new Image();
                    break;

                case 'element':
                    object = new Element('div');
                    data.rule = element.get('data-rokbox-element');
                    waitLoad = false;
                    break;

                case 'audio':
                    overrideSize = size ? {width: size[0], height: size[1]} : {width: 300, height: 30};
                    if (url.match(/\.mp3$/i) && Browser.firefox){
                        object = new Element('object', {data: url, type: 'application/x-mplayer-2', width: overrideSize.width, height: overrideSize.height + 60}).
                            set('html', '<param name="filename" value="'+url+'"><param name="ShowControls" value="1"><param name="AutoStart" value="true"><embed type="application/x-mplayer-2" src="'+url+'" width="'+overrideSize.width+'" height="'+(overrideSize.height+60)+'" showcontrols="true" autostart="true"></embed>').
                            setStyles({width: overrideSize.width, height: overrideSize.height});

                        data.html5 = false;
                    } else {
                        var mode = url.match(/\.mp3$/i) ? 'video' : 'audio';
                        object = new Element(mode).set('html', 'Your browser does not support the <code><audio></code> element.');
                        params = this.media.getParams(url);
                        object.set('src', url).set('width', overrideSize.width).set('height', overrideSize.height + 60);

                        Object.forEach(params, function(value, param){
                            object.set(param, value);
                        }, this);

                        if (mode == 'video') object.set('type', 'audio/mpeg');
                        data.html5 = true;
                    }

                    waitLoad = false;
                    break;

                case 'video':
                    overrideSize = size ? {width: size[0], height: size[1]} : {width: 600, height: 400};
                    if (url.match(/\.swf$/i) && Browser.firefox){
                        object = new Element('object', {classid: "clsid:D27CDB6E-AE6D-11cf-96B8-444553540000", codebase: "http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0", width: overrideSize.width, height: overrideSize.height}).
                            set('html', '<param name="movie" value="'+url+'"><param name="quality" value="high"><embed src="'+url+'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" width="'+overrideSize.width+'" height="'+overrideSize.height+'" type="application/x-shockwave-flash"></embed>').
                            setStyles({width: overrideSize.width, height: overrideSize.height});

                        data.html5 = false;
                        waitLoad = false;
                    } else {
                        object = new Element('video').set('html', 'Your browser does not support the <code>video</code> element.');

                        params = this.media.getParams(url);
                        if (url.match(/\.mp4$/i) && object.canPlayType('video/mp4').length) object.set('src', href);
                        if (url.match(/\.webm$/i) && object.canPlayType('video/webm').length) object.set('src', href);
                        if (url.match(/\.ogg$/i) && object.canPlayType('video/ogg').length) object.set('src', href);
                        if (url.match(/\.ogv$/i) && object.canPlayType('video/ogv').length) object.set('src', href);
                        if (size) object.setStyles(overrideSize);
                        else object.setStyles({width: '100%', height: 'auto'});

                        Object.forEach(params, function(value, param){
                            object.set(param, value);
                        }, this);

                        data.html5 = true;
                        waitLoad = true;
                    }
                    break;

                case 'swf':
                    var w = Math.min(window.getSize().x - 100, aspect.w),
                        h = w * aspect.h / aspect.w;

                    if (size){
                        w = size[0]; h = size[1];
                    }

                    var embed = new Element('embed', {src: href, type: 'application/x-shockwave-flash', width: w, height: h});
                    params = this.media.getParams(url);
                    object = new Element('object', {
                        classid: 'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000',
                        width: '100%',
                        height: '100%'
                    });

                    object.adopt(new Element('param', {name: 'move', value: href}));

                    Object.forEach(params, function(value, param){
                        new Element('param').set(param, value);
                        embed.set(param, value);
                    }, this);

                    embed.inject(object);

                    this.bound.resize = this._resizeObject.pass([object, data], this);
                    if (!isWebkit) window.addEvent('resize', this.bound.resize);

                    waitLoad = false;
                    break;

                case 'iframe': default:
                    var w = Math.min(window.getSize().x - 100, aspect.w),
                        h = w * aspect.h / aspect.w;

                    if (size){
                        w = size[0]; h = size[1];
                    }

                    object = new IFrame({resize: true, frameborder: 0, webkitAllowFullScreen: true, mozallowfullscreen: true, allowFullScreen: true, width: w, height: h});

                    this.bound.resize = this._resizeObject.pass([object, data], this);
                    if (!isWebkit) window.addEvent('resize', this.bound.resize);

                    if (href.match(/\.pdf$/)){
                        waitLoad = false;
                        object.set('src', href);
                    }

                    break;
            }

            document.id(object).inject(document.body).setStyle('display', 'none');

            if (waitLoad){
                if (data.html5 && data.type == 'video'){
                    object.addEventListener('canplay', function(){
                        data.videoSize = {width: object.videoWidth, height: object.videoHeight};
                        this['_load' + type.capitalize()](object, data);
                    }.bind(this), false);
                    if (isiOS){
                        data.videoSize = {width: object.videoWidth, height: object.videoHeight};
                        this['_load' + type.capitalize()](object, data);
                        object.play();
                    }
                } else {
                    object.addEvent('load', this['_load' + type.capitalize()].pass([object, data], this));
                    object.set('src', href);
                }

                if (Browser.firefox && !!url.match(/\.mp4$/i)){
                    // mp4 is not supported by firefox, yet
                    // so we are going to add a webm and ogv fallback
                    if (!object.canPlayType('video/mp4')){
                        new Element('source')
                                .set('src', url)
                                .set('type', 'video/mp4')
                                .inject(object, 'top');
                        new Element('source')
                                .set('src', url.replace(/\.mp4/, '.ogg'))
                                .set('type', 'video/ogg')
                                .inject(object, 'top');
                        new Element('source')
                                .set('src', url.replace(/\.mp4/, '.ogv'))
                                .set('type', 'video/ogv')
                                .inject(object, 'top');
                        new Element('source')
                                .set('src', url.replace(/\.mp4/, '.webm'))
                                .set('type', 'video/webm')
                                .inject(object, 'top')
                                .onerror = this._setError.pass([object, data], this);
                    }
                }

                object.onerror = this._setError.pass([object, data], this);
            } else {
                this['_load' + type.capitalize()](object, data);
            }
        },

        fitscreen: function(element){
            var dummy = (this.containerContent.getElement('img') || this.containerContent.getElement('iframe'));
            if (!dummy) return;
            var size = {
                    maxWidth: this.containerContent.getStyle('maxWidth'),
                    maxHeight: this.containerContent.getStyle('maxHeight'),
                    width: dummy ? dummy.getStyle('width').toInt() || 'auto' : 'auto',
                    height: dummy ? dummy.getStyle('height').toInt() || 'auto' : 'auto'
                },
                viewport = window.getSize();

            if (isSafari6) moofx(dummy).style({width: 'inherit'});

            this.container.store(this.options.data + ':fitscreen-size', {
                maxWidth: size.maxWidth,
                maxHeight: size.maxHeight,
                width: size.width,
                height: size.height,
                viewport: viewport
            });

            moofx(this.containerContent).style({maxWidth: null, maxHeight: null});
            moofx(dummy).style({width: null, height: null});
            moofx(this.container).style({maxWidth: '100%', maxHeight: '100%'});
            document.getElement('[data-rokboxfitscreen]').setStyle('display', 'none');
            document.getElement('[data-rokboxunfitscreen]').setStyle('display', 'block');

            (function(){
                if (isSafari6 || isIE8) moofx(dummy).style({width: '100%'});
            }.bind(this)).delay(5);

            this.isFitting = true;
        },

        unfitscreen: function(element){
            var size = {
                    maxWidth: this.container.retrieve(this.options.data + ':fitscreen-size').maxWidth,
                    maxHeight: this.container.retrieve(this.options.data + ':fitscreen-size').maxHeight,
                    width: this.container.retrieve(this.options.data + ':fitscreen-size').width,
                    height: this.container.retrieve(this.options.data + ':fitscreen-size').height
                },
                viewport = this.container.retrieve(this.options.data + ':fitscreen-size').viewport;

            if (isSafari6 || isIE8) moofx(this.containerContent.getElement('img')).style({width: 'inherit'});

            this.containerContent.setStyles({maxWidth: size.maxWidth, maxHeight: size.maxHeight});
            moofx(this.container).style({maxWidth: null, maxHeight: null});
            moofx(this.containerContent.getElement('img') || this.containerContent.getElement('iframe')).style({width: size.width, height: size.height});
            document.getElement('[data-rokboxunfitscreen]').setStyle('display', 'none');
            document.getElement('[data-rokboxfitscreen]').setStyle('display', 'block');

            if (isSafari6 || isIE8) moofx(this.containerContent.getElement('img')).style({width: '100%'});

            this.isFitting = false;
        },

        setNavigation: function(element){
            var gallery = element.get('data-rokbox-album'),
                arrows = document.getElements('[data-rokboxprevious], [data-rokboxnext]'),
                prevs = document.getElements('[data-rokboxprevious]'),
                nexts = document.getElements('[data-rokboxnext]');

            if (!gallery){
                arrows.setStyle('display', 'none');
                return this;
            }

            var elements = document.getElements('[data-rokbox-album='+gallery+']'),
                index = elements.indexOf(element),
                previous = elements[index - 1],
                next = elements[index + 1];

            if (index == -1 || elements.length == 1){
                arrows.setStyle('display', 'none');
                this.footer.setStyle('display', 'none');
                return this;
            }

            if (typeof previous == 'undefined') previous = elements[elements.length - 1];
            if (typeof next == 'undefined') next = elements[0];

            prevs.set('data-rokboxnavigation', previous.get('href')).set('data-rokboxnavigation-album', previous.get('data-rokbox-album'));
            nexts.set('data-rokboxnavigation', next.get('href')).set('data-rokboxnavigation-album', next.get('data-rokbox-album'));
            arrows.setStyle('display', 'block');
            if (Browser.Features.Touch) this.footer.setStyle('display', 'block');

            return this;
        },

        setType: function(type){
            this.container.removeClass(this.options.data + '-type-' + this.type);
            this.type = type;
            this.container.addClass(this.options.data + '-type-' + this.type);
        },

        setFitting: function(){
            document.getElements(this.isFitting ? '[data-rokboxunfitscreen]' : '[data-rokboxfitscreen]').setStyle('display', 'none');
        },

        showSpinner: function(){
            this.container.addClass('rokbox-loading');
        },

        hideSpinner: function(){
            this.container.removeClass('rokbox-loading');
        },

        /* Private */
        _build: function(){
            if (this.wrapper || document.getElement('[data-rokboxwrapper]')) return this;

            var list = ['outer', 'row', 'inner', 'container'],
                containerList = ['loader', 'content', 'controls'],
                controlsList = {
                    x: 'close',
                    p: 'previous',
                    n: 'next',
                    /*f: 'fullscreen',*/
                    d: 'fitscreen',
                    w: 'unfitscreen'
                },
                Label;

            this.wrapper = new Element('div[data-rokboxwrapper].' + this.options.data + '-wrapper').inject(document.body);
            list.forEach(function(mode, i){
                this[mode] = new Element('div[data-' + this.options.data + mode + '].' + this.options.data + '-' + mode).inject(this[list[i - 1]] || this.wrapper);
            }, this);

            containerList.forEach(function(mode){
                this['container' + mode.capitalize()] = new Element('div[data-' + this.options.data + mode + '].' + this.options.data + '-' + mode).inject(this.container);
            }, this);


            // touch devices
            ['header', 'footer'].forEach(function(row, r){
                this[row] = new Element('div[data-' + this.options.data + row + '].' + this.options.data + '-' + row).inject(this.row, !r ? 'before' : 'after');
            }, this);
            // end touch devices

            Object.forEach(controlsList, function(label, mode){
                Label = label.capitalize();
                if (mode != 'p' && mode != 'n'){
                    this['controls' + Label] = new Element('div[data-rokboxicon="'+mode+'"][data-' + this.options.data + label + '].' + this.options.data + '-' + label).inject(this.containerControls);
                } else {
                    this['controls' + Label] = new Element('div[data-' + this.options.data + label + '].' + this.options.data + '-' + label).inject(this.containerControls);
                    new Element('div[data-rokboxicon="'+mode+'"]').inject(this['controls' + Label]);
                }

                if (['p', 'n'].contains(mode)) this['controls' + Label].clone().inject(this.footer);
                if (mode == 'x') this['controls' + Label].clone().inject(this.header);

            }, this);

            this.containerLoader.adopt(new Element('div.' + this.options.data + '-loader-image'));
            this.containerCaption = new Element('div[data-' + this.options.data + 'caption].' + this.options.data + '-caption').inject(this.container);
        },

        _loadImage: function(object, data){
            var sizeBefore = this.containerContent.getSize(),
                size, computedHeight, imgOriginalSize = {};

            this.setType(data.type);

            if (isIE8){
                // soon you will die, IE8.
                var ie8 = object.clone().inject(document.body).setStyles({display: 'block', 'visibility': 'visible', position: 'absolute', top: '-30000px'});
                imgOriginalSize = {width: ie8.width, height: ie8.height}
                ie8.dispose();
            }

            // initial cleanup
            moofx(object).style({opacity: 0, visibility: 'hidden', display: 'block'});

            if (isSafari6 || isIE8) moofx(object).style({width: 'inherit'}); // safari 6 sux

            if (data.size){
                moofx(object).style({width: data.size[0], height: data.size[1]});
            }

            object.inject(this.containerContent.empty());
            if (data.type != 'element' && data.type != 'audio' && data.type != 'video') this.containerContent.adopt(new Element('div.rokbox-contentborder'));

            moofx(this.containerContent).style({maxWidth: null, maxHeight: null});
            if (data.size && this.isFitting){
                moofx(object).style({width: null, height: null});
            }

            // caption
            this.containerCaption.set('html', data.element.get('data-rokbox-caption') || '').removeClass('rokbox-hidden');

            // size checks and setup
            size = this.containerContent.getSize();
            if (data.videoSize) size = {x: data.videoSize.width || size.x, y: data.videoSize.height || size.y};
            computedHeight = this.container.getComputedSize({styles: ['padding', 'border', 'margin']}).totalHeight;
            //if (data.videoSize) computedHeight += data.videoSize.height;
            if (!size.x) return;

            // hide object and caption before animating
            moofx(object).style({display: 'none'});
            moofx(this.containerCaption.addClass('rokbox-hidden')).style({opacity: 0});

            if (isSafari6 || isIE8) moofx(object).style({width: '100%'}); // safari 6 sux

            moofx(this.containerContent).style({
                width: !this.isOpen ? this.containerContent.getSize().x : sizeBefore.x,
                height: !this.isOpen ? this.containerContent.getSize().y : sizeBefore.y
            });

            // ensure the height fits on the viewport
            var innerSize = window.getSize(),
                contentSize = this.containerContent.getSize();

            if (computedHeight >= innerSize.y){
                var margin = this.container.getStyle('margin-bottom').toInt(),
                    caption = document.getElement('[data-rokboxcaption]'),
                    captionSize = caption ? caption.getSize().y : 0,

                    objectHeight = Math.round(innerSize.y * size.y / computedHeight) - captionSize - margin,
                    objectWidth = Math.round(size.x * objectHeight / size.y);

                if (!this.isFitting){
                    size.x = objectWidth;
                    size.y = objectHeight;
                }
            }

            // show / hide the fit button
            if (!data.error && data.type != 'element'){
                document.getElements(this.isFitting ? '[data-rokboxunfitscreen]' : '[data-rokboxfitscreen]').setStyle('display', object.width == size.x ? 'none' : 'block');
            } else {
                document.getElements(this.isFitting ? '[data-rokboxunfitscreen]' : '[data-rokboxfitscreen]').setStyle('display', 'none');
            }

            // show caption if needed
            if (!data.error && data.element.get('data-rokbox-caption') && data.element.get('data-rokbox-caption').length){
                moofx(this.containerCaption.removeClass('rokbox-hidden')).animate({opacity: 1});
            }

            //if (data.html5) moofx(object).style({width: '100%', height: '100%'});

            // animate
            moofx(this.containerContent)['animate']({width: size.x, height: size.y}, {
                duration: 250,
                callback: function(){
                    moofx(object).style({
                        display: 'block',
                        visibility: 'visible'
                    });

                    if (data.html5 && object.play) object.play();

                    moofx(object).style({
                        maxWidth: (object.naturalWidth || imgOriginalSize.width || object.width || object.videoWidth || size.x) + 'px',
                        maxHeight: (object.naturalHeight || imgOriginalSize.height || object.height || object.videoHeight || size.y) + 'px'
                    });

                    moofx(object)[!isIE8 ? 'animate' : 'style']({opacity: 1});

                    this.containerContent.setStyles({maxWidth: size.x, maxHeight: size.y, width: null, height: null});
                    if (!this.isTouch) this.containerControls.setStyle('display', 'block');

                    if (this.isFitting) this.fitscreen();

                    this.container.store(this.options.data + ':fitscreen-size', {
                        maxWidth: objectWidth || size.x,
                        maxHeight: objectHeight || size.y
                    });

                }.bind(this)
            });

            this.isOpen = true;
            this.isOpening = false;
            this.object = object;

            this.hideSpinner();
        },

        _loadIframe: function(object, data){
            var sizeBefore = this.containerContent.getSize(),
                size, computedHeight;

            object.removeEvents('load');
            this.setType(data.type);

            // hide and inject in the cleaned out content container
            object.setStyles({visibility: 'hidden', display: 'block'});
            object.inject(this.containerContent.empty());
            if (isIE8) object.setStyle('max-width', 'inherit');

            // max width and height are useless with iframes, unfortunately
            moofx(this.containerContent).style({
                maxWidth: null,
                maxHeight: null
            });

            // caption
            this.containerCaption.set('html', data.element.get('data-rokbox-caption') || '').removeClass('rokbox-hidden');

            // size checks and setup
            size = this.containerContent.getSize();
            if (size.x <= parseInt(object.get('width'), 10)) size.x = parseInt(object.get('width'), 10);
            computedHeight = this.container.getComputedSize({styles: ['padding', 'border', 'margin']}).totalHeight || this.container.getSize().y;

            if (!size.x) return;
            size.y = size.y || 100;

            // hide object and caption before animating
            object.setStyles({display: 'none'});
            moofx(this.containerCaption.addClass('rokbox-hidden')).style({opacity: 0});

            moofx(this.containerContent).style({
                width: !this.isOpen ? this.containerContent.getSize().x : sizeBefore.x,
                height: !this.isOpen ? this.containerContent.getSize().y : sizeBefore.y
            });

            // ensure the height fits on the viewport
            var innerSize = window.getSize(),
                contentSize = this.containerContent.getSize();

            if (computedHeight >= innerSize.y){
                var margin = this.container.getStyle('margin-bottom').toInt(),
                    caption = document.getElement('[data-rokboxcaption]'),
                    captionSize = caption ? caption.getSize().y : 0,

                    objectHeight = Math.round(innerSize.y * size.y / computedHeight) - captionSize - margin,
                    objectWidth = Math.round(size.x * objectHeight / size.y);

                if (!this.isFitting){
                    size.x = objectWidth;
                    size.y = objectHeight;
                }
            }

            document.getElements(this.isFitting ? '[data-rokboxunfitscreen]' : '[data-rokboxfitscreen]').setStyle('display', 'none');

            if (data.element.get('data-rokbox-caption') && data.element.get('data-rokbox-caption').length) moofx(this.containerCaption.removeClass('rokbox-hidden')).animate({opacity: 1});
            moofx(this.containerContent).animate({width: size.x, height: size.y}, {
                duration: 250,
                callback: function(){
                    object.setStyles({
                        display: 'block',
                        visibility: 'visible',
                        width: size.x,
                        height: size.y
                    });
                    this.containerContent.setStyles({maxWidth: size.x, maxHeight: size.y, width: null, height: null});
                    if (!this.isTouch) this.containerControls.setStyle('display', 'block');

                    this.container.store(this.options.data + ':fitscreen-size', {
                        maxWidth: objectWidth || size.x,
                        maxHeight: objectHeight || size.y
                    });

                }.bind(this)
            });

            this.isOpen = true;
            this.isOpening = false;
            this.hideSpinner();
        },

        _loadSwf: function(object, data){
            this._loadIframe(object, data);
        },

        _loadAudio: function(object, data){
            this[data.html5 ? '_loadImage' : '_loadIframe'](object, data);
        },

        _loadVideo: function(object, data){
            this[data.html5 ? '_loadImage' : '_loadIframe'](object, data);
        },

        _loadElement: function(object, data){
            var element = document.getElement(data.rule);
            if (!element){
                return this._setError(object, data);
                //throw Error('Element[@'+data.rule+'] not found. Unable to load RokBox.');
            }

            object.adopt(element.clone(true, true).cloneEvents(element).setStyle('display', 'block').addClass('rokbox-content-element'));
            this._loadImage(object, data);
        },

        _setError: function(object, data){
            var error = new Element('div#rokbox-error.rokbox-error' + data.type);

            if (data.type == 'element') error.set('html', '<h3>Error</h3><p>The '+data.type+' <code>'+data.rule+'</code> was not found in the DOM.</p>');
            else if (data.type == 'video' && data.html5 && Browser.firefox && data.href.match(/\.mp4/i)){
                error.set('html', '<h3>Error</h3><p>An error occurred while trying to load the '+data.type+' link: <br /> <code>'+data.href+'</code> <br />Note that Firefox does not support MP4 files. Try adding a WebM or Ogg converted file at the same location of the video above (<a target="_blank" href="https://developer.mozilla.org/en-US/docs/HTML/Supported_media_formats?redirectlocale=en-US&redirectslug=Media_formats_supported_by_the_audio_and_video_elements#WebM">More details</a>).</p>');
            }
            else error.set('html', '<h3>Error</h3><p>An error occurred while trying to load the '+data.type+' link: <br /> <code>'+data.href+'</code></p>');

            data.error = true;
            this['_load' + (data.type == 'element' ? 'Image' : data.type.capitalize())](error, data);
        },

        _resizeObject: function(object, data){
            var margin = this.container.getStyle('margin-bottom').toInt(),
                caption = document.getElement('[data-rokboxcaption]'),
                captionSize = caption ? caption.getSize().y : 0,
                viewport = window.getSize(),
                containerSize = this.containerContent.getSize(),
                computedHeight = this.container.getComputedSize({styles: ['padding', 'border', 'margin']}).totalHeight || this.container.getSize().y;
                size = {};

            size.y = Math.round(viewport.y * containerSize.y / computedHeight) - captionSize - margin;
            size.x = Math.round(viewport.x * size.y / viewport.y);

            moofx(this.containerContent).style({maxWidth: size.x, maxHeight: size.y});
            moofx(object).style({maxWidth: size.x, maxHeight: size.y});
        },

        _openAndFixJump: function(){
            var previousSize = afterSize = document.body.scrollWidth, compensation = 0;
            document.body.addClass('rokbox-opened');
            afterSize = document.body.scrollWidth;
            compensation = afterSize - previousSize;
            document.body.setStyle('margin-right', compensation);
        }

    });

    window.addEvent('domready', function(){
        this.rokbox = new RokBox.Class();
    });

})());
