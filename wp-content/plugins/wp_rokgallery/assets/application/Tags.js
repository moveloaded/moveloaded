/**
 * @version   2.31 March 4, 2015
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

((function(){

DOMEvent.definePseudo('stop', function(split, fn, args){
	var value = split.value ? split.value.replace(/\s/g, '').split(',') : false;
	var event = args[0];

	if (!split.event.test(/^key.+/) || (value && value.contains(event.key))) event.stop();

	fn.apply(this, args);
});

this.Tags = new Class({

	Implements: [Events, Options],

	options: {
		/*onInit: function(){},
		onInsert: function(tags, values){},
		onErase: function(tags, values){},
		onInvalid: function(values){},
		onActivate: function(tag){},
		onDeactivate: function(tag){},
		onFocus: function(tag){},
		onBlur: function(tag){},*/

		input: '.tags-add .add-input',
		container: '.tags-list',
		list: '.tags-list .tag',
		classes: [],
		layout: '<div class="tag-value"><span class="tag-name">{value}</span></div><div class="tag-erase"><span>x</span></div>',
		chars: (/^[\w\s-]+$/),

		onInsert: function(tags, values){
			tags.inject(this.container);
		},

		onErase: function(tags, values){
			if (tags.length) tags.each(this.list.erase.bind(this.list));
			else this.list.erase(tags);

			tags.dispose();
		}
	},

	initialize: function(element, options){
		this.setOptions(options);
		element = this.wrapper = document.getElement(element);

		if (!element) return this;

		this.container = element.getElement(this.options.container);
		this.list = element.getElements(this.options.list);
		this.input = element.getElement(this.options.input).removeEvents();

		this.bounds = {
			'click': this.toggle.bind(this),
			'mouseenter': this.actions.mouseenter.bind(this),
			'focus': this.actions.focus.bind(this),
			'blur': this.actions.blur.bind(this),
			'click:relay(.tag-erase)': this.actions.deleteClick.bind(this)
		};

		var keypress = Browser.Engine.trident ? 'keyup:stop' : Browser.Engine.webkit ? 'keydown:stop' : 'keypress:stop';

		this.input.addEvent(keypress + '(enter)', function(e){
			if (e.key == 'enter') this.addNew();
		}.bind(this));


		this.list.forEach(function(tag){
			if (!tag.getElement('.tag-erase')) return;

			tag.set('tabindex', 0);
			this.attachEvents(tag);
		}.bind(this));

		this.container.addEvent(keypress + '(backspace, delete, enter, space)', function(e){
			if (e.key == 'backspace' || e.key == 'delete') this.eraseActives();
			else if (e.key == 'enter' || e.key == 'space') this.toggle(e.target);
		}.bind(this));

		this.fireEvent('init');

		return this;

	},

	attachEvents: function(tag){
		for (var action in this.bounds){
			tag.addEvent(action, this.bounds[action].pass(tag));
		}
	},

	detachEvents: function(tags){
		if (typeOf(tags) == 'element') tags = new Elements([tags]);

		tags.each(function(tag){
			for (var action in this.bounds){
				tag.removeEvent(action, this.bounds[action].pass(tag));
			}
		}, this);
	},

	actions: {
		'mouseenter': function(tag){
			return this.actions.blur(tag);
		},

		'focus': function(tag){
			tag.addClass('focus');
			if (instanceOf(this, Tags)) this.fireEvent('focus', tag);
		},

		'blur': function(tag){
			tag.removeClass('focus');
			if (instanceOf(this, Tags)) this.fireEvent('blur', tag);
		},

		deleteClick: function(tag){
			this[tag.hasClass('active') ? 'deactivate' : 'activate'](tag);
			this.erase(tag);
		}
	},

	addNew: function(){
		var values = this.input.value.split(','),
			valids = [], invalids = [];
		values.forEach(function(value){
			value = value.replace(/\s{1,},{1,}\s{1,},/g, '').clean().toLowerCase();
			if (!this.check(value) && value.length && (this.options.chars).test(value)){
				this.input.get('tween').cancel();
				//this.insert(value);
				valids.push(value);
			}
			else {
				// already exists || preg_match failed
				//this.invalidate(value);
				invalids.push(value);
			}
		}, this);

		if (valids.length) this[valids.length > 1 ? 'insertMany' : 'insert'](valids);
		if (invalids.length) this.invalidate(invalids);
	},

	insert: function(value, noFire){
		var tag = new Element('div[class="tag"][tabindex=0]');
		this.options.classes.each(tag.addClass.bind(tag));

		tag.set('html', this.options.layout.replace('{value}', value));

		this.attachEvents(tag);

		this.list.push(tag);
		this.input.value = '';

		return (!noFire) ? this.fireEvent('insert', [tag, value]) : tag;
	},

	insertMany: function(values, noFire){
		var tags = [];
		values.each(function(value){
			tags.push(this.insert(value, true));
		}, this);

		return (!noFire) ? this.fireEvent('insert', [tags, values]) : values;
	},

	invalidate: function(values){
		return this.fireEvent('invalid', values);
	},

	erase: function(tag){
		this.detachEvents(tag);
		this.fireEvent('erase', [tag, this.getValue(tag)]);

		this.list.erase(tag);

		this.fireEvent('afterErase', [tag, this.getValue(tag)]);

		return this;
	},

	eraseMany: function(tags){
		this.detachEvents(tags);
		this.fireEvent('erase', [tags, this.getValues(tags)]);

		tags.each(this.list.erase.bind(this.list));

		this.fireEvent('afterErase', [tags, this.getValues(tags)]);

		return this;
	},

	eraseActives: function(){
		var actives = this.getActives();

		this[actives.length > 1 ? 'eraseMany' : 'erase'](actives);
	},

	toggle: function(tag){
		return this[tag.hasClass('active') ? 'deactivate' : 'activate'](tag);
	},

	activate: function(tag){
		tag.addClass('active');
		tag.focus();

		this.fireEvent('activate', tag);
	},

	deactivate: function(tag){
		tag.removeClass('active');

		this.fireEvent('deactivate', tag);
	},

	check: function(tag){
		var values = this.getValues();

		return values.contains(tag);
	},

	getValue: function(tag){
		return this.list.indexOf(tag) != -1 ? tag.getElement('.tag-name').get('text') : false;
	},

	getValues: function(tags){
		return ((tags && tags.length) ? tags : this.list).getElement('.tag-name').get('text');
	},

	getActives: function(){
		return this.list.filter(function(tag){
			return tag.hasClass('active') || tag.hasClass('focus');
		});
	}

});

})());

