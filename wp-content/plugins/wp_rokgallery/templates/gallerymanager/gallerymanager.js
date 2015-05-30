
((function(){

	this.Galleries = new Class({

		Implements: [Options, Events],

		options: {
			url: ''/*,
			onBeforeCreate: 	function(){},
			onCreate: 			function(){},
			onBeforeUpdate: 	function(){},
			onUpdate: 			function(){},
			onBeforeDelete: 	function(){},
			onDelete: 			function(){},

			onBeforeGet: 		function(){},
			onGet: 				function(){},

			onDone: 			function(){},
			onError: 			function(){}
			*/
		},

		initialize: function(options){
			this.setOptions(options);

			this.request = new Request({url: this.options.url, onSuccess: this.success.bind(this)});

			['get', 'create', 'update', 'order', 'publish', 'delete'].each(function(action){
				this.actions(false, action);
			}, this);

			['get'].each(function(action){
				this.actions(true, action)
			}, this);
		},

		success: function(response){
			if (!JSON.validate(response)) return this.error('Invalid JSON response.');

			response = JSON.decode(response);

			if (response.status != 'success') return this.error(response.message);

			if (this.action == 'create') this.id = response.payload.job;

			this.fireEvent(this.action, [response, this.id]);

			if (this.queue){
				this[this.queue].delay(10, this);
				this.queue = null;

				return this;
			}

			this.done(response);

			return this;
		},

		start: function(){
			this.fireEvent('start', this.id);
		},

		done: function(response){
			this.fireEvent('done', [this.id, response]);
		},

		error: function(message){
			this.fireEvent('error', [message, this.action]);
		},

		actions: function(plural, action){
			var actionMethod = (!plural && action == 'get') ? 'getSingle' : action;

			this[actionMethod] = function(properties, order){
				this.start();

				this.fireEvent('before' + actionMethod.capitalize());
				this.action = actionMethod;

				var data = {};
				data[RokGallery.ajaxVars.model] = (!plural) ? 'gallery' : 'galleries';
				data[RokGallery.ajaxVars.action] = action;

				if (properties && action != 'delete' && action != 'order'){
					if (this.id){
						if (!order) data.params = JSON.encode({id: this.id.toInt(), gallery: properties});
						else data.params = JSON.encode({id: this.id.toInt(), gallery: properties, order: order});
					}
					else data.params = JSON.encode({gallery: properties});
				} else if (action == 'order') {
					data.params = JSON.encode({id: this.id.toInt(), order: properties});
				} else {
					if (action == 'delete'){
						if (this.id) data.params = JSON.encode({id: this.id.toInt(), delete_slices: properties});
					} else {
						if (this.id) data.params = JSON.encode({id: this.id.toInt()});
						else data.params = JSON.encode({});
					}
				}

				this.request.send({data: data});

				return this;
			};
		}.protect()
	});

	this.GalleryOrder = new Class({
		Extends: Galleries,

		options: {
			url: '',
			element: '.galleries-mini-thumbs',
			onBeforeOrder: function(){
				this.statusBar.getElement('.loading').setStyle('display', 'block');
			},
			onOrder: function(){
				this.statusBar.getElement('.loading').setStyle('display', 'none');
				this.hide();
			},
			onBeforeGetSingle: function(){
				this.element.getElement('.mini-thumbs-loading').setStyle('display', 'block');
				this.statusBar.getElement('.loading').setStyle('display', 'block');
				this.scrollbar.update();
			},
			onGetSingle: function(response){
				this.element.getElement('.mini-thumbs-loading').setStyle('display', 'none');
				this.statusBar.getElement('.loading').setStyle('display', 'none');

				if (!response.payload.gallery.Slices.length){
					this.applyButton.setStyle('display', 'none');
					this.element.getElement('.mini-thumbs-list').addClass('empty');
				} else {
					this.applyButton.setStyle('display', 'block');
					this.element.getElement('.mini-thumbs-list').set('html', response.payload.html);
					this.sortable = new GallerySort(this.element.getElement('.mini-thumbs-list ul'), {
						constrain: true,
					    clone: function(event, element, list){
					    	var clone = element.clone(true).setStyles({
								margin: 0,
								position: 'absolute',
								visibility: 'hidden',
								width: element.getStyle('width')
							}).addEvent('mousedown', function(event){
								element.fireEvent('mousedown', event);
							});
							return clone.inject(this.list).setPosition(element.getPosition(element.getOffsetParent()));
						},
					    revert: true,
						opacity: 0.55
					});
				}

				this.scrollbar.update();
			},
			onError: function(message, action){
				this.statusBar.getElement('.loading').setStyle('display', 'none');
				window.Popup.setPopup({'type': 'warning'});

				if (action == 'get'){
					window.Popup.content.getElement('.galleries-loading').set('html', message);
				} else {
					this.galleriesInfo.set('text', message);
				}
			}
		},

		initialize: function(options){
			this.setOptions(options);
			this.parent(options);

			this.element = document.id(this.options.element) || document.getElement(this.options.element) || null;
			this.element.set('tween', {duration: 400, transition: 'expo:out', unit: '%', onStart: this.tweenStart.bind(this), onComplete: this.tweenComplete.bind(this)});

			var galleriesInfo = window.Popup.popup.getElement('.galleries-info');
			this.galleriesInfo = galleriesInfo || new Element('div.galleries-info').inject(window.Popup.popup.getElement('.statusbar .clr'), 'before');
			this.statusBar = window.Popup.statusBar;

			this.closeButton = this.element.getElement('.button.cancel');
			this.applyButton = this.element.getElement('.button.ok');

			this.id = null;
			this.isOpen = false;
			this.bounds = {
				close: this.hide.bind(this),
				apply: this.apply.bind(this)
			};

			this.scrollbar = new Scrollbar(this.element.getElement('.mini-thumbs-list'), {
				gutter: true,
				fixed: true,
				wrapStyles: {
					'width': '100%',
					'height': '100%'
				}
			});

			this.attach();

			return this;
		},

		tweenStart: function(element){
			var fx = element.retrieve('tween'),
				now = fx.from[0].value;

			if (this.isOpen) this.element.getParent('.galleries-content-container').removeClass('overflow-visible').addClass('overflow-hidden');
		},

		tweenComplete: function(element){
			var fx = element.retrieve('tween'),
				now = fx.to[0].value;

			if (now == 0) this.isOpen = true;
			else this.isOpen = false;

			if (this.isOpen) this.element.getParent('.galleries-content-container').removeClass('overflow-hidden').addClass('overflow-visible');
		},

		attach: function(){
			this.closeButton.addEvent('click', this.bounds.close);
			this.applyButton.addEvent('click', this.bounds.apply);

			return this;
		},

		detach: function(){
			this.closeButton.removeEvent('click', this.bounds.close);
			this.applyButton.removeEvent('click', this.bounds.apply);

			return this;
		},

		getOrder: function(){
			return this.sortable.serialize(function(el){
				return el.get('data-id');
			});
		},

		setID: function(id){
			this.id = id;

			return this;
		},

		load: function(){
			this.cleanList();
			this.getSingle();

			return this;
		},

		apply: function(){
			this.order(this.getOrder());

			return this;
		},

		show: function(){
			this.element.tween('right', 0);

			return this;
		},

		hide: function(){
			this.element.tween('right', -100);

			return this;
		},

		cleanList: function(){
			this.element.getElement('.mini-thumbs-list').removeClass('empty').empty();
		}

	});

	this.GalleriesManager = new Class({

		Extends: Galleries,

		options: {
			required: ['name'],
			onGet: function(response){
				this.autodelete = response.payload.delete_slices.toInt() || false;
				window.Popup.popup.getElement('.button.ok').setStyle('display', 'block');
				this.statusBar.getElement('.loading').setStyle('display', 'none');
				//window.Popup.content.set('html', response.payload.html);
				this.content = window.Popup.content;
				this.defaultValue = this.content.getElement('#gallery-name').get('value');
				this.galleries = response.payload.galleries;
				this.manualOrder = new GalleryOrder({url: this.options.url});

				this.manualButton = this.content.getElement('.manual-order-gallery');
				this.manualWrapper = this.content.getElement('.manual-order-wrapper');

				var fixedvalue = this.content.getElement('#fixed-gallery').get('value');
				this.fixedGallery = (fixedvalue == 'false' || fixedvalue == false || fixedvalue == '0' || fixedvalue == 0) ? false : true;

				//this.deleteButton = this.content.getElement('.delete-gallery');
				this.publishButton = this.content.getElement('.publish-gallery');
				//this.deleteSlices = this.content.getElement('#gallery-delete_slices');
				this.basedOn = this.content.getElement('.button.base-on-gallery').removeEvents().setStyle('display', 'none');

				this.publishButton.removeEvents().setStyle('display', 'none');
				this.manualButton.removeEvents();
				this.manualWrapper.setStyle('display', 'none');
				//this.deleteSlices.set('checked', this.autodelete);

				this.manualButton.addEvent('click', this.manualAction.bind(this));
				this.publishButton.addEvent('click', this.publishAction.bind(this));
				//this.deleteButton.addEvent('click', this.deleteAction.bind(this));
				this.basedOn.addEvent('click', this.basedOnAction.bind(this));

				//window.Popup.reposition();

				this.build();
			},
			onBeforeCreate: function(){
				this.statusBar.getElement('.loading').setStyle('display', 'block');
			},
			onCreate: function(response){
				this.statusBar.getElement('.loading').setStyle('display', 'none');
				var payload = response.payload.gallery;

				var li = new Element('li[data-id='+payload.id+']').set('html', '<span>'+payload.name+'</span>');
				li.store('data', payload);
				li.inject(this.content.getElement('.galleries-list ul'));

				this.galleriesList.push(li);
				this.attachGalleries(li);

				//var editPanel = document.getElement('#file-edit .file-gallery-list ul');
				//if (editPanel){
				//	var eP = new Element('li[data-key='+payload.id+']').set('html', '<span>'+payload.name+'</span>');
				//	eP.inject(editPanel);
				//	RokGallery.editPanel.setGalleries();
				//}
				//
				//var fG = new Element('li[data-key='+payload.id+']').set('html', '<span>'+payload.name+'</span>');
				//var ul = document.getElement('.filters-gallery-list ul'),
				//	list = document.getElement('.filter-gallery.filters-list'),
				//	title = list.getElement('> .title'),
				//	dropdown = list.getElement('.filters-dropdown');
				//
				//fG.inject(ul);
				//fG.addEvent('click', RokGallery.filters.itemClick.bind(RokGallery.filters, fG));
				//fG.store('list', {list: list, title: title, dropdown: dropdown, items: ul.getChildren()});
				//fG.store('ignores', []);
				//fG.store('ignore-list', []);
				//fG.store('show_input', false);

				//RokGallery.blocks.editFileSettings.beforeClose();

				window.Popup.setPopup({type: 'success'});
				window.Popup.select(payload.id, payload.name, 'gallery_id');
			},
			onBeforeUpdate: function(){
				this.statusBar.getElement('.loading').setStyle('display', 'block');
			},
			onUpdate: function(response){
				this.statusBar.getElement('.loading').setStyle('display', 'none');
				var payload = response.payload.gallery;

				var li = this.content.getElement('li[data-id='+payload.id+']'),
					title = this.content.getElement('.galleries-list .title');

				li.set('html', '<span>'+payload.name+'</span>');
				title.set('text', payload.name);

				li.store('data', payload);

				//if (RokGallery.blocks.editFileSettings.isOpen) RokGallery.blocks.editFileSettings.beforeClose();

				window.Popup.setPopup({type: 'success'});
				window.Popup.select(payload.id, payload.name, 'gallery_id');
			},
			onPublish: function(response){
				window.Popup.setPopup({type: 'success'});
				(function(){window.Popup.setPopup({type: ''});}).delay(2000);
			},
			onDelete: function(response){
				var id = response.payload.id,
					li = this.content.getElement('li[data-id='+id+']');

				this.galleriesList.erase(li);
				li.dispose();

				this.galleriesList[0].fireEvent('click');

				var editPanel = document.getElement('#file-edit .file-gallery-list ul');
				if (editPanel){
					var eP = editPanel.getElement('li[data-key='+id+']');
					if (eP) eP.dispose();
					RokGallery.editPanel.setGalleries();
				}

				var fG = document.getElement('.filters-gallery-list ul li[data-key='+id+']');
				if (fG){
					fG.dispose();
				}

				window.Popup.setPopup({type: 'success'});
				(function(){window.Popup.setPopup({type: ''});}).delay(2000);
			},
			onError: function(message, action){
				this.statusBar.getElement('.loading').setStyle('display', 'none');
				window.Popup.setPopup({'type': 'warning'});

				if (action == 'get'){
					window.Popup.content.set('html', '<div class="error"><p>' + message + '</p></div>');
				} else {
					this.galleriesInfo.set('text', message);
				}
			}
		},

		initialize: function(element, options){
			this.parent(options);

			this.element = document.id(element) || document.getElement(element) || null;

			if (!this.element) this.open();
			else {
				this.element.addEvent('click', function(e){
					e.stop();
					this.open();
				}.bind(this));
			}

			this.properties = {};
			this.id = null;
		},

		build: function(){
			this.inputs = this.content.getElements('input');
			this.galleriesList = this.content.getElements('.galleries-dropdown li');

			this.saveButton = this.statusBar.getElement('.button.ok');
			this.saveButton.addEvent('click', this.saveAction.bind(this));

			this.storeGalleries();
			this.attachGalleries();

			this.updateProps();
			this.AspectAndForce();

			var load = this.content.getElement('#load-gallery');
			if (load){
				var id = load.get('value');
				var gallery = this.galleriesList.filter(function(li){
					return li.get('data-id') == id;
				});

				if (gallery.length) gallery[0].fireEvent('click');
			}
		},

		storeGalleries: function(){
			this.galleries.each(function(obj){
				var list = this.content.getElement('li[data-id='+obj.id+']');

				if (list) list.store('data', obj);
			}, this);
		},

		attachGalleries: function(single){
			var galleries;

			galleries = (!single) ? this.galleriesList : single;
			galleries = Array.from(galleries);

			galleries.forEach(function(gallery) {
				gallery.removeEvents('click');
				gallery.addEvent('click', function(){
					var data = gallery.retrieve('data');
					this.id = gallery.get('data-id');
					this.manualOrder.setID(this.id);

					this.content.getElement('.galleries-list').addClass('hidden');
					this.content.getElement('.galleries-list .title').set('text', (data) ? data.name : gallery.get('text'));

					document.addEvent('mousemove:once', function(){
						this.content.getElement('.galleries-list').removeClass('hidden');
					}.bind(this));

					if (!data && !this.id){
						this.manualWrapper.setStyle('display', 'none');
						this.publishButton.setStyle('display', 'none');
						//this.deleteButton.setStyle('display', 'none');
						this.basedOn.setStyle('display', 'none');
						this.id = null;
						this.manualOrder.hide();

						this.resetInputs();

						return;
					}

					this.manualWrapper.setStyle('display', 'block');
					this.publishButton.setStyle('display', 'block');
					//this.deleteButton.setStyle('display', 'block');
					this.basedOn.setStyle('display', 'block');


					this.manualOrder.load();

					this.updateInputs(gallery.retrieve('data'));

				}.bind(this));
			}, this);

		},

		manualAction: function(){
			if (!this.id) return;

			this.manualOrder.show();
		},

		publishAction: function(){
			if (!this.id) return;

			this.publish();
		},

		deleteAction: function(){
			if (!this.id) return;

			var deleteSlices = this.inputs.filter(function(input){
				return input.get('id') == 'gallery-delete_slices';
			}, this);

			if (deleteSlices.length) deleteSlices = deleteSlices[0].get('checked');
			else deleteSlices = false;

			this['delete'](deleteSlices);
		},

		basedOnAction: function(){
			var props = this.content.getElement('li[data-id='+this.id+']').retrieve('data');

			this.galleriesList[0].fireEvent('click');
			this.updateInputs(props);
			this.content.getElement('#gallery-name').set('value', '');
			this.id = null;
			this.manualOrder.setID(this.id);
		},

		updateProps: function(){
			this.inputs.each(function(input){
				if (input.get('type') == 'hidden') return;
				var id = input.get('id').replace(/^gallery\-/, '');

				this.properties[id] = {
					value: (input.get('type') == 'checkbox') ? input.get('checked') : input.get('value'),
					input: input
				};
			}, this);
		},

		AspectAndForce: function(){
			var keep = this.content.getElement('#gallery-keep_aspect'),
				force = this.content.getElement('#gallery-force_image_size');

			if (this.fixedGallery){
				keep.set('disabled', 'disabled').set('checked', '');
				force.set('disabled', 'disabled').set('checked', 'checked');

				return;
			}

			keep.removeEvents('click').addEvent('click', function(){
				if (keep.get('checked')) force.set('checked', '').set('disabled', 'disabled');
				else force.set('disabled', '');
			});

			force.removeEvents('click').addEvent('click', function(){
				if (force.get('checked')) keep.set('checked', '').set('disabled', 'disabled');
				else keep.set('disabled', '');
			});
		},

		checkAspectAndForce: function(){
			var keep = this.content.getElement('#gallery-keep_aspect'),
				force = this.content.getElement('#gallery-force_image_size');

			if (this.fixedGallery){
				keep.set('disabled', 'disabled').set('checked', '');
				force.set('disabled', 'disabled').set('checked', 'checked');

				return;
			}

			if (keep.get('checked')) force.set('checked', '').set('disabled', 'disabled');
			if (force.get('checked')) keep.set('checked', '').set('disabled', 'disabled');

			if (!force.get('checked') && !keep.get('checked')){
				force.set('disabled', '');
				keep.set('disabled', '');
			}
		},

		updateInputs: function(props){
			this.resetInputs();

			Object.forEach(props, function(value, key){
				var input = this.content.getElement('#gallery-' + key);

				if (input){
					if (input.get('type') == 'hidden') return;
					if (input.get('type') == 'checkbox') input.set('checked', value);
					else {
						if (typeOf(value) == 'array'){
							value = (value.length) ? value.join(', ') : '';
						}
						input.set('value', value);
					}
				}

			}, this);

			this.checkAspectAndForce();

			//this.deleteSlices.set('checked', this.autodelete);
		},

		resetInputs: function(){
			this.inputs.each(function(input){
				if (input.get('type') == 'hidden') return;
				if (input.get('type') == 'checkbox') input.set('checked', (input.get('id') == 'gallery-auto_publish') ? true : false);
				else input.set('value', (input.get('id') == 'gallery-name') ? this.defaultValue : '');
			}, this);

			this.checkAspectAndForce();
		},

		getProps: function(){
			var props = {};

			Object.forEach(this.properties, function(obj, key){
				var value = obj.value;

				if (key == 'filetags'){
					value = value.clean().replace(/,\s/, ',');
					value = value.length ? value.split(',') : [];
				}

				props[key] = value;

			}, this);

			return props;
		},

		getOrder: function(){
			if (!this.manualOrder.sortable) return [];
			return this.manualOrder.sortable.serialize(function(el){
				return el.get('data-id');
			});
		},

		open: function(){
			var galleriesInfo = window.Popup.popup.getElement('.galleries-info');
			this.galleriesInfo = galleriesInfo || new Element('div.galleries-info').inject(window.Popup.popup.getElement('.statusbar .clr'), 'before');

			this.popup({
				type: '',
				title: 'Galleries Manager',
				message: '<div class="galleries-loading">Retrieving list of Galleries...</div>'
			});

			this.statusBar = window.Popup.statusBar;
			this.statusBar.getElement('.loading').setStyle('display', 'block');

			window.Popup.popup.getElement('.button.ok').setStyle('display', 'none');

			this.get();

			this.isOpen = true;
		},

		close: function(){
			this.statusBar.getElement('.loading').setStyle('display', 'none');
			window.Popup.popup.getElement('.button.ok').setStyle('display', 'none');

			this.isOpen = false;
		},

		saveAction: function(){
			this.galleriesInfo.set('text', '');
			window.Popup.setPopup({'type': ''});

			this.statusBar.getElement('.loading').setStyle('display', 'block');

			this.updateProps();

			var canSave = this.checkRequired();

			if (canSave){
				if (this.id) this.update(this.getProps(), this.getOrder());
				else this.create(this.getProps());
			}
		},

		checkRequired: function(){
			this.statusBar.getElement('.loading').setStyle('display', 'none');
			var valid = true;

			this.options.required.forEach(function(required){
				if (!this.properties[required].value){
					valid = false;

					var input = this.properties[required].input,
						label = input.getPrevious('label'),
						red = '#ed2e26';

					label.set('tween', {duration: 350, link: 'chain', transition: 'quad:out'});
					input.set('tween', {duration: 350, link: 'chain', transition: 'quad:out'});

					label.tween('color', red)
						 .tween('color', '#888')
						 .tween('color', red)
						 .tween('color', '#888');

					input.tween('border-color', red)
						 .tween('border-color', '#ddd')
						 .tween('border-color', red)
						 .tween('border-color', '#ddd');
				}
			}, this);

			return valid;
		},

		popup: function(options){
			var defaults = {
				type: 'warning',
				title: 'Galleries Manager - Error',
				message: '',
				buttons: {
					ok: {show: true, label: 'save'},
					cancel: {show: true, label: 'close'}
				}
			};

			window.Popup.setPopup(Object.merge(defaults, options)).open();
		}

	});

	this.GallerySort = new Class({
		Extends: Sortables,
		options: {
			onStart: function(element, clone){
				if (!this.placeholder){
					this.placeholder = new Element('li', {'class': 'mini-thumb-placeholder', styles: {opacity: 0, 'width': 0}});
					this.placeholderOut = new Element('li', {'class': 'mini-thumb-placeholder out', styles: {opacity: 0, 'width': 0}});

					$$(this.placeholder, this.placeholderOut).store('size', {x: 70, y: 70}).set('tween', {duration: 200, transition: 'expo:in:out', link: 'cancel'});
					this.placeholder.setStyle('width', this.placeholder.retrieve('size').x);
				}

				this.placeholder.inject(element, 'before');
				clone.setStyles({'z-index': 2, opacity: 0.55});//.inject(element.getParent('.mini-thumbs-list'), 'before');
				element.setStyle('display', 'none').inject(document.body);
			}
		},
		insert: function(dragging, element){
			var where = 'inside';
			if (this.lists.contains(element)){
				this.list = element;
				this.drag.droppables = this.getDroppables();
			} else {
				where = this.placeholder.getAllPrevious('[class!=mini-thumb-placeholder]').contains(element) ? 'before' : 'after';
			}

			var location = this.placeholder.retrieve('location');
			if (!location || location.element != element || location.where != where){
				this.placeholder.store('location', {element: element, where: where});
				this.placeholderOut.setStyle('width', this.placeholder.retrieve('size').x).inject(this.placeholder, where).tween('width', 0);
				this.placeholder.setStyle('width', 0).inject(element, where).tween('width', this.placeholder.retrieve('size').x).retrieve('tween').chain(function(){

				}.bind(this));
			}

			this.fireEvent('sort', [this.element, this.clone]);
		},
		end: function(){
			var element = this.element;
			this.drag.detach();
			this.element.setStyle('display', 'inline-block').set('opacity', this.opacity);
			if (this.placeholder){
				this.element.setStyle('opacity', 0).inject(this.placeholder, 'before');
				this.placeholder.dispose();
				this.placeholderOut.dispose();
			}

			if (this.effect){
				var dim = this.element.getStyles('width', 'height'),
					clone = this.clone,
					pos = clone.computePosition(this.element.getPosition(this.clone.getOffsetParent()));

				var destroy = function(){
					this.removeEvent('cancel', destroy);
					clone.destroy();
					element.setStyle('opacity', 1);
				};

				this.effect.element = clone;
				this.effect.start({
					top: pos.top,
					left: pos.left,
					width: dim.width,
					height: dim.height,
					opacity: 0.55
				}).addEvent('cancel', destroy).chain(destroy);
			} else {
				this.clone.destroy();
			}
			this.reset();
		}
	});

	this.Drag.Move.implement({
		checkDroppables: function(){
			var overed = this.droppables.filter(function(el, i){
				el = this.positions ? this.positions[i] : this.getDroppableCoordinates(el);
				var now = this.mouse.now;
				return (
					now.x > (el.left + (el.width / 2)) &&
					now.x < (el.right + (el.width / 2)) &&
					now.y < (el.bottom + (el.height / 2)) &&
					now.y > (el.top + (el.height / 2))
				);
			}, this).getLast();

			if (this.overed != overed){
				if (this.overed) this.fireEvent('leave', [this.element, this.overed]);
				if (overed) this.fireEvent('enter', [this.element, overed]);
				this.overed = overed;
			}
		}
	});

//    squeezbox is Joomla Native, Wordpress ThickBox will require calling the tb_remove() function.

	window.addEvent('domready', function(){
		window.Popup = {
			popup: document.id('popup'),
			content: document.getElement('.content', true),
			statusBar: document.getElement('.statusbar', true),
			setPopup: function(){ return window.Popup; },
			open: function(){ return window.Popup; },
			select: function(id, title, object) {
                if (typeof window.parent.SqueezeBox != 'undefined'){
				    window.parent.document.getElementById('jform_params_' + object + '_id').value = id;
				    window.parent.document.getElementById('jform_params_' + object + '_name').value = title;
                }
                else{
                    window.parent.document.getElement('.rg_' + object + '_id').value = id;
                    window.parent.document.getElement('.rg_' + object + '_name').value = title;
                }
				window.Popup.close();
			},
			close: function(){
                if (typeof window.parent.SqueezeBox != 'undefined'){
				    window.parent.SqueezeBox.close();
                }
                else{
                    window.parent.tb_remove()
                }
			}
		};

		document.id(document.body).getElement('.statusbar .button.cancel').addEvent('click', window.Popup.close);

		new GalleriesManager(null, {url: RokGallery.url});
	});

})());
