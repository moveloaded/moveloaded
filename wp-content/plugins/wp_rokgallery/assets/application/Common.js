((function(){

if (!Browser.Engine){
	if (Browser.Platform.ios) Browser.Platform.ipod = true;

	Browser.Engine = {};

	var setEngine = function(name, version){
		Browser.Engine.name = name;
		Browser.Engine[name + version] = true;
		Browser.Engine.version = version;
	};

	if (Browser.ie){
		Browser.Engine.trident = true;

		switch (Browser.version){
			case 6: setEngine('trident', 4); break;
			case 7: setEngine('trident', 5); break;
			case 8: setEngine('trident', 6);
		}
	}

	if (Browser.firefox){
		Browser.Engine.gecko = true;

		if (Browser.version >= 3) setEngine('gecko', 19);
		else setEngine('gecko', 18);
	}

	if (Browser.safari || Browser.chrome){
		Browser.Engine.webkit = true;

		switch (Browser.version){
			case 2: setEngine('webkit', 419); break;
			case 3: setEngine('webkit', 420); break;
			case 4: setEngine('webkit', 525);
		}
	}

	if (Browser.opera){
		Browser.Engine.presto = true;

		if (Browser.version >= 9.6) setEngine('presto', 960);
		else if (Browser.version >= 9.5) setEngine('presto', 950);
		else setEngine('presto', 925);
	}

	if (Browser.name == 'unknown'){
		switch ((ua.match(/(?:webkit|khtml|gecko)/) || [])[0]){
			case 'webkit':
			case 'khtml':
				Browser.Engine.webkit = true;
			break;
			case 'gecko':
				Browser.Engine.gecko = true;
		}
	}

	this.$exec = Browser.exec;
}

// Flash detection

var version = (Function.attempt(function(){
	return navigator.plugins['Shockwave Flash'].description;
}, function(){
	return new ActiveXObject('ShockwaveFlash.ShockwaveFlash').GetVariable('$version');
}) || '0 r0').match(/\d+/g);

Browser.Plugins.Flash = {
	version: Number(version[0] || '0.' + version[1]) || 0,
	fullversion: version.join('.'),
	build: Number(version[2]) || 0
};

if (MooTools.lang) {
	MooTools.lang.set('en-US', 'Uploader', phrases);
} else {
	MooTools.lang = {
		get: function(from, key) {
			return phrases[key];
		}
	};
}

if (!this.Uploader) this.Uploader = {};
this.UploaderSupport = {
	ready: false,
	load: function(path){
		path = path || '';

		var type = UploaderSupport.check();
		if (!type) return;

		new Asset.javascript(path + 'Uploader.' + type + '.js', {
			onLoad: function(){
				this.inject(document.head);
				UploaderSupport.ready = true;
				RokGallery.uploader = new Uploader('toolbar-upload');
			}
		});
	},
	check: function(){
		if ((Browser.name == 'firefox' && Browser.version > 3.6) || Browser.Engine.webkit) return 'HTML5';
		if (Browser.Plugins.Flash && Browser.Plugins.Flash.version >= 9) return 'Flash';

		var flash = function(type){
			if (type == 'notice'){
				if (!Browser.Plugins.Flash || !Browser.Plugins.Flash.version) return "it looks like you don't have Flash plugin installed.";
				else {
					var plugin = Browser.Plugins.Flash;
					return "your Flash plugin version <strong>" + plugin.fullversion + "</strong> is too old.";
				}
			}
			if (type == 'link'){
				return '<p>Alternatively you can install or update your <a href="http://get.adobe.com/flashplayer/">Flash plugin</a> to the latest version</p>';
			}
		};

		window.Popup.setPopup({
			type: 'warning',
			title: 'Unsupported Browser',
			message: '<p>Your browser <strong>' + Browser.name.capitalize() + '</strong> v<strong>' + Browser.version + '</strong> does not support modern files uploading specs and ' + flash('notice') + '</p>' +
					'<p>In order to be able to upload files it is highly suggested to use a more modern browser such as <a href="http://www.mozilla.com/firefox">Firefox</a>, <a href="http://www.google.com/chrome/">Chrome</a> or <a href="http://www.apple.com/safari/">Safari</a>.</p> ' + flash('link'),
			buttons: {
				ok: {show: true, label: 'close'}
			},
			'continue': function(){
				var uploader = (document.id('uploader') || document.id('toolbar-upload')).set('tween', {duration: 200});
				uploader.retrieve('tween').start('opacity', 0).chain(uploader.dispose.bind(uploader));

				this.close();
			}
		}).open();

		return false;
	}
};

var animations = {
	'Frames': function(){
		var div = new Element('div#test3d'),
			support = false,
			properties = ['animationName', 'WebkitAnimationName', 'MozAnimationName', 'OAnimationName', 'msAnimationName', 'KhtmlAnimationName'];

		for (var i = properties.length - 1; i >= 0; i--){
			support = support ? support : div.style[properties[i]] != undefined;
		}

		return support;
	},

	'2D': function(){
		var div = new Element('div#test3d'),
			support = false,
			properties = ['transformProperty', 'WebkitTransform', 'MozTransform', 'OTransform', 'msTransform'];

		for (var i = properties.length - 1; i >= 0; i--){
			support = support ? support : div.style[properties[i]] != undefined;
		}

		return support;
	},

	'3D': function(){
		var div = new Element('div#test3d'),
			support = false,
			properties = ['perspectiveProperty', 'WebkitPerspective', 'MozPerspective', 'OPerspective', 'msPerspective'];

		for (var i = properties.length - 1; i >= 0; i--){
			support = support ? support : div.style[properties[i]] != undefined;
		}

		// webkit has 3d transforms disabled for chrome, though
		// it works fine in safari on leopard and snow leopard
		// as a result, it 'recognizes' the syntax and throws a false positive
		// thus we must do a more thorough check:
		if (support && 'webkitPerspective' in document.documentElement.style){
			var style = document.createElement('style');
			// webkit allows this media query to succeed only if the feature is enabled.
			// "@media (transform-3d),(-o-transform-3d),(-moz-transform-3d),(-ms-transform-3d),(-webkit-transform-3d),(modernizr){#modernizr{height:3px}}"
			style.textContent = '@media (-webkit-transform-3d){#test3d{height:3px}}';
			document.head.appendChild(style);

			div.inject(document.body);

			support = div.offsetHeight === 3;

			style.dispose();
			div.dispose();
		}

		return support;
	}
};

/* SqueezeBox for Article Picker */

var squeezebox = this.RokGallerySqueezeBox = {
	linkElement: 'slice-linkdata',
	dataElement: 'slice-link',

	setData: function(id, title, catid, type){
		var slug = title.replace(/ /g, '-').toLowerCase(),
			linkElement = document.id(squeezebox.linkElement) || document.getElement(squeezebox.linkElement),
			objElement = document.id(squeezebox.dataElement) || document.getElement(squeezebox.dataElement),
			data;

		data = {
			type: type,
			id: id,
			title: title,
			link: 'index.php?option=com_content&view=article&id='+id+':'+slug+'&catid='+catid
		};

		linkElement.addClass('disabled').set('value', title);
		objElement.set('value', JSON.encode(data));

		RokGallery.editPanel.disableSliceLink();
	},

	getData: function(){
		var linkElement = document.id(squeezebox.linkElement) || document.getElement(squeezebox.linkElement),
			objElement = document.id(squeezebox.dataElement) || document.getElement(squeezebox.dataElement),
			data;

		data = JSON.validate(objElement.get('value')) ? JSON.decode(objElement.get('value')) : {};

		return data;
	}

};

/* J! 1.7 */
this.jSelectArticle_jform_request_id = function(id, title, catid) {
	squeezebox.setData(id, title, catid, 'article');
	SqueezeBox.close();
};

/* WP */
this.wpSelectArticle = function(id, title, type, link){
	var slug = title.replace(/ /g, '-').toLowerCase(),
		linkElement = document.id(squeezebox.linkElement) || document.getElement(squeezebox.linkElement),
		objElement = document.id(squeezebox.dataElement) || document.getElement(squeezebox.dataElement),
		data;

	data = {
		type: type,
		id: id,
		title: title,
		link: link
	};

	linkElement.addClass('disabled').set('value', title);
	objElement.set('value', JSON.encode(data));

	RokGallery.editPanel.disableSliceLink();
};

/* DOMReady */

window.addEvent('domready', function(){
	for (var dimension in animations){
		this['Supports' + dimension] = animations[dimension]();
	}

	if (typeof SqueezeBox != 'undefined'){
		SqueezeBox.addEvents({
			onOpen: function(){ this.overlay.setStyle('width', '200%'); },
			onClose: function(){ this.overlay.setStyle('width', '100%'); }
		});
	}
});




/* Language */

var phrases = {
	'progressOverall': '{total}',
	'currentTitle': 'File Progress',
	'currentFile': 'Uploading "{name}"',
	'currentProgress': 'Upload: {bytesLoaded} with {rate}, {timeRemaining} remaining.',
	'fileName': '{name}',
	'remove': 'remove',
	'removeTitle': 'Click to remove this entry.',
	'fileError': 'Upload failed',
	'validationErrors': {
		'duplicate': 'File <em>{name}</em> is already added, duplicates are not allowed.',
		'sizeLimitMin': 'File <em>{name}</em> (<em>{size}</em>) is too small, the minimal file size is {fileSizeMin}.',
		'sizeLimitMax': 'File <em>{name}</em> (<em>{size}</em>) is too big, the maximal file size is <em>{fileSizeMax}</em>.',
		'fileListMax': 'File <em>{name}</em> could not be added, amount of <em>{fileListMax} files</em> exceeded.',
		'fileListSizeMax': 'File <em>{name}</em> (<em>{size}</em>) is too big, overall filesize of <em>{fileListSizeMax}</em> exceeded.'
	},
	'errors': {
		'httpStatus': 'Server returned HTTP-Status <code>#{code}</code>',
		'securityError': 'Security error occured ({text})',
		'ioError': 'Error caused a send or load operation to fail ({text})'
	}
};

})());
