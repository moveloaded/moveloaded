/**
 * @version   2.31 March 4, 2015
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

((function(){

this.Tags.Ajax = new Class({

	Extends: Tags,

	options: {
		classes: ['dark'],
		url: '',
		data: {
			id: 0
		},
		onInsert: function(tags, values){
			var data = Object.clone(this.options.data.insert),
				params = {params: JSON.encode({id: this.options.data.id, tags: Array.from(values)})};

			data = {data: Object.merge(data, params)};

			this.insertBind = function(response){
				this.insertSuccess(tags, values, response);
			}.bind(this);

			this.fireEvent('beforeInsert');

			this.ajax.addEvent('onSuccess:once', this.insertBind);
			this.ajax.send(data);
		},
		onErase: function(tags, values){
			var data = Object.clone(this.options.data.erase),
				params = {params: JSON.encode({id: this.options.data.id, tags: Array.from(values)})};

			data = {data: Object.merge(data, params)};

			this.eraseBind = function(response){
				this.eraseSuccess(tags, values, response);
			}.bind(this);

			this.fireEvent('beforeErase');

			tags.addClass('loader');
			this.ajax.addEvent('onSuccess:once', this.eraseBind);
			this.ajax.send(data);
		},
		onInvalid: function(value){
			if (!value.length) return;

			var color = this.input.retrieve('color') || this.input.getStyle('color'),
				errorColor = '#eb9191';
			this.input.set('tween', {link: 'chain', duration: 150, transition: 'sine'});
			this.input.tween('color', errorColor)
					.tween('color', color)
					.tween('color', errorColor)
					.tween('color', color)
					.tween('color', errorColor)
					.tween('color', color);
		},
		onFocus: function(tag){
			this.scrollbar.update();
		}
	},

	initialize: function(element, options){
		this.parent(element, options);

		var add = this.wrapper.getElements('.add-tag');
		if (add.length) add.setStyle('tabindex', '-1').removeEvents().addEvent('click:stop', this.addNew.bind(this));

		this.input.store('color', this.input.getStyle('color'));

		var tagsList = this.wrapper.getElement('.tags-list');
		this.scrollbar = new Scrollbar(tagsList, {fixed: true});
		this.ajax = new Request({
			url: this.options.url,
			method: 'post',
			link: 'ignore'
		});

	},

	insert: function(value, noFire){
		if (this.ajax.isRunning()) return this;

		return this.parent(value, noFire);
	},

	insertMany: function(values, noFire){
		if (this.ajax.isRunning()) return this;

		return this.parent(values, noFire);
	},

	erase: function(tag){
		if (this.ajax.isRunning()) return this;

		return this.parent(tag);
	},

	eraseMany: function(tags){
		if (this.ajax.isRunning()) return this;

		return this.parent(tags);
	},

	insertSuccess: function(tags, values, response){
		this.ajax.removeEvent('onSuccess:once', this.insertBind);

		if (!JSON.validate(response)) return this.popup({
			title: 'Add Tag - Invalid Response',
			message: '<p class="error-intro">The response from the server had an invalid JSON string while adding Tags. Following is the reply.</p>' + response
		});

		response = JSON.decode(response);

		if (response.status != 'success') return this.popup({title: 'Add Tag - Error', message: response.message});

		if (this.list.length){
			this.container.getElement('.oops').setStyle('display', 'none');
		}

		tags = new Elements(tags.length ? tags : [tags]);
		tags.set('tween', {duration: 'short'});
		tags.inject(this.container).setStyle('opacity', 0).fade('in');

		this.scrollbar.update().toBottom();

		this.fireEvent('afterInsertSuccess', [response, tags, values]);

		return this;
	},

	eraseSuccess: function(tags, values, response){
		this.ajax.removeEvent('onSuccess:once', this.eraseBind);

		if (!JSON.validate(response)) return this.popup({
			title: 'Remove Tag - Invalid Response',
			message: '<p class="error-intro">The response from the server had an invalid JSON string while removing Tags. Following is the reply.</p>' + response
		});

		response = JSON.decode(response);

		if (response.status != 'success') return this.popup({title: 'Remove Tag - Error', message: response.message});

		tags = new Elements(tags.length ? tags : [tags]);
		tags.set('tween', {duration: 'short', onComplete: this.disposeTags.bind(this, tags)});

		tags.retrieve('tween').each(function(fx){
			fx.start('opacity', 0);
		});

		this.fireEvent('afterEraseSuccess', [response, tags, values]);

		return this;
	},

	disposeTags: function(tags){
		tags.dispose();

		if (!this.list.length){
			this.container.getElement('.oops').setStyle('display', 'block');
		}

		this.scrollbar.update();
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
