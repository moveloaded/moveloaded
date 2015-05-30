
((function(){

String.implement({
	toBool: function(){
		return (/^true$/i).test(this);
	}
});

this.RokGallery.Filters = new Class({
	
	Implements: [Events, Options],
	
	options: {
		input: '.filtering #filter-query',
		lists: '.filtering .filters-list',
		apply: '.filtering .filter-submit',
		sort: '.filtering .filter-sort',
		container: '.filters-generated .filters-wrapper',
		
		defaults: ['tags', 'contains'],
		
		layout: '<span class="filter-query">{query}</span><span class="filter-types">{type}</span><span class="filter-operator">{operator}</span><span class="remove">x</span>'
	},

	initialize: function(options){
		this.setOptions(options);
		
		this.input = document.getElement(this.options.input);
		this.lists = document.getElements(this.options.lists);
		this.apply = document.getElement(this.options.apply);
		this.sort = document.getElement(this.options.sort);
		this.container = document.getElement(this.options.container);
		
		this.options.defaults = Array.combine(this.options.defaults, RokGallerySettings.order);
		
		this.selected = {};
		this.order = {};
		this.listsKeys = {};
		this.itemsKeys = {};
		this.filters = [];
		
		this.bounds = {
			apply: this.addFilter.bind(this),
			sort: this.sortFilters.bind(this)
		};
		
		this.lists.each(this.attachList.bind(this));
		this.attach();
		
		this.options.defaults.each(function(def){
			var el = this.lists.getElement('li[data-key='+def+']').clean();
			if (el && el[0]) el[0].fireEvent('click');
		}, this);
	},
	
	attach: function(){
		this.lists.each(function(list){
			this.listsKeys[list.get('data-key')] = list;
			list.removeClass('disabled');
			var items = list.getElements('li');
			items.each(function(item){
				item.addEvent('click', this.itemClick.bind(this, item));
			}, this);
		}, this);
		
		var keypress = Browser.Engine.trident ? 'keyup:stop' : Browser.Engine.webkit ? 'keydown:stop' : 'keypress:stop';
		this.input.addEvent(keypress + '(enter)', function(e){
			if (e.key == 'enter') this.addFilter();
		}.bind(this));
		this.input.removeClass('disabled').removeProperty('disabled');
		
		this.apply.addEvent('click', this.bounds.apply);
		this.apply.setStyle('display', 'block');
		
		this.sort.addEvent('click', this.bounds.sort);
		this.sort.setStyle('display', 'block');
		
		this.detached = false;
	},
	
	detach: function(){
		this.lists.each(function(list){
			list.addClass('disabled');
			var items = list.getElements('li');
			items.each(function(item){
				item.removeEvents('click');
			}, this);
		}, this);
		
		this.input.removeEvents().addClass('disabled').setProperty('disabled', 'disabled').blur();
		this.apply.removeEvent('click', this.bounds.apply);
		this.apply.setStyle('display', 'none');
		
		this.sort.removeEvent('click', this.bounds.sort);
		this.sort.setStyle('display', 'none');
		
		this.detached = true;
	},
	
	addFilter: function(){
		var filter = Object.clone(this.selected), self = this, newFilter;
		if (this.input.getStyle('display') != 'none'){
			filter = Object.merge(filter, {'query': this.input.get('value')});
		}
		
		var check = Object.some(filter, function(value, key){
			return value.length;
		});
		
		var exists = this.checkExistance(filter);
		
		if (!check || !filter.type || !filter.operator || (typeOf(filter.query) != 'null' && !filter.query.length)) return;
		if (exists) return;
		
		//if (filter.type == 'gallery' && (typeOf(filter.gallery) != 'null' && !filter.gallery.length)) return;
				
		newFilter = Object.clone(filter);
		
		var tag = new Element('div.filter-tag').setStyles({visibility: 'hidden', opacity: 0}),
			layout = this.options.layout;
		
		if (newFilter.type == 'gallery'){
			var list = this.listsKeys[newFilter.type],
				item = list.getElement('.selected');
			
			if (!item) return;
			
			newFilter.query = item.get('text');
		}
		
		this.filters.push(filter);
				
		layout = layout.substitute(newFilter);
		tag.set('html', layout);
		
		if (!newFilter['query']) tag.getElement('.filter-query').setStyle('display', 'none');
		
		tag.getElement('.remove').addEvent('click', function(){
			if (self.detached) return;
			
			tag.fade('out').get('tween').chain(function(){
				
				self.filters.erase(filter);
				tag.destroy();
				self.resetPage();
				if (!self.filters.length) self.container.getParent('.filters-generated').addClass('empty');
			});
		});
		
		if (this.filters.length) self.container.getParent('.filters-generated').removeClass('empty');
		tag.inject(this.container).fade('in');
		this.input.set('value', '');
		
		this.resetPage();
	},
	
	sortFilters: function(){
		var by = this.order['order_by'],
			dir = this.order['order_direction'];
		
		
		this.resetPage();
	},
	
	checkExistance: function(filter){
		var exists = false;
		
		this.filters.each(function(stored){
			var check = [], result;
			Object.each(stored, function(value, key){
				if (typeof filter[key] != 'undefined'){
					if (!filter[key].length && !stored[key].length) check.push(true);
					else if (filter[key] == stored[key]) check.push(true);
					else check.push(false);
				} else {
					check.push(false);
				}
			});
			
			result = true * check.every(Math.floor);
			exists = exists + result;
		});
		
		return exists;
	},
	
	resetPage: function(){
		RokGallery.loadMore.attach();
		RokGallery.loadMore.element.setStyle('display', 'block').fade('in');
		document.getElements('#gallery-list .gallery-row').dispose();
		
		RokGallery.loadMore.setPageData({
			page: 1,
			items_per_page: 12
		});
		
		RokGallery.loadMore.request.cancel();
		RokGallery.loadMore.load();
	},
	
	attachList: function(list){
		var title = list.getElement('> .title'),
			dropdown = list.getElement('.filters-dropdown'),
			items = list.getElements('li');
		
		if (!title.retrieve('original-value')) title.store('original-value', title.get('text'));
		
		list.removeClass('disabled');
		if (list.get('data-key') == 'order_by' || list.get('data-key') == 'order_direction'){
			this.order[list.get('data-key')] = '';
		} else {
			this.selected[list.get('data-key')] = '';
		}
		
		items.forEach(function(item){
			var key = item.get('data-key');
			if (!key || !key.length || this.itemsKeys[key]){
				items.erase(item);
				return;
			}
			
			var ignores = (item.get('data-ignores') || '').clean(),
				ignoreList = (item.get('data-ignore-list') || '').clean(),
				input = item.get('data-input') || 'ignore';
			
			this.itemsKeys[key] = item;
			
			item.store('list', {list: list, title: title, dropdown: dropdown, items: items});
			item.store('ignores', ignores.length ? ignores.split(',') : []);
			item.store('ignore-list', ignoreList.length ? ignoreList.split(',') : []);
			item.store('show_input', input);
			
		}, this);
	},
	
	itemClick: function(item){
		if (item.hasClass('disabled')) return;
		
		var data = item.retrieve('list'),
			dataKey = data.list.get('data-key');
		
		item.addClass('selected');
		data.title.set('text', item.get('text'));
		
		data.items.filter(function(other){
			return other != item;
		}.bind(this)).removeClass('selected');
		
		if (dataKey == 'type'){ 
			this.enableItems();
			this.enableLists();
			this.disableItems(item.retrieve('ignores'));
			this.disableLists(item.retrieve('ignore-list'));
		}
		
		if (dataKey == 'order_by' || dataKey == 'order_direction'){
			this.order[dataKey] = item.get('data-key').replace(/^order-/, '');
		} else {
			this.selected[dataKey == 'gallery' ? 'query' : dataKey] = item.get('data-key');
		}
		
		if (item.retrieve('show_input') == 'true') this.input.setStyle('display', 'block').removeProperty('disabled');
		if (item.retrieve('show_input') == 'false') this.input.setStyle('display', 'none').setProperty('disabled', 'disabled');
		
		//self.refreshLists();
	},
	
	enableItems: function(keys){
		(!keys ? Object : Array)['forEach']((!keys ? this.itemsKeys : keys), function(item, key){
			
			element = (!keys ? item : this.itemsKeys[item]);
			element.removeClass('disabled');
		
		}.bind(this));
		
		return this;
	},
	
	enableLists: function(keys){
		(!keys ? Object : Array)['forEach']((!keys ? this.listsKeys : keys), function(item, key){
			
			element = (!keys ? item : this.listsKeys[item]);
			element.removeClass('disabled').setStyle('display', 'block');
		
		}.bind(this));
		
		return this;
	},
	
	disableItems: function(keys){
		(!keys ? Object : Array)['forEach']((!keys ? this.itemsKeys : keys), function(item, key){
			
			var element = (!keys ? item : this.itemsKeys[item]);
			if (element.hasClass('selected')) this.unselectList(element.retrieve('list').list);
			element.addClass('disabled');
			
		}.bind(this));
		
		return this;
	},
	
	disableLists: function(keys){
		(!keys ? Object : Array)['forEach']((!keys ? this.listsKeys : keys), function(item, key){
			
			var element = (!keys ? item : this.listsKeys[item]);
			if (element.hasClass('selected')) this.unselectList(element.retrieve('list').list);
			element.addClass('disabled').setStyle('display', 'none');
			
		}.bind(this));
		
		return this;
	},
	
	unselectList: function(list){
		var key = list.get('data-key'),
			title = list.getElement('> .title');
		
		if (key == 'order_by' || key == 'order_direction'){
			this.order[key] = '';
		} else {
			this.selected[key] = '';
		}
		
		list.getElements('li').removeClass('selected');
		title.set('text', title.retrieve('original-value'));
		
		return this;
	},
	
	refreshLists: function(){
		var width = 0;
		
		this.lists.each(function(list){
			width += list.getSize().x - 2;
		}, this);
		
		this.lists.getElement('.filters-dropdown').setStyle('width', width);
	}

});

})());