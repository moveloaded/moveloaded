

((function(){

	this.RokGallery.FileSettings = new Class({

		Implements: [Options, Events],

		options: {},

		initialize: function(element, options){
			var self = this;

			this.setOptions(options);
			this.element = document.id(element) || document.getElement(element);
			this.indicator = this.element.getElement('.indicator');
			this.loader = document.id(this.options.loader) || document.id('file-settings-loader');

			this.margins = {
				element: {'margin-top': this.element.getStyle('margin-top'), 'margin-bottom': this.element.getStyle('margin-bottom')},
				loader: {'margin-top': this.loader.getStyle('margin-top'), 'margin-bottom': this.loader.getStyle('margin-bottom')}
			};

			this.bounds = {
				load: {
					onSuccess: this.prepopulate.bind(this)
				},
				remove: {
					onSuccess: this.deleteSliceResponse.bind(this)
				},
				publishSlice: {
					onSuccess: this.publishSliceResponse.bind(this)
				},
				arrows: {
					previous: this.previousSlice.bind(this),
					next: this.nextSlice.bind(this)
				},
				buttons: {
					'new': this.newSlice.bind(this),
					'edit': this.editSlice.bind(this),
					'share': this.shareSlice.bind(this),
					'delete': this.deleteSliceRequest.bind(this),
					'publishSlice': this.publishSliceRequest.bind(this),
					'publish': this.publishFile.bind(this),
					'remove': this.removeFile.bind(this)
				},
				inputs: {
					'title': this.updateTitle.bind(this),
					'slug': this.updateSlug.bind(this),
					'description': this.updateDescription.bind(this)
				},
				document: {
					'click': this.documentClose.bind(this)
				}
			};

			var data = {};
			data[RokGallery.ajaxVars.model] = 'File';
			data[RokGallery.ajaxVars.action] = 'update';

			this.ajax = new Request({url: RokGallery.url, method: 'post'}).addEvents(this.bounds.load);
			this.publishSliceAjax = new Request({url: RokGallery.url, method: 'post'}).addEvents(this.bounds.publishSlice);
			this.deleteAjax = new Request({url: RokGallery.url, method: 'post'}).addEvents(this.bounds.remove);
			this.fieldsAjax = new Request({
				url: RokGallery.url,
				method: 'post',
				data: data,
				onRequest: this.updateRequest.bind(this),
				onSuccess: this.updateSuccess.bind(this)
			});
			this.scroller = new Fx.Scroll(window, {duration: 300, transition: 'quad:out'});
			this.slide = new Fx.Slide(this.element, {
				onStart: function(){
					if (this.open){
						self.setOverflow('hidden');
						document.removeEvents(self.bounds.document);
					} else {
						document.addEvents(self.bounds.document);
					}
				},
				onComplete: function(){
					self.isOpen = !this.open;
					if (!this.open){
						self.setOverflow('visible');

						self.scroller.toElementEdge('file-settings', 'y');
					} else {
						var currentRow = self.current.getParent('.gallery-row'),
							nextRow = currentRow.getNext('.gallery-row');

						currentRow.removeClass('before-filesettings');
						if (nextRow) nextRow.removeClass('after-filesettings');

						this.wrapper.setStyle('display', 'none');
						RokGallery.rubberband.attach();
						RokGallery.filters.attach();
					}
				}
			}).hide();

			this.slide.wrapper.setStyle('display', 'none');

			this.loader.set('tween', {duration: 350}).setStyles({'opacity': 0, 'visibility': 'hidden'});

			this.current = null;
			this.id = 0;
			this.isOpen = false;
			this.slices = [];
			this.arrows = this.element.getElements('.slices-wrapper .status .previous, .slices-wrapper .status .next');
			this.buttons = this.element.getElements('.slices-controls > .button, .slices-controls > .publish-button');
			this.inputs = this.element.getElements('#file-settings .info input, #file-settings .info textarea');

			//if (!document.body.retrieve('filesettings-attached'))
			this.attachEvents();

			window.addEvent('resize:throttle(150)', this.updateIndicator.bind(this));

		},

		attachEvents: function(){
			var actions = ['new', 'edit', 'share', 'delete', 'publishSlice'];

			this.buttons.each(function(button, i){
				button.removeEvents('click');
				button.addEvent('click', this.bounds.buttons[actions[i]]);
			}, this);

			this.element.getElement('.editfile-publish, .editfile-unpublish').removeEvents('click');
			this.element.getElement('.editfile-delete').removeEvents('click');
			this.element.getElement('.editfile-publish, .editfile-unpublish').addEvent('click', this.bounds.buttons.publish);
			this.element.getElement('.editfile-delete').addEvent('click', this.bounds.buttons.remove);

			this.element.getElement('.statusbar .editfile-save').removeEvents('click').addEvent('click', this.saveFields.bind(this));

			['title', 'slug', 'description'].each(function(action, i){
				this.inputs[i].removeEvents('focus');
				this.inputs[i].removeEvents('blur');
				this.inputs[i].addEvent('focus', function(){

					clearTimeout(this.inputs[i].savingTimer);
					this.inputs[i].savingTimer = this.bounds.inputs[action].periodical(500, this.bounds.inputs[action], this.inputs[i]);

				}.bind(this)).addEvent('blur', function(){

					clearTimeout(this.inputs[i].savingTimer);
					this.bounds.inputs[action](this.inputs[i]);

				}.bind(this));

				this.inputs[i].store('saved-value', this.inputs[i].get('value'));
				//this.inputs[i].store('saving', new Request({
				//	url: RokGallery.url,
				//	method: 'post',
				//	data: {
				//		'model': 'File',
				//		'action': 'update'
				//	},
				//	onRequest: this.updateRequest.bind(this, this.inputs[i]),
				//	onSuccess: this.updateSuccess.bind(this, this.inputs[i])
				//})).store('saved-value', this.inputs[i].get('value'));

			}, this);

			document.body.store('filesettings-attached', true);
		},

		documentClose: function(e){
			if (RokGallery.editPanel.isOpen) return;

			var fileSettings = document.getElement('#file-settings ! div'),
				popup = document.id('popup'),
				overlay = document.id('overlay'),
				hasChild;

			hasChild = (e.target !== fileSettings && fileSettings.contains(e.target));
			hasChild = hasChild || (e.target !== popup && popup.contains(e.target));
			hasChild = hasChild || (e.target === overlay);

			if (!hasChild) this.element.getElement('.editfile-close').fireEvent('click');
		},

		setOverflow: function(status){
			$$(this.slide.wrapper, this.slide.element).setStyle('overflow', status);
		},

		inject: function(block){
			this.current = block;
			this.id = this.current.retrieve('file-id');
			var currentRow = block.getParent('.gallery-row'),
				nextRow = currentRow.getNext('.gallery-row');

			this.loader.inject(currentRow, 'after');
			this.slide.wrapper.inject(this.loader, 'after');
			this.indicator.setStyle('left', this.getBlockPosition(block));

			currentRow.addClass('before-filesettings');
			if (nextRow) nextRow.addClass('after-filesettings');

			/* this is a possible workaround for missioncontrol */
			if (document.id('mc-standard')){
				if (!nextRow) this.loader.setStyle('margin-bottom', -10);
				else this.loader.setStyle('margin-bottom', 0);
			}

			return this;
		},

		load: function(){
			this.loader.setStyles({'display': 'block', 'opacity': 1, 'visibility': 'visible'});
			this.slide.wrapper.setStyle('display', 'none');
			this.element.setStyle('display', 'none');

			RokGallery.filters.detach();

			var data = {
				params: JSON.encode({
					id: this.id
				})
			};
			data[RokGallery.ajaxVars.model] = 'File';
			data[RokGallery.ajaxVars.action] = 'get';

			this.ajax.send({
				data: data
			});
		},

		getBlockPosition: function(block){
			var indicatorSize = this.indicator.getSize().x / 2,
				blockSize = block.getSize().x / 2;

			return block.getPosition(block.getParent('.gallery-row')).x + blockSize;
		},

		updateIndicator: function(){
			if (!this.isOpen || !this.current) return;

			this.indicator.setStyle('left', this.getBlockPosition(this.current));
		},

		prepopulate: function(response){
			RokGallery.rubberband.detach();
			this.element.getElement('.editfile-close').addEvent('click:once', this.beforeClose.bind(this));

			this.loader.fade('out').retrieve('tween').chain(function(){
				this.populate(response);
			}.bind(this));
		},

		populate: function(response){
			this.loader.setStyle('display', 'none');
			this.slide.wrapper.setStyle('display', 'block');
			this.element.setStyle('display', 'block');

			if (!JSON.validate(response)){
				this.element.getElement('.editfile-close').fireEvent('click:once');

				return this.popup({
					title: 'File Settings - Invalid Response',
					message: '<p class="error-intro">The response from the server had an invalid JSON string while trying to load Image Data. Following is the reply.</p>' + response
				});
			}

			response = JSON.decode(response);

			if (response.status != 'success'){
				this.element.getElement('.editfile-close').fireEvent('click:once');
				return this.popup({title: 'File Settings - Error', message: response.message});
			}

			var filedata = response.payload.file,
				thumbdefaults = response.payload.defaults;

			['title', 'description', 'slug', 'published', 'Tags', 'Slices'].each(function(action){
				this['set' + action.capitalize()](filedata[action]);
			}, this);

			if (!thumbdefaults.thumb_background) thumbdefaults.thumb_background = '';

			this.current.store('file-data', filedata);
			this.current.store('thumb-defaults', thumbdefaults);

			return this.open();
		},

		open: function(){
			this.current.retrieve('switcher:filesettings').close.fade('out');
			this.slide.slideIn();

			return this;
		},


		beforeClose: function(){
			if (this.slide.open) {
				this.slide.chain(function(){
					RokGallery.blocks.enable().unflip(this.current);
				}.bind(this));

				this.close();
			} else {
				RokGallery.blocks.enable().unflip(this.current);
			}
		},

		close: function(){
			this.current.retrieve('switcher:filesettings').close.fade('in');
			this.slide.slideOut();

			return this;
		},

		toggle: function(){
			this.slide.toggle();

			return this;
		},

		setTitle: function(title){
			this.element.getElement('.info .title input').set('value', title).store('saved-value', title);
		},

		setDescription: function(description){
			this.element.getElement('.info .description textarea').set('value', description).store('saved-value', description);
		},

		setSlug: function(slug){
			this.element.getElement('.info .slug input').set('value', slug).store('saved-value', slug);
		},

		setPublished: function(published){
			this.element.getElement('.editfile-publish span > span:last-child, .editfile-unpublish span > span:last-child').set('text', published ? 'unpublish' : 'publish');

			this.element.getElement('.editfile-publish, .editfile-unpublish')
				.removeClass('editfile-publish')
				.removeClass('editfile-unpublish')
				.addClass('editfile-' + (published ? 'unpublish' : 'publish'));
		},

		setTags: function(tags){
			tags = tags.map(function(tag){
				return tag.tag;
			});

			this.element.getElements('.tags-list .tag').dispose();

			delete RokGallery.tags;
			RokGallery.tags = RokGallery.initTags('.tags.edit-block', this.id);
			RokGallery.tags.insertMany(tags, true);
			RokGallery.tags.list.inject(RokGallery.tags.container);

			if (!tags.length) RokGallery.tags.fireEvent('emptyList');
			else RokGallery.tags.fireEvent('nonEmptyList');

			RokGallery.tags.scrollbar.update();
		},

		setSlices: function(slices){
			this.slices = slices;
			this.currentSlice = 0;

			var page = this.element.getElements('.count span:last-child');
			page.set('text', this.slices.length);

			this.loadSlice(this.currentSlice);
		},

		updateSliceData: function(data, nocache){
			var index = this.currentSlice,
				currentData = this.slices[index];

			this.slices[index] = Object.merge(currentData, data);

			this.loadSlice(index, nocache);
		},

		loadSlice: function(index, nocache){
			var data = this.slices[index], element = this.element.getElement('.slices .slices-wrapper');

			var page = this.element.getElements('.count span:last-child');
			page.set('text', this.slices.length);

			// title
			element.getElement('.title').set('text', data['title'] || '');

			// image
			this.loadSliceThumb(index, nocache);

			// details
			element.getElement('.slice-width').set('text', data['xsize']);
			element.getElement('.slice-height').set('text', data['ysize']);
			element.getElement('.slice-size').set('text', Uploader.formatUnit(data['filesize'], 'b'));

			// publish
			this.publishSlice(index, data.published);
		},

		loadSliceThumb: function(index, nocache){
			var data = this.slices[index],
				wrapper = this.element.getElement('.slices'),
				element = wrapper.getElement('.image-wrapper'),
				image = element.getElement('.image'),
				page = wrapper.getElement('.count span:first-child'),
				total = wrapper.getElement('.count span:last-child'),
				gallery = wrapper.getElement('.gallery');

			image.set('class', 'image');
			image.setStyle('background-image', '');
			wrapper.addClass('loader');

			// badge
			element.removeClass('admin').removeClass('front');
			if (data.admin_thumb) element.addClass('admin');

			this.detachArrows();

			page.set('text', this.currentSlice + 1);
			total.set('text', this.slices.length);

			if (data['admin_thumb']){
				wrapper.getElement('.slice-delete').setStyle('display', 'none');
				wrapper.getElement('.slice-publish, .slice-unpublish').setStyle('display', 'none');
			} else {
				wrapper.getElement('.slice-delete').setStyle('display', 'block');
				wrapper.getElement('.slice-publish, .slice-unpublish').setStyle('display', 'block');
			}

			if (data['gallery_id']) gallery.set('text', 'Gallery: ' + document.getElement('#file-edit .file-gallery li[data-key='+data['gallery_id']+']').get('text'));
			else gallery.set('text', '');

			var thumburl = (!nocache) ? data['adminthumburl'] : data['adminthumburl'] + '?nocache=' + Date.now();

			if (Browser.Engine.webkit && nocache){
				(2).times(function(){
					new Asset.image(thumburl);
				});
			};

			new Asset.image(thumburl, {
				onload: function(){
					wrapper.removeClass('loader');
					image.setStyle('background-image', 'url('+thumburl+')');

					this.attachArrows();
				}.bind(this),
				onerror: function(){
					wrapper.removeClass('loader');
					image.addClass('error');

					this.attachArrows();
				}.bind(this)
			});

			if (data['admin_thumb']){
				this.current.getElement('.gallery-thumb-wrapper img').set('src', thumburl);
			}
		},

		update: function(evt, type){
			//var target = evt.target || evt,
			//	request = target.retrieve('saving'),
			//	value = target.retrieve('saved-value'),
			//	file = {};
			//
			//if (request.isRunning()) return;
			//
			//if (value != target.get('value')){
			//	file[type] = target.get('value');
			//	var params = {params: JSON.encode({id: this.id, file: file})},
			//		data = Object.merge(Object.clone(request.options.data), params);
			//
			//	request.updateType = type;
			//	request.send({data: data});
			//}

			var save = this.element.getElements('.statusbar .editfile-save'),
				target = evt.target || evt,
				value = target.retrieve('saved-value');

			if (value != target.get('value')){
				save.setStyle('display', 'inline-block');
			}
		},

		saveFields: function(){
			if (this.fieldsAjax.isRunning()) return;

			var file = {};

			['title', 'slug', 'description'].each(function(action, i){
				file[action] = this.inputs[i].get('value');
			}, this);

			var params = {params: JSON.encode({id: this.id, file: file})},
				data = Object.merge(Object.clone(this.fieldsAjax.options.data), params);

			this.fieldsAjax.send({data: data});
		},

		updateTitle: function(evt){
			this.update(evt, 'title');
		},

		updateSlug: function(evt){
			this.update(evt, 'slug');
		},

		updateDescription: function(evt){
			this.update(evt, 'description');
		},

		updateRequest: function(input){
			//var note = input.getPrevious('h1').getElement('span');
			//
			//input.store('saved-value', input.get('value'));
			//
			//note.set('tween', {link: 'chain'}).removeClass('error').fade('in');

			this.element.getElement('.statusbar .editfile-loader').setStyle('display', 'inline-block');
		},

		updateSuccess: function(response){
			//var note = input.getPrevious('h1').getElement('span');

			if (!JSON.validate(response)){
				return this.popup({
					title: 'File Settings - Invalid Response',
					message: '<p class="error-intro">The response from the server had an invalid JSON string while trying to save Image Data. Following is the reply.</p>' + response
				});
			}

			response = JSON.decode(response);

			if (response.status != 'success'){
				//input.blur();
				//note.fade('in').addClass('error');
				return this.popup({title: 'File Settings - Error', message: response.message});
			}

			//var request = input.retrieve('saving'),
			//	type = request.updateType,
			//	blockInfo = this.current.getElement('.image-' + type);
			//
			//note.fade('out');
			//
			//if (blockInfo) blockInfo.set('text', input.get('value'));
			//if (type == 'description') this.current.retrieve('descScrollbar').update();

			var payload = response.payload.file;

			['title', 'description'].each(function(type){
				var block = this.current.getElement('.image-' + type);
				if (block) block.set('text', payload[type]);

				if (type == 'description') this.current.retrieve('descScrollbar').update();
			}, this);

			this.slices = payload.Slices;
			this.loadSlice(this.currentSlice, true);

			this.element.getElement('.statusbar .editfile-loader').setStyle('display', 'none');
			this.element.getElement('.statusbar .editfile-save').setStyle('display', 'none');

			return this;
		},

		newSlice: function(){
			var fileData = this.current.retrieve('file-data');

			RokGallery.editPanel.setOptions({
				imageSize: {width: fileData.xsize.toInt(), height: fileData.ysize.toInt()}
			});

			var refreshCache = [];
			RokGallery.tags.getValues().forEach(function(tag){
				refreshCache.push({tag: tag});
			});

			fileData.Tags = refreshCache;

			RokGallery.editPanel.container.getElement('.image-status .navigation').setStyle('display', 'none');
			RokGallery.editPanel.load(fileData);
		},

		editSlice: function(){
			var fileData = this.current.retrieve('file-data');

			RokGallery.editPanel.setOptions({
				imageSize: {width: fileData.xsize.toInt(), height: fileData.ysize.toInt()}
			});

			var refreshCache = [];
			RokGallery.tags.getValues().forEach(function(tag){
				refreshCache.push({tag: tag});
			});

			fileData.Tags = refreshCache;
			this.slices[this.currentSlice].FileTags = refreshCache;

			RokGallery.editPanel.container.getElement('.image-status .navigation').setStyle('display', 'block');
			RokGallery.editPanel.container.getElement('.image-status .slice-current-no').set('text', this.currentSlice + 1);
			RokGallery.editPanel.container.getElement('.image-status .slice-total-no').set('text', this.slices.length);
			RokGallery.editPanel.load(fileData, this.slices[this.currentSlice]);
		},

		shareSlice: function(){
			var data = this.slices[this.currentSlice],
				host = window.location['protocol'] + '//' + window.location['host'],
				//link = host + data['imageurl'];
                link = data['imageurl'];

			var title = data['title'];
			var embed = '<img src=\"'+data['imageurl']+'\" width=\"300\" height=\"180\" alt=\"'+title+'\" title=\"'+title+'\" />';

			var message = '';
			message += '<p><label class="share" for="share-link">Share image slice link</label>\
				 		<input id="share-link" type="text" class="share" readonly="readonly" value="'+link+'" /></p>';
			message += '<p><label class="share" for="share-embed">Embed on your page</label>\
						<input id="share-embed" type="text" class="share" readonly="readonly" value="" /></p>';

			window.Popup.setPopup({
				title: 'Share Slice',
				message: message,
				buttons: {
					ok: {show: true, label: "close"}
				},
				'continue': function(){
					this.content.getElements('input').removeEvents();
					this.close();
				}
			}).open();

			window.Popup.popup.getElement('#share-embed').set('value', embed);

			window.Popup.content.getElements('input').addEvent('click', function(){
				this.select();
			});
		},

		publishSliceRequest: function(){
			if (this.publishSliceAjax.isRunning()) return;

			this.element.getElement('.slices-controls .slice-publish, .slices-controls .slice-unpublish').addClass('loader');

			var data = {
				params: JSON.encode({id: this.slices[this.currentSlice].id, slice: {published: !this.slices[this.currentSlice].published}})
			};

			data[RokGallery.ajaxVars.model] = 'Slice';
			data[RokGallery.ajaxVars.action] = 'update';

			this.publishSliceAjax.send({data: data});
		},

		publishSliceResponse: function(response){
			this.element.getElement('.slices-controls .slice-publish, .slices-controls .slice-unpublish').removeClass('loader');

			if (!JSON.validate(response)){
				return this.popup({
					title: 'Slice Publish / Unpublish - Invalid Response',
					message: '<p class="error-intro">The response from the server had an invalid JSON string while trying to publish/unpublish the Slice. Following is the reply.</p>' + response
				});
			}

			response = JSON.decode(response);

			if (response.status != 'success'){
				return this.popup({title: 'Slice Publish / Unpublish - Error', message: response.message});
			}

			this.slices[this.currentSlice] = response.payload.slice;
			this.publishSlice();

			RokGallery.blocks.setPublishState(response.payload.slice.File.published);

			return this;
		},

		deleteSliceRequest: function(){
			if (this.deleteAjax.isRunning()) return;

			var self = this, data = {
				params: JSON.encode({id: this.slices[this.currentSlice].id})
			};

			data[RokGallery.ajaxVars.model] = 'Slice';
			data[RokGallery.ajaxVars.action] = 'delete';

			window.Popup.setPopup({
				type: 'warning',
				title: 'Slice Deletion - Are you sure?',
				message: '<p>You are about to delete the current Slice <strong>'+this.slices[this.currentSlice].title+'</strong></p> \
						  <p>This operation is irreversible, are you sure you want to continue?</p>',
				buttons: {
					ok: {show: true, label: 'yes'},
					cancel: {show: true, label: 'no'}
				},
				'continue': function(){
					self.deleteAjax.send({data: data});
					this.close();
				}
			}).open();
		},

		deleteSliceResponse: function(response){
			if (!JSON.validate(response)){
				return this.popup({
					title: 'Slice Deletion - Invalid Response',
					message: '<p class="error-intro">The response from the server had an invalid JSON string while trying to delete the Slice. Following is the reply.</p>' + response
				});
			}

			response = JSON.decode(response);

			if (response.status != 'success'){
				return this.popup({title: 'Slice Deletion - Error', message: response.message});
			}

			this.deleteSlice();

			return this;
		},

		publishSlice: function(index, published){
			index = (index && index.toInt()) || this.currentSlice;

			published = typeof published == 'undefined' ? this.slices[index].published : published;

			var publish = this.element.getElement('.slices-controls .slice-publish, .slices-controls .slice-unpublish'),
				status = published,
				title = status ? 'unpublish' : 'publish',
				other = status ? 'publish' : 'unpublish';

			publish.set('title', title).removeClass('slice-' + other).addClass('slice-' + title);
		},

		deleteSlice: function(index){
			index = (index && index.toInt()) || this.currentSlice;

			this.slices.splice(index, 1);
			this.currentSlice = this.slices.length - 1;

			if (this.currentSlice < 0) this.currentSlice = this.currentSlice.length;

			this.loadSlice(this.currentSlice, true);
		},

		publishFile: function(){
			RokGallery.blocks.publish(this.current);
		},

		removeFile: function(){
			RokGallery.blocks.remove(this.current);
		},

		attachArrows: function(){
			var i = 0;
			Object.forEach(this.bounds.arrows, function(bound, index) {
				this.arrows[i].addEvent('click', bound);
				i++;
			}, this);
		},

		detachArrows: function(){
			var i = 0;
			Object.forEach(this.bounds.arrows, function(bound) {
				this.arrows[i].removeEvent('click', bound);
				i++;
			}, this);
		},

		previousSlice: function(){
			if (this.slices.length == 1) return;

			var current = this.currentSlice;

			this.currentSlice = (!~(current - 1)) ? this.slices.length - 1 : current - 1;

			this.loadSlice(this.currentSlice);
		},

		nextSlice: function(){
			if (this.slices.length == 1) return;

			var current = this.currentSlice;

			this.currentSlice = (current + 1 > this.slices.length - 1) ? 0 : current + 1;

			this.loadSlice(this.currentSlice);
		},

		popup: function(options){
			var defaults = {
				type: 'warning',
				title: 'Error',
				message: '',
				buttons: {
					ok: {show: false},
					cancel: {show: true, label: 'close'}
				}
			};

			window.Popup.setPopup(Object.merge(defaults, options)).open();
		}

	});

})());
