/**
 * FancyUpload - Flash meets Ajax for powerful and elegant uploads.
 *
 * Updated to latest 3.0 API. Hopefully 100% compat!
 *
 * @version		3.0
 *
 * @license		MIT License
 *
 * @author		Harald Kirschner <http://digitarald.de>
 * @copyright	Authors
 */

((function(){

this.Uploader.Flash = new Class({

	Extends: Swiff.Uploader,

	options: {
		verbose: false,
		appendCookieData: true,
		queued: 1,
		// compat
		limitSize: 0,
		limitFiles: 0,
		validateFile: Function.from(true),
		typeFilter: {
			'Images (*.jpg, *.jpeg, *.gif, *.png)': '*.jpg; *.jpeg; *.gif; *.png'
		},
		target: 'files-browse',
		container: 'files-browse',
		fileSizeMax: 0,

		data: {
			'params': {}
		},

		onBeforeStart: function(){
			var data = this.options.data;
			data[RokGallery.ajaxVars.model] = 'upload';
			data[RokGallery.ajaxVars.action] = 'file';
			data.params = JSON.encode({id: this.job});
			this.setOptions({data: data});

			if (this.options.appendCookieData) this.appendCookieData();
		},

		onLoad: function() {
			this.target.addEvents({
				click: function() {
					this.removeClass('hover').removeClass('down');
					return false;
				},
				mouseenter: function() {
					this.addClass('hover').removeClass('down');
				},
				mouseleave: function() {
					this.removeClass('hover').removeClass('down');
					this.blur();
				},
				mousedown: function() {
					this.addClass('down');
					this.focus();
				},
				mouseup: function(){
					this.removeClass('down');
					this.blur();
				}
			});

			this.clearall = new Element('span', {id: 'files-clear', text: 'clear all'}).inject(window.Popup.statusBar.getElement('.browse'), 'after');
			document.id('files-clear').addEvent('click', function() {
				this.remove(); // remove all files
				window.Popup.counter.getElement('span').set('text', 0);
				this.overallProgress.reset();

				var children = document.id('files-form').getChildren(), details;

				details = children.shift().setStyle('display', 'block');
				children = children.slice(0, children.length - 1);

				new Elements(children).setStyle('display', 'none');

				document.id('files-clear').setStyle('display', 'none');



				return false;
			}.bind(this));

			var upload = window.Popup.statusBar.getElement('.ok').set('id', 'files-upload');
			upload.addEvent('click', function() {
				this.start(); // start upload
				return false;
			}.bind(this));
		},

		onSelectFail: function(files) {
			files.each(function(file) {

				new Element('li', {
					'class': 'validation-error',
					html: file.validationErrorMessage || file.validationError,
					title: MooTools.lang.get('Uploader', 'removeTitle'),
					events: {
						click: function() {
							window.Popup.reposition();
							this.destroy();
						}
					}
				}).inject(this.list, 'top');

			}, this);
		},

		onFileSuccess: function(file, response) {
			if (response.charCodeAt(0) == 65279){
				for(var i = 1, tmp = []; i < response.length; i++)  tmp.push(response[i]);
				response = tmp.join('');
			}

			if (!JSON.validate(response)){
				file.element.addClass('file-failed');
				file.info.set('html', '<strong>An error occured:</strong> ' + 'Invalid JSON response.');

				this.scrollbar.update();
				return this;
			}

			response = JSON.decode(response);

			if (response.status != 'success'){
				file.element.addClass('file-failed');
				file.info.set('html', '<strong>An error occured:</strong> ' + response.message);

				return this;
			}

			file.element.addClass('file-success');
			this.scrollbar.update();

			return this;
		},

		onFileComplete: function(file, response){
			this.uploaded++;
			this.setJobText('Uploading... (' + this.uploaded + '/' + this.fileList.length + ')');
		},

		onFail: function(error) {
			switch (error) {
				case 'hidden': // works after enabling the movie and clicking refresh
					alert('To enable the embedded uploader, unblock it in your browser and refresh (see Adblock).');
					break;
				case 'blocked': // This no *full* fail, it works after the user clicks the button
					alert('To enable the embedded uploader, enable the blocked Flash movie (see Flashblock).');
					break;
				case 'empty': // Oh oh, wrong path
					alert('A required file was not found, please be patient and we fix this.');
					break;
				case 'flash': // no flash 9+ :(
					alert('To enable the embedded uploader, install the latest Adobe Flash plugin.');
			}
		}
	},

	initialize: function(status, list, options) {
		this.status = document.id(status);
		this.list = document.id(list);

		this.options.url = document.id('files-form').action;
		options.path = RokGallerySettings.application + 'Swiff.Uploader.swf';

		this.uploader = RokGallery.uploader;

		this.setOptions(options);

		this.scrollbar = new Scrollbar(list, {triggerElement: '#popup .content', gutter: true, wrapStyles: {'float': 'right'}});
		new Element('div.clr').inject(this.scrollbar.wrapper, 'after');

		// compat
		options.fileClass = options.fileClass || Uploader.Flash.File;
		options.fileSizeMax = options.limitSize || options.fileSizeMax;
		options.fileListMax = options.limitFiles || options.fileListMax;

		this.parent(options);

		this.addEvents({
			'load': this.render,
			'select': this.onSelect,
			'cancel': this.onCancel,
			'start': this.onStart,
			'queue': this.onQueue,
			'complete': this.onComplete
		});

		this.job = false;
	},

	setJobs: function(){
		window.Popup.popup.getElements('.button').setStyles({'visibility': 'hidden', 'position': 'absolute'});
		this.clearall.setStyle('display', 'none');

		this.jobsInfo = new Element('div.job-info').inject(window.Popup.popup.getElement('.statusbar .clr'), 'before');
		window.Popup.popup.getElement('.loading').setStyle('display', 'block');

		return this;
	},

	setJobText: function(message){
		this.jobsInfo.set('html', message);
	},

	createJob: function(){
		this.uploader.attachUnload('You are about to leave this page with an upload job in progress.\n\nAre you sure you want to continue?');
		this.setJobs();

		this.uploader.job.create();
	},

	setReady: function(){
		this.uploader.job.ready();
	},

	render: function() {
		this.overallTitle = this.status.getElement('.overall-title');
		//this.currentTitle = this.status.getElement('.current-title');
		this.currentText = this.status.getElement('.current-text');

		var progress = this.status.getElement('.overall-progress');
		this.overallProgress = new Progress(progress);
		/*progress = this.status.getElement('.current-progress')
		this.currentProgress = new ProgressBar(progress);*/

		this.updateOverall();
	},

	start: function(){
		if (!this.fileList.length || !this.size) return this;
		if (!this.job) return this.createJob();

		this.uploaded = 0;

		this.fireEvent('beforeStart');
		this.remote('xStart');

		return this;
	},

	onSelect: function() {
		this.status.removeClass('status-browsing');
		this.status.setStyle('display', 'block');
		this.list.setStyle('display', 'block');

		var children = document.id('files-form').getChildren(), details;

		details = children.shift().setStyle('display', 'none');
		children = children.slice(0, children.length - 1);

		new Elements(children).setStyle('display', 'block');

		document.id('files-clear').setStyle('display', 'inline-block');

		document.body.focus();
	},

	onCancel: function() {
		this.status.removeClass('file-browsing');
	},

	onStart: function() {
		this.status.addClass('file-uploading');
		this.overallProgress.set(0);
	},

	onQueue: function() {
		this.updateOverall();
		window.Popup.reposition();
		this.scrollbar.update();
	},

	onComplete: function() {
		this.status.removeClass('file-uploading');
		if (this.size) {
			this.overallProgress.set(100);
			this.status.getElements('canvas').setStyle('display', 'none');
			this.status.getElement('#files-success').setStyle('display', 'block');
		} else {
			this.overallProgress.set(0);
			this.status.getElements('canvas').setStyle('display', 'block');
			this.status.getElement('#files-success').setStyle('display', 'none');
		}

		this.setReady();
		this.uploader.detachUnload();
	},

	updateOverall: function() {
		this.overallTitle.set('html', MooTools.lang.get('Uploader', 'progressOverall').substitute({
			total: Uploader.formatUnit(this.size, 'b')
		})).store('total', Uploader.formatUnit(this.size, 'b'));
		if (!this.size) {
			//this.currentTitle.set('html', MooTools.lang.get('Uploader', 'currentTitle'));
			this.currentText.set('html', '');
		}
	},

	/**
	 * compat
	 */
	upload: function() {
		this.start();
	},

	removeFile: function() {
		return this.remove();
	}

});

this.Uploader.Flash.File = new Class({

	Extends: Swiff.Uploader.File,

	render: function() {
		if (this.invalid) {
			if (this.validationError) {
				var msg = MooTools.lang.get('Uploader', 'validationErrors')[this.validationError] || this.validationError;
				this.validationErrorMessage = msg.substitute({
					name: this.name,
					size: Uploader.formatUnit(this.size, 'b'),
					fileSizeMin: Uploader.formatUnit(this.base.options.fileSizeMin || 0, 'b'),
					fileSizeMax: Uploader.formatUnit(this.base.options.fileSizeMax || 0, 'b'),
					fileListMax: this.base.options.fileListMax || 0,
					fileListSizeMax: Uploader.formatUnit(this.base.options.fileListSizeMax || 0, 'b')
				});
			}
			this.remove();
			return;
		}

		this.addEvents({
			'start': this.onStart,
			'progress': this.onProgress,
			'complete': this.onComplete,
			'error': this.onError,
			'remove': this.onRemove
		});

		this.info = new Element('span', {'class': 'file-info'});
		this.canvas = new Element('canvas', {width: 12, height: 12, 'class': 'file-canvas'});
		this.element = new Element('li', {'class': 'file file-' + Number.random(1, 3)}).adopt(
			new Element('span', {'class': 'file-name', 'html': MooTools.lang.get('Uploader', 'fileName').substitute(this)}),
			new Element('span', {'class': 'file-size', 'html': '(' + Uploader.formatUnit(this.size, 'b') + ')'}),
			new Element('a', {
				'class': 'file-remove',
				href: '#',
				html: '<span>'+MooTools.lang.get('Uploader', 'remove') + '</span>',
				title: MooTools.lang.get('Uploader', 'removeTitle'),
				events: {
					click: function() {
						this.remove();
						window.Popup.reposition();
						return false;
					}.bind(this)
				}
			}),
			this.info,
			this.canvas,
			new Element('div.clr')
		).inject(this.base.list);

		new Element('div.file-canvas-wrapper').wraps(this.canvas);
		this.progressCanvas = new Progress(this.canvas);
	},

	validate: function() {
		window.Popup.reposition();
		return (this.parent() && this.base.options.validateFile(this));
	},

	onStart: function() {
		this.element.addClass('file-uploading');
		//this.base.currentProgress.cancel().set(0);
		//this.base.currentTitle.set('html', MooTools.lang.get('Uploader', 'currentFile').substitute(this));
	},

	onProgress: function() {
		this.base.overallProgress.set(this.base.percentLoaded);
		this.progressCanvas.set(this.progress.percentLoaded);

		var total = this.base.overallTitle.retrieve('total');
		this.base.overallTitle.set('html', total + '<br /><span style="color: #999;">' + Uploader.formatUnit(this.base.bytesLoaded, 'b') + '</span>');
		this.base.currentText.set('html', this.base.percentLoaded + '% <br />' + ((this.base.rate) ? Uploader.formatUnit(this.progress.rate, 'bps') : '- B'));
		//this.base.currentText.set('html', MooTools.lang.get('Uploader', 'currentProgress').substitute({
		//	rate: (this.base.rate) ? Uploader.formatUnit(this.progress.rate, 'bps') : '- B',
		//	bytesLoaded: Uploader.formatUnit(this.base.bytesLoaded, 'b'),
		//	timeRemaining: (this.base.timeRemaining) ? Uploader.formatUnit(this.base.timeRemaining, 's') : '-'
		//}));
		//this.base.currentProgress.start(this.progress.percentLoaded);
	},

	onComplete: function() {
		this.element.removeClass('file-uploading');
		//this.base.currentText.set('html', 'Upload completed');
		//this.base.currentProgress.start(100);

		if (this.response.error) {
			var msg = MooTools.lang.get('Uploader', 'errors')[this.response.error] || '{error} #{code}';
			this.errorMessage = msg.substitute(Object.append({
				name: this.name,
				size: Uploader.formatUnit(this.size, 'b')
			}, this.response));
			var args = [this, this.errorMessage, this.response];

			this.fireEvent('error', args).base.fireEvent('fileError', args);
		} else {
			this.base.fireEvent('fileSuccess', [this, this.response.text || '']);
		}
	},

	onError: function() {
		this.element.addClass('file-failed');
		var error = MooTools.lang.get('Uploader', 'fileError').substitute(this);
		this.info.set('html', '<strong>' + error + ':</strong> ' + this.errorMessage);
	},

	onRemove: function() {
		this.element.getElements('a').setStyle('visibility', 'hidden');
		this.element.fade('out').retrieve('tween').chain(function(){
			Element.destroy(this.element);
			this.base.scrollbar.update();
		}.bind(this));

		window.Popup.counter.getElement('span').set('text', this.base.fileList.length);
	}

});

})());
