
((function(){

	this.GalleryPicker = new Class({

		Implements: [Options, Events],

		options: {
			url: ''
		},

		initialize: function(el, options){
			this.setOptions(options);

			this.element = document.id(el) || document.getElement(el) || null;
			this.type = '';
			this.activeTab = 'filelist';

			this.inputs = {
				file_id: this.element.getElement('input[type=hidden][name=file_id]'),
				gallery_id: this.element.getElement('input[type=hidden][name=gallery_id]')
			};
			this.back = this.element.getElement('.back-button').setStyle('display', 'none');
			this.instructions = this.element.getElement('.instructions').setStyle('display', 'none');

			this.ajax = new Request.HTML({url: RokGallerySettings.modal_url, evalScripts: false, onRequest: this.clickRequest.bind(this), onSuccess: this.clickSuccess.bind(this)});

			this.tabs = this.element.getElements('#gallerypicker-tabs li');
			this.panels = new Elements(this.tabs.get('data-panel').map(function(panel, i){
				return this.element.getElement('.panel.' + panel);
			}, this));

			this.loader = new GalleryPickerMainPage(document.id('load-more'), {
				url: RokGallerySettings.url,
				pageData: {
					page: RokGallerySettings.next_page,
					items_per_page: RokGallerySettings.items_per_page,
					filters: [],
					composite: {
						context: 'rokgallery.gallerypicker',
						layout: 'default_file'
					}
				}
			});

			this.loader.addEvent('getPage', this.refreshLinks.bind(this));
			this.loader.element.addEvent('click', function(){
				this.type = 'files';
			}.bind(this));

			this.files = this.element.getElements('li.file');
			this.galleries = this.element.getElements('li.gallery');
			this.slices = this.element.getElements('li.slice');
			this.menuitems = this.element.getElements('a.menu-item');

			this.attachLists();
			this.attachTabs();
			this.attachBack();
		},

		attachLists: function(){
			this.files.each(this.attachFile.bind(this));
			this.galleries.each(this.attachGallery.bind(this));
			this.slices.each(this.attachSlice.bind(this));
			this.menuitems.each(this.attachMenuItem.bind(this));
		},

		attachFile: function(file){
			var id;

			file.addEvent('click', function(){
				if (file.hasClass('total-slices-0')) return false;
				this.type = 'files';

				id = file.get('data-id').replace('file-', '');
				this.inputs.file_id.set('value', id);
				this.ajax.send({data: {'file_id': id}});
			}.bind(this));
		},

		attachGallery: function(gallery){
			var id;

			gallery.addEvent('click', function(){
				if (gallery.hasClass('total-slices-0')) return false;
				this.type = 'gallery';
				id = gallery.get('data-id').replace('gallery-', '');
				this.inputs.gallery_id.set('value', id);
				this.ajax.send({data: {'gallery_id': id}});
			}.bind(this));
		},

		attachSlice: function(slice){
			var anchor = slice.getElement('.jinsert_action'),
				selects = slice.getElements('select'),
				string = '';
			slice.addEvent('click', function(){
				this.type = 'slice';
			}.bind(this));

			anchor.addEvent('click', function(){
				if (selects.length) string = this.buildString(selects);
				else string = anchor.get('data-display') || '';

				var size = {width: anchor.get('data-width'), height: anchor.get('data-height')},
					minithumb = anchor.get('data-minithumb');
                //    squeezbox is Joomla Native, Wordpress ThickBox will require calling the tb_remove() function.
                if (typeof window.parent.SqueezeBox != 'undefined'){
                    if (RokGallerySettings.textarea){
                        window.parent.jInsertEditorText(string, RokGallerySettings.textarea);
                        window.parent.SqueezeBox.close();
                        //window.parent.document.getElementById('sbox-window')
                    } else if (RokGallerySettings.inputfield){
                        if (typeof window.parent.GalleryPickerInsertText != 'undefined') window.parent.GalleryPickerInsertText(RokGallerySettings.inputfield, string, size, minithumb);
                        else window.parent.document.getElementById(RokGallerySettings.inputfield).value = string;
                        window.parent.SqueezeBox.close();
                    }
                } else if (typeof window.parent.GalleryPickerInsertText != 'undefined') {
					window.parent.GalleryPickerInsertText(window.parent.currentIMGPickerID || RokGallerySettings.inputfield || null, string, size, minithumb);
					if (typeof window.parent.SqueezeBox != 'undefined') window.parent.SqueezeBox.close();
					if (typeof window.parent.tb_remove != 'undefined') window.parent.tb_remove();
				} else if (window.tinyMCE) {
                    string = '<img src="'+ string + '">';
                    window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, string);
//                    tinyMCEPopup.editor.execCommand('mceRepaint');
                    window.parent.tb_remove();
                } else {
                    string = '<img src="'+ string + '">';
                    var wpActiveEditor = 'content';
                    window.parent.QTags.insertContent(string);
                    window.parent.tb_remove();
                }

			}.bind(this));
		},

		attachMenuItem: function(menuitem){
			var string = '',
				data = {
					open: menuitem.get('data-opentag') || '',
					close: menuitem.get('data-closetag') || '',
					display: menuitem.get('data-display') || ''
				};

			menuitem.addEvent('click', function(e){
				e.stop();

				string = this.buildMenuItemString(data);
                //    squeezbox is Joomla Native, Wordpress ThickBox will require calling the tb_remove() function.
                if (typeof window.parent.SqueezeBox != 'undefined'){
                    if (RokGallerySettings.textarea){
                        window.parent.jInsertEditorText(string, RokGallerySettings.textarea);
                        window.parent.SqueezeBox.close();
                        //window.parent.document.getElementById('sbox-window')
                    } else if (RokGallerySettings.inputfield){
                        if (typeof window.parent.GalleryPickerInsertText != 'undefined') window.parent.GalleryPickerInsertText(RokGallerySettings.inputfield, string);
                        else window.parent.document.getElementById(RokGallerySettings.inputfield).value = string;
                        window.parent.SqueezeBox.close();
                    }
                } else if (window.tinyMCE) {
                    window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, string);
//                    tinyMCEPopup.editor.execCommand('mceRepaint');
                    window.parent.tb_remove();
                } else {
                    var wpActiveEditor = 'content';
                    window.parent.QTags.insertContent(string);
                    window.parent.tb_remove();
                }

			}.bind(this));
		},

		buildMenuItemString: function(data){
			var string = data.open + data.display + data.close;

			return string.replace(/\'/g, '"');
		},

		buildString: function(selects){
			var string = '', close = '';

			selects.each(function(select){

				var data = {
					open: select.getSelected().get('data-opentag') || '',
					close: select.getSelected().get('data-closetag') || '',
					display: select.getSelected().get('data-display') || ''
				};

				if (!close.length && data.close.length) close = data.close;
				string += data.open + data.display;
			});

			string += close;

			return string.replace(/\'/g, '"');

		},

		attachTabs: function(){
			this.tabs.each(function(tab, i){

				tab.addEvent('click', function(){

					var panel = this.panels[i];
					this.activeTab = tab.get('data-panel');
					this.type = tab.get('data-type');

					this.tabs.removeClass('active');
					this.panels.setStyle('display', 'none');

					tab.addClass('active');
					panel.setStyle('display', 'block');

					if (panel.getElement('.slice')) $$(this.back, this.instructions).setStyle('display', 'block');
					else $$(this.back, this.instructions).setStyle('display', 'none');

				}.bind(this));

			}, this);
		},

		attachBack: function(){
			this.back.addEvent('click', function(){
				var active = this.activeTab.replace('list', '_id'),
					data = {};

				this.inputs[active].set('value', 0);
				data[active] = 0;
				data['page'] = this.loader.currentPage.page;

				this.type = this.getActiveTab()

				$$(this.back, this.instructions).setStyle('display', 'none');

				this.ajax.send({data: data});
			}.bind(this));
		},

		clickRequest: function(){
			var type = (this.type == 'slice') ? this.getActiveTab()  : this.type,
				list = 'gallerypicker-' + type + 'list',
				container = document.id(list);

			if (this.type == 'files') this.loader.element.setStyle('display', 'none');
			container.empty().getParent('.panel').addClass('loader');
		},

		clickSuccess: function(nodes, tree, html){
			var type = (this.type == 'slice') ? this.getActiveTab() : this.type,
				dummy = new Element('div', {styles: {'display': 'none'}}),
				list = 'gallerypicker-' + type + 'list',
				container = document.id(list),
				children;

			container.getParent('.panel').removeClass('loader');
			dummy.inject(document.body, 'top').set('html', html);

			children = dummy.getElement('#' + list).getChildren();
			container.adopt(children);

			dummy.dispose();

			if (container.getElement('.slice')){
				$$(this.back, this.instructions).setStyle('display', 'block');
				this.type = 'slice';
			} else {
				this.type = this.getActiveTab();
				$$(this.back, this.instructions).setStyle('display', 'none');
			}

			this.refreshLinks();

			if (this.type == 'files'){
				if (container.getElement('.slice')) this.loader.element.setStyle('display', 'none');
				else {
					var status = this.loader.element.retrieve('display');
					this.loader[status == 'none' ? 'hideElement' : 'showElement']();
				}
			}

		},

		getActiveTab: function(){
			return document.id('gallerypicker-tabs').getElement('.active').get('data-type');
		},

		refreshLinks: function(){
			var type = (this.type == 'slice') ? this.getActiveTab() : this.type,
				list = 'gallerypicker-' + type + 'list',
				container = document.id(list);

			if (!container) return;

			var children = container.getChildren();
			switch(this.type){
				case 'slice':
					children.removeEvents('click').each(this.attachSlice.bind(this));
					break;
				case 'gallery':
					children.removeEvents('click').each(this.attachGallery.bind(this));
					break;
				case 'files': default:
					children.removeEvents('click').each(this.attachFile.bind(this));
			};
		}

	});

	this.GalleryPickerMainPage = new Class({
		Extends: MainPage,
		options: {
			onKeyDown: function(e, element){
				element.addClass('load-all').getElement('span.text').set('html', 'load all');
			},
			onKeyUp: function(e, element){
				element.removeClass('load-all').getElement('span.text').set('html', 'load more');
			},
			onGetPage: function(response){
				this.element.removeClass('loader');

				var payload = response.payload;

				if (!payload.more_pages){
					this.detach();
					this.element.fade('out').get('tween').chain(function(){
						this.hideElement();
					}.bind(this));
				}

				var page = (payload.next_page) ? payload.next_page - 1 : this.currentPage.page + 1;
				this.currentPage = {
					page: page,
					items_per_page: RokGallerySettings.items_per_page
				};

				this.setPageData({
					page: payload.next_page,
					items_per_page: RokGallerySettings.items_per_page,
					filters: [],
					composite: {
						context: 'rokgallery.gallerypicker',
						layout: 'default_file'
					}
				});

				RokGallerySettings.total_items = payload.total_items;

				var html = response.payload.html,
					dummy = new Element('ul').set('html', html),
					items = dummy.getChildren();

				document.id('gallerypicker-fileslist').adopt(items);
				dummy.dispose();


				return this;

			},
			onError: function(message){
				this.element.removeClass('loader');
				document.getElement('.panel.filelist').empty().set('html', '<div class="error"><h2>An error occurred: </h2>' + message + '</div>');
			}
		},
		initialize: function(element, options){
			this.parent(element, options);
			this.currentPage = {page: 1, items_per_page: RokGallerySettings.items_per_page, filters: []};
		},
		showElement: function(){
			this.element.store('display', 'block');
			this.parent();
		},
		hideElement: function(){
			this.element.store('display', 'none');
			this.parent();
		},
		setPageData: function(properties){
			this.pageData = properties;
		}
	});

})());
