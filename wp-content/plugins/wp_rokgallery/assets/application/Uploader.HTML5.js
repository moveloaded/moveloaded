((function(){

this.Uploader.HTML5 = new Class({

	Implements: [Options, Events],

	options: {
		queued: 1,
		target: 'files-browse',
		container: null,
		fileSizeMax: 0,
		fileSizeMin: 0,
		fileListMax: 0,
		fileListSizeMax: 0,
		accept: 'image/*',

		onFileSuccess: function(file, response){
			var json = new Hash(JSON.validate(response) ? JSON.decode(response, true) : {} || {});

			if (json.get('status') == 'success') {
				file.element.addClass('file-success');
				//file.info.set('html', '<strong>Image was uploaded:</strong> ' + json.get('width') + ' x ' + json.get('height') + 'px, <em>' + json.get('mime') + '</em>)');
			} else {
				file.element.addClass('file-failed');
				file.info.set('html', '<strong>An error occured:</strong> ' + (json.get('status') == 'error' ? json.get('message') : response));
			}

			this.scrollbar.update();
		}
	},

	initialize: function(status, list, options){
		this.setOptions(options);

		this.status = document.id(status);
		this.list = document.id(list);
		this.form = this.status.getParent('form');
		this.target = document.id(this.options.target);
		this.browse = new Element('input[type=file][name=Filedata][multiple]').inject(this.target);
		this.browse.set('accept', this.options.accept);

		this.uploader = RokGallery.uploader;

		this.scrollbar = new Scrollbar(list, {triggerElement: '#popup .content', gutter: true, wrapStyles: {'float': 'right'}});
		new Element('div.clr').inject(this.scrollbar.wrapper, 'after');

		this.files = [];
		this.filesBounds = {
			'remove': this.remove.bind(this)
		};

		this.values = {
			size: 0,
			files: {
				uploading: 0,
				uploaded: 0
			},
			bytes: {
				now: 0,
				last: 0
			},
			percent: 0
		};

		this.bounds = {
			upload: {
				progress: this.progress.bind(this),
				fileRequest: this.fileRequest.bind(this),
				fileComplete: this.fileComplete.bind(this)
			},
			drag: {
				dragover: this.dragover.bind(this),
				dragleave: this.dragout.bind(this),
				drop: this.drop.bind(this)
			},
			buttons: {
				upload: this.start.bind(this),
				browse: function(e){
					var firefox5 = Browser.firefox && Browser.version > 4 && e.target.get('tag') == 'input';
					if (!this.running && !firefox5) this.target.getElement('input[type=file]').click();
				}.bind(this),
				change: this.enqueue.bind(this, false)
			}
		};

		this.queued = false;
		this.job = false;
		this.render('init');

		this.attach();
	},

	attach: function(){

		document.id('files-upload').addEvent('click', this.bounds.buttons.upload);
		this.target.addEvent('click', this.bounds.buttons.browse);
		this.browse.addEvent('change', this.bounds.buttons.change);
		$$(window.Popup.overlay, window.Popup.popup).addEvents(this.bounds.drag);
		this.addEvents(this.bounds.upload);

		return this;
	},

	detach: function(){

		document.id('files-upload').removeEvent('click', this.bounds.buttons.upload);
		this.target.removeEvent('click', this.bounds.buttons.browse);
		this.browse.removeEvent('change', this.bounds.buttons.change);
		$$(window.Popup.overlay, window.Popup.popup).removeEvents(this.bounds.drag);
		this.removeEvents(this.bounds.upload);

		return this;
	},

	render: function(type){

		if (!type || type == 'init'){
			var popup = window.Popup;

			//if (this.form.getFirst() != popup.popup){
			//	this.form.getChildren().inject(this.form, 'before');
			//	this.form.wraps(popup.popup);
			//}

			this.browse.setStyles({'visibility': 'hidden', 'position': 'absolute'});

			if (Browser.opera){
				this.browse.setStyles({
					'opacity': 0.00001,
					'height': 30,
					'width': this.browse.getSize().x,
					'right': -1,
					'top': -1,
					'visibility': 'visible'
				});
				this.target.setStyle('overflow', 'hidden');
			}

			new Element('div.clr').inject(this.list, 'after');
			this.clearall = new Element('span#files-clear', {text: 'clear all'}).inject(this.target, 'after');

			popup.popup.getElement('.ok').set('id', 'files-upload');

			this.overallTitle = this.status.getElement('.overall-title');
			this.currentText = this.status.getElement('.current-text');

			var progress = this.status.getElement('.overall-progress');
			this.overallProgress = new Progress(progress);

			this.clearall.addEvent('click', function(){
				if (this.running) return;

				this.clear(); // remove all files
				this.overallProgress.reset();

				var children = this.form.getChildren(), dragdrop;

				dragdrop = children.shift().setStyle('display', 'block');
				children = children.slice(0, children.length - 1);

				new Elements(children).setStyle('display', 'none');

				this.clearall.setStyle('display', 'none');

				this.status.getElements('canvas').setStyle('display', 'block');
				this.status.getElement('#files-success').setStyle('display', 'none');

				this.status.store('rendered:files', false);
			}.bind(this));

			this.status.store('rendered:init', true);
		}

		if (type == 'files'){
			var children = this.form.getChildren();

			new Elements(children.slice(1, children.length - 1)).setStyle('display', 'block');
			this.list.setStyle('display', 'block');
			document.id('files-clear').setStyle('display', 'inline-block');
			document.id('files-empty-desc').setStyle('display', 'none');

			this.status.store('rendered:files', true);
		}
	},

	setJobs: function(){
		window.Popup.popup.getElements('.button').setStyle('display', 'none');
		this.clearall.setStyle('display', 'none');

		this.jobsInfo = new Element('div.job-info').inject(window.Popup.popup.getElement('.statusbar .clr'), 'before');
		window.Popup.popup.getElement('.loading').setStyle('display', 'block');

		return this;
	},

	setJobText: function(message){
		if (this.jobsInfo) this.jobsInfo.set('html', message);
	},

	createJob: function(){
		this.uploader.attachUnload('You are about to leave this page with an upload job in progress.\n\nAre you sure you want to continue?');
		this.setJobs();

		this.uploader.job.create();
	},

	setReady: function(){
		this.uploader.job.ready();
	},

	start: function(){
		clearTimeout(this.timer);

		if (this.queued.length || !this.files.length) return this;


		window.Popup.popup.removeEvents('dragover', 'dragleave', 'drop');
		this.detach().attach();
		this.fireEvent('beforeStart');

		if (!this.job) return this.createJob();

		var val = this.values;
		val.bytes.now = val.bytes.last = val.percent = val.uploaded = val.uploading = val.left = 0;

		this.queued = Array.flatten([this.files]); //.filter(function(file){
		//	return !file.element.hasClass('file-success') && !file.element.hasClass('file-uploading');
		//});

		this.running = true;
		this.timer = this.updateSpeed.periodical(500, this);

		files = this.queued.splice(0, this.options.queued || this.files.length);

		(files.length ? files : this.files).each(function(file, i){
			(function(){
				file.request.append(RokGallery.ajaxVars.model, 'upload');
				file.request.append(RokGallery.ajaxVars.action, 'file');
				file.request.append('params', JSON.encode({id: this.job}));
				file.request.send({data: file.file});
			}.bind(this)).delay(5 * i);
		}, this);

		return this;
	},

	progress: function(){
		var percentage = this.values.percent = Math.round(this.values.bytes.now * 100 / this.values.size);
		this.overallProgress.set(percentage);

		var total = this.overallTitle.retrieve('total');
		this.overallTitle.set('html', total + '<br /><span style="color: #999;">' + Uploader.formatUnit(this.values.bytes.now, 'b') + '</span>');
		this.currentText.set('html', percentage + '% <br />' + ((this.rate) ? Uploader.formatUnit(this.rate, 'bps') : '- B'));
	},

	fileRequest: function(file){
		this.values.files.uploading++;
	},

	fileComplete: function(file, response){
		var values = this.values.files;
		values.uploaded++;
		values.uploading--;

		this.setJobText('Uploading... (' + values.uploaded + '/' + this.files.length + ')');

		if (this.options.queued && values.uploading < this.options.queued){
			var files = this.queued.splice(0, this.options.queued - values.uploading);
			files.each(function(file, i){
				(function(){
					file.request.append(RokGallery.ajaxVars.model, 'upload');
					file.request.append(RokGallery.ajaxVars.action, 'file');
					file.request.append('params', JSON.encode({id: this.job}));
					file.request.send({data: file.file});
				}.bind(this)).delay(5 * i);
			}, this);
		}

		if (values.uploaded == this.files.length) this.complete();
	},

	complete: function(){
		clearTimeout(this.timer);
		this.status.removeClass('file-uploading');
		if (this.values.size) {
			if (Browser.firefox) this.values.bytes.now = this.values.size;
			this.progress();
			this.overallProgress.set(100);
			this.values.files.uploaded = this.files.length;
			this.values.files.uploading = this.values.left = 0;

			this.status.getElements('canvas').setStyle('display', 'none');
			this.status.getElement('#files-success').setStyle('display', 'block');
		} else {
			this.overallProgress.set(0);
			this.status.getElements('canvas').setStyle('display', 'block');
			this.status.getElement('#files-success').setStyle('display', 'none');
			//this.currentProgress.set(0);
		}

		this.running = false;

		this.setReady();
		this.uploader.detachUnload();
	},

	updateSpeed: function(){
		var previous = this.values.bytes.last,
			current = this.values.bytes.now,
			diff = current - previous;

		if (!diff) return;

		this.values.bytes.last = current;
		this.rate = diff * 2;
	},

	enqueue: function(list){

		if (!list) list = this.browse.files;

		if (this.files.length >= 100 || this.files.length + list.length > 100){
			var red = '#ed2e26';
			window.Popup.counter.tween('color', red).tween('color', '#999')
				.tween('color', red).tween('color', '#999')
				.tween('color', red).tween('color', '#999');
		}

		Object.each(list, this.add.bind(this));

		if (!this.status.retrieve('rendered:files') && this.files.length) this.render('files');

		this.updateOverall();
		this.scrollbar.update();
	},

	add: function(file, index){
		if (this.files.length + 1 > 100) return;

		if (this.validate(file)){
			var obj = new Uploader.HTML5.File(this, file, {list: this.list});
			obj.addEvents(this.filesBounds);
			this.files.push(obj);
			window.Popup.counter.getElement('span').set('text', this.files.length);
		}
	},

	clear: function(){
		clearTimeout(this.timer);

		window.Popup.counter.getElement('span').set('text', 0);

		this.files.each(function(file, i){
			file.request.cancel();
			file.remove.delay(1 * i, file);
		}, this);

		this.fireEvent('clear');
	},

	remove: function(file){
		this.files.erase(file);

		this.values.size -= file.file.size;

		this.updateOverall();
		window.Popup.reposition();
		this.scrollbar.update();

		window.Popup.counter.getElement('span').set('text', this.files.length);

		this.fireEvent('fileRemove');
	},

	dragover: function(e){
		if (!e.dataTransfer) e.dataTransfer = e.event.dataTransfer;

		e.stop();
		e.dataTransfer.dropEffect = (this.running) ? 'none' : 'copy';

		return this.fireEvent('dragOver');
	},

	dragout: function(e){
		return this.fireEvent('dragOut');
	},

	drop: function(e){
		if (!e.dataTransfer) e.dataTransfer = e.event.dataTransfer;
		e.stop();

		if (this.running) return false;

		this.enqueue(e.dataTransfer.files);
		document.body.focus();

		return this.fireEvent('drop');
	},

	validate: function(file){
		var opts = this.options,
			msg = MooTools.lang.get('Uploader', 'validationErrors'),
			msgType = false;

		if (!file.type && !file.size) return false; // probably folder
		if (!file.type.test(/^image\/(jp(e)?g|gif|png)$/)) return false; // it's not an image

		if (opts.fileSizeMax && file.size > opts.fileSizeMax) msgType = 'sizeLimitMax'; // exceeds file size limit
		else if (opts.fileSizeMin && file.size < opts.fileSizeMin) msgType = 'sizeLimitMin'; // too small
		else if (opts.fileListSizeMax && (file.size + this.values.size) > opts.fileListSizeMax) msgType = 'fileListSizeMax';
		else if (opts.fileListMax && this.files.length >= opts.fileListMax) msgType = 'fileListMax';

		if (msgType){
			var error = this.error(file, msg[msgType]);
			return false;
		}

		this.values.size += file.size;

		return true;
	},

	error: function(file, msg){
		return new Element('li.validation-error', {
			html: msg.substitute({
				name: file.name,
				size: Uploader.formatUnit(file.size, 'b'),
				fileSizeMin: Uploader.formatUnit(this.options.fileSizeMin || 0, 'b'),
				fileSizeMax: Uploader.formatUnit(this.options.fileSizeMax || 0, 'b'),
				fileListMax: this.options.fileListMax || 0,
				fileListSizeMax: Uploader.formatUnit(this.options.fileListSizeMax || 0, 'b')
			}),
			title: MooTools.lang.get('Uploader', 'removeTitle'),
			events: {
				click: function() {
					this.destroy();
					window.Popup.reposition();
				}
			}
		}).inject(this.list, 'top');
	},

	updateOverall: function() {
		this.overallTitle.set('html', MooTools.lang.get('Uploader', 'progressOverall').substitute({
			total: Uploader.formatUnit(this.values.size, 'b')
		})).store('total', Uploader.formatUnit(this.values.size, 'b'));
		if (!this.values.size) {
			this.currentText.set('html', '');
			this.clearall.fireEvent('click');
		}
	}

});


this.Uploader.HTML5.File = new Class({

	Implements: [Options, Events],

	options: {
		list: null,
		file: null
	},

	initialize: function(base, file, options){
		this.setOptions(options);

		this.list = document.id(this.options.list);
		this.file = file;
		this.base = base;

		this.bounds = {
			onProgress: this.progress.bind(this),
			onRequest: this.request.bind(this),
			onSuccess: this.success.bind(this),
			onComplete: this.complete.bind(this)
		};

		this.request = new Request.File({url: this.base.form.get('action')});
		this.request.addEvents(this.bounds);

		this.render();
	},

	render: function(){
		this.info = new Element('span', {'class': 'file-info'});
		this.canvas = new Element('canvas', {width: 12, height: 12, 'class': 'file-canvas'});
		this.element = new Element('li', {'class': 'file file-' + Number.random(1, 3)}).adopt(
			new Element('span', {'class': 'file-name', 'html': this.file.name}),
			new Element('span', {'class': 'file-size', 'html': '(' + Uploader.formatUnit(this.file.size, 'b') + ')'}),
			new Element('a', {
				'class': 'file-remove',
				href: '#',
				html: '<span>remove</span>',
				title: MooTools.lang.get('Uploader', 'removeTitle'),
				events: {
					'click:once': function(e) {
						e.stop();
						this.remove();
					}.bind(this)
				}
			}),
			this.info,
			this.canvas,
			new Element('div.clr')
		).inject(this.list);

		new Element('div.file-canvas-wrapper').wraps(this.canvas);

		if (!this.progressBar) this.progressBar = new Progress(this.canvas);

		this.fireEvent('add', this);
	},

	remove: function(){
		this.element.fade('out').retrieve('tween').chain(function(){
			Element.destroy(this.element);
			this.fireEvent('remove', this);
		}.bind(this));
	},

	request: function(){
		this.element.addClass('file-uploading');
		this.progressBar.reset();
		this.element.store('loaded', 0);

		this.base.fireEvent('onFileRequest', this);
	},

	progress: function(evt){
		var stored = this.element.retrieve('loaded'),
			loaded = evt.loaded;

		var percent = Math.round(evt.loaded * 100 / evt.total);
		this.progressBar.set(percent);

		if (stored < loaded)
		this.base.values.bytes.now += ((loaded - stored) * this.file.size) / evt.total;

		this.element.store('loaded', loaded);
		this.base.fireEvent('progress', evt.loaded);
	},

	success: function(response){
		var stored = this.element.retrieve('loaded');

		//if (Browser.firefox){
		//	this.base.values.bytes.now += this.file.size - stored;
		//	this.base.fireEvent('progress');
		//}

		this.element.store('loaded', this.file.size);

		this.progressBar.set(100);
		this.element.addClass('file-success');

		this.base.fireEvent('onFileSuccess', [this, response]);
	},

	complete: function(response){
		this.element.removeClass('file-uploading');

		this.base.fireEvent('onFileComplete', [this, response]);
	}

});


var empty = function(){},
	progressSupport = ('onprogress' in new Browser.Request);


Request.File = new Class({

	Extends: Request,

	options: {
		emulation: false,
		urlEncoded: false
	},

	initialize: function(options){
		this.xhr = new Browser.Request();
		this.formData = new FormData();
		this.setOptions(options);
		this.headers = this.options.headers;
	},

	append: function(key, value){
		if (typeof key == 'object'){
			if (key.constructor == File) return this.append('Filedata', key);
			for (var val in key) return this.append(val, key[val]);
		}

		this.formData.append(key, value);
		return this.formData;
	},

	send: function(options){
		if (!this.check(options)) return this;

		this.options.isSuccess = this.options.isSuccess || this.isSuccess;
		this.running = true;

		var type = typeOf(options);
		if (type == 'string' || type == 'element') options = {data: options};

		var old = this.options;
		options = Object.append({data: old.data, url: old.url, method: old.method}, options);
		var data = options.data, url = String(options.url), method = options.method.toLowerCase();

		switch (typeOf(data)){
			case 'element': data = new FormData(data); break;
			case 'object': case 'hash': data = this.append(data);
		}

		if (this.options.format){
			data.append('format', this.options.format);
		}

		if (this.options.emulation && !['get', 'post'].contains(method)){
			data.append('_method', method);
		}

		if (this.options.urlEncoded && ['post', 'put'].contains(method)){
			var encoding = (this.options.encoding) ? '; charset=' + this.options.encoding : '';
			this.headers['Content-type'] = 'application/x-www-form-urlencoded' + encoding;
		}

		if (!url) url = document.location.pathname;

		var trimPosition = url.lastIndexOf('/');
		if (trimPosition > -1 && (trimPosition = url.indexOf('#')) > -1) url = url.substr(0, trimPosition);

		if (this.options.noCache)
			url += (url.contains('?') ? '&' : '?') + String.uniqueID();

		if (data && method == 'get'){
			url += (url.contains('?') ? '&' : '?') + data;
			data = null;
		}

		var xhr = this.xhr;
		if (progressSupport){
			xhr.onloadstart = this.loadstart.bind(this);
			xhr.onprogress = this.progress.bind(this);
			xhr.upload.onprogress = this.progress.bind(this);
		}

		xhr.open(method.toUpperCase(), url, this.options.async, this.options.user, this.options.password);
		if (this.options.user && 'withCredentials' in xhr) xhr.withCredentials = true;

		xhr.onreadystatechange = this.onStateChange.bind(this);

		Object.each(this.headers, function(value, key){
			try {
				xhr.setRequestHeader(key, value);
			} catch (e){
				this.fireEvent('exception', [key, value]);
			}
		}, this);

		this.fireEvent('request');
		xhr.send(data);
		if (!this.options.async) this.onStateChange();
		if (this.options.timeout) this.timer = this.timeout.delay(this.options.timeout, this);
		return this;
	}

});

})());
