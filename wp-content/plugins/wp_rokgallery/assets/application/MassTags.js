
((function(){

	this.MassTags = new Class({

		Implements: [Options, Events],

		options: {
			url: ''/*,
			onBeforeGetTagPopup: 	function(){},
			onGetTagPopup: 			function(){},
			onBeforeAddTags: 		function(){},
			onAddTags: 				function(){},
			onBeforeRemoveTags: 	function(){},
			onRemoveTags: 			function(){},

			onDone: 				function(){},
			onError: 				function(){}
			*/
		},

		initialize: function(options){
			this.setOptions(options);

			this.request = new Request({url: this.options.url, onSuccess: this.success.bind(this)});

			['addTags', 'removeTags', 'getTagPopup'].each(function(action){
				this.actions(false, action);
			}, this);
		},

		success: function(response){
			if (!JSON.validate(response)) return this.error('Invalid JSON response. ', response);

			response = JSON.decode(response);

			if (response.status != 'success') return this.error(response.message);

			this.fireEvent(this.action, [response, this.id]);

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

			this[action] = function(properties){
				this.start();

				this.fireEvent('before' + action.capitalize());
				this.action = action;

				var data = {};
				data[RokGallery.ajaxVars.model] = 'files';
				data[RokGallery.ajaxVars.action] = action;

				if (properties){
					data.params = JSON.encode(properties);
				} else {
					data.params = JSON.encode({});
				}

				this.request.send({data: data});

				return this;
			};
		}.protect()
	});

	this.MassTagsManager = new Class({

		Extends: MassTags,

		options: {
			url: '',
			onGetTagPopup: function(response){
				window.Popup.statusBar.getElement('.loading').setStyle('display', 'none');
				window.Popup.content.set('html', response.payload.html);

				window.Popup.content.getElement('.selected-files span').set('text', RokGallery.blocks.multiSelection.elements.length);

				this.galleries = {
					container: window.Popup.content.getElement('.galleries-list'),
					list: window.Popup.content.getElements('.galleries-dropdown li'),
					title: window.Popup.content.getElements('.galleries-list .title')
				};

				this.radio = window.Popup.content.getElements('input[name=mass-tags-action]');
				this.input = window.Popup.content.getElement('#mass-tags-list');
				this.ids = new Elements(RokGallery.blocks.multiSelection.elements).retrieve('file-id');

				this.build();
			},
			onBeforeAddTags: function(){
				window.Popup.statusBar.getElement('.loading').setStyle('display', 'block');
			},
			onAddTags: function(response){
				window.Popup.statusBar.getElement('.loading').setStyle('display', 'none');
				RokGallery.loadMore.refresh();
				window.Popup.statusBar.getElement('.button.cancel').fireEvent('click');
			},
			onBeforeRemoveTags: function(){
				window.Popup.statusBar.getElement('.loading').setStyle('display', 'block');
			},
			onRemoveTags: function(response){
				window.Popup.statusBar.getElement('.loading').setStyle('display', 'none');
				RokGallery.loadMore.refresh();
				window.Popup.statusBar.getElement('.button.cancel').fireEvent('click');
			},
			onError: function(message, action){
				window.Popup.statusBar.getElement('.loading').setStyle('display', 'none');
				window.Popup.setPopup({'type': 'warning'});

				if (action == 'getTagPopup'){
					window.Popup.statusBar.getElement('.button.ok').setStyle('display', 'none');
					window.Popup.content.set('html', message);
				} else {
					window.Popup.statusBar.getElement('.tags-info').set('html', message);
				}
			}
		},

		initialize: function(options){
			this.parent(options);

			this.getTagPopup();
		},

		build: function(){
			this.galleries.list.removeEvents('click').each(function(gallery){
				gallery.addEvent('click', function(){
					this.galleries.title.set('text', gallery.get('text'));
					var tags = gallery.get('data-tags') || '';

					this.galleries.container.addClass('hidden');

					this.input.set('value', tags);

					document.addEvent('mousemove:once', function(){
						this.galleries.container.removeClass('hidden');
					}.bind(this));

				}.bind(this));
			}, this);
		},

		getRadioAction: function(){
			var action = null;

			this.radio.each(function(radio){
				if (radio.get('checked')) action = radio.get('value');
			}, this);

			return action;
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

})());
