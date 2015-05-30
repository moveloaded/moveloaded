((function(){

this.RokGallery.Blocks = new Class({

	Implements: Events,

	initialize: function(){
		this.list = document.id('gallery-list');
		this.rubberband = RokGallery.rubberband;
		this.blocks = this.list.getElements('.gallery-block');
		this.batch = ['publish', 'unpublish', 'tag', 'delete'];
		var data = {
			update: {}, remove: {}
		};

		data.update[RokGallery.ajaxVars.model] = data.remove[RokGallery.ajaxVars.model] = 'Files';
		data.update[RokGallery.ajaxVars.action] = 'update';
		data.remove[RokGallery.ajaxVars.action] = 'delete';

		this.batchRequests = {
			'update': new Request({
				url: RokGallery.url,
				method: 'post',
				onSuccess: function(response){
					this.batchSuccess('update', response);
				}.bind(this),
				data: data.update
			}),
			'delete': new Request({
				url: RokGallery.url,
				method: 'post',
				onSuccess: function(response){
					this.batchSuccess('delete', response);
				}.bind(this),
				data: data.remove
			})
		};

		this.bounds = {
			activation: {
				mouseenter: this.activate.bind(this)
			},
			blocks: {
				flip: this.flip,
				unflip: this.unflip,
				edit: this.edit.bind(this),
				publish: this.publish.bind(this),
				remove: this.remove.bind(this)
			}
		};

		this.disabled = false;
		this.editFileSettings = new RokGallery.FileSettings('file-settings');

		this.attach();
		this.batchActions();
	},

	attach: function(block){
		((block) ? document.id(block) || document.getElements(block) : this.blocks)['addEvents'](this.bounds.activation);
	},

	addBlocks: function(blocks){
		this.blocks.push(blocks);
		this.blocks = Array.flatten(this.blocks);

		blocks.each(function(block){
			block.set('placement');
			this.attach(block);
			['elements', 'index', 'selected'].each(function(container){
				if (container == 'index') (this.rubberband || RokGallery.rubberband).add(block);
				else (this.rubberband || RokGallery.rubberband)[container].push(block);
				RokGallery.blocks.updateCounter();
			}, this);
		}, this);
	},

	batchActions: function(){
		var button, self = this;

		this.batch.each(function(batch){
			button = document.id('toolbar-' + batch);

			button.removeEvents('click');

			button.addEvent('click', function(e){
				e.stop();
				var rubberband = this.rubberband || RokGallery.rubberband;
				var tempFn;

				if (rubberband.detached) return button;

				if (!rubberband.selected.length){
					window.Popup.setPopup({
						type: 'warning',
						title: 'No selection',
						message: 'No image has been selected. In order to ' + batch + ' multiple images, you need to select them first.<br />To do so, start dragging in the page and a rubberband will show allowing you to select multiple images.<br /><br />Alternatively, you can also use the shift key to select or unselect a single block, by simply holding the shift key and clicking on an image block.',
						buttons: {
							ok: {show: false},
							cancel: {show: true, label: 'close'}
						}
					}).open();
				} else {
					this.multiSelection = {
						elements: rubberband.selected,
						settings: [],
						mode: false
					};

					var mode = (batch == 'delete') ? 'delete' : (batch == 'tag') ? 'tag' : 'update',
						params = {},
						ids = [],
						data;

					this.multiSelection.mode = batch;
					this.multiSelection.elements.each(function(block){
						this.activate(block);
						this.multiSelection.settings.push(block.retrieve('switcher:filesettings'));
						if (batch == 'publish' || batch == 'unpublish'){
							block.retrieve('switcher:filesettings').publish.addClass('loader');
						} else if (batch == 'delete' && rubberband.selected.length <= 4){
							this.removeAnimation(block, 'start');
						}
						ids.push(block.retrieve('file-id'));
					}, this);

					if (batch == 'tag'){
						var masstags = new MassTagsManager({url: RokGallery.url});
						tempFn = function(){
							var params = {
								ids: masstags.ids,
								tags: masstags.input.get('value').clean().replace(/,\s/, ',').split(',')
							};

							var action = masstags.getRadioAction();

							if ((params.tags.length == 1 && !params.tags[0].length)){
								var input = masstags.input,
									label = masstags.input.getPrevious('label');

								var red = '#ed2e26';

								label.set('tween', {duration: 250, link: 'chain', transition: 'quad:out'});
								input.set('tween', {duration: 250, link: 'chain', transition: 'quad:out'});

								label.tween('color', red)
									 .tween('color', '#888')
									 .tween('color', red)
									 .tween('color', '#888')
									 .tween('color', red)
									 .tween('color', '#888');

								input.tween('border-color', red)
									 .tween('border-color', '#ddd')
									 .tween('border-color', red)
									 .tween('border-color', '#ddd')
									 .tween('border-color', red)
									 .tween('border-color', '#ddd');

								window.Popup.statusBar.getElement('.button.ok').removeEvents('click');
								window.Popup.statusBar.getElement('.button.ok').addEvent('click', tempFn);
							} else {
								masstags[action + 'Tags'](params);
							}
						};

						window.Popup.setPopup({
							type: '',
							title: 'Multiple Files Tag',
							message: 'Loading...',
							buttons: {
								ok: {show: true, label: 'apply'},
								cancel: {show: true, label: 'close'}
							},
							'continue': tempFn
						}).open();

						var tagsInfo = window.Popup.popup.getElement('.tags-info');
						masstags.tagsInfo = tagsInfo || new Element('div.tags-info').inject(window.Popup.popup.getElement('.statusbar .clr'), 'before');

						window.Popup.statusBar.getElement('.button.cancel').addEvent('click:once', function(){
							//RokGallery.loadMore.refresh();
							masstags.tagsInfo.dispose();
						});

						return this;
					}

					switch (batch){
						case 'publish':
							params = {ids: ids, settings: {published: true}};
							break;
						case 'unpublish':
							params = {ids: ids, settings: {published: false}};
							break;
						case 'delete':
							params = {ids: ids};
							break;
					};

					params = {params: JSON.encode(params)};
					data = Object.merge(Object.clone(this.batchRequests[mode]['options']['data']), params);

					if (mode == 'delete'){
						var self = this;
						tempFn = function(){
							this.multiSelection.elements.each(function(block){
								if (rubberband.selected.length <= 4) this.removeAnimation(block, 'stop');
							}, this);
						}.bind(this);

						window.Popup.setPopup({
							type: 'warning',
							title: 'File Deletion - Are you sure?',
							message: '<p>You are about to delete <strong>'+this.multiSelection.elements.length+'</strong> Images and all the Slices associated to them.</p> \
									  <p>This operation is irreversible, are you sure you want to continue?</p>',
							buttons: {
								ok: {show: true, label: 'yes'},
								cancel: {show: true, label: 'no'}
							},
							'continue': function(){
								this.statusBar.getElement('.button.cancel').removeEvent('click:once', tempFn);
								if (self.editFileSettings.isOpen) self.editFileSettings.beforeClose();

								self.batchRequests[mode].send({data: data});

								//var total = document.getElement('.total-count .total-files span'),
								//	viewing = document.getElement('.total-viewing span:last-child'),
								//	amount_view = viewing.get('text').toInt(),
								//	amount_total = total.get('text').toInt();
								//
								//total.set('text', amount_total - self.multiSelection.elements.length);
								//viewing.set('text', amount_view - self.multiSelection.elements.length);

								this.close();
							}
						}).open();

						window.Popup.statusBar.getElement('.button.cancel').addEvent('click:once', tempFn);
					} else {
						this.batchRequests[mode].send({data: data});
					}

				}

				return button;
			}.bind(this));
		}, this);
	},

	activate: function(evt){
		var element = document.id(evt) || evt.target.getParent('.gallery-block') || evt.target;

		if (element.retrieve('activated')) return;
		element.removeEvents(this.bounds.activation).store('activated', true);
		element.store('file-id', element.get('id').match(/[0-9]{1,}/).join('').toInt());

		var filesettings = {
			front: element.getElement('.front-view'),
			back: element.getElement('.back-view'),
			edit: element.getElements('.image-edit'),
			publish: element.getElements('.image-publish, .image-unpublish'),
			remove: element.getElements('.image-delete'),
			open: element.getElement('.info-switcher'),
			close: element.getElement('.image-close')
		};

		if (!Supports3D){
			filesettings.front.setStyle('opacity', 1).set('tween', {duration: 600, link: 'cancel'});
			filesettings.back.setStyles({opacity: 0, zIndex: 1000}).set('tween', {duration: 600, link: 'cancel'});
		}

		element.store('switcher:filesettings', filesettings);

		filesettings.block = element;
		filesettings.open.store('switcher:filesettings', filesettings);
		filesettings.close.store('switcher:filesettings', filesettings);
		filesettings.edit.store('switcher:filesettings', filesettings);
		filesettings.publish.store('switcher:filesettings', filesettings);
		filesettings.remove.store('switcher:filesettings', filesettings);

		//element.store('switcher:open', switcher.open).store('switcher:close', switcher.close);

		if (!element.retrieve('descScrollbar')){
			var desc = element.getElement('.back-view .image-description');
			element.store('descScrollbar', new Scrollbar(desc, {fixed: true, wrapStyles: {'padding-right': 10}}));
		}

		if (!this.disabled) this.enable(element);
	},

	enable: function(element){
		this.disabled = false;
		if (!element) element = this.blocks;

		this.switchEvents(element, 'addEvent');

		return this;
	},

	disable: function(element){
		this.disabled = true;
		if (!element) element = this.blocks;

		this.switchEvents(element, 'removeEvent');

		return this;
	},

	switchEvents: function(element, type){
		if (!type) return;

		if (typeOf(element) == 'array') element = new Elements(element);
		if (typeOf(element) == 'elements'){
			element.each(function(block){
				this.activate(block);
				this.switchEvents(block, type);
			}, this);

			return;
		}

		var storage = element.retrieve('switcher:filesettings');
		var open = storage.open,
			close = storage.close,
			edit = storage.edit,
			publish = storage.publish,
			remove = storage.remove;

		if (typeOf(open) == 'array'){
			open = open.clean();
			close = close.clean();
			open.each(function(block, i){
				block[type]('click', this.bounds.blocks.flip);
				close[i][type]('click', this.bounds.blocks.unflip);
				publish[i][type]('click', this.bounds.blocks.publish);
				remove[i][type]('click', this.bounds.block.remove);
			}, this);
		} else {
			open[type]('click', this.bounds.blocks.flip);
			close[type]('click', this.bounds.blocks.unflip);
			edit[type]('click', this.bounds.blocks.edit);
			publish[type]('click', this.bounds.blocks.publish);
			remove[type]('click', this.bounds.blocks.remove);
		}

	}.protect(),

	flip: function(evt){
		var element = (typeOf(evt) == 'element' ? evt : this),
			storage = element.retrieve('switcher:filesettings');

		if (storage['block'].retrieve('flipped')) return;

		if (!Supports3D){
			storage['back'].fade('in').setStyle('z-index', 800);
			storage['front'].fade('out').setStyle('z-index', 500);
		} else {
			storage['block'].addClass('flip');
		}

		storage['block'].store('flipped', true);
	},

	unflip: function(evt){
		var element = (typeOf(evt) == 'element' ? evt : this),
			storage = element.retrieve('switcher:filesettings');

		if (!storage['block'].retrieve('flipped')) return;

		if (!Supports3D){
			storage['front'].fade('in').setStyle('z-index', 800);
			storage['back'].fade('out').setStyle('z-index', 500);
		} else {
			storage['block'].removeClass('flip');
		}

		storage['block'].store('flipped', false);
	},

	edit: function(evt){
		var storage = (evt.target || evt).retrieve('switcher:filesettings'),
			block = storage['block'];

		this.editFileSettings.inject(block).load();

		this.unflipAll(block);
		this.disable();
	},

	publish: function(evt){
		var params, data, storage = (evt.target || evt).retrieve('switcher:filesettings'),
			block = storage['block'],
			request = block.retrieve('switcher:publishing');

		if (!request){
			var dataRequest = {};
			dataRequest[RokGallery.ajaxVars.model] = 'File';
			dataRequest[RokGallery.ajaxVars.action] = 'update';

			block.store('switcher:publishing', request = new Request({
				url: RokGallery.url,
				method: 'post',
				onSuccess: function(response){
					this.publishSuccess(block, response);
				}.bind(this),
				data: dataRequest
			}));
		}

		if (request.isRunning()) return;

		var id = block.retrieve('file-id');
		storage.publish.addClass('loader');

		if (storage.publish.hasClass('image-publish').every(Math.floor)){
			params = {params: JSON.encode({id: id, file: {published: true}})};
		} else {
			params = {params: JSON.encode({id: id, file: {published: false}})};
		};

		data = Object.merge(Object.clone(request.options.data), params);
		request.send({data: data});
	},

	publishSuccess: function(block, response){
		if (!JSON.validate(response)){
			return this.popup({
				title: 'File Publishing - Invalid Response',
				message: '<p class="error-intro">The response from the server had an invalid JSON string while trying to publish/unpublish an Image. Following is the reply.</p>' + response
			});
		}

		response = JSON.decode(response);

		if (response.status != 'success'){
			return this.popup({title: 'File Publishing - Error', message: response.message});
		}

		var publish = block.retrieve('switcher:filesettings').publish;
		publish.removeClass('loader');

		if (publish.hasClass('image-publish').every(Math.floor)) publish.removeClass('image-publish').addClass('image-unpublish');
		else publish.removeClass('image-unpublish').addClass('image-publish');

		var publishValue = publish.hasClass('image-publish').every(Math.floor) ? 'publish' : 'unpublish',
			published = publishValue == 'publish' ? 'unpublished' : 'published';

		publish.set('title', publishValue);
		block.getElements('.indicator-published, .indicator-unpublished').removeClass('indicator-published').removeClass('indicator-unpublished').addClass('indicator-' + published);

		if (this.editFileSettings.isOpen && this.editFileSettings.id == block.retrieve('file-id')){
			this.editFileSettings.setPublished(publishValue != 'publish');

			var currentSliceID = this.editFileSettings.slices[this.editFileSettings.currentSlice].id;

			this.editFileSettings.slices = response.payload.file.Slices;

			this.editFileSettings.slices.each(function(slices, i){
				if (slices.id == currentSliceID){
					this.editFileSettings.loadSlice(this.editFileSettings.currentSlice, true);
					this.editFileSettings.currentSlice = i;
				}
			}, this);

			this.editFileSettings.loadSlice(this.editFileSettings.currentSlice, true);
		};

		return this;
	},

	setPublishState: function(state){
		var block = this.editFileSettings.current,
			publish = block.retrieve('switcher:filesettings').publish;

		publish.removeClass('image-publish').removeClass('image-unpublish').addClass('image-' + (state ? 'unpublish' : 'publish'));
		publish.set('title', (state ? 'unpublish' : 'publish'));

		var indicators = block.getElements('.indicator-published, .indicator-unpublished');
		indicators.removeClass('indicator-published').removeClass('indicator-unpublished');
		indicators.addClass('indicator-' + (state ? 'published' : 'unpublished'));


		this.editFileSettings.setPublished(state);
	},

	batchSuccess: function(type, response){
		var selection = this.multiSelection,
			rubberband = this.rubberband || RokGallery.rubberband;

		this.multiSelection = null;

		if (type == 'update'){
			selection.settings.each(function(settings){
				var publish = settings.publish;
				publish.removeClass('loader');
			}, this);
		};

		if (type == 'delete'){
			selection.settings.each(function(settings){
				if (rubberband.selected.length <= 4) this.removeAnimation(settings.block, 'stop');
			}, this);
		};

		if (!JSON.validate(response)){
			return this.popup({
				title: 'Batch Action - Invalid Response',
				message: '<p class="error-intro">The response from the server had an invalid JSON string while trying to perform a batch action on Images. Following is the reply.</p>' + response
			});
		}

		response = JSON.decode(response);

		if (response.status != 'success'){
			return this.popup({title: 'Batch Action - Error', message: response.message});
		}

		if (type == 'update'){
			selection.settings.each(function(settings){
				var state = selection.mode == 'publish', block = settings.block, publish = settings.publish;

				publish.removeClass('image-publish').removeClass('image-unpublish').addClass('image-' + (state ? 'unpublish' : 'publish'));
				publish.set('title', (state ? 'unpublish' : 'publish'));

				var indicators = block.getElements('.indicator-published, .indicator-unpublished');
				indicators.removeClass('indicator-published').removeClass('indicator-unpublished');
				indicators.addClass('indicator-' + (state ? 'published' : 'unpublished'));

				if (this.editFileSettings.isOpen && this.editFileSettings.id == block.retrieve('file-id')){
					this.editFileSettings.element.getElement('.editfile-publish, .editfile-unpublish').set('text', state);
				};

			}, this);
		};

		if (type == 'delete'){
			selection.elements.each(function(block){
				block.fade('out').retrieve('tween').chain(function(){
					var row = block.getParent('.gallery-row');
					this.removeAnimation(block, 'stop');
					block.getParent('.gallery-block-wrapper').dispose();
					this.eraseBlock(block);

					this.reorder(row);
				}.bind(this));
			}, this);
		};

		return this;

	},

	remove: function(evt){
		var params, data, storage = (evt.target || evt).retrieve('switcher:filesettings'),
			block = storage['block'],
			request = block.retrieve('switcher:removing'),
			self = this;

		if (!request){
			var dataRequest = {};
			dataRequest[RokGallery.ajaxVars.model] = 'File';
			dataRequest[RokGallery.ajaxVars.action] = 'delete';

			block.store('switcher:removing', request = new Request({
				url: RokGallery.url,
				method: 'post',
				onSuccess: function(response){
					this.removeSuccess(block, response);
				}.bind(this),
				data: dataRequest
			}));
		}

		if (request.isRunning()) return;

		var id = block.retrieve('file-id');

		this.removeAnimation(block, 'start');

		params = {params: JSON.encode({id: id})};
		data = Object.merge(Object.clone(request.options.data), params);

		var title = block.getElement('.gallery-description h1').get('text'),
			tempFn = function(){ this.removeAnimation(block, 'stop'); }.bind(this);

		window.Popup.setPopup({
			type: 'warning',
			title: 'File Deletion - Are you sure?',
			message: '<p>You are about to delete the Image <strong>'+title+'</strong> and all the Slices associated to it.</p> \
					  <p>This operation is irreversible, are you sure you want to continue?</p>',
			buttons: {
				ok: {show: true, label: 'yes'},
				cancel: {show: true, label: 'no'}
			},
			'continue': function(){
				this.statusBar.getElement('.button.cancel').removeEvent('click:once', tempFn);
				if (self.editFileSettings.isOpen) self.editFileSettings.beforeClose();
				request.send({data: data});
				this.close();
			}
		}).open();

		window.Popup.statusBar.getElement('.button.cancel').addEvent('click:once', tempFn);
	},

	removeSuccess: function(block, response){

		if (!JSON.validate(response)){
			this.removeAnimation(block, 'stop');
			return this.popup({
				title: 'File Deletion - Invalid Response',
				message: '<p class="error-intro">The response from the server had an invalid JSON string while trying to delete an Image. Following is the reply.</p>' + response
			});
		}

		response = JSON.decode(response);

		if (response.status != 'success'){
			this.removeAnimation(block, 'stop');
			return this.popup({title: 'File Deletion - Error', message: response.message});
		}


		block.fade('out').retrieve('tween').chain(function(){
			var row = block.getParent('.gallery-row');
			this.removeAnimation(block, 'stop');
			block.dispose();
			this.eraseBlock(block).reorder(row);
		}.bind(this));

		return this;
	},

	removeAnimation: function(block, action){
		if (SupportsFrames){
			if (action == 'start') block.addClass('remove');
			if (action == 'stop') block.removeClass('remove');

			return this;
		}

		var fx = block.retrieve('switcher:removeAnim');
		if (!fx){
			var tween = new Fx.Tween(block, {
			    duration: 5,
				fps: 12,
			    onComplete: function(){
			        var now = this.to[0].value;
			        if (now == -2) this.start('left', 2);
			        else this.start('left', -2);
			    }
			}).set('left', 0);
			fx = tween;
			block.store('switcher:removeAnim', tween);
		};

		if (action == 'start') fx.start('left', -2);
		if (action == 'stop') fx.cancel().set('left', 0);

		return this;

	},

	flipAll: function(exclude){
		var blocks = this.blocks.filter(function(block){
			return !block.retrieve('flipped');
		});

		blocks.each(function(block) {
			if (block.retrieve('flipped')) return;

			this.activate(block);
			block.retrieve('switcher:filesettings')['open'].fireEvent('click');
		}, this);

		if (exclude) this.unflip(exclude);
	},

	unflipAll: function(exclude){
		var blocks = this.blocks.filter(function(block){
			return block.retrieve('flipped');
		});

		blocks.each(function(block) {
			this.activate(block);
			block.retrieve('switcher:filesettings')['close'].fireEvent('click');
		}, this);

		if (exclude) this.flip(exclude);
	},

	eraseBlock: function(block){
		var rubberband = this.rubberband;
		this.blocks.erase(block);
		if (rubberband){
			['elements', 'index', 'selected'].each(function(container){
				if (rubberband[container].contains(block)) rubberband[container].erase(block);
				this.updateCounter();
			}, this);
		}
		return this;
	},

	reorder: function(from){
		RokGallery.loadMore.refresh();

		return;

		//var blocks = this.blocks,
		//	rows = document.getElements('.gallery-row'),
		//	rubberband = this.rubberband || RokGallery.rubberband,
		//	row;
		//
		//blocks = new Elements(blocks);
		//blocks = blocks.getParent('.gallery-block-wrapper');
		//
		//for (var i = 0, index = 0; i < blocks.length; i++, index = i % 3){
		//	if (!index) row = rows.shift();
		//
		//	blocks[i].inject(row.getElement('> .clr'), 'before');
		//
		//}
		//
		//rows.dispose();
		//rubberband.refresh();
	},

	updateCounter: function(){
		var counter = document.getElement('.total-count'),
			selected = counter.getElement('.total-selected > span'),
			total = counter.getElement('.total-files > span'),

			rubberband = this.rubberband || RokGallery.rubberband;

		//total.set('text', this.blocks.length);

		if (rubberband) selected.set('text', rubberband.selected.length);
		else selected.set('text', 0);
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
