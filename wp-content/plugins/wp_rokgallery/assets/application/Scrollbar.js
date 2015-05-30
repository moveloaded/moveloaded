((function(){

this.Scrollbar = new Class({

	Implements: [Options, Events],

	options: {
		chunk: 20,
		fixed: false,
		gutter: true,
		injection: 'after',
		direction: 'vertical',
		wrap: true,
		effects: {
			duration: 250,
			transition: 'quad:in:out',
			link: 'cancel'
		},
		wrapStyles: {},
		resizeWrapper: true,
		onShow: function(trigger, scrollbar, gutter){
			gutter.fade(0.7);
		},
		onHide: function(trigger, scrollbar, gutter){
			gutter.fade(0);
		}
	},

	initialize: function(element, options){

		element = this.element = document.id(element);
		if (!element) return;

		this.setOptions(options);

		this.bounds = {
			trigger: {
				mouseenter: this.show.bind(this),
				mouseleave: this.hide.bind(this)
			},
			element: {
				mousewheel: this.mousewheel.bind(this)
			},
			drag: {
				mousedown: this.start.bind(this),
				mouseup: this.end.bind(this)
			},
			document: {
				mousemove: this.drag.bind(this),
				mouseup: this.end.bind(this)
			}
		};

		// in case of jakub's stupidity
		var position = element.getStyle('position') || 'relative';
		if (!position.test(/absolute|relative|fixed/i)){
			element.setStyle('position', 'relative');
		}

		if (this.options.direction == 'vertical') this.property = {dir: 'top', axis: 'y', size: 'height'};
		else this.property = {dir: 'left', axis: 'x', size: 'width'};

		if (this.options.wrap){
			var wrap = new Element('div', {styles: {position: 'relative'}}).wraps(element);
			this.wrapper = wrap;
			this.wrapper.store('custom', true);
		} else {
			this.wrapper = element.getParent();
		}

		this.wheelDir = {'horizontal': 1, 'vertical': 2};

		if (element.getStyle('width')) this.wrapper.setStyle('width', element.getStyle('width'));
		this.wrapper.setStyles(this.options.wrapStyles);

		this.triggerElement = document.getElement(this.options.triggerElement) || this.wrapper;
		this.scrollbar = new Element('div.scrollbar');

		if (this.options.gutter){
			this.gutter = new Element('div.gutter.' + this.options.direction).inject(element, 'after').setStyle('opacity', 0).setStyle(this.property.size,  this.element.getSize()[this.property.axis]);
			this.scrollbar.inject(this.gutter).setStyle(this.property.dir, 0);
			this.gutter.setStyle(this.property.dir, this.gutter.getStyle(this.property.dir));
			this.gutter.set('tween', {duration: 200});
			this['stored' + this.property.dir.capitalize()] = 0;
		} else {
			this.scrollbar.inject(this.element, 'after');
			this['stored' + this.property.dir.capitalize()] = this.scrollbar.getStyle(this.property.dir).toInt() || 0;
			this.scrollbar.setStyle('opacity', 0).setStyle(this.property.dir, this['stored' + this.property.dir.capitalize()]);
		}

		if (this.options.fixed){
			delete this.bounds.trigger;
			this.show();
		}

		this.attach();
		this.update();
	},

	attach: function(){
		this.triggerElement.addEvents(this.bounds.trigger);
		this.element.addEvents(this.bounds.element);
		(this.options.gutter ? this.gutter : this.scrollbar).addEvents(this.bounds.element);
		this.scrollbar.addEvents(this.bounds.drag);

		this.scrollbar.store('detached', false);

		return this;
	},

	detach: function(){
		this.triggerElement.removeEvents(this.bounds.trigger);
		this.element.removeEvents(this.bounds.element);
		(this.options.gutter ? this.gutter : this.scrollbar).removeEvents(this.bounds.element);
		this.scrollbar.removeEvents(this.bounds.drag);

		document.removeEvents(this.bounds.document);

		this.scrollbar.store('detached', true);

		return this;
	},

	update: function(){
		var detached = this.scrollbar.retrieve('detached');
		var dimensions = this.dimensions = {
			size: this.element.getSize()[this.property.axis],
			scrollSize: this.element.getScrollSize()[this.property.axis]
		};

		if (this.options.gutter) this.gutter.setStyle(this.property.size, dimensions.size);
		if (this.wrapper.retrieve('custom') && this.options.resizeWrapper) this.wrapper.setStyle('width', this.element.getStyle('width'));
		if (dimensions.scrollSize <= dimensions.size) return (!detached) ? this.hide().detach() : this.hide();

		if (detached){
			this.attach();
			if (this.options.fixed) this.show();
		}

		var ratio = this.ratio = {
			content: dimensions.size / dimensions.scrollSize,
			scroll: dimensions.scrollSize / (dimensions.size - this['stored' + this.property.dir.capitalize()])
		};

		var scrollbarSize = this.scrollbarSize = (dimensions.size * ratio.content).limit(10, dimensions.size);

		this.scrollbar.setStyle(this.property.size, scrollbarSize);
		this.updatePosition();

		return this;
	},

	updatePosition: function(){
		if (!this.ratio) return;

		var scroll = (this.element['scroll' + this.property.dir.capitalize()] / this.ratio.scroll).limit(this['stored' + this.property.dir.capitalize()] || 0, (this.dimensions.size - this.scrollbarSize));
        this.scrollbar.setStyle(this.property.dir, scroll);
	},

	show: function(){
		return this.fireEvent('show', [this.triggerElement, this.scrollbar, this.gutter || this.scrollbar]);
	},

	hide: function(){
		return this.fireEvent('hide', [this.triggerElement, this.scrollbar, this.gutter || this.scrollbar]);
	},

	mousewheel: function(evt){
		evt.stop();

		var canScroll = evt.event.axis == this.wheelDir[this.options.direction], delta;
		if (Browser.Engine.webkit){
			delta = evt.event['wheelDelta' + this.property.axis.capitalize()];
			canScroll = delta;
		}

		if (Browser.ie || Browser.opera){
			delta = evt.event['wheelDelta'];
			canScroll = this.options.direction == 'vertical' ? delta : false;
		}

		if (canScroll) this.element['scroll' + this.property.dir.capitalize()] -= evt.wheel * this.options.chunk;
		this.updatePosition();
	},

	start: function(evt){
		evt.stop();
		if (!this.coordinates) this.coordinates = {mouse: {start: 0, now: 0}, position: {start: 0, now: 0}};

		this.coordinates.mouse.start = evt.page[this.property.axis];
		this.coordinates.position.start = this.scrollbar.getStyle(this.property.dir).toInt();

		this.triggerElement.removeEvents(this.bounds.trigger);
		document.addEvents(this.bounds.document);

	},

	drag: function(evt){
		evt.stop();
		var coords = this.coordinates;

		coords.mouse.now = evt.page[this.property.axis];
		coords.position.now = (coords.position.start + (coords.mouse.now - coords.mouse.start)).limit(0, (this.dimensions.size + this.scrollbarSize));

		this.updatePosition();
		this.element['scroll' + this.property.dir.capitalize()] = coords.position.now * this.ratio.scroll;
	},

	end: function(evt){
		evt.stop();
		document.removeEvents(this.bounds.document);
		this.triggerElement.addEvents(this.bounds.trigger);

		if (!this.triggerElement.contains(evt.target) && !this.options.fixed) this.hide();
	},

	scroll: function(){},

	toBottom: function(){
		this.element['scroll' + this.property.dir.capitalize()] = this.element['scroll' + this.property.size.capitalize()];

		return this.updatePosition();
	}

});

})());
