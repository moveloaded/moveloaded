((function(){

Element.Properties.placement = {
    set : function(options){
        this.store('placement', this.getCoordinates());
    },

    get : function(){
        return this.retrieve('placement');
    }
};

var Rubberband = this.Rubberband = new Class({
	Implements : [Options, Events],

	options : {
		draggable: false,
		drag: {},
		triggers: [],
		elements: [],
		select: function(element){
			element.addClass('selected');
			element.store('rubberbanded', true);
		},
		deselect: function(element){
			element.removeClass('selected');
			element.store('rubberbanded', false);
		},
		itemClick : function(element, e){
			if (!e.shift && !e.meta && !e.control) return;

			if (e.shift){
				if (document.selection && document.selection.empty){
					document.selection.empty();
				} else if (window.getSelection) {
					sel = window.getSelection();
					if (sel && sel.removeAllRanges) sel.removeAllRanges();
				}
			}

			if (element.hasClass('selected')){
				(this.selected || RokGallery.rubberband.selected)['erase'](element);
				(this.options || RokGallery.rubberband.options)['deselect'](element);
			} else {
				(this.selected || RokGallery.rubberband.selected)['include'](element);
				(this.options || RokGallery.rubberband.options)['select'](element);
			}
		}
	},

	initialize: function(options){

		this.setOptions(options);

		this.triggers = null;
		this.ignores = null;
		this.selected = [];
		this.index = [];
		this.box = null;

		this.triggers = typeOf(this.options.triggers) == 'string' ? document.getElements(this.options.triggers) : this.options.triggers;
		this.elements = typeOf(this.options.elements) == 'string' ? document.getElements(this.options.elements) : this.options.elements;
		this.ignores = typeOf(this.options.ignores) == 'string' ? document.getElements(this.options.ignores) : this.options.ignores;

		this.elements.each(this.add.bind(this));

		this.box = new Element('div', {
			'styles' : {'position' : 'absolute', 'border' : '1px dotted #999', 'display' : 'none', 'z-index': 100}
		}).inject(document.body);

		this.overlay = new Element('div',{
			'styles' : {'opacity' : 0.4, 'height' : '100%', 'width' : '100%', 'background-color' : '#5EA8F6'}
		}).inject(this.box);

		this.bounds = {
			trigger: {
				mousedown: this.start.bind(this)
			},
			document: {
				mousemove: this.move.bind(this),
				mouseup: this.end.bind(this)
			}
		};

		this.options.trigger = (!this.options.trigger) ? document.body : document.id(this.options.trigger);

		this.resetCoords();

		return this;
	},

	attach: function(){
		if (RokGallery.editPanel.isOpen || RokGallery.blocks.editFileSettings.isOpen) return this;

		//document.body.onselectstart = function(e){ e = new Event(e).stop(); return false; };
		this.options.trigger.addEvents(this.bounds.trigger);
		document.body.addEvents(this.bounds.document);

		this.detached = false;

		return this;
	},

	detach: function(){
		this.end();
		//document.body.onselectstart = function(){};
		this.options.trigger.removeEvents(this.bounds.trigger);
		document.body.removeEvents(this.bounds.document);

		this.detached = true;

		return this;
	},

	add: function(el,obj){
		el.set('placement');
		var click = el.retrieve('itemClick', function(e){
			this.options.itemClick(el, e);
		}.bind(this));
		el.addEvent('click', click);
		this.index.push(el);
	},

	remove: function(el){
		el.eliminate('placement');
		var click = el.retrieve('itemClick');
		el.removeClick('click', click);
		this.index.erase(el);
		this.elements.erase(el);
		this.selected.erase(el);

		this.refresh();
	},

	refresh: function(){
		this.index.each(function(el){
			el.set('placement');
		});
	},

	start: function(event){
		var go = true;
		document.body.onselectstart = function(e){ e = new Event(e).stop(); return false; };

		this.ignores.each(function(ignore){
			if (ignore == event.target || ignore.getElement(event.target)) go = false;
		}, this);

		if (event.meta || event.shift || event.control || !go) return this;
		this.bActive = true;
		this.setStartCoords(event.page);
		this.selected.empty();

		this.fireEvent('start', [this.selected]);

		return this;
	},

	end: function(event){
		if(!this.bActive) return false;
		this.bActive = false;
		document.body.onselectstart = function(){};

		if (this.coords.w < 5 && this.coords.h < 5) this.index.each(this.checkNodes, this);
		if (this.coords.move.x || this.coords.move.y) this.fireEvent('end', [this.selected]);

		this.resetCoords();

		return true;
	},

	move: function(event){
		if (this.bActive && this.box.style.display == ''){
			this.setMoveCoords(event.page);

			this.index.each(this.checkNodes, this);

			var sel;
			if (document.selection && document.selection.empty){
				document.selection.empty();
			} else if (window.getSelection) {
				sel = window.getSelection();
				if (sel && sel.removeAllRanges) sel.removeAllRanges();
			}

		}
	},

	selectAll: function(){
		this.selected.empty();
		this.index.each(function(el){
			el.removeClass('selected');
			el.fireEvent('click', [{shift: true}]);
		}, this);
	},

	setStartCoords: function(coords){
		this.coords.start = coords;
		this.coords.w = 0;
		this.coords.h = 0;

		this.box.setStyles({
			'display': '', 'top': this.coords.start.y, 'left': this.coords.start.x, 'z-index': -100
		});

		this.box.zindez = 0;
	},

	setMoveCoords: function(coords){
		if (!this.box.zindez){
			this.box.setStyle('z-index', 100);
			this.box.zindez = 1;
		}
		this.coords.move = coords;
		this.coords.w = Math.abs(this.coords.start.x - this.coords.move.x);
		this.coords.h = Math.abs(this.coords.start.y - this.coords.move.y);
		this.coords.top = this.coords.start.y > this.coords.move.y ? this.coords.move.y : this.coords.start.y;
		this.coords.left = this.coords.start.x > this.coords.move.x ? this.coords.move.x : this.coords.start.x;
		this.coords.end = { 'x': (this.coords.left + this.coords.w), 'y': (this.coords.top + this.coords.h)};

		this.box.style.width = this.coords.w + 'px';
		this.box.style.height = this.coords.h + 'px';
		this.box.style.top = this.coords.top + 'px';
		this.box.style.left = this.coords.left + 'px';
	},

	resetCoords: function(){
		this.coords = { start: {x: 0, y: 0}, move: {x: 0, y: 0}, end: {x: 0, y: 0}, w: 0, h: 0 };
		this.box.setStyles({'display': 'none', 'top': 0, 'left': 0, 'width': 0, 'height': 0});
	},

	checkNodes: function(el){
		var box = this.coords;
		var elb = el.get('placement');
		if (Math.min(box.end.x, elb.right) > Math.max(box.left, elb.left) && Math.max(box.top, elb.top) < Math.min(box.end.y, elb.bottom)){
			this.options.select(el);
			this.selected.include(el);
		} else {
			this.options.deselect(el);
			this.selected.erase(el);
		}
	}

});
})());
