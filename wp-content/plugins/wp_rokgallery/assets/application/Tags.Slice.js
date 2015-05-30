/**
 * @version   2.31 March 4, 2015
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

((function(){

this.Tags.Slice = new Class({
	
	Extends: Tags,
	
	options: {
		classes: ['dark'],
		url: '',
		data: {
			id: 0
		},
		
		'onEmptyList': function(){
			this.container.getElement('.oops').setStyle('display', 'block');	
		},
		'onNonEmptyList': function(){
			this.container.getElement('.oops').setStyle('display', 'none');	
		},
		
		onInsert: function(tags, values){
			if (this.list.length && !this.container.getElements('.tag').length){
				this.container.getElement('.oops').setStyle('display', 'none');
			}

			tags = new Elements(tags.length ? tags : [tags]);
			tags.set('tween', {duration: 'short'});
			tags.inject(this.container).setStyle('opacity', 0).fade('in');

			this.scrollbar.update().toBottom();
			
			return this;
		},
		onErase: function(tags, values){
			tags = new Elements(tags.length ? tags : [tags]);
			tags.set('tween', {duration: 'short', onComplete: this.disposeTags.bind(this, tags)});

			tags.retrieve('tween').each(function(fx){
				fx.start('opacity', 0);
			});

			return this;
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
		if (this.wrapper.getElement('.gutter')) {
			tagsList.inject(tagsList.getParent(), 'before');
			tagsList.getNext().empty().dispose();
		}
		this.scrollbar = new Scrollbar(tagsList, {fixed: true});		
	},
	
	check: function(tag){
		var values = this.getValues();
		
		return values.contains(tag) || RokGallery.tags.getValues().contains(tag);
	},
	
	disposeTags: function(tags){
		tags.dispose();
		
		if (!this.list.length && !this.container.getElements('.tag').length){
			this.container.getElement('.oops').setStyle('display', 'block');
		}
				
		this.scrollbar.update();
	}
	
});

})());