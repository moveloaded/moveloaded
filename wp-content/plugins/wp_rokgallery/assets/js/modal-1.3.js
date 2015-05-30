/**
 * SqueezeBox - Expandable Lightbox
 *
 * Allows to open various content as modal,
 * centered and animated box.
 *
 * Dependencies: MooTools 1.2
 *
 * Inspired by
 *  ... Lokesh Dhakar	- The original Lightbox v2
 *
 * @version		1.1 rc4
 *
 * @license		MIT-style license
 * @author		Harald Kirschner <mail [at] digitarald.de>
 * @copyright	Author
 */

var SqueezeBox = {
	presets: {
		onOpen: function() {},
		onClose: function() {},
		onUpdate: function() {},
		onResize: function() {},
		onMove: function() {},
		onShow: function() {},
		onHide: function() {},
		size: {
			x: 600,
			y: 450
		},
		sizeLoading: {
			x: 200,
			y: 150
		},
		marginInner: {
			x: 20,
			y: 20
		},
		marginImage: {
			x: 50,
			y: 75
		},
		handler: false,
		target: null,
		closable: true,
		closeBtn: true,
		zIndex: 65555,
		overlayOpacity: 0.7,
		classWindow: '',
		classOverlay: 'squeezebox-overlay',
		overlayFx: {},
		resizeFx: {},
		contentFx: {},
		parse: 'rel',
		parseSecure: false,
		shadow: true,
		document: null,
		ajaxOptions: {}
	},
	initialize: function(a) {
		if (this.options) return this;
		this.presets = Object.merge(this.presets, a);
		this.doc = this.presets.document || document;
		this.options = {};
		this.setOptions(this.presets).build();
		this.bound = {
			window: this.reposition.bind(this),
			scroll: this.checkTarget.bind(this),
			close: this.close.bind(this),
			key: this.onKey.bind(this)
		};
		this.isOpen = this.isLoading = false;
		return this
	},
	build: function() {
		this.overlay = new Element('div', {
			id: 'sbox-overlay',
			styles: {
				display: 'none',
				zIndex: this.options.zIndex
			}
		});
		this.win = new Element('div', {
			id: 'sbox-window',
			styles: {
				display: 'none',
				zIndex: this.options.zIndex + 2
			}
		});
		
		this.win.close = this.close.bind(this);
		
		if (this.options.shadow) {
			if (Browser.Engine && Browser.Engine.webkit420) {
				this.win.setStyle('-webkit-box-shadow', '0 0 10px rgba(0, 0, 0, 0.7)')
			} else if (!Browser.ie6) {
				var b = new Element('div', {
					'class': 'sbox-bg-wrap'
				}).inject(this.win);
				var c = function(e) {
					this.overlay.fireEvent('click', e)
				}.bind(this);
				['n', 'ne', 'e', 'se', 's', 'sw', 'w', 'nw'].each(function(a) {
					new Element('div', {
						'class': 'sbox-bg sbox-bg-' + a
					}).inject(b).addEvent('click', c)
				})
			}
		}
		this.content = new Element('div', {
			id: 'sbox-content'
		}).inject(this.win);
		this.closeBtn = new Element('a', {
			id: 'sbox-btn-close',
			href: '#'
		}).inject(this.win);
		this.fx = {
			overlay: new Fx.Tween(this.overlay, Object.merge({
				onStart: Events.prototype.clearChain,
				duration: 250,
				link: 'cancel'
			},
			this.options.overlayFx)).set('opacity', 0),
			win: new Fx.Morph(this.win, Object.merge({
				onStart: Events.prototype.clearChain,
				unit: 'px',
				duration: 750,
				transition: Fx.Transitions.Quint.easeOut,
				link: 'cancel',
				unit: 'px'
			},
			this.options.resizeFx)),
			content: new Fx.Tween(this.content, Object.merge({
				duration: 250,
				link: 'cancel'
			},
			this.options.contentFx)).set('opacity', 0)
		};
		document.id(this.doc.body).adopt(this.overlay, this.win)
	},
	assign: function(a, b) {
		return (document.id(a) || $$(a)).addEvent('click', function() {
			return !SqueezeBox.fromElement(this, b)
		})
	},
	open: function(d, e) {
		this.initialize();
		if (this.element != null) this.trash();
		this.element = document.id(d) || false;
		this.setOptions(Object.merge(this.presets, e || {}));
		if (this.element && this.options.parse) {
			var f = this.element.get(this.options.parse);
			if (f && (f = JSON.decode(f, this.options.parseSecure))) this.setOptions(f)
		}
		this.url = ((this.element) ? (this.element.get('href')) : d) || this.options.url || '';
		this.assignOptions();
		var g = g || this.options.handler;
		if (g) return this.setContent(g, this.parsers[g].call(this, true));
		var h = false;
		return this.parsers.some(function(a, b) {
			var c = a.call(this);
			if (c) {
				h = this.setContent(b, c);
				return true
			}
			return false
		},
		this)
	},
	fromElement: function(a, b) {
		return this.open(a, b)
	},
	assignOptions: function() {
		this.overlay.set('class', this.options.classOverlay);
		this.win.set('class', this.options.classWindow);
		if (Browser.Engine.trident4) this.win.addClass('sbox-window-ie6')
	},
	close: function(e) {
		var a = (typeOf(e) == 'event');
		if (a) e.stop();
		if (!this.isOpen || (a && !Function.from(this.options.closable).call(this, e))) return this;
		this.fx.overlay.start('opacity', 0).chain(this.toggleOverlay.bind(this));
		this.win.setStyle('display', 'none');
		this.fireEvent('onClose', this.content);
		this.trash();
		this.toggleListeners();
		this.isOpen = false;
		return this
	},
	trash: function() {
		this.element = this.asset = null;
		this.content.empty();
		this.options = {};
		this.setOptions(this.presets);
		this.callChain()
	},
	onError: function() {
		this.asset = null;
		this.setContent('string', this.options.errorMsg || 'An error occurred')
	},
	setContent: function(a, b) {
		if (!this.handlers[a]) return false;
		this.content.className = 'sbox-content-' + a;
		this.applyTimer = this.applyContent.delay(this.fx.overlay.options.duration, this, this.handlers[a].call(this, b));
		if (this.overlay.retrieve('opacity')) return this;
		this.toggleOverlay(true);
		this.fx.overlay.start('opacity', this.options.overlayOpacity);
		return this.reposition()
	},
	applyContent: function(a, b) {
		if (!this.isOpen && !this.applyTimer) return;
		this.applyTimer = clearTimeout(this.applyTimer);
		this.hideContent();
		if (!a) {
			this.toggleLoading(true)
		} else {
			if (this.isLoading) this.toggleLoading(false);
			this.fireEvent('onUpdate', this.content, 20)
		}
		if (a) {
			if (['string', 'array'].contains(typeOf(a))) this.content.set('html', a);
			else if (!(a !== this.content && this.content.contains(a))) this.content.adopt(a)
		}
		this.callChain();
		if (!this.isOpen) {
			this.toggleListeners(true);
			this.resize(b, true);
			this.isOpen = true;
			this.fireEvent('onOpen', this.content)
		} else {
			this.resize(b)
		}
	},
	resize: function(a, b) {
		this.showTimer = clearTimeout(this.showTimer || null);
		var c = this.doc.getSize(),
			scroll = this.doc.getScroll();
		this.size = Object.merge((this.isLoading) ? this.options.sizeLoading : this.options.size, a);
		var d = {
			width: this.size.x,
			height: this.size.y,
			left: (scroll.x + (c.x - this.size.x - this.options.marginInner.x) / 2).toInt(),
			top: (scroll.y + (c.y - this.size.y - this.options.marginInner.y) / 2).toInt()
		};
		this.hideContent();
		if (!b) {
			this.fx.win.start(d).chain(this.showContent.bind(this))
		} else {
			this.win.setStyles(d).setStyle('display', '');
			this.showTimer = this.showContent.delay(50, this)
		}
		return this.reposition()
	},
	toggleListeners: function(a) {
		var b = (a) ? 'addEvent' : 'removeEvent';
		this.closeBtn[b]('click', this.bound.close);
		this.overlay[b]('click', this.bound.close);
		this.doc[b]('keydown', this.bound.key)[b]('mousewheel', this.bound.scroll);
		this.doc.getWindow()[b]('resize', this.bound.window)[b]('scroll', this.bound.window)
	},
	toggleLoading: function(a) {
		this.isLoading = a;
		this.win[(a) ? 'addClass' : 'removeClass']('sbox-loading');
		if (a) this.fireEvent('onLoading', [this.win])
	},
	toggleOverlay: function(a) {
		var b = this.doc.getSize().x;
		this.overlay.setStyle('display', (a) ? '' : 'none');
		this.doc.body[(a) ? 'addClass' : 'removeClass']('body-overlayed');
		if (a) {
			this.scrollOffset = this.doc.getWindow().getSize().x - b;
			this.doc.body.setStyle('margin-right', this.scrollOffset)
		} else {
			this.doc.body.setStyle('margin-right', '')
		}
	},
	showContent: function() {
		if (this.content.get('opacity')) this.fireEvent('onShow', [this.win]);
		this.fx.content.start('opacity', 1)
	},
	hideContent: function() {
		if (!this.content.get('opacity')) this.fireEvent('onHide', [this.win]);
		this.fx.content.cancel().set('opacity', 0)
	},
	onKey: function(e) {
		switch (e.key) {
		case 'esc':
			this.close(e);
		case 'up':
		case 'down':
			return false
		}
	},
	checkTarget: function(e) {
		//element.hasChild(item) => item !== element && element.contains(item)
		return e.target !== this.content && this.content.contains(e.target)
	},
	reposition: function() {
		var a = this.doc.getSize(),
			scroll = this.doc.getScroll(),
			ssize = this.doc.getScrollSize();
		this.overlay.setStyles({
			width: '100%',
			height: ssize.y,
			top: 0,
			left: 0,
			bottom: 0,
			right: 0
		});
		this.win.setStyles({
			left: (scroll.x + (a.x - this.win.offsetWidth) / 2 - this.scrollOffset).toInt() + 'px',
			top: (scroll.y + (a.y - this.win.offsetHeight) / 2).toInt() + 'px'
		});
		return this.fireEvent('onMove', [this.overlay, this.win])
	},
	removeEvents: function(a) {
		if (!this.$events) return this;
		if (!a) this.$events = {};
		else if (this.$events[a]) this.$events[a] = {};
		return this
	},
	extend: function(a) {
		return Object.append(this, a)
	},
	handlers: new Hash(),
	parsers: new Hash()
};
SqueezeBox.extend(new Events()).extend(new Options()).extend(new Chain());
SqueezeBox.parsers.extend({
	image: function(a) {
		return (a || (/\.(?:jpg|png|gif)$/i).test(this.url)) ? this.url : false
	},
	clone: function(a) {
		if (document.id(this.options.target)) return document.id(this.options.target);
		if (this.element && !this.element.parentNode) return this.element;
		var b = this.url.match(/#([\w-]+)$/);
		return (b) ? document.id(b[1]) : (a ? this.element : false)
	},
	ajax: function(a) {
		return (a || (this.url && !(/^(?:javascript|#)/i).test(this.url))) ? this.url : false
	},
	iframe: function(a) {
		return (a || this.url) ? this.url : false
	},
	string: function(a) {
		return true
	}
});
SqueezeBox.handlers.extend({
	image: function(b) {
		var c, tmp = new Image();
		this.asset = null;
		tmp.onload = tmp.onabort = tmp.onerror = (function() {
			tmp.onload = tmp.onabort = tmp.onerror = null;
			if (!tmp.width) {
				this.onError.delay(10, this);
				return
			}
			var a = this.doc.getSize();
			a.x -= this.options.marginImage.x;
			a.y -= this.options.marginImage.y;
			c = {
				x: tmp.width,
				y: tmp.height
			};
			for (var i = 2; i--;) {
				if (c.x > a.x) {
					c.y *= a.x / c.x;
					c.x = a.x
				} else if (c.y > a.y) {
					c.x *= a.y / c.y;
					c.y = a.y
				}
			}
			c.x = c.x.toInt();
			c.y = c.y.toInt();
			this.asset = document.id(tmp);
			tmp = null;
			this.asset.width = c.x;
			this.asset.height = c.y;
			this.applyContent(this.asset, c)
		}).bind(this);
		tmp.src = b;
		if (tmp && tmp.onload && tmp.complete) tmp.onload();
		return (this.asset) ? [this.asset, c] : null
	},
	clone: function(a) {
		if (a) return a.clone();
		return this.onError()
	},
	adopt: function(a) {
		if (a) return a;
		return this.onError()
	},
	ajax: function(e) {
		var f = this.options.ajaxOptions || {};
		this.asset = new Request.HTML(Object.merge({
			method: 'get',
			evalScripts: false,
			onSuccess: function(a, b, c, d) {
				this.applyContent(c);
				if (f.evalScripts !== null && f.evalScripts) $exec(d);
				this.fireEvent('onAjax', [a, b, c, d]);
				this.asset = null
			}.bind(this),
			onFailure: this.onError.bind(this)
		},
		this.options.ajaxOptions));
		this.asset.send.delay(20, this.asset, {
			url: e
		})
	},
	iframe: function(a) {
		this.asset = new Element('iframe', Object.merge({
			src: a,
			frameBorder: 0,
			width: this.options.size.x,
			height: this.options.size.y
		},
		this.options.iframeOptions));
		if (this.options.iframePreload) {
			this.asset.addEvent('load', function() {
				this.applyContent(this.asset.setStyle('display', ''))
			}.bind(this));
			this.asset.setStyle('display', 'none').inject(this.content);
			return false
		}
		return this.asset
	},
	string: function(a) {
		return a
	}
});
SqueezeBox.handlers.url = SqueezeBox.handlers.ajax;
SqueezeBox.parsers.url = SqueezeBox.parsers.ajax;
SqueezeBox.parsers.adopt = SqueezeBox.parsers.clone;
