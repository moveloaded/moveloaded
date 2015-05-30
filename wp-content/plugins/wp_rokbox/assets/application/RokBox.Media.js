((function(){

	if (typeof this.RokBox == 'undefined') this.RokBox = {};

	this.RokBox.Media = new Class({

		Implements: [Options, Events],

		options:{
			data: 'rokbox',

			formats: {
				image: {
					matcher: /(^data:image\/.*,)|(\.(jp(e|g|eg)|gif|png|bmp|webp)((\?|#).*)?$)/i,
					params : {},
					type   : 'image'
				},

				iframe: {
					matcher: '',
					params: {},
					type: 'iframe'
				},

				audio: {
					matcher: /(\.(mp3|wav|ogg)((\?|#).*)?$)/i,
					params : {
						autoplay: 'autoplay',
						controls: 'controls'
					},
					type   : 'audio'
				},

				video: {
					matcher: /(\.(ogm|ogv|webm|mp4|swf)((\?|#).*)?$)/i,
					params : {
						autoplay: 'autoplay',
						controls: 'controls'
					},
					type   : 'video'
				},

				youtube: {
					matcher : /(youtube\.com|youtu\.be|youtube-nocookie\.com)\/(watch\?v=|v\/|u\/|embed\/?)?(videoseries\?list=(.*)|[\w-]{11}|\?listType=(.*)&list=(.*)).*/i,
					params  : {
						autoplay    : 1,
						autohide    : 1,
						fs          : 1,
						rel         : 0,
						hd          : 1,
						vq			: 'hd1080',
						wmode       : 'opaque',
						enablejsapi : 1
					},
					type : 'iframe',
					url  : '//www.youtube.com/embed/$3'
				},
				vimeo : {
					matcher : /(?:vimeo(?:pro)?.com)\/(?:[^\d]+)?(\d+)(?:.*)/,
					params  : {
						autoplay      : 1,
						hd            : 1,
						show_title    : 1,
						show_byline   : 1,
						show_portrait : 0,
						fullscreen    : 1
					},
					type : 'iframe',
					url  : '//player.vimeo.com/video/$1'
				},
				metacafe : {
					matcher : /metacafe.com\/(?:watch|fplayer)\/([\w\-]{1,10})/,
					params  : {
						flashVars: "playerVars=autoPlay=yes"
					},
					type    : 'swf',
					aspect  : {w: 600, h: 338},
					url     : function(rez, params, obj) {
						return '//www.metacafe.com/fplayer/' + rez[1] + '/.swf';
					}
				},
				dailymotion : {
					matcher : /dailymotion.com\/video\/(.*)\/?(.*)/,
					params  : {
						additionalInfos : 0,
						autoStart : 1
					},
					type : 'swf',
					url  : '//www.dailymotion.com/swf/video/$1'
				},
				twitvid : {
					matcher : /(twitvid|telly)\.com\/([a-zA-Z0-9_\-\?\=]+)/i,
					params  : {
						autoplay : 1
					},
					type : 'iframe',
					url  : '//www.$1.com/embed.php?guid=$2'
				},
				spotify : {
					matcher : /open\.spotify\.com\/([a-zA-Z0-9\/]+)/i,
					params  : {},
					type    : 'iframe',
					aspect  : {w: 300, h: 380},
					url     : function(rez, params){
						return '//embed.spotify.com/?uri=' + rez[1].split('/').join(':');
					}
				},
				twitpic : {
					matcher : /twitpic\.com\/(?!(?:place|photos|events)\/)([a-zA-Z0-9\?\=\-]+)/i,
					type : 'image',
					url  : '//twitpic.com/show/full/$1/'
				},
				instagram : {
					matcher : /(instagr\.am|instagram\.com)\/p\/([a-zA-Z0-9_\-]+)\/?/i,
					type : 'image',
					url  : '//$1/p/$2/media/?size=l'
				},
				google_maps : {
					matcher : /maps\.google\.([a-z]{2,3}(\.[a-z]{2})?)\/(\?ll=|maps\?)(.*)/i,
					type    : 'iframe',
					aspect  : {w: 640, h: 480},
					url     : function(rez, params) {
						return '//maps.google.' + rez[1] + '/' + rez[3] + '' + rez[4] + '&output=' + (rez[4].indexOf('layer=c') > 0 ? 'svembed' : 'embed');
					}
				}
			}
		},

		initialize: function(options){
			this.setOptions(options);
		},

		format: function(url){
			var format = this.getFormat(url),
				matcher = url.match(format.matcher ? format.matcher : ''),
				params = format.params || {};

			if (typeof format.url == 'function'){
				return format.url.call(this, matcher, params);
			}

			url = format.url || url;

			matcher.forEach(function(value, key){
				url = url.replace( '$' + key, value || '' );
			}, this);

			params = Object.toQueryString(params);
			if (params.length) {
				url += (url.indexOf('?') > 0 ? '&' : '?') + params;
			}

			return url;
		},

		getFormat: function(url){
			var format;

			Object.forEach(this.options.formats, function(obj, type){
				if (obj.matcher && url.match(obj.matcher)) format = obj;
			});

			return format || 'iframe';
		},

		getType: function(url){
			var match;

			Object.forEach(this.options.formats, function(obj, type){
				if (obj.matcher && url.match(obj.matcher)) match = obj.type;
			});

			return match || 'iframe';
		},

		getAspect: function(url){
			var match;

			Object.forEach(this.options.formats, function(obj, type){
				if (obj.matcher && url.match(obj.matcher)) match = obj.aspect;
			});

			return match || this.options.formats.iframe.aspect || {w: 1280, h: 720};
		},

		getParams: function(url){
			var match;

			Object.forEach(this.options.formats, function(obj, type){
				if (obj.matcher && url.match(obj.matcher)) match = obj.params;
			});

			return match || {};
		}
	});

})());
