
(function(){

	if (typeof this.RokGallery == 'undefined') this.RokGallery = {};

	this.RokGallery.Showcase = new Class({

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
					this.adjustHeight(index);

					this.captionsAnimation.setAnimation(this.options.captions.animation);
					this.captionsAnimation.setFromTo(this.current, index);
					this.captionsAnimation.play();
				}
			},

			animation: 'random',
			duration: 500,
			imgpadding: 0,

			captions:{
				fixedheight: false,
				animated: true,
				animation: 'crossfade'
			},

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

			this.container = this.element.getElement('.rg-sc-slice-container');
			this.main = this.element.getElement('.rg-sc-main');
			this.imglist = this.element.getElement('.rg-sc-img-list');
			this.slices = this.element.getElements('.rg-sc-slice');
			this.captions = this.element.getElements('.rg-sc-info');
			this.arrows = this.element.getElements('.rg-sc-controls .next, .rg-sc-controls .prev');

			this.loaderBar = this.element.getElement('.rg-sc-loader');
			this.progressBar = this.element.getElement('.rg-sc-progress');


			if (this.options.autoplay.enabled){
				this.progress = new RokGallery.Showcase.Progress(this.progressBar, {
					duration: this.options.autoplay.delay,
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
			//if (this.scrollerContainer) this.setNavigation();

			this.jump(0, true);

			if (Browser.Features.Touch){
				this.element.addEvent('swipe', function(event){
					event.preventDefault();
					this[event.direction == 'right' ? 'previous' : 'next']();
				}.bind(this));
			}
		},

		setup: function(){
			this.animation = new RokGallery.Showcase.Animations(this.element, {
				container: this.imglist,
				width: this.container.getSize().x,
				height: this.container.getSize().y,
				duration: this.options.duration,
				transition: 'quad:in:out',
				//imgpadding: 0,
				onStart: function(){
					if (this.options.autoplay.enabled) this.progress.pause();
				}.bind(this),
				onComplete: function(){
					this.slices[this.current].setStyle('display', 'block');
					if (this.current != this.animation.index) this.slices[this.animation.index].setStyle('display', 'none');

					this.animation.clean();

					if (this.options.autoplay.enabled) this.progress.play();
				}.bind(this)
			});

			if (this.captions.length){
				this.captions.setStyles({display: 'block', 'opacity': 0});
				this.captionsAnimation = new RokGallery.Showcase.CaptionsAnimations(this.captions, {
					duration: this.options.duration,
					onStart: function(){
						this.captions.setStyle('visibility', 'visible');
					}.bind(this),
					onComplete: function(){
						this.captions.setStyle('visibility', 'hidden');
						this.captions[this.current].setStyle('visibility', 'visible');
					}.bind(this)
				});

				if (this.options.captions.fixedheight){
					var max = 0;
					this.captions.each(function(caption){
						if (caption.offsetHeight > max) max = caption.offsetHeight;
					});

					this.main.setStyle('height', max);
				}
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


		adjustHeight: function(index){
			if (this.options.captions.fixedheight) return;

			var caption = this.captions[index].offsetHeight,
				container = this.container.getSize().y,
				main = this.main.offsetHeight,
				max = Math.max(caption, container);

			if (main != max){
				if (this.options.captions.animated){
					this.main.set('tween', {duration: this.options.duration}).get('tween').start('height', max);
				} else {
					this.main.setStyle('height', max);
				}
			}
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

			if (typeof GantrySmartLoad != 'undefined') window.fireEvent('scroll');
			this.fireEvent('jump', index);

			this.current = index;

			return this;
		},

		getNext: function(){
			return (this.current + 1 >= this.slices.length) ? 0 : this.current + 1;
		},

		getPrevious: function(){
			return (this.current - 1 < 0) ? this.slices.length - 1 : this.current - 1;
		}

	});

	this.RokGallery.Showcase.Progress = new Class({

		Extends: Fx,

		options: {
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

	this.RokGallery.Showcase.Animations = new Class({

		Extends: Fx.CSS,

		options: {
			duration: 1000,
			transition: 'expo:in:out',

			animation: 'crossfade',
			container: '',
			background: '',
			imgpadding: 0,
			width: 0,
			height: 0,

			blinds: 24,
			boxes: {
				rows: 10,
				cols: 24
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
				'class': 'rg-sc-slice-animations'
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
			if (name == 'random') name = this.animationsKeys.getRandom();
			if (!this.animations[name]) this.setOptions({animation: 'crossfade'});

			for (var prop in this.animations[name]){
				this[prop] = this.animations[name][prop];
			}

			if (!this.animations[name]['blinds']) this.blinds = this.options.blinds;
			if (!this.animations[name]['boxes']) this.boxes = this.options.boxes;
			if (!this.animations[name]['reorder']) this.reorder = false;

			this.build(this.type);
		},

		buildBlinds: function(){
			var size = {
					width: this.options.width,
					height: this.options.height
				},
				background = this.background,
				blinds = background && background.length ? this.blinds : 0,
				width = Math.round(size.width / blinds);

			this.sliceSize = {width: width, height: size.height};

			(blinds).times(function(i){

				var position = ((width + (i * width)) - width);
				var blind = new Element('div', {
					styles: {
						opacity: 0,
						position: 'absolute',
						top: this.options.imgpadding,
						left: (width * i) + this.options.imgpadding + 'px',
						height: size.height + 'px',
						width: (i == blinds - 1) ? size.width - (width * i) : width,
						background: 'url(' + background + ') no-repeat -' + position + 'px 0%'
					}
				}).inject(this.container);

				this.slides.push(blind);

			}, this);
		},

		buildBoxes: function(){
			var size = {width: this.options.width, height: this.options.height},
				background = this.background,
				boxes = background && background.length ? this.boxes : {rows: 0, cols: 0},
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
							top: (boxSize.height * row) + this.options.imgpadding + 'px',
							left: (boxSize.width * col) + this.options.imgpadding + 'px',
							width: (col == boxes.cols - 1) ? size.width - (boxSize.width * col) : boxSize.width,
							height: boxSize.height,
							background: 'url(' + background + ') no-repeat -' + position.x + 'px -' + position.y + 'px'
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
			var obj = {}; properties = properties || this.properties;

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
								if (['top', 'right', 'bottom', 'left'].contains(prop) && !properties[prop][i]) properties[prop][i] += this.options.imgpadding;
							}, this);
						break;
					case 'string':
						properties[prop] = properties[prop].replace('%height2%', this.sliceSize.height * 2).replace('%width2%', this.sliceSize.width * 2)
														.replace('%height%', this.sliceSize.height).replace('%width%', this.sliceSize.width).toInt();
						if (['top', 'right', 'bottom', 'left'].contains(prop) && !properties[prop][i]) properties[prop][i] += this.options.imgpadding;
						break;
				}
			}

			(this.elements.length).times(function(index){
				obj[index] = properties;
			}, this);

			this.start(obj);
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


	this.RokGallery.Showcase.CaptionsAnimations = new Class({

		Extends: Fx.Elements,

		options: {
			duration: 1000,
			transition: 'expo:in:out',
			link: 'cancel',

			animation: 'crossfade'
		},

		animations: {},
		animationsKeys: [],

		initialize: function(elements, options){
			this.parent(options);
			this.setOptions(options);
			this.isPaused = false;
			this.elements = this.subject = $$(elements);
			this.properties = {};

			//this.addEvent('onComplete', this.onComplete.bind(this));
			this.setAnimation(this.options.animation);
			this.setFromTo(0, 0);
		},

		addAnimation: function(name, props){
			if (!this.animations[name]) this.animations[name] = props;
			this.animationsKeys.include(name);
		},

		setAnimation: function(name){
			if (name == 'random') name = this.animationsKeys.getRandom();
			if (!this.animations[name]) this.setOptions({animation: 'crossfade'});

			this.properties = {};
			for (var prop in this.animations[name]){
				this.properties[prop] = this.animations[name][prop];
			}
			//this.build(this.type);
		},

		setFromTo: function(from, to){
			this.fromCaption = this.elements[from] ? from : 0;
			this.toCaption = this.elements[to] ? to : 0;
		},

		reset: function(properties, force){
			var obj = {};
			(this.elements.length).times(function(index){
				obj[index] = properties;
			}, this);

			this.set(obj, force);
		},

		play: function(properties){
			var obj = {}, current = {}, next = {}; properties = properties || this.properties;

			var fromSize = {'width': this.elements[this.fromCaption].offsetWidth, 'height': this.elements[this.fromCaption].offsetHeight},
				toSize = {'width': this.elements[this.toCaption].offsetWidth, 'height': this.elements[this.toCaption].offsetHeight};

			if (this.fromCaption != this.toCaption){
				obj[this.fromCaption] = properties['current'];
				obj[this.toCaption] = properties['next'];
			} else {
				obj[this.toCaption] = properties['next'];
			}

			for (var prop in obj[this.fromCaption]){
				obj[this.fromCaption][prop].each(function(value, i){
					if (typeof value == 'string')
						obj[this.fromCaption][prop][i] = value.replace('%height2%', fromSize.width * 2).replace('%width2%', fromSize.width * 2)
												.replace('%height%', fromSize.height).replace('%width%', fromSize.width).toInt();

				}, this);
			}

			for (var prop in obj[this.toCaption]){
				obj[this.toCaption][prop].each(function(value, i){
					if (typeof value == 'string')
						obj[this.toCaption][prop][i] = value.replace('%height2%', toSize.height * 2).replace('%width2%', toSize.width * 2)
										.replace('%height%', toSize.height).replace('%width%', toSize.width).toInt();
				}, this);
			}

			this.start(obj);
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

	var captionsAnimations = {
		'crossfade': {
			current: {
				'opacity': [1, 0]
			},
			next: {
				'opacity': [0, 1]
			}
		},

		'topdown': {
			current: {
				'opacity': [1, 0],
				'top': [0, '%height%']
			},
			next: {
				'opacity': [0, 1],
				'top': ['-%height%', 0]
			}
		},

		'bottomup': {
			current: {
				'opacity': [1, 0],
				'top': [0, '-%height%']
			},
			next: {
				'opacity': [0, 1],
				'top': ['%height2%', 0]
			}
		}
	};

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
				width: [0, '%width%'],
				height: [0, '%height%'],
				opacity: [0, 1]
			}
		},

		'blindsDownRight': {
			type: 'blinds',
			direction: 'right',
			delay: 50,
			properties: {
				width: [0, '%width%'],
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
				width: [0, '%width%'],
				height: [0, '%height%']
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
		this.RokGallery.Showcase.Animations.prototype.addAnimation(animation, animations[animation]);
	}

	for (var animation in captionsAnimations){
		this.RokGallery.Showcase.CaptionsAnimations.prototype.addAnimation(animation, captionsAnimations[animation]);
	}

}());
