
(function(){

	if (typeof this.RokGallery == 'undefined') this.RokGallery = {};

	Element.implement({
		sumStyles: function(){
			var styles = this.getStyles(arguments), sum = 0;

			for (var style in styles) sum += styles[style].toInt();

			return sum;
		}
	});

	Elements.implement({
		sumStyles: function(styles){
			var sum = 0;

			this.each(function(element){
				sum += element.sumStyles(styles);
			}, this);

			return sum;
		}
	});

	this.RokGallery.Slideshow = new Class({

		Implements: [Options, Events],

		options: {
			/*
				onJump:		function(index){}
			*/
			onJump: function(index){
				this.animation.index = this.current;
				this.animation.setBackground(this.slices[index].getElement('img').get('src'));
				this.animation.setAnimation(this.options.animation);
				this.animation.play();

				if (this.captions.length){
					if (this.current == index){
						this.captions[index].fade('in');
					} else {
						this.captions[this.current].fade('out');
						this.captions[index].fade('in');
					}
				}
			},

			animation: 'random',
			duration: 500,

			autoplay: {
				enabled: true,
				delay: 5000,
				complete: function(){
					this.progress.reset();
					this.next();
				}
			}
		},

		initialize: function(element, options){
			this.setOptions(options);

			this.element = document.id(element) || document.getElement(element) || null;
			this.rtl = document.id(document.body).getStyle('direction') == 'rtl';

			if (!this.element) return false;

			this.current = 0;

			this.container = this.element.getElement('.rg-ss-slice-container');
			this.slices = this.element.getElements('.rg-ss-slice');
			this.captions = this.element.getElements('.rg-ss-info');
			this.arrows = this.element.getElements('.rg-ss-controls .next, .rg-ss-controls .prev');
			this.scrollerContainer = this.element.getElement('.rg-ss-thumb-scroller');

			this.loaderBar = this.element.getElement('.rg-ss-loader');
			this.progressBar = this.element.getElement('.rg-ss-progress');

			this.slices[0].setStyle('position', 'relative');

			if (this.options.autoplay.enabled){
				this.progress = new RokGallery.Slideshow.Progress(this.progressBar, {
					duration: this.options.autoplay.delay,
					container: this.container,
					width: this.loaderBar ? this.loaderBar.getSize().x : 0,
					onComplete: this.options.autoplay.complete.bind(this)
				}).play();

				this.bounds = {
					progress: {
						'mouseenter': this.progress.pause.bind(this.progress),
						'mouseleave': this.progress.resume.bind(this.progress)
					}
				};

				this.element.addEvents(this.bounds.progress);
			} else {
				if (this.loaderBar) this.loaderBar.setStyle('display', 'none');
			}

			this.setup();

			if (this.arrows.length) this.setArrows();
			if (this.scrollerContainer) this.setNavigation();

			if (Browser.Features.Touch){
				this.container.addEvent('swipe', function(event){
					event.preventDefault();
					this[event.direction == 'right' ? 'previous' : 'next']();
				}.bind(this));
			}

			// fix for webkit and thumbnails
			/*if (((Browser.Engine && Browser.Engine.webkit) || Browser.safari) && this.scrollerContainer){
				window.addEvent('load', function(){
					this.refreshThumbs(this.current);
				}.bind(this));
			}*/
			//this.jump(0, true);
		},
		setup: function(){
			this.animation = new RokGallery.Slideshow.Animations(this.element, {
				container: this.container,
				width: this.container.getSize().x,
				height: this.container.getSize().y,
				duration: this.options.duration,
				transition: 'quad:in:out',
				onStart: function(){
					if (this.options.autoplay.enabled) this.progress.pause();
				}.bind(this),
				onComplete: function(){
					this.slices[this.current].setStyle('display', 'block');
					if (this.current != this.animation.index) this.slices[this.animation.index].setStyles({'display': 'none', 'position': 'absolute'});
					this.slices[this.current].setStyle('position', 'relative');

					this.animation.clean();

					if (this.options.autoplay.enabled) this.progress.play();
				}.bind(this)
			});

			if (this.captions.length){
				this.captions.setStyles({display: 'block', 'opacity': 0, 'visibility': 'hidden'});
				//this.captions[this.current].setStyles({display: 'block', 'opacity': 1, 'visibility': 'visible'});
				this.captions.set('tween', {duration: this.options.duration, transition: 'quad:in:out'});
			}

			this.fireEvent('setup');
		},

		setArrows: function(){
			var action;
			this.arrows.each(function(arrow){
				action = arrow.className.contains('next') ? 'next' : 'previous';
				arrow.addEvent('click', this[action].bind(this));
			}, this);
		},

		setNavigation: function(){
			this.scroller = new RokGallery.Slideshow.Thumbnails(this.element, this, {});
		},

		next: function(){
			var next = this.getNext();
			this.jump(next);

			if (this.options.autoplay.enabled && !this.progress.isPaused) this.progress.play();

			return this;
		},

		previous: function(){
			var previous = this.getPrevious();
			this.jump(previous);

			return this;
		},

		jump: function(index, force){
			if (index == this.current && !force) return this;
			if (this.animation.timer) return this;

			this.fireEvent('jump', index);

			this.refreshThumbs(index);

			this.current = index;

			return this;
		},

		refreshThumbs: function(index){
			if (this.scrollerContainer){
				this.scroller.setActive(index);
				this.scroller.toThumb(index);
			}
		},

		getNext: function(){
			return (this.current + 1 >= this.slices.length) ? 0 : this.current + 1;
		},

		getPrevious: function(){
			return (this.current - 1 < 0) ? this.slices.length - 1 : this.current - 1;
		}

	});

	this.RokGallery.Slideshow.Progress = new Class({

		Extends: Fx,

		options: {
			container: null,
			transition: 'linear',
			fps: 24,
			width: 100
		},

		initialize: function(element, options){
			this.element = document.id(element);
			this.isPaused = false;

			this.parent(options);
		},

		set: function(now){
			if (this.element) this.element.setStyle('width', now);

			return this;
		},

		reset: function(){
			this.set(0);

			return this;
		},

		play: function(){
			this.options.width = this.options.container.getSize().x;
			this.start(0, this.options.width);

			return this;
		},

		pause: function(){
			this.isPaused = true;
			return this.parent();
		},

		resume: function(){
			this.isPaused = false;
			return this.parent();
		}

	});

	this.RokGallery.Slideshow.Thumbnails = new Class({

		Implements: [Options, Events],

		options: {
			/*
			onClickElement:		function(thumb, index){}
			*/
			onClickElement: function(thumb, index){
				if (this.base.animation.timer) return this;
				this.base.jump(index);
				this.setActive(index);

				return this;
			},

			scroller: {
				duration: 300,
				transition: Fx.Transitions.Expo.easeInOut
			}
		},

		initialize: function(element, base, options){
			this.setOptions(options);

			this.base = base;
			this.element = element;
			this.thumbSize = 0;

			this.wrapper = this.element.getElement('.rg-ss-navigation-container');
			this.container = this.wrapper.getElement('.rg-ss-thumb-scroller');
			this.arrows = this.wrapper.getElements('.rg-ss-arrow-left, .rg-ss-arrow-right');
			this.list = this.wrapper.getElement('.rg-ss-thumb-list');
			this.thumbsWrapper = this.wrapper.getElements('.rg-ss-thumb-list .rg-ss-thumb');
			this.thumbs = this.wrapper.getElements('.rg-ss-thumb-list .rg-ss-thumb img');

			this.arrowsSize = this.arrows.sumStyles('width');

			this.scroller = new Fx.Scroll(this.container, this.options.scroller);
			this.scroller.set(0, 0);

			this.setup();
			this.attach();

			return this;
		},

		attach: function(){
			this.thumbsWrapper.each(function(thumb, i){

				thumb.bounds = {
					'click': function(event){
						this.clickElement(event, thumb);
					}.bind(this)
				};

				thumb.addEvents(thumb.bounds);

			}, this);

			this.arrows.each(function(arrow, i) {

				arrow.bounds = {
					'click': function(event){
						this.arrowsClick(event, arrow, (!i ? 'left' : 'right'));
					}.bind(this)
				};

				arrow.addEvent('dblclick', function(e){ e.stop(); });
				arrow.addEvents(arrow.bounds);

			}, this);
		},

		detach: function(){
			this.thumbsWrapper.each(function(thumb, i){
				thumb.removeEvents(thumb.bounds);
			}, this);

			this.arrows.removeEvents(this.bounds.arrows);
		},

		arrowsClick: function(event, arrow, direction){
			event.stop();

			var amount = this.container.scrollLeft + (direction == 'left' ? -this.thumbSize : +this.thumbSize),
				total = this.container.scrollWidth - this.container.offsetWidth,
				actual = this.container.scrollLeft + this.thumbSize;

			if (total - actual < this.thumbSize) amount += total - actual;
			if (actual - this.thumbSize * 2 < this.thumbSize && direction == 'left') amount = 0;

			this.scroller.start(amount, 0);
		},

		toThumb: function(index){
			//var styles = [];
			//['padding-%x', 'border-%x-width', 'margin-%x'].each(function(style){
			//	['left', 'right'].each(function(direction){
			//		styles.push(style.replace(/%x/g, direction));
			//	});
			//});
			//this.thumbSize = this.thumbsWrapper[index].sumStyles(styles) + (this.thumbs[index].offsetWidth ? this.thumbsWrapper[index].offsetWidth : this.thumbs[index].get('width').toInt());

			this.setup();
			this.scroller.start(this.thumbSize * index, 0);
		},

		clickElement: function(event, thumb){
			if (event) event.stop();


			var index = this.thumbsWrapper.indexOf(thumb);

			this.fireEvent('clickElement', [thumb, index]);
		},

		setup: function(){
			var size = 0, styles = [];

			['padding-%x', 'border-%x-width', 'margin-%x'].each(function(style){
				['left', 'right'].each(function(direction){
					styles.push(style.replace(/%x/g, direction));
				});
			});

			this.thumbs.each(function(thumb, i){
				this.thumbSize = this.thumbsWrapper[i].sumStyles(styles) + (thumb.offsetWidth ? this.thumbsWrapper[i].offsetWidth : (thumb.get('width') || this.thumbsWrapper[i].offsetWidth || 0).toInt());
				size += this.thumbSize;
			}, this);

			this.list.setStyle('width', size);

			if (size < this.wrapper.offsetWidth - this.arrowsSize){
				this.wrapper.removeClass('arrows-enabled');
			} else {
				this.wrapper.addClass('arrows-enabled');
			}
		},

		setActive: function(index){
			this.thumbsWrapper.removeClass('active');
			this.thumbsWrapper[index].addClass('active');
		}

	});


	this.RokGallery.Slideshow.Animations = new Class({

		Extends: Fx.CSS,

		options: {
			duration: 1000,
			transition: 'expo:in:out',

			animation: 'crossfade',
			container: '',
			background: '',
			width: 0,
			height: 0,

			blinds: 24,
			boxes: {
				rows: 5,
				cols: 10
			}
		},

		animations: {},
		animationsKeys: [],

		initialize: function(elements, options){
			this.isPaused = false;

			this.parent(options);

			this.slides = [];

			this.originalDuration = this.options.duration;
			this.delay = 0;
			this.type = 'blinds';
			this.background = this.options.background;
			this.blinds = 1;
			this.boxes = {rows: 1, cols: 1};
			this.properties = {};
			this.direction = 'right';
			this.reorder = false;

			this.container = new Element('div', {
				'class': 'rg-ss-slice-animations'
			}).inject(this.options.container);

			this.setAnimation(this.options.animation);
		},

		build: function(type){
			this.container.empty();
			this.slides.empty();

			this['build' + type.capitalize()]();

			this.elements = $$(this.slides);
		},

		addAnimation: function(name, props){
			if (!this.animations[name]) this.animations[name] = props;
			this.animationsKeys.include(name);
		},

		setAnimation: function(name){
			// debug
			// refractored: blindsDownLeft , blindsDownRight, boxesMirror

			if (name == 'random') name = this.animationsKeys.getRandom();
			if (!this.animations[name]) this.setOptions({animation: 'crossfade'});

			this.properties = {};
			for (var prop in this.animations[name]){
				this[prop] = this.animations[name][prop];
			}

			if (!this.animations[name]['blinds']) this.blinds = this.options.blinds;
			if (!this.animations[name]['boxes']) this.boxes = this.options.boxes;
			if (!this.animations[name]['reorder']) this.reorder = false;

			this.build(this.type);
		},

		buildBlinds: function(){
			var containerSize = this.options.container.getSize(),
				size = {
					width: containerSize.x,
					height: containerSize.y
				},
				background = this.background,
				blinds = background.length ? this.blinds : 0,
				width = Math.round(size.width / blinds);

			this.sliceSize = {width: width, height: size.height};

			(blinds).times(function(i){

				var position = ((width + (i * width)) - width);
				var blind = new Element('div', {
					styles: {
						opacity: 0,
						position: 'absolute',
						top: 0,
						zIndex: 2,
						left: (width * i) + 'px',
						height: size.height + 'px',
						width: (i == blinds - 1) ? size.width - (width * i) : width,
						background: 'url(' + background + ') no-repeat -' + position + 'px 0%',
						backgroundSize: size.width + 'px ' + size.height + 'px'
					}
				}).inject(this.container);

				this.slides.push(blind);

			}, this);
		},

		buildBoxes: function(){
			var containerSize = this.options.container.getSize(),
				size = {
					width: containerSize.x,
					height: containerSize.y
				},
				background = this.background,
				boxes = background.length ? this.boxes : {cols: 0, rows: 0},
				boxSize = {width: Math.round(size.width / boxes.cols), height: Math.round(size.height / boxes.rows)};

			this.sliceSize = {width: boxSize.width, height: boxSize.height};

			(boxes.rows).times(function(row){
				(boxes.cols).times(function(col){
					var position = {
						x: (boxSize.width + (col * boxSize.width)) - boxSize.width,
						y: (boxSize.height + (row * boxSize.height)) - boxSize.height
					};

					var box = new Element('div', {
						styles: {
							opacity: 0,
							position: 'absolute',
							zIndex: 2,
							top: (boxSize.height * row) + 'px',
							left: (boxSize.width * col) + 'px',
							width: (col == boxes.cols - 1) ? size.width - (boxSize.width * col) : boxSize.width,
							height: boxSize.height,
							background: 'url(' + background + ') no-repeat -' + position.x + 'px -' + position.y + 'px',
							backgroundSize: size.width + 'px ' + size.height + 'px'
						}
					}).inject(this.container);

					this.slides.push(box);

				}, this);
			}, this);
		},

		setBackground: function(background){
			this.background = background;
		},

		compute: function(from, to, delta){
			var now = {};

			for (var i in from){
				var iFrom = from[i], iTo = to[i], iNow = now[i] = {};
				for (var p in iFrom){
					iNow[p] = this.parent(iFrom[p], iTo[p], delta);
				}
			}

			return now;
		},

		set: function(now, force){
			var buffer = this.buffer = 0;
			for (var i in now){
				if (!this.elements[i]) continue;

				var iNow = now[i];
				for (var p in iNow) this.render.delay(force ? 0 : buffer, this, [this.elements[i], p, iNow[p], this.options.unit]);

				buffer += this.delay;
			}

			this.buffer = buffer;

			return this;
		},

		step: function(){
			var time = Date.now();
			var expected = (this.delay || 1) * this.slides.length + this.options.duration;

			if (time < this.time + this.options.duration){
				var delta = this.transition((time - this.time) / this.options.duration);
				this.set(this.compute(this.from, this.to, delta));
			} else {
				if (time > this.time + expected){
					this.set(this.compute(this.from, this.to, 1));
					this.complete();
				}
			}
		},

		start: function(obj){
			if (!this.check(obj)) return this;
			var from = {}, to = {};

			for (var i in obj){
				if (!this.elements[i]) continue;

				var iProps = obj[i], iFrom = from[i] = {}, iTo = to[i] = {};

				for (var p in iProps){
					var parsed = this.prepare(this.elements[i], p, iProps[p]);
					iFrom[p] = parsed.from;
					iTo[p] = parsed.to;
				}
			}

			var duration = this.options.duration;
			this.options.duration = Fx.Durations[duration] || duration.toInt();
			this.from = from;
			this.to = to;
			this.time = 0;
			this.transition = this.getTransition();
			this.startTimer();
			this.onStart();
			return this;
			//return this.parent(from, to);
		},

		reset: function(properties, force){
			var obj = {};
			(this.elements.length).times(function(index){
				obj[index] = properties;
			}, this);

			this.set(obj, force);
		},

		play: function(properties){
			var obj = {}, anims = {from: {}, to: {}};properties = properties || Object.clone(this.properties);

			if (this.direction == 'left') this.elements.reverse();
			if (this.reorder){
				this.elements = this.reorder.bind(this, [this.elements])();
			}

			for (var prop in properties){
				switch (typeof properties[prop]){
					case 'object': case 'array':
							var values = properties[prop];
							values.each(function(value, i){
								if (typeof value == 'string')
								properties[prop][i] = value.replace('%height2%', this.sliceSize.height * 2).replace('%width2%', this.sliceSize.width * 2)
													.replace('%height%', this.sliceSize.height).replace('%width%', this.sliceSize.width).toInt();

								anims[!i ? 'from' : 'to'][prop] = properties[prop][i];
							}, this);
						break;
					case 'string':
						properties[prop] = properties[prop].replace('%height2%', this.sliceSize.height * 2).replace('%width2%', this.sliceSize.width * 2)
														.replace('%height%', this.sliceSize.height).replace('%width%', this.sliceSize.width).toInt();
						anims['to'][prop] = properties[prop];
						break;
				}
			}


			//(this.elements.length).times(function(index){
			//	obj[index] = properties;
//
					//var anim = {
					//	from: {}
					//	to: {}
					//};
					//for (var p in obj[index]){
					//	anim[from] = obj[index][p]
					//	console.log(p, obj[index][p]);
					//}
			//	(function(){
			//		moofx(this.elements[index]).animate(obj[index]);
			//	}).delay(this.delay * index, this);
			//}, this);

			//(this.elements.length).times(function(index){
			//	obj[index] = properties;
			//}, this);
//
			//this.start(obj);

			var settings;

			this.time = 0;
			this.transition = this.getTransition();
			this.startTimer();
			this.onStart();

			//moofx(this.elements).style(anims['from']);
			(this.elements.length).times(function(index){
				(function(){
					if (index != this.elements.length - 1) settings = {duration: this.options.duration, equation: 'cubic-bezier(0.37,0.61,0.59,0.87)'};
					else settings = {duration: this.options.duration, equation: 'cubic-bezier(0.37,0.61,0.59,0.87)', callback: function(){ this.complete(); }.bind(this)};

					if (this.elements[index]) moofx(this.elements[index]).animate(anims['to'], settings);
				}).delay(this.delay * index, this);
			}, this);

			return this;
		},

		complete: function(){
			if (this.stopTimer()) this.onComplete();
			return this;
		},

		cancel: function(){
			if (this.stopTimer()) this.onCancel();
			return this;
		},

		onStart: function(){
			this.fireEvent('start', this.subject);
		},

		onComplete: function(){
			this.fireEvent('complete', this.subject);
			if (!this.callChain()) this.fireEvent('chainComplete', this.subject);
		},

		onCancel: function(){
			this.fireEvent('cancel', this.subject).clearChain();
		},

		pause: function(){
			this.isPaused = true;
			this.stopTimer();
			return this;
		},

		resume: function(){
			this.isPaused = false;
			this.startTimer();
			return this;
		},

		clean: function(){
			$$(this.slides).dispose();
			this.slides.empty();

			return this;
		},

		stopTimer: function(){
			if (!this.timer) return false;
			this.time = Date.now() - this.time;
			this.timer = removeInstance(this);
			return true;
		},

		startTimer: function(){
			if (this.timer) return false;
			this.time = Date.now() - this.time;
			this.timer = addInstance(this);
			return true;
		}

	});
	// global timers

	var instances = {}, timers = {};

	var loop = function(){
		for (var i = this.length; i--;){
			if (this[i]) this[i].step();
		}
	};

	var addInstance = function(instance){
		var fps = instance.options.fps,
			list = instances[fps] || (instances[fps] = []);
		list.push(instance);
		if (!timers[fps]) timers[fps] = loop.periodical(Math.round(1000 / fps), list);
		return true;
	};

	var removeInstance = function(instance){
		var fps = instance.options.fps,
			list = instances[fps] || [];
		list.erase(instance);
		if (!list.length && timers[fps]) timers[fps] = clearInterval(timers[fps]);
		return false;
	};


	// animations

	var animations = {

		'crossfade': {
			type: 'blinds',
			blinds: 1,
			delay: 0,
			direction: 'right',
			properties: {
				'opacity': [0, 1]
			}
		},

		'blindsRight': {
			type: 'blinds',
			direction: 'right',
			delay: 50,
			properties: {
				'opacity': [0, 1]
			}
		},

		'blindsLeft': {
			type: 'blinds',
			direction: 'left',
			delay: 50,
			properties: {
				'opacity': [0, 1]
			}
		},

		'blindsDownLeft': {
			type: 'blinds',
			direction: 'left',
			delay: 50,
			properties: {
				height: [0, '%height%'],
				opacity: [0, 1]
			}
		},

		'blindsDownRight': {
			type: 'blinds',
			direction: 'right',
			delay: 50,
			properties: {
				height: [0, '%height%'],
				opacity: [0, 1]
			}
		},

		'boxesOpacityRight': {
			type: 'boxes',
			direction: 'right',
			delay: 6,
			properties: {
				opacity: [0, 1]
			}
		},

		'boxesOpacityLeft': {
			type: 'boxes',
			direction: 'left',
			delay: 6,
			properties: {
				opacity: [0, 1]
			}
		},

		'slideDown': {
			type: 'boxes',
			boxes: {
				cols: 1,
				rows: 1
			},
			direction: 'right',
			delay: 200,
			properties: {
				height: [0, '%height%'],
				opacity: [0, 1]
			}
		},

		'slideUp': {
			type: 'boxes',
			boxes: {
				cols: 1,
				rows: 1
			},
			direction: 'right',
			delay: 200,
			properties: {
				top: ['%height%', 0],
				opacity: [0, 1]
			}
		},

		'slideLeft': {
			type: 'boxes',
			boxes: {
				cols: 1,
				rows: 1
			},
			direction: 'left',
			delay: 200,
			properties: {
				left: ['-%width%', 0],
				opacity: [0, 1]
			}
		},

		'slideRight': {
			type: 'boxes',
			boxes: {
				cols: 1,
				rows: 1
			},
			direction: 'right',
			delay: 200,
			properties: {
				left: ['%width%', 0],
				opacity: [0, 1]
			}
		},

		'boxesRight': {
			type: 'boxes',
			direction: 'right',
			delay: 6,
			properties: {
				width: [0, '%width%'],
				height: [0, '%height%'],
				opacity: [0, 1]
			}
		},

		'boxesLeft': {
			type: 'boxes',
			direction: 'left',
			delay: 6,
			properties: {
				width: [0, '%width%'],
				height: [0, '%height%'],
				opacity: [0, 1]
			}
		},

		'boxesMirror': {
			type: 'boxes',
			direction: 'left',
			reorder: function(elements){
				var boxes = [];

				for (var i = 0, j = elements.length - 1; i < elements.length / 2;i++,j--){
					boxes.push(elements[i]);
					boxes.push(elements[j]);
				}

				return $$(boxes);
			},
			delay: 6,
			properties: {
				opacity: [0, 1],
				width: [0, '%width%']
			}
		},

		'boxesRandom': {
			type: 'boxes',
			direction: 'right',
			reorder: function(elements){
				var boxes = elements;

				boxes.sort(function() {
					return 0.5 - Math.random();
				});

				return $$(boxes);
			},
			delay: 4,
			properties: {
				opacity: [0, 1]
			}
		},

		'blindsMirrorIn': {
			type: 'blinds',
			blinds: 12,
			direction: 'left',
			reorder: function(elements){
				var boxes = [];

				for (var i = 0, j = elements.length - 1; i < elements.length / 2;i++,j--){
					boxes.push(elements[i]);
					boxes.push(elements[j]);
				}

				return $$(boxes);
			},
			delay: 50,
			properties: {
				opacity: [0, 1],
				width: [0, '%width%'],
				height: [0, '%height%']
			}
		},

		'blindsMirrorOut': {
			type: 'blinds',
			blinds: 12,
			direction: 'left',
			reorder: function(elements){
				var boxes = [];

				for (var i = 0, j = elements.length - 1; i < elements.length / 2;i++,j--){
					boxes.push(elements[i]);
					boxes.push(elements[j]);
				}

				boxes.reverse();

				return $$(boxes);
			},
			delay: 50,
			properties: {
				opacity: [0, 1],
				width: [0, '%width%'],
				height: [0, '%height%']
			}
		},

		'blindsRandom': {
			type: 'blinds',
			blinds: 12,
			direction: 'right',
			reorder: function(elements){
				var boxes = elements;

				boxes.sort(function() {
					return 0.5 - Math.random();
				});

				return $$(boxes);
			},
			delay: 35,
			properties: {
				opacity: [0, 1]
			}
		}

	};

	for (var animation in animations){
		this.RokGallery.Slideshow.Animations.prototype.addAnimation(animation, animations[animation]);
	}

}());

RokGallery.Slideshow.prototype.options.onSetup = function(){
	var current = this.captions[this.current];
	if (current) current.setStyles({display: 'block', 'opacity': 1, 'visibility': 'visible'});
};
