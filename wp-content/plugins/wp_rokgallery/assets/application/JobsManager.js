
((function(){

	this.JobsManager = new Class({

		Extends: Job,

		options: {/*
			onBeforeGet: 		function(){},
			onGet: 				function(response, id){},
			onBeforeClean: 		function(){},
			onClean: 			function(response, id){},
		*/},

		initialize: function(element, options){
			this.setOptions(options);

			this.element = document.id(element) || document.getElement(element) || null;

			if (!this.element) return;

			this.parent();
			['get', 'clean', 'wipe'].each(function(action){
				this.jobs(action);
			}, this);

			this.container = window.Popup.popup;

			this.bounds = {
				click: this.open.bind(this)
			};

			this.attach();

		},

		attach: function(){
			this.element.addEvents(this.bounds);
		},

		detach: function(){
			this.element.removeEvents(this.bounds);
		},

		open: function(){
			var jobsInfo = window.Popup.popup.getElement('.jobs-info');
			this.jobsInfo = jobsInfo || new Element('div.jobs-info').inject(window.Popup.popup.getElement('.statusbar .clr'), 'before');

			this.popup({
				type: '',
				title: 'Jobs Manager',
				message: '<div class="jobs-loading">Retrieving the Jobs list...</div>'
			});

			this.statusBar = window.Popup.statusBar;
			this.statusBar.getElement('.loading').setStyle('display', 'block');

			this.get();

			this.isOpen = true;
		},

		close: function(){
			this.statusBar.getElement('.loading').setStyle('display', 'none');

			this.isOpen = false;
		},

		popup: function(options){
			var defaults = {
				type: 'warning',
				title: 'Jobs Manager - Error',
				message: '',
				buttons: {
					ok: {show: false},
					cancel: {show: true, label: 'close'}
				}
			};

			window.Popup.setPopup(Object.merge(defaults, options)).open();
		},

		jobs: function(action){
			this[action] = function(){
				this.fireEvent('before' + action.capitalize());
				this.action = action;

				var data = {};
				data[RokGallery.ajaxVars.model] = 'jobs';
				data[RokGallery.ajaxVars.action] = action;
				//if (action != 'create') data.params = JSON.encode({id: this.id});

				this.request.send({data: data});

				return this;
			};
		}.protect()

	});

})());
