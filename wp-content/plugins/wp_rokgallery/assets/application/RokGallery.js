
((function(){

var gallery = this.RokGallery = {
	init: function(){
		gallery.url = RokGallerySettings.url;
		gallery.ajaxVars = RokGallerySettings.ajaxVars;

		gallery.wrapBlueStork();

		gallery.blocks = new RokGallery.Blocks();
		gallery.editPanel = new RokGallery.Edit();
		gallery.filters = new RokGallery.Filters();
		gallery.jobsManager = gallery.createJobsManager();
		gallery.galleryManager = gallery.createGalleries();
		gallery.loadMore = gallery.createLoadMore();
		gallery.rubberband = gallery.createRubberBand();
		window.Popup = gallery.popup = new Popup();

		window.Popup.popup.addEvents({
			open: gallery.rubberband.detach.bind(gallery.rubberband),
			close: function(){
				if (Browser.firefox) document.getElements('html, body').setStyle('height', 'auto');
				gallery.rubberband.attach();
			}
		});

		document.body.addClass(Browser.name);

		gallery.attachSelectAll();

		//gallery.textareaScrollbar();
	},

	wrapBlueStork: function(){
		var body = document.id(document.body);
		if (body.get('id') != 'minwidth-body') return;

		var children = body.getChildren(),
			wrapper = new Element('div').inject(body, 'top');

		children.inject(wrapper);
		wrapper.setStyle('padding', body.getStyle('padding'));
		body.setStyle('padding', 0);
	},

	attachSelectAll: function(){
		var selectAll = document.getElement('.total-select-all');
		if (selectAll) selectAll.addEvent('click', function(e){
			e.stop();
			if (gallery.rubberband) gallery.rubberband.selectAll();
		});
	},

	createGalleries: function(){
		return new GalleriesManager('toolbar-galleries', {url: RokGallery.url});
	},

	createRubberBand: function(){
		return new Rubberband({
			'select': function(element){
				element.addClass('selected');
				element.store('rubberbanded', true);
				gallery.blocks.updateCounter();
			},
			'deselect': function(element){
				element.removeClass('selected');
				element.store('rubberbanded', false);
				gallery.blocks.updateCounter();
			},
			'elements': '.gallery-block',
			'ignores': '#toolbar-publish, #toolbar-unpublish, #toolbar-tag, #toolbar-delete',
			'trigger': 'rokgallery'
		}).attach();
	},

	createLoadMore: function(){
		return new MainPage('load-more', {
			url: RokGallery.url,
			pageData: {
				page: RokGallerySettings.next_page,
				items_per_page: RokGallerySettings.items_per_page,
				filters: []
			},
			onKeyDown: function(e, element){
				element.addClass('load-all').getElement('span.text').set('html', 'load all');
			},
			onKeyUp: function(e, element){
				element.removeClass('load-all').getElement('span.text').set('html', 'load more');
			},
			onError: function(message){
				this.element.removeClass('loader');
				this.popup({type: 'warning', 'message': message});
			},
			onGetPage: function(response){
				this.element.removeClass('loader');

				var payload = response.payload;

				if (!payload.more_pages){
					this.detach();
					this.element.fade('out').get('tween').chain(function(){
						this.element.setStyle('display', 'none');
					}.bind(this));
				}

				this.currentPage = {
					page: 1,
					items_per_page: payload.total_items_shown
				};

				this.setPageData({
					page: payload.next_page,
					items_per_page: payload.items_per_page
				});

				document.getElement('.total-viewing span:first-child').set('text', payload.total_items_shown);
				document.getElement('.total-viewing span:last-child').set('text', payload.total_items_in_filter);
				document.getElement('.total-files span:first-child').set('text', payload.total_items);
				RokGallerySettings.total_items = payload.total_items;
				RokGallerySettings.last_page = payload.last_page;

				var position = document.getElement('#gallery-list > .clr !~'), dummy, addBlocks = false, createBlocks = false;

				if (!position){
					position = document.getElement('#gallery-list > .clr');
					dummy = new Element('div').inject(position, 'before');
					createBlocks = true;
					['elements', 'index', 'selected'].forEach(function(array){
						RokGallery.rubberband[array].empty();
					});
				} else {
					dummy = new Element('div').inject(position, 'after');
					RokGallery.blocks.blocks.empty();

					// TODO to get fixed

					//['elements', 'index', 'selected'].forEach(function(array){
					//	RokGallery.rubberband[array].empty();
					//	console.log('2 getPage rubberband['+array+']', RokGallery.rubberband[array].length);
					//});
				}


				dummy.set('html', payload.html);
				var blocks = dummy.getElements('.gallery-block');

				dummy.getChildren().inject(dummy, 'before');
				dummy.dispose();

				if (createBlocks){
					RokGallery.blocks = new RokGallery.Blocks();
					RokGallery.editPanel.fileSettings = RokGallery.blocks.editFileSettings;
				}
				RokGallery.blocks.addBlocks(blocks);

				RokGallery.blocks.rubberband = RokGallery.rubberband;
				RokGallery.blocks.rubberband.selected.empty();

				document.getElement('.total-count .total-selected span').set('text', 0);

				return this;
			}
		});
	},

	createJobsManager: function(){

		var jobManager = new JobsManager('toolbar-jobs', {
			url: RokGallery.url,
			singleJob: {
				onStart: function(id){
					if (!this.element){
						this.element = document.id(this.id);
						this.element.set('slide', {duration: 350, transition: 'expo:in'});
					}
					this.element.getElement('.loader').setStyle('display', 'block');
				},
				onBeforeDelete: function(){
					this.element.getElement('.refresh').setStyle('display', 'none');
					this.element.getElement('.delete').setStyle('display', 'none');
				},
				onDelete: function(){
					this.element.slide('out').slide('out').get('slide').chain(function(){
						var parent = this.element.getParent();
						parent.set('tween', {duration: 200, transition: 'expo:out'});
						parent.tween('margin-bottom', 0).get('tween').chain(function(){
							parent.dispose();
							var total = jobManager.jobsCount.get('text').toInt();
							total = total - 1 < 0 ? 0 : total - 1;
							jobManager.jobsCount.set('text', total);
							jobManager.scrollbar.update();
						}.bind(this));
					}.bind(this));
				},
				onDone: function(id, response){
					if (this.action != 'status') return this.status.delay(10, this);

					var job = response.payload,
						oldData = this.element.retrieve('job-data');

					oldData.type = job.type;
					oldData.state = job.state;
					oldData.status = job.status;
					oldData.lastUpdate = job.lastUpdate;
					oldData.percent = job.percent;

					this.element.store('job-data', oldData);

					this.element.className = 'job-item ' + job.state.toLowerCase();
					this.element.getElement('.job-id').set('text', job.type + ' updated at ' + job.lastUpdate);
					this.element.getElement('.job-updated').set('text', job.status);
					this.element.getElement('.job-state').set('text', job.state + ' ('+job.percent+'%)');


					jobManager.options.refreshIcons(this.element, job.state);

					if (['prepping', 'pausing', 'canceling'].contains(job.state.toLowerCase())) return this.status.delay(10, this);

					return this;
				}
			},
			attachIcons: function(job){
				var actions = job.retrieve('actions'),
					request = job.retrieve('job-request');

				request.queue = 'status';
				actions.each(function(action, i){
					action.addEvent('click', function(){
						var data = job.retrieve('job-data'),
							state = data['state'].toLowerCase();

						if (!i){
							request.status();
						} else if (i == 1){
							if (state == 'ready') request.process();
							else if (state == 'paused') request.resume();
						} else if (i == 2){
							if (state == 'running') request.pause();
						} else if (i == 3){
							request.cancel();
						} else if (i == 4){
							request.queue = '';
							request['delete']();
						}
					});
				}, this);
			},
			refreshIcons: function(job){
				var actions = job.retrieve('actions'),
					loader = job.retrieve('loader'),
					data = job.retrieve('job-data'),
					request = job.retrieve('job-request'),
					state = data['state'].toLowerCase();

				actions.setStyle('display', 'none');
				loader.setStyle('display', 'none');
				actions[0].setStyle('display', 'block');

				if (['prepping', 'pausing', 'canceling'].contains(state)) actions[0].setStyle('display', 'block');
				if (['ready', 'paused'].contains(state)) actions[1].setStyle('display', 'block');
				if ('running' == state) actions[2].setStyle('display', 'block');
				if (['ready', 'paused', 'running'].contains(state)) actions[3].setStyle('display', 'block');
				if (['completed', 'canceled', 'errored'].contains(state)) actions[4].setStyle('display', 'block');
			},
			onError: function(message){
				this.statusBar.getElement('.loading').setStyle('display', 'none');
				this.jobsInfo.set('text', message);
				window.Popup.setPopup({'type': 'warning'});
			},
			onClean: function(response){
				this.get.delay(10, this);
			},

			onBeforeWipe: function(){
				this.statusBar.getElement('.loading').setStyle('display', 'block');
			},

			onWipe: function(response){
				this.get();
			},

			onBeforeGet: function(){
				this.statusBar.getElement('.loading').setStyle('display', 'block');
			},

			onGet: function(response){
				this.statusBar.getElement('.loading').setStyle('display', 'none');

				var jobs = response.payload.jobs, render, jobItem;

				//if (!window.Popup.content.getElement('div.jobs-wrapper')){
					var content = response.payload.html;

					window.Popup.content.addClass('jobs-popup');
					window.Popup.content.set('html', content);

					this.content = window.Popup.content.getElement('div.jobs-wrapper');
					this.topBar = window.Popup.content.getElement('div.jobs-batch');
					this.wipeContent = window.Popup.content.getElement('div#jobs-wipe-warning');
					this.wipeYes = this.wipeContent.getElement('.button.wipe-yes');
					this.wipeNo = this.wipeContent.getElement('.button.wipe-no');

					this.scrollbar = new Scrollbar(this.content, {gutter: true, fixed: true, wrapStyles: {'width': '100%'}});
					new Element('div.clr').inject(this.scrollbar.wrapper, 'after');

					this.refreshJobs = this.topBar.getElement('.button.refresh');
					this.cleanJobs = this.topBar.getElement('.button.clean');
					this.wipeAllJobs = this.topBar.getElement('.button.wipeall');
					this.jobsCount = this.topBar.getElement('.jobs-counter span');

					this.refreshJobs.addEvent('click', function(){
						this.statusBar.getElement('.loading').setStyle('display', 'block');
						this.get();
					}.bind(this));

					this.cleanJobs.addEvent('click', function(){
						if (!jobs.length) return;
						this.statusBar.getElement('.loading').setStyle('display', 'block');
						this.clean();
					}.bind(this));

					this.wipeAllJobs.addEvent('click', function(){
						if (!jobs.length) return;

						this.content.getParent('div').setStyle('display', 'none');
						this.wipeContent.setStyle('display', 'block');
					}.bind(this));

					this.wipeYes.addEvent('click', this.wipe.bind(this));
					this.wipeNo.addEvent('click', this.get.bind(this));
				//}

				this.jobsCount.set('text', jobs.length);

				this.scrollbar.update();
				if (!jobs.length) return this.content.set('text', '');

				jobs.each(function(job){
					jobItem = this.content.getElement('#' + job.id);

					var top = jobItem.getElement('div.job-top');
					var bottom = jobItem.getElement('div.job-bottom');

					//new Element('div.job-id').set('text', job.type + ' started at ' + job.created_at).inject(top);

					var actions = jobItem.getElement('div.job-actions');

					// actions
					var refreshButton = actions.getElement('div.refresh');
					var startButton = actions.getElement('div.start');
					var pauseButton = actions.getElement('div.pause');
					var cancelButton = actions.getElement('div.cancel');
					var removeButton = actions.getElement('div.delete');
					var loader = actions.getElement('div.loader');

					jobItem.store('actions', new Elements([refreshButton, startButton, pauseButton, cancelButton, removeButton]));
					jobItem.store('loader', loader);
					jobItem.store('job-data', job);
					jobItem.store('job-request', new Job(Object.merge({url: RokGallery.url, id: job.id}, this.options.singleJob)));

					this.options.attachIcons(jobItem);
					this.options.refreshIcons(jobItem);

				}, this);

				this.scrollbar.update();
				window.Popup.reposition();

				return this;

			}
		});

		return jobManager;

	},

	initTags: function(container, id, model){
		var data = {insert: {}, erase: {}};

		data.insert[RokGallery.ajaxVars.model] = data.erase[RokGallery.ajaxVars.model] = model || 'File';
		data.insert[RokGallery.ajaxVars.action] = 'addTags';
		data.erase[RokGallery.ajaxVars.action] = 'removeTags';

		var tags = new Tags.Ajax(container, {
			url: gallery.url,
			data: {
				id: id || 0,
				insert: data.insert,
				erase: data.erase
			},
			'onEmptyList': function(){
				this.container.getElement('.oops').setStyle('display', 'block');
			},
			'onNonEmptyList': function(){
				this.container.getElement('.oops').setStyle('display', 'none');
			}
		});

		var block = document.id('file-' + id);

		if (block){
			var tagsLabel = block.getElement('.gallery-data .tags');

			tags.addEvents({
				'onInsert': function(){
					RokGallery.blocks.editFileSettings.element.getElement('.statusbar .editfile-loader').setStyle('display', 'inline-block');
					if (tagsLabel) tagsLabel.set('text', this.list.length);
				},
				'onErase': function(){
					RokGallery.blocks.editFileSettings.element.getElement('.statusbar .editfile-loader').setStyle('display', 'inline-block');
				},
				'onAfterErase': function(){
					if (tagsLabel) tagsLabel.set('text', this.list.length);
				},
				'onAfterInsertSuccess': function(response){
					var fileSettings = RokGallery.blocks.editFileSettings;
					fileSettings.slices = response.payload.file.Slices;

					fileSettings.slices.each(function(slices, i){
						if (slices.id == fileSettings.currentSlice){
							fileSettings.currentSlice = i;
						}
					}, this);


					fileSettings.loadSlice(fileSettings.currentSlice, true);
					RokGallery.blocks.setPublishState(response.payload.file.published);

					RokGallery.blocks.editFileSettings.element.getElement('.statusbar .editfile-loader').setStyle('display', 'none');
				},
				'onAfterEraseSuccess': function(response){
					var fileSettings = RokGallery.blocks.editFileSettings;
					fileSettings.slices = response.payload.file.Slices;

					fileSettings.slices.each(function(slices, i){
						if (slices.id == fileSettings.currentSlice){
							fileSettings.currentSlice = i;
						}
					}, this);

					fileSettings.loadSlice(fileSettings.currentSlice, true);
					RokGallery.blocks.setPublishState(response.payload.file.published);

					RokGallery.blocks.editFileSettings.element.getElement('.statusbar .editfile-loader').setStyle('display', 'none');
				}
			});
		}

		return tags;
	},

	textareaScrollbar: function(){
		return;

		//var ta = document.getElement('#edit-filesettings textarea');
		//new Scrollbar(ta, {fixed: false, wrapStyles: {width: ta.getSize().x}});
	}
};

if (typeof RokGallerySqueezeBox != 'undefined') RokGallery['SqueezeBox'] = RokGallerySqueezeBox;

Object.merge(Element.NativeEvents, {
	dragstart: 2, drag: 2, dragenter: 2, dragleave: 2, dragover: 2, drop: 2, dragend: 2 // drag and drop
});

window.addEvent('domready', gallery.init);

/*
window.onbeforeunload = function(e){
	var e = e || window.event,
		message = "You are about to leave this page and any unsaved changed will be lost. Are you sure you want to continue?";

	// For IE and Firefox prior to version 4
	if (e) e.returnValue = message;

	// For Safari
	return message;
};
*/

})());
