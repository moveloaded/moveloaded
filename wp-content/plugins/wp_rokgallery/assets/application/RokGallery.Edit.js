
((function(){

this.RokGallery.Edit = new Class({

	Implements: [Events, Options],

	options: {
		imageSize: {width: 0, height: 0},
		scroll: {
			duration: 400,
			transition: 'expo:in:out',
			wheelStops: false
		}
	},

	initialize: function(options){
		this.setOptions(options);

		this.fileSettings = RokGallery.blocks.editFileSettings;

		this.id = null;

		this.isOpen = false;
		this.scale = 100;
		this.container = document.id('file-edit');
		this.scroll = new Fx.Scroll(window, this.options.scroll);
		this.wrapper = this.container.getElement('.image-wrapper');
		this.image = this.wrapper.getElement('img');

		this.loader = document.getElement('#file-edit .panel-actions .loader');

		this.scaleInfo = this.container.getElement('.scale span');
		this.inputs = {
			marquee: this.container.getElements('.edit-image .resize .marquee input'),
			image: this.container.getElements('.edit-image .resize .size input')
		};

		this.infoInputs = this.container.getElements('.info input, .info textarea').filter(function(input){
			return !input.hasClass('add-input') && input.id != 'slice-linkdata';
		});
		this.defaultThumb = this.container.getElement('#reset-thumb-sizes');

		this.attachSliceLink();
		this.attachInputs();

		this.actions = this.container.getElements('.panel-actions .button');

		// top right actions
		var actions = {save: 'saveRequest', publish: 'beforePublish', 'delete': 'deleteRequest', close: 'close'};

		this.actions.each(function(action){
			var cls = action.className.split(' ');
			if (cls.length){
				cls = cls[0].replace(/file\-edit\-/, '');
				if (actions[cls] && this[actions[cls]]) action.addEvent('click', this[actions[cls]].bind(this));
			}
		}, this);

		this.navigation = {
			arrows: this.container.getElements('.previous-slice, .next-slice'),
			current: this.container.getElement('.slice-current-no'),
			total: this.container.getElement('.slice-total-no')
		};

		this.attachNavigation();


		this.imageSize = this.options.imageSize;
		this.gallery = null;

		this.bounds = {
			resize: this.setSize.bind(this),
			load: {
				onRequest: function(){
					this.loader.setStyle('display', 'block');
				}.bind(this),
				onSuccess: this.save.bind(this)
			},
			remove: {
				onRequest: function(){
					this.loader.setStyle('display', 'block');
				}.bind(this),
				onSuccess: this.remove.bind(this)
			}
		};

		var data = {};
		data[RokGallery.ajaxVars.model] = 'Slice';
		data[RokGallery.ajaxVars.action] = 'update';

		this.ajax = new Request({url: RokGallery.url, method: 'post'}).addEvents(this.bounds.load);
		this.deleteAjax = new Request({url: RokGallery.url, method: 'post'}).addEvents(this.bounds.remove);
		this.publishAjax = new Request({
			url: RokGallery.url,
			method: 'post',
			data: data,
			onRequest: this.publishRequest.bind(this, this.container.getElement('.file-edit-publish, .file-edit-unpublish')),
			onSuccess: function(response){
				this.publishSuccess(this.container.getElement('.file-edit-publish, .file-edit-unpublish'), response);
			}.bind(this)
		});


		this.build();

		return this;
	},

	build: function(){
		var size = document.body.getSize(),
			styles = {
			    width: size.x,
			    height: '100%',
			    position: 'absolute',
			    right: -size.x,
			    top: 0,
				display: 'none'
			};

		this.container.setStyles(styles);
		this.container.inject(document.body);

		this.scrollbar = [
			new Scrollbar(this.wrapper, {fixed: true, resizeWrapper: false, wrap: true}),
			new Scrollbar(this.wrapper, {direction: 'horizontal', resizeWrapper: false, wrap: false, fixed: true})
		];
	},

	setSliderRanges: function(x, y){
		var min = Math.min(x, y),
			max = Math.max(x, y);

		this.slider.setRange([min * 12 / 100, min * 600 / 100]);
	},

	attachInputs: function(){
		var self = this,
			apply = document.getElement('#file-edit .file-edit-apply'),
			revert = document.getElement('#file-edit .file-edit-revert');/*,
			slider = document.getElement('#file-edit .size-slider'),
			knob = document.getElement('#file-edit .size-slider-knob');

		this.slider = new Slider(slider, knob, {
			offset: 8,
			onChange: function(step){
				if (!self.imageSize || !self.toolbar) return;

				var watch = this.element.retrieve('watchedProperty'), other = (watch == 'width' ? 'height' : 'width'), size = {};
				size[watch] = Math.round(step);
				size[other] = Math.round(self.imageSize[other] * step / self.imageSize[watch]);

				self.resize({width: size.width, height: size.height});
				//if (self.toolbar && self.toolbar.marquee.box.detached){
				//
				//} else {
				//	self.image.set('width', size.width).set('height', size.height);
				//}

				self.updateScrollbars();
			}
		});*/

		// defaults Thumbnails link
		this.defaultThumb.removeEvents('click').addEvent('click', this.resetThumbDefaults.bind(this));

		apply.removeEvents('click');
		revert.removeEvents('click');

		revert.addEvent('click', this.revert.bind(this));

		apply.addEvent('click', function(){
			var values = {image: [], marquee: []};

			for (var type in this.inputs){
				var inputs = this.inputs[type];
				inputs.each(function(input){
					values[type].push(input.get('value').toInt());
				});
			}

			var coords = {width: values.image[0], height: values.image[1]};
			this.resize(coords);

			var marquee = values.marquee;

			var realScale = this.image.retrieve('realScale');

			coords = {
				left: marquee[0] * realScale / 100,
				top: marquee[1] * realScale / 100,
				width: marquee[2] * realScale / 100,
				height: marquee[3] * realScale / 100
			};

			this.crop(coords);

		}.bind(this));

		for (var type in this.inputs){
			var inputs = this.inputs[type];

			inputs.removeEvents('blur');

			if (type == 'marquee'){

				var marquee = inputs;
				marquee.addEvent('blur', function(){
					var values = marquee.get('value'),
						image = this.inputs['image'].get('value');

					values.each(function(value, i){
						values[i] = value.toInt();
					});

					image[0] = image[0].toInt();
					image[1] = image[1].toInt();

					if (values[0] < 0) values[0] = 0;
					if (values[1] < 0) values[1] = 0;
					// marquee y + marquee h > image h = reset marquee y to image h - marquee h
					if ((values[0] + values[2]) > image[0]) values[0] = image[0] - values[2];
					if ((values[1] + values[3]) > image[1]) values[1] = image[1] - values[3];

					this.toolbar.marquee.box.setCoords(values);
				}.bind(this));

			} else if (type == 'image'){

				var image = inputs;
				image.addEvent('blur', function(){
					var values = image.get('value'),
						size = self.image.retrieve('size'),
						index = inputs.indexOf(this),
						otherIndex = index == 0 ? 1 : 0,
						current = ['width', 'height'][index],
						other = ['width', 'height'][otherIndex];

					// nw : ow = x : oh
					var lock = Math.round(values[index] * size[other] / size[current]);
					inputs[otherIndex].set('value', lock);
				});

			}
		};
	},

	attach: function(){
		window.addEvent('resize', this.bounds.resize);
	},

	detach: function(){
		window.removeEvent('resize', this.bounds.resize);
	},

	setSize: function(){
		//window.Popup.overlay.setStyle('width', '100%');
		var size = document.body.getSize().x, scrollSize = window.getScrollSize().x, height = 0;
		this.container.setStyles({width: size, right: -size});

		if (Browser.ie || Browser.opera) this.wrapper.getParent().setStyle('width', 'auto');

		document.getElements('#file-edit .edit-header, #file-edit .edit-block').each(function(el){
		    if (el.getStyle('display') != 'none') height += el.getSize().y;
		});

		this.wrapper.setStyle('max-height', window.getSize().y - height - 150);
		window.scrollTo(scrollSize, 0);
		//window.Popup.overlay.setStyle('width', scrollSize);

		this.updateScrollbars();
	},

	setTags: function(tags){

		tags = tags.map(function(tag){
			return tag.tag;
		});

		this.container.getElements('.tags-list .tag').dispose();

		delete this.tags;
		this.tags = new Tags.Slice('#file-edit .column.tags');
		this.tags.insertMany(tags, true);
		this.tags.list.inject(this.tags.container);

		if (!tags.length) this.tags.fireEvent('emptyList');
		else this.tags.fireEvent('nonEmptyList');

		this.tags.scrollbar.update();
	},

	addFileTags: function(tags){
		var container = this.container.getElement('.tags-list > .oops');

		if (tags.length) container.setStyle('display', 'none');
		tags.reverse().forEach(function(tag){
			var tagElement = new Element('div[class="tag dark"]');
			tagElement.set('html', '<div class="tag-value"><span class="tag-name">'+tag.tag+'</span></div>');
			tagElement.inject(container, 'after');

			//<div class="tag-value"><span class="tag-name">{value}</span></div><div class="tag-erase"><span>x</span></div>
		});
	},

	updateScrollbars: function(){
		this.scrollbar.each(function(scroll){
			scroll.update();
		});
	},

	attachNavigation: function(){
		var arrows = this.navigation.arrows,
			current = this.navigation.current,
			total = this.navigation.total;

		arrows.each(function(arrow){
			arrow.addEvent('click', function(e){

				e.stop();
				if (this.wrapper.hasClass('loader')) return;

				var current = this.fileSettings.currentSlice;

				if (arrow.hasClass('previous-slice')) this.fileSettings.previousSlice();
				else this.fileSettings.nextSlice();

				if (this.fileSettings.currentSlice == current) return;

				// detaching tools
				this.toolbar.detachAll();
				this.wrapper.getElement('.image-overlay').empty();
				this.toolbar = function(){};

				this.fileSettings.editSlice();
				RokGallery.editPanel.updateScrollbars();

			}.bind(this));
		}, this);
	},

	load: function(fileData, sliceData){
		var self = this,
			fullImage = fileData.imageurl,
			thumbDefaults = this.fileSettings.current.retrieve('thumb-defaults');

		document.removeEvents(RokGallery.blocks.editFileSettings.bounds.document);

		this.container.removeClass('has-ribbon');
		this.container.getElement('.ribbon').setStyle('display', 'none');

		this.container.getElement('.edit-block').removeClass('with-thumb');
		this.container.getElement('.button.file-edit-delete').setStyle('display', 'none');
		this.container.getElements('.info input[type=checkbox]').set('checked', '');

		for (var action in this.inputs){
			this.inputs[action].removeClass('disabled').removeProperty('disabled');
		}

		if (!sliceData){
			sliceData = {
				id: null,
				gallery_id: null,
				xsize: fileData.xsize,
				ysize: fileData.ysize,
				title: '',
				slug: '',
				link: '',
				caption: '',
				admin_thumb: false,
				published: false,
				thumb_xsize: thumbDefaults.thumb_xsize.toString(),
				thumb_ysize: thumbDefaults.thumb_ysize.toString(),
				thumb_keep_aspect: thumbDefaults.thumb_keep_aspect,
				thumb_background: thumbDefaults.thumb_background.toString(),
				manipulations: [{
					action: 'resize',
					options: {
						width: fileData.xsize,
						height: fileData.ysize
					}
				},
				{
					action: 'crop',
					options: {
						top: 0,
						left: 0,
						width: fileData.xsize,
						height: fileData.ysize
					}
				}],
				Tags: [],
				FileTags: fileData.Tags,
				Gallery: {}
			};
		};

		if (!sliceData.gallery_id) this.container.getElement('.edit-block').addClass('with-thumb');

		if (this.image) this.wrapper.getElement('.image-overlay').empty();
		this.wrapper.addClass('loader');

		this.container.getElement('.button.file-edit-delete').setStyle('display', 'none');

		this.id = sliceData.id;

		this.sliceData = sliceData;

		var status = this.container.getElement('.image-status .left');

		status.set('html', '<span>original file:</span> '+fileData.title+' <span> &#x25CF; </span> '+fileData.xsize+' x '+fileData.ysize+' <span>(1:1)</span> <span> &#x25CF; </span> '+Uploader.formatUnit(fileData.filesize, 'b'));

		/*this.slider.element.store('watchedProperty', (fileData.xsize <= fileData.ysize) ? 'width' : 'height');*/

		if (!this.isOpen) this.open();
		(function(){
		new Asset.image(fullImage, {
			onload: function(){
				self.wrapper.removeClass('loader');
				self.imageSize = self.options.imageSize;
				self.image = this;
				var inp = self.inputs;

				if (self.sliceData.admin_thumb){
					self.container.addClass('has-ribbon');
					self.container.getElement('.ribbon').setStyle('display', 'block');

					$$([inp.image[0], inp.image[1], inp.marquee[2], inp.marquee[3]]).addClass('disabled').set('disabled', 'disabled');
				}

				self.image.store('size', {width: self.imageSize.width, height: self.imageSize.height});
				self.image.store('resize', {width: self.sliceData.xsize, height: self.sliceData.ysize});
				self.image.store('realScale', 100);
				self.image.store('fakeScale', 100);
				self.image.store('revert', self.sliceData);

				self.image.set('width', self.imageSize.width).set('height', self.imageSize.height);

				/*var prop = self.slider.element.retrieve('watchedProperty') == 'width' ? 'xsize' : 'ysize';
				self.setSliderRanges(fileData.xsize, fileData.ysize);
				self.slider.set(self.sliceData[prop]);*/


				this.inject(self.wrapper.getFirst());
				RokGallery.editPanel.updateScrollbars();

				self.toolbar = new RokGallery.Edit.Toolbar();

				self.resize({width: self.sliceData.xsize, height: self.sliceData.ysize});

				self.setTags(self.sliceData['Tags'] || []);
				self.addFileTags(self.sliceData['FileTags'] || []);

				self.publish(self.sliceData.published);

				var gElement = self.galleries.container.getElement('li[data-key='+self.sliceData['gallery_id']+']');
				if (self.sliceData['gallery_id'] && gElement){
					gElement.fireEvent('click');
				} else {
					self.galleries.list[0].fireEvent('click');
				}

				['title', 'slug', 'link', 'caption', 'thumb_xsize', 'thumb_ysize', 'thumb_keep_aspect', 'thumb_background'].each(function(input ,i){
					var data = (self.infoInputs[i].get('type') != 'checkbox') ? self.sliceData[input] : (self.sliceData[input] ? 'checked' : '');
					self.infoInputs[i].set(self.infoInputs[i].get('type') == 'checkbox' ? 'checked' : 'value', data);
				}, this);

				self.parseSliceLink(self.sliceData['link']);

				self.sliceData.manipulations.each(function(manipulation){
					self[manipulation.action](manipulation.options);
				}, this);

				if (!self.id){
					self.toolbar.buttons[1].fireEvent('click');
				}

				if (self.id && !self.sliceData.admin_thumb){
					self.container.getElement('.button.file-edit-delete').setStyle('display', 'block');
				}

				if (self.sliceData.admin_thumb){
					if (self.toolbar.marquee) self.toolbar.marquee.box.setOptions({noHandlers: true}).hideHandlers();
				}

				if (self.sliceData.Gallery && self.sliceData.Gallery.force_image_size){
					if (self.toolbar.marquee) self.toolbar.marquee.box.setOptions({noHandlers: true}).hideHandlers();
					$$([inp.image[0], inp.image[1], inp.marquee[2], inp.marquee[3]]).addClass('disabled').set('disabled', 'disabled');
				}

				if ((self.sliceData.Gallery && self.sliceData.Gallery.force_image_size) || self.sliceData.admin_thumb){
					document.getElement('#file-edit .file-edit-apply').setStyle('display', 'none');
				} else {
					document.getElement('#file-edit .file-edit-apply').setStyle('display', 'block');
				}

				var els = self.container.getElements('.edit-block, .file-edit-publish, .file-edit-unpublish, .file-gallery.galleries-list');
				if (self.sliceData.admin_thumb){
					els.setStyle('display', 'none');
					self.setSize();
				} else {
					els.setStyle('display', 'block');
					self.setSize();
				}

			}
		});
		}).delay(500);

	},

	attachSliceLink: function(){
		this.sliceInputLink = document.id(RokGallery.SqueezeBox.linkElement) || document.getElement(RokGallery.SqueezeBox.linkElement);
		this.sliceInputData = document.id(RokGallery.SqueezeBox.dataElement) || document.getElement(RokGallery.SqueezeBox.dataElement);
		this.sliceClearData = document.getElement('.link-clear-input');

		this.sliceInputLink.addEvent('keyup', function(){
			var obj = {type: 'manual', link: this.sliceInputLink.get('value')};
			this.sliceInputData.set('value', JSON.encode(obj));
		}.bind(this));

		this.sliceClearData.addEvent('click:stop', this.enableSliceLink.bind(this));
	},

	disableSliceLink: function(){
		this.sliceInputLink.set('disabled', 'disabled').getParent().addClass('disabled');
		this.sliceClearData.setStyle('display', 'block');
	},

	enableSliceLink: function(nofocus){
		this.sliceInputLink.getParent().removeClass('disabled');
		this.sliceClearData.setStyle('display', 'none');

		this.sliceInputLink.set('value', '').removeProperty('disabled').fireEvent('keyup');
		if (nofocus !== true) this.sliceInputLink.focus();
	},

	parseSliceLink: function(data){
		if (!data) data = '';
		var json = data.length ? (JSON.validate(data) ? JSON.decode(data) : {'type': 'manual', link: data}) : {'type': 'manual', link: ''};

		if (json.title){
			this.disableSliceLink();
			this.sliceInputLink.set('value', json.title);
			this.sliceInputData.set('value', JSON.encode(json));
		} else {
			this.enableSliceLink(true);
			this.sliceInputLink.set('value', json.link);
			this.sliceInputData.set('value', JSON.encode(json));
		}
	},

	resetThumbDefaults: function(e){
		e.stop();
		// ['title', 'slug', 'link', 'caption', 'thumb_xsize', 'thumb_ysize', 'thumb_keep_aspect', 'thumb_background']

		var options = ['thumb_xsize', 'thumb_ysize', 'thumb_keep_aspect', 'thumb_background'],
			inputs = this.infoInputs.slice(4, 8),
			defaults = this.fileSettings.current.retrieve('thumb-defaults');

		options.each(function(input ,i){
			var data = (inputs[i].get('type') != 'checkbox') ? defaults[input] : (defaults[input] ? 'checked' : '');
			inputs[i].set(inputs[i].get('type') == 'checkbox' ? 'checked' : 'value', data);
		}, this);
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
	},

	open: function(){
		this.attach();
		RokGallery.rubberband.detach();

		document.body.setStyle('overflow', 'hidden');
		this.container.setStyle('display', 'block');

		this.setSize();

		this.setGalleries();

		//window.Popup.overlay.setStyle('width', window.getScrollSize().x);
		window.scrollTo(0, window.getScroll().y);

		this.infoInputs.set('value', '');
		this.scroll.toRight().chain(function(){
			this.isOpen = true;
			window.Popup.overlay.setStyle('width', '200%');
		}.bind(this));
	},

	close: function(){
		this.scroll.toLeft().chain(function(){
			this.detach();

			this.id = null;

			// detaching tools
			this.toolbar.detachAll();
			this.wrapper.getElement('.image-overlay').empty();
			this.toolbar = function(){};

			window.Popup.overlay.setStyle('width', '');
			RokGallery.rubberband.attach();
			this.container.setStyle('display', 'none');
			document.body.setStyle('overflow', 'visible');

			RokGallery.blocks.editFileSettings.scroller.toElementEdge('file-settings', 'y');
			document.body.addEvents(RokGallery.blocks.editFileSettings.bounds.document);

			this.isOpen = false;
			window.Popup.overlay.setStyle('width', '');
		}.bind(this));
	},

	setGalleries: function(){
		this.galleries = {
			container: this.container.getElement('.file-gallery'),
			text: this.container.getElements('.file-gallery .title'),
			list: this.container.getElements('.file-gallery li')
		};

		this.galleries.list.removeEvents('click').each(function(item, i){
			item.addEvent('click', function(){
				this.galleries.list.removeClass('selected');
				if (i) item.addClass('selected');

				this.galleries.text.set('text', item.get('text'));
				this.gallery = item.get('data-key') || null;

				if (!this.gallery) this.container.getElement('.edit-block').addClass('with-thumb');
				else this.container.getElement('.edit-block').removeClass('with-thumb');
				this.tags.scrollbar.update();

				this.galleries.container.addClass('hidden');

				document.addEvent('mousemove:once', function(){
					this.galleries.container.removeClass('hidden');
				}.bind(this));

			}.bind(this));
		}, this);
	},

	beforePublish: function(){
		if (!this.id) this.publish();
		else {
			if (!this.publishAjax.isRunning()){
				var data = {};
				data[RokGallery.ajaxVars.model] = 'Slice';
				data[RokGallery.ajaxVars.action] = 'update';
				data.params = JSON.encode({id: this.id, slice: {published: !this.sliceData.published}});
				this.publishAjax.send({
					data: data
				});
			}
		}
	},

	publish: function(force){
		var publish = this.container.getElement('.file-edit-publish, .file-edit-unpublish'),
			check = typeOf(force) == 'boolean' || force === 0 ? force : publish.hasClass('file-edit-publish');

		if (check){
			publish.removeClass('file-edit-publish').addClass('file-edit-unpublish');
			publish.getLast('span > span').set('text', 'unpublish');
			this.sliceData.published = true;
		} else {
			publish.removeClass('file-edit-unpublish').addClass('file-edit-publish');
			publish.getLast('span > span').set('text', 'publish');
			this.sliceData.published = false;
		}
	},

	publishRequest: function(input){
		//
	},

	publishSuccess: function(input, response){
		if (!JSON.validate(response)){
			return this.popup({
				title: 'Publish Action - Invalid Response',
				message: '<p class="error-intro">The response from the server had an invalid JSON string while trying to publishing / unpublishing the Slice. Following is the reply.</p>' + response
			});
		}

		response = JSON.decode(response);

		if (response.status != 'success') return this.popup({title: 'Publish Action - Error', message: response.message});

		this.publish(response.payload.published);

		return this;
	},

	saveRequest: function(){
		if (this.ajax.isRunning()) return;

		var data = this.sliceData, apply = {manipulations: []}, action = '', params = {};

		var actions = {
			resize: {
				width: this.inputs.image[0].get('value').toInt(),
				height: this.inputs.image[1].get('value').toInt()
			},
			crop: {
				left: this.inputs.marquee[0].get('value').toInt(),
				top: this.inputs.marquee[1].get('value').toInt(),
				width: this.inputs.marquee[2].get('value').toInt(),
				height: this.inputs.marquee[3].get('value').toInt()
			}
		};

		// infos
		['title', 'slug', 'link', 'caption', 'thumb_xsize', 'thumb_ysize', 'thumb_keep_aspect', 'thumb_background'].each(function(input ,i){
			apply[input] = this.infoInputs[i].get(this.infoInputs[i].get('type') == 'checkbox' ? 'checked' : 'value');
		}, this);

		// published
		apply['published'] = data.published;

		// tags
		apply['Tags'] = this.tags.getValues();

		// gallery
		apply['gallery_id'] = this.gallery;

		// manipulations
		for (var manipulation in actions){
			apply['manipulations'].push({action: manipulation, options: actions[manipulation]});
		}


		// building request based on new/existing slice
		if (this.id){
			Object.merge(this.sliceData['manipulations'], apply['manipulations']);
			params = {id: this.id, slice: apply};
			action = 'update';
		} else {
			params = {fileId: RokGallery.blocks.editFileSettings.id, slice: apply};
			action = 'create';
		}

		apply = {
			params: JSON.encode(params)
		};

		apply[RokGallery.ajaxVars.model] = 'Slice';
		apply[RokGallery.ajaxVars.action] = action;

		// sending request
		this.ajax.send({data: apply});
	},

	deleteRequest: function(){
		if (this.deleteAjax.isRunning() || !this.id) return;

		var self = this, data = {
			params: JSON.encode({id: this.id})
		};

		data[RokGallery.ajaxVars.model] = 'Slice';
		data[RokGallery.ajaxVars.action] = 'delete';

		window.Popup.setPopup({
			type: 'warning',
			title: 'Slice Deletion - Are you sure?',
			message: '<p>You are about to delete the current Slice <strong>'+this.sliceData.title+'</strong></p> \
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

	save: function(response){
		this.loader.setStyle('display', 'none');

		if (!JSON.validate(response)){
			return this.popup({
				title: 'Slice Save - Invalid Response',
				message: '<p class="error-intro">The response from the server had an invalid JSON string while trying to save the Slice. Following is the reply.</p>' + response
			});
		}

		response = JSON.decode(response);

		if (response.status != 'success'){
			return this.popup({title: 'Slice Save - Error', message: response.message});
		}

		if (!this.id && response.payload.slice.id){
			this.id = response.payload.slice.id;
			this.sliceData = response.payload.slice;
			this.setTags(this.sliceData['Tags'] || []);
			this.fileSettings.slices.push(response.payload.slice);
			this.fileSettings.currentSlice = this.fileSettings.slices.length - 1;
		}

		// refresh thumb and update currentSlice data with fresh one
		this.fileSettings.slices[this.fileSettings.currentSlice] = response.payload.slice;
		this.fileSettings.loadSlice(this.fileSettings.currentSlice, true);

		this.close();

		return this;
	},

	remove: function(response){
		this.loader.setStyle('display', 'none');

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

		// clean the slices list and close
		this.id = null;
		this.sliceData = null;

		this.fileSettings.deleteSlice();
		//this.fileSettings.slices.splice(this.fileSettings.currentSlice, 1);
		//this.fileSettings.currentSlice = this.fileSettings.slices.length - 1;
		//if (this.fileSettings.currentSlice < 0) this.fileSettings.currentSlice = this.fileSettings.currentSlice.length;
		//
		//this.fileSettings.loadSlice(this.fileSettings.currentSlice, true);

		this.close();

		return this;
	},

	revert: function(){
		var sliceData = this.image.retrieve('revert'),
			manipulations = sliceData.manipulations,
			actions = [];

		this.image.store('size', {width: this.imageSize.width, height: this.imageSize.height});
		this.image.store('resize', {width: sliceData.xsize, height: sliceData.ysize});
		this.image.store('realScale', 100);
		this.image.store('fakeScale', 100);
		this.image.store('revert', sliceData);

		manipulations.each(function(manipulation){
			actions.push(manipulation.action);
			this[manipulation.action](manipulation.options);
		}, this);

		if (!actions.contains('crop')){
			this.crop({left: 0, top: 0, width: sliceData.xsize, height: sliceData.ysize});
		}

		return this;
	},

	resize: function(options){
		var min = options.width < options.height ? 'width' : 'height';
		var scale = Math.round((options[min] / this.image.retrieve('size')[min]) * 100);

		this.image.set('width', options.width).set('height', options.height);

		this.toolbar.image.store('realScale', scale);
		this.toolbar.image.store('fakeScale', 100);
		this.toolbar.scaling.include(scale);
		this.toolbar.scaling.sort(function(a, b){
			return a - b;
		});

		this.image.store('resize', {width: options.width, height: options.height});

		RokGallery.editPanel.inputs.image[0].set('value', options.width);
		RokGallery.editPanel.inputs.image[1].set('value', options.height);
		//if (options.left) RokGallery.editPanel.inputs.marquee[0].set('value', options.left);
		//if (options.top) RokGallery.editPanel.inputs.marquee[1].set('value', options.top);
		//RokGallery.editPanel.inputs.marquee[2].set('value', options.width);
		//RokGallery.editPanel.inputs.marquee[3].set('value', options.height);

		this.toolbar.marquee.updateBox({scale: scale, zoom: 100, width: options.width, height: options.height, zooming: true});

		RokGallery.editPanel.updateScrollbars();
	},

	crop: function(options){

		var button = this.toolbar.buttons.filter(function(button){
			return button.hasClass('marquee');
		});
		button = Array.from(button)[0];
		if (!button.hasClass('pressed')) button.fireEvent('click');

		this.toolbar.marquee.box.setCoords([options.left, options.top, options.width, options.height]);

		RokGallery.editPanel.updateScrollbars();
	}

});


this.RokGallery.Edit.Toolbar = new Class({

	Implements: [Options],

	options: {
		'handtool': {
			onChange: function(x, y){
				this.element.scrollTo(x, y);
				RokGallery.editPanel.updateScrollbars();
			}
		}
	},

	initialize: function(options){
		var tools = ['handtool', 'marquee'],
			zoom = ['zoomin', 'zoom100', 'zoomout'],
			wrapper = document.getElement('#file-edit-wrapper .image-wrapper'),
			image = wrapper.getElement('img'),
			container = document.getElement('#file-edit-wrapper .toolbar');

		this.buttons = [];

		this.image = image;

		this.scaling = [6, 12, 16, 25, 33, 50, 66, 100, 150, 200, 300, 400, 600];
		this.size = {width: image.get('width'), height: image.get('height')};

		tools.each(function(tool){
			var button = container.getElement('.' + tool),
				klass = RokGallery.Edit.Toolbar[tool.capitalize()];

			if (typeof klass == 'undefined') return;

			if (this[tool]) delete this[tool];
			this[tool] = new klass(wrapper, this.options[tool] || {});

			button.store('tool', tool);
			button.addEvent('click', function(){
				this.buttons.each(function(other){
					if (other != button) other.removeClass('pressed');
				});

				tools.each(function(tool){
					if (typeof this[tool] != 'undefined') this[tool].detach();
				}, this);

				if (button.hasClass('pressed')){
					button.removeClass('pressed');
					this[button.retrieve('tool')].detach();
				} else {
					button.addClass('pressed');
					this[button.retrieve('tool')].attach();
				}

			}.bind(this));

			this.buttons.push(button);
		}, this);

		zoom.each(function(type){
			var button = container.getElement('.' + type);
			button.addEvent('click', function(){

				this.zoom(type);

			}.bind(this));
		}, this);

	},

	zoom: function(z){
		var zoom,
			realScale = this.image.retrieve('realScale'),
			fakeScale = this.image.retrieve('fakeScale'),
			index = this.scaling.indexOf(realScale),
			size = this.image.retrieve('size');

		switch (z){
			case 'zoomin':
				zoom = (this.scaling[index + 1]) ? this.scaling[index + 1] : this.scaling[index];
				break;
			case 'zoom100':
				index = this.scaling.indexOf(100);
				zoom = this.scaling[index];
				break;
			case 'zoomout':
				zoom = (this.scaling[index - 1] >= 0) ? this.scaling[index - 1] : this.scaling[index];
				break;
		};

		this.image.store('realScale', zoom);
		//this.image.store('fakeScale', () / 100)
		this.image.set('width', size.width * zoom / 100).set('height', size.height * zoom / 100);

		if (this.marquee) this.marquee.updateBox({zoom: zoom, scale: realScale, width: size.width, height: size.height, zooming: true});

		RokGallery.editPanel.updateScrollbars();
		//RokGallery.editPanel.scaleInfo.set('text', this.retrieve('realScale') + '%');
	},

	enable: function(tool){
		tool.attach();
	},

	disable: function(tool){
		tool.detach();
	},

	detachAll: function(){
		this.buttons.each(function(button){
			var tool = button.retrieve('tool');
			this.disable(this[tool]);

			button.removeClass('pressed').removeEvents('click');
		}, this);
	}

});

this.RokGallery.Edit.Toolbar.Handtool = new Class({

	Implements: [Options, Events],

	options: {
		velocity: 1,
		onStart: function(){
			document.body.addClass('dragging');
			this.element.addClass('dragging');
		},
		onChange: function(x, y){
			this.element.scrollTo(x, y);
		},
		onEnd: function(){
			document.body.removeClass('dragging');
			this.element.removeClass('dragging');
		}
	},

	initialize: function(element, options){
		this.setOptions(options);

		this.element = document.id(element) || document.getElement(element) || false;

		if (!this.element) return this;

		this.bounds = {
			element: {
				mousedown: this.start.bind(this),
				mouseup: this.end.bind(this)
			},
			document: {
				mousemove: this.drag.bind(this),
				mouseup: this.end.bind(this)
			}
		};

		return this;
	},

	attach: function(){
		this.element.addEvents(this.bounds.element);
		this.element.addClass('handtool');

		return this;
	},

	detach: function(){
		this.element.removeEvents(this.bounds.element);
		document.body.removeEvents(this.bounds.document);
		this.element.removeClass('handtool');

		return this;
	},

	start: function(event){
		event.stop();
		document.addEvents(this.bounds.document);

		if (!this.coordinates) this.coordinates = {mouse: {start: 0, now: 0}, position: {start: 0, now: 0}};

		this.coordinates.mouse.start = event.page;
		this.coordinates.position.start = this.element.getScroll();

		this.fireEvent('start');
	},

	drag: function(event){
		event.stop();
		var coords = this.coordinates;

		coords.mouse.now = event.page;
		coords.position.now = {
			x: (coords.position.start.x - (coords.mouse.now.x - coords.mouse.start.x)),
			y: (coords.position.start.y - (coords.mouse.now.y - coords.mouse.start.y))
		};

		this.scroll();
	},

	end: function(event){
		event.stop();
		document.removeEvents(this.bounds.document);

		this.fireEvent('end');
	},

	scroll: function(){
		var now = this.coordinates.position.now;

		this.fireEvent('change', [now.x, now.y]);
	}

});



this.RokGallery.Edit.Toolbar.Marquee = new Class({

	Implements: [Options, Events],

	options: {

	},

	initialize: function(element, options){
		var self = this;
		this.setOptions(options);

		this.element = document.id(element) || document.getElement(element) || false;

		if (!this.element) return this;

		this.image = this.element.getElement('img');

		var box = this.box = new Marquee.Crop(this.image, {
			ratio : false,
			preset : [0, 0, 0, 0],
			min : [1, 1],
			handleSize : 4,
			opacity : 0.1,
			color : '#fff',
			border : RokGallerySettings.images + 'crop.gif',
			onResize : function(coords){
				var size = {width: self.image.retrieve('resize').width, height: self.image.retrieve('resize').height},
					scale = self.image.retrieve('fakeScale'),
					factor = scale / 100,
					w = Math.round(coords.w / factor),
					h = Math.round(coords.h / factor),
					x = Math.round(coords.x / factor),
					y = Math.round(coords.y / factor);

				x = (x < 0) ? 0 : x;
				y = (y < 0) ? 0 : y;

				RokGallery.editPanel.inputs.marquee[0].set('value', x);
				RokGallery.editPanel.inputs.marquee[1].set('value', y);
				RokGallery.editPanel.inputs.marquee[2].set('value', w);
				RokGallery.editPanel.inputs.marquee[3].set('value', h);
			}
		});

		this.scroller = new Scroller(this.element, {
		   onChange: function(x, y){
		 		box.getContainCoords();
		 		box.getRelativeOffset();
				this.element.scrollTo(x, y);
				RokGallery.editPanel.updateScrollbars();
		   }
		});

		this.box.scroller = this.scroller;

		this.detach();

		return this;
	},

	attach: function(){
		if (this.box.detached){
			this.box.attach();
			this.box.img.setStyle('display', 'none');
			this.box.container.setStyle('display', 'block');
		}
	},

	detach: function(){
		if (!this.box.detached){
			this.box.detach();
			this.box.img.setStyle('display', '');
			this.box.container.setStyle('display', 'none');
		}
	},

	updateBox: function(values){
		// zoom = actual zoom, scale = previous zoom
		var zooming = values.zooming || false;

		values.oSize = {width: this.image.retrieve('size').width, height: this.image.retrieve('size').height};
		this.box.refreshClip(values.width * values.zoom / 100, values.height * values.zoom / 100, zooming);

		var factorx = Math.round(this.box.coords.box.x[0] / (values.scale / 100)),
			factory = Math.round(this.box.coords.box.y[0] / (values.scale / 100)),
			factorw = Math.round(this.box.coords.w / (values.scale / 100)),
			factorh = Math.round(this.box.coords.h / (values.scale / 100));

		var w = factorw * values.zoom / 100,
			h = factorh * values.zoom / 100,
			x = factorx * values.zoom / 100,
			y = factory * values.zoom / 100;

		this.box.coords.box.x[0] = x;
		this.box.coords.box.y[0] = y;
		this.box.coords.box.x[1] = x + w;
		this.box.coords.box.y[1] = y + h;

		var currentWidth = this.image.retrieve('resize').width,
			displayWidth = values.width * values.zoom / 100,
			fakeScale = (displayWidth / currentWidth) * 100;

		this.image.store('fakeScale', fakeScale);
		RokGallery.editPanel.scaleInfo.set('text', Math.round(fakeScale) + '%');
		this.box.refresh(zooming);

		//RokGallery.editPanel.inputs.image[0].set('value', values.width);
		//RokGallery.editPanel.inputs.image[1].set('value', values.height);
	}
});

})());
