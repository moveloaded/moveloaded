((function(){

this.MainPage = new Class({

	Implements: [Options, Events],

	options:{
		url: '',
		pageData: {
			page: 3,
			items_per_page: 6,
			filters: []
		}/*,
		onStart: 			function(){},

		onBeforeGetPage: 	function(){},
		onGetPage: 			function(response, id){},

		onKeyDown: 			function(){},
		onKeyUp: 			function(){},

		onComplete: 		function(){},
		onError: 			function(message){}*/
	},

	initialize: function(element, options){
		this.setOptions(options);

		this.pageData = this.options.pageData;
		this.currentPage = {page: 1, items_per_page: 12, filters: []};
		this.bounds = {
			'click': this.load.bind(this)
		};

		this.boundsDoc = {
			'keydown:keys(shift)': this.docKeyDown.bind(this),
			'keyup:keys(shift)': this.docKeyUp.bind(this)
		};

		this.element = document.id(element) || document.getElement(element) || null;
		this.request = new Request({url: this.options.url, onSuccess: this.success.bind(this)});

		['getPage'].each(function(action){
			this.actions(action);
		}, this);

		this.attach();

		if (!RokGallerySettings.more_pages){
			this.detach();
			this.hideElement();
		}

	},

	showElement: function(){
		this.element.setStyle('display', 'block');
	},

	hideElement: function(){
		this.element.setStyle('display', 'none');
	},

	docKeyDown: function(e){
		this.fireEvent('keyDown', [e, this.element]);
	},

	docKeyUp: function(e){
		this.fireEvent('keyUp', [e, this.element]);
	},

	attach: function(){
		if (this.attached) return;

		document.addEvents(this.boundsDoc);
		this.element.addEvents(this.bounds).removeClass('disabled');
		this.attached = true;
	},

	detach: function(){
		if (!this.attached) return;

		document.removeEvents(this.boundsDoc);
		this.element.removeEvents(this.bounds).addClass('disabled');
		this.attached = false;
	},

	load: function(e){
		if (this.request.isRunning()) return;

		this.attach();
		this.docKeyUp();
		this.showElement();
		this.element.addClass('loader');

		if (e && e.shift) this.pageData.get_remaining = true;
		else this.pageData.get_remaining = false;

		this.setPageData(this.pageData);
		this.getPage(this.pageData);
	},

	refresh: function(items_per_page){
		this.attach();
		this.showElement();
		this.element.fade('in');
		document.getElements('#gallery-list .gallery-row').dispose();

		if (items_per_page && items_per_page + RokGallerySettings.total_items <= RokGallerySettings.items_per_page){
			this.currentPage.items_per_page = items_per_page + RokGallerySettings.total_items;
		}

		this.setPageData(this.currentPage);

		this.request.cancel();
		this.load();
	},

	setPageData: function(properties){
		this.pageData = properties;
		this.pageData.filters = RokGallery.filters.filters;
		this.pageData.order = RokGallery.filters.order;
	},

	success: function(response){
		if (!JSON.validate(response)) return this.error('<p class="error-intro">The response from the server had an invalid JSON string while trying to load more pages. Following is the reply.</p>' + response);

		response = JSON.decode(response);

		if (response.status != 'success') return this.error(response.message);

		this.fireEvent(this.action, response);

		return this;
	},

	start: function(){
		this.fireEvent('start', this.id);
	},

	complete: function(){
		this.fireEvent('complete', this.id);
	},

	error: function(message){
		this.fireEvent('error', message);
	},

	actions: function(action){
		this[action] = function(properties){
			this.start();

			this.fireEvent('before' + action.capitalize());
			this.action = action;

			var data = {};
			data[RokGallery.ajaxVars.model] = 'mainpage';
			data[RokGallery.ajaxVars.action] = action;

			data.params = JSON.encode(properties);

			this.request.send({data: data});

			return this;
		};
	}.protect(),

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
