
((function(){
	
	if (!this.Uploader) this.Uploader = {};
	
	this.Uploader = new Class({
		
		Implements: [Options, Events],
		
		options: {
			url: ''
		},
		
		initialize: function(element, options){
			this.options.url = RokGallerySettings.url;
			
			this.setOptions(options);
			
			this.type = UploaderSupport.check();
			if (!this.type) return;
			
			if (this.type == 'Flash'){
				var session = RokGallerySettings.session;
				this.setOptions({url: this.options.url + "&" + RokGallerySettings.token + "=1&" + session.name + '=' + session.id});
			}
			
			this.element = document.id(element) || document.getElement('element');
			
			this.bounds = {
				'click': this.load.bind(this)
			};
			
			this.job = new Job({
				
				url: RokGallery.url,
				
				onError: function(message){
					this.instance.setJobText(message);
				}.bind(this),
				
				onBeforeCreate: function(){
					this.instance.setJobText('Creating Job...');
				}.bind(this),
				
				onCreate: function(response, id){
					// assigning job id to uploader
					this.instance.job = id;
					this.instance.setJobText('Uploading...');
					
					// starting uploading
					this.instance.start();
				}.bind(this),
				
				onBeforeReady: function(){
					this.instance.setJobText('Waiting for the Job to be ready.');
				}.bind(this),
				
				onReady: function(response, id){
					this.instance.setJobText('Job is in ready state mode.');
					
					window.Popup.popup.getElement('.loading').setStyle('display', 'none');
					
					var canvas = new Element('canvas', {width: 18, height: 18, 'class': 'job-canvas'});
					canvas.inject(this.instance.jobsInfo, 'before');
					this.instance.canvasWrapper = new Element('div.job-canvas-wrapper').wraps(canvas);
					this.job.progress = new Progress(canvas);
					
					this.job.process();
				}.bind(this),
				
				onBeforeProcess: function(){
					this.instance.setJobText('Processing the uploaded files.');
				}.bind(this),
				
				onProcess: function(response){
					window.Popup.setPopup({buttons:{cancel: {label: 'background'}}});
					window.Popup.popup.getElement('.button:last-child').setStyle('display', 'block');
					
					if (RokGallery.uploader.type == 'Flash'){
						window.Popup.popup.getElement('.button.cancel').setStyles({
							'display': 'block', 'position': 'relative', 'visibility': 'visible', 'float': 'right'
						});
					};
					
					this.timer = this.job.status.periodical(1000, this.job);
					this.job.status();
				}.bind(this),
				
				onBeforeStatus: function(){
					//this.timer = this.status.periodical(500, this);
					if (this.request.isRunning()) this.request.cancel();
				},
				
				onStatus: function(response){
					this.job.progress.set(response.payload.percent);
					this.instance.setJobText(response.payload.percent + '% - ' + response.payload.status);
					
					if (response.payload.percent == '100'){
						clearTimeout(this.timer);
						this.job.complete();
					}
					
					return this.job;
				}.bind(this),
				
				onComplete: function(id){
					clearTimeout(this.timer);
					this.job.request.cancel();
					window.Popup.setPopup({type: 'success', buttons:{cancel: {label: 'close'}}});
					window.Popup.popup.getElement('.button:last-child').setStyle('display', 'block');
					
					if (RokGallery.uploader.type == 'Flash'){
						window.Popup.popup.getElement('.button.cancel').setStyles({
							'display': 'block', 'position': 'relative', 'visibility': 'visible', 'float': 'right'
						});
					};
					
					var files = this.type == 'HTML5' ? this.instance.files.length : this.instance.fileList.length;
					this.instance.jobsInfo.dispose();
					this.instance.canvasWrapper.dispose();
					window.Popup.popup.getElement('.loading').setStyle('display', 'none');
					RokGallery.loadMore.refresh(files);
				}.bind(this)
				
			});
			
			this.attach();
			//this.target = document.id(this.options.target);
			//this.container = document.id(this.options.container);
			
			//document.id('uploader').addEvent('click', UploaderSupport.trigger);
			
		},
		
		attach: function(){
			this.element.addEvents(this.bounds);
		},
		
		detach: function(){
			this.element.removeEvents(this.bounds);
		},
		
		load: function(e){
			if (e) e.stop();
			this.popup();
			
			this.instance = new Uploader[this.type]('files-status', 'files-list', this.options);
			
			if (this.type == 'HTML5'){
				window.Popup.popup.addEvents({
					dragover: function(){
						var drop = this.form.getElement('.drop-info'),
							tween = drop.set('morph', {link: 'cancel'});
						
						drop.addClass('drag-over');
					}.bind(this.instance),
				
					dragleave: function(){
						this.form.getElement('.drop-info').removeClass('drag-over');
					}.bind(this.instance),
					
					drop: function(){
						this.form.getElement('.drop-info').removeClass('drag-over');
					}.bind(this.instance)
				});
			}
		},
		
		popup: function(){
			var popup = window.Popup, infoText;
			
			infoText = this.type == 'Flash' 
				? '<p>Click the Browse button and select the images you want to upload.</p><p>Once you are ready to upload, click the Upload button and keep track of the progress bars</p>' 
				: '<div class="drop-info">Drag &amp; Drop Files Here</div>';
			
			popup.setPopup({
				'type': '',
				'title': 'Files Upload',
				'message': '<form action="'+this.options.url+'" method="post" enctype="multipart/form-data" id="files-form"><div id="files-empty-desc">'+infoText+'</div><div id="files-status"><div class="total-progress"><canvas class="overall-progress" height="25" width="25"></canvas><div id="files-success"></div></div><div class="overall-title"></div><div class="current-text"></div></div><div id="files-list"></div></form>',
				'buttons': {
					'ok': {show: true, label: 'upload'},
					'cancel': {show: true, label: 'close'}
				},
				'continue': function(){

				}
			});

			var custom = popup.statusBar.getElement('div.button.browse.custom');
			if (!custom) custom = new Element('div#files-browse.button.browse.custom', {text: 'browse'}).inject(popup.statusBar.getElement('.clr'), 'before');
			var counter = popup.topBar.getElement('div.counter');
			if (!counter) counter = new Element('div.counter', {html: '<span>0</span> files'}).inject(popup.topBar.getElement('.icon'), 'after');
			counter.set('tween', {duration: 200, transition: 'quad:in:out', link: 'chain'});
			popup.counter = counter;
			
			popup.popup.addEvent('close:once', function(){
				//fancy.detach();
				//clearTimeout(this.job.timer);
				//this.job.request.cancel();
				
				var clear = document.id('files-clear');
				counter.dispose();
				document.id('files-browse').dispose();
				document.getElements('#popup .job-canvas-wrapper, #popup .job-info').dispose();
				if (clear) clear.dispose();
			}.bind(this));

			popup.open();
		},
		
		onBeforeUnload: function(message){
			var e = e || window.event,
				message = message || "You are about to leave this page and any unsaved changed will be lost. Are you sure you want to continue?";

			// For IE and Firefox prior to version 4
			if (e) e.returnValue = message;

			// For Safari
			return message;
		},
		
		attachUnload: function(message){
			window.onbeforeunload = this.onBeforeUnload.pass(message);
		},
		
		detachUnload: function(){
			window.onbeforeunload = function(){};
		}
		
	});
	
	window.addEvent('domready', function(){
		UploaderSupport.load(RokGallerySettings.application);
	});
	
	
	Object.append(this.Uploader, {

		STATUS_QUEUED: 0,
		STATUS_RUNNING: 1,
		STATUS_ERROR: 2,
		STATUS_COMPLETE: 3,
		STATUS_STOPPED: 4,

		log: function() {
			if (window.console && console.info) console.info.apply(console, arguments);
		},

		unitLabels: {
			b: [{min: 1, unit: 'B'}, {min: 1024, unit: 'KB'}, {min: 1048576, unit: 'MB'}, {min: 1073741824, unit: 'GB'}],
			s: [{min: 1, unit: 's'}, {min: 60, unit: 'm'}, {min: 3600, unit: 'h'}, {min: 86400, unit: 'd'}]
		},

		formatUnit: function(base, type, join) {
			var labels = Uploader.unitLabels[(type == 'bps') ? 'b' : type];
			var append = (type == 'bps') ? '/s' : '';
			var i, l = labels.length, value;

			if (base < 1) return '0 ' + labels[0].unit + append;

			if (type == 's') {
				var units = [];

				for (i = l - 1; i >= 0; i--) {
					value = Math.floor(base / labels[i].min);
					if (value) {
						units.push(value + ' ' + labels[i].unit);
						base -= value * labels[i].min;
						if (!base) break;
					}
				}

				return (join === false) ? units : units.join(join || ', ');
			}

			for (i = l - 1; i >= 0; i--) {
				value = labels[i].min;
				if (base >= value) break;
			}

			return (base / value).toFixed(2) + ' ' + labels[i].unit + append;
		}

	});
	
	
})());