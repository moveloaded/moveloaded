/**
 * @version   2.31 March 4, 2015
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

((function(){
	
	this.Popup = new Class({
		
		Implements: [Options, Events],
		
		options: {
			popup: 'popup',
			overlay: 'overlay'
		},
		
		initialize: function(options){
			
			this.setOptions(options);
			this.bounds = this.setBounds();
			
			this.popup = document.id(this.options.popup).inject(document.body);
			this.overlay = document.id(this.options.overlay).inject(document.body);
			
			this.topBar = this.popup.getElement('.topbar');
			this.content = this.popup.getElement('.content');
			this.statusBar = this.popup.getElement('.statusbar');
			
			this.setEvents();
			
		},
		
		setBounds: function(){
			
			return {
				'popup': {
					'open': this.showPopup.bind(this),
					'close': this.hidePopup.bind(this),
					'ok': this.continuePopup.bind(this)
				},
				'overlay': {
					'show': this.showOverlay.bind(this),
					'hide': this.hideOverlay.bind(this)
				}
			};
			
		},
		
		setEvents: function(){
			if (this.popup){
				this.reposition(true);
				this.popup.inject(this.overlay, 'after');
				this.popup.setStyle('visibility', 'hidden');
				this.popup.set('morph', {duration: 300, link: 'cancel', transition: 'expo:in'});
				this.popup.addEvents({
					'open': this.bounds.popup.open,
					'close': this.bounds.popup.close,
					'ok': this.bounds.popup.ok
				});
			}
			
			if (this.overlay){
				this.overlay.setStyles({visibility: 'hidden', opacity: 0, display: 'block'});
				this.overlay.addEvents({
					'show': this.bounds.overlay.show,
					'hide': this.bounds.overlay.hide
				});
			}
			
			window.addEvent('resize:throttle', this.reposition.bind(this));
		},
		
		attachEvents: function(){
			var buttons = this.popup.getElements('.button');
			
			buttons.each(function(button){
				button.removeEvents('click');
				if (button.hasClass('close') || button.hasClass('cancel')) button.addEvent('click:once', this.close.bind(this));
				if (button.hasClass('ok')) button.addEvent('click:once', this.popup.fireEvent.pass('ok', this.popup));
			}, this);
		},
		
		showPopup: function(){
			this.popup.setStyles({visibility: 'visible', opacity: 1});
		},
		
		
		hidePopup: function(){
			this.popup.setStyles({visibility: 'hidden', opacity: 0});
		},
		
		continuePopup: function(){
			if (this.continueFn) this.continueFn();
			this.fireEvent('ok');
		},
		
		setContinue: function(fn){
			this.continueFn = fn.bind(this);
		},
		
		showOverlay: function(){
			this.overlay.setStyles({visibility: 'visible', opacity: 1});
		},
		
		hideOverlay: function(){
			this.overlay.setStyles({visibility: 'hidden', opacity: 0});
		},
		
		open: function(){
			var winSize = window.getSize();
			if (winSize.y >= window.getScrollSize().y) document.getElements('html, body').setStyle('height', '100%');
			else document.getElements('html, body').setStyle('height', 'auto');
			
			this.content.set('class', 'content');
			this.overlay.fireEvent('show');
			this.popup.fireEvent('open');

			this.popup.store('open', true);
			
			this.attachEvents();
			
			return this;
		},
		
		close: function(){
			this.overlay.fireEvent('hide');
			this.popup.fireEvent('close');

			this.popup.store('open', false);
		},
		
		reposition: function(force){
			if (!this.popup.retrieve('open') && !force) return this.popup;
			
			var popupSize = this.popup.getSize(),
				overlaySize = this.overlay.getSize(),
				winSize = window.getSize();
			
			if (winSize.y >= window.getScrollSize().y) document.getElements('html, body').setStyle('height', '100%');
			else document.getElements('html, body').setStyle('height', 'auto');
			
			//if (Browser.firefox) document.getElements('html, body').setStyle('height', 'auto');
			
			var top = winSize.y / 2 - (popupSize.y / 2),
				left = winSize.x / 2 - (popupSize.x / 2);
			
			return this.popup['setStyles']({
				top: top.limit(5, winSize.y),
				left: left.limit(5, winSize.x)
			});
		},
		
		setPopup: function(data){
			this.continueFn = function(){};
			this.setType('');
			
			if (!('size' in data)){
				this.setSize({width: 400});
				this.reposition();
			}
			
			for (var name in data){
				this['set' + name.capitalize()](data[name]);
			}
			
			return this;
		},
		
		setButtons: function(buttons){
			this.popup.getElements('.button').setStyle('display', 'none');
			for(var type in buttons){
				var button = buttons[type];
				var element = this.popup.getElement('.button.' + type);
				element.setStyle('display', button.show ? 'block' : 'none');
				if (button['label']) element.set('text', button['label']);
			}
		},
		
		setType: function(type){
			this.popup.className = 'popup ' + type;
		},
		
		setTitle: function(title){
			this.topBar.getElement('span:first-child').set('text', title);
		},
		
		setMessage: function(content){
			this.content.set('html', content);
			this.reposition();
		},
		
		setSize: function(size){
			var morphing = {}, self = this;
			
			if (size && typeOf(size) == 'object'){
				for (var prop in size) morphing[prop] = size[prop];
				
				this.popup.setStyles(morphing);
			}
			
			
			this.reposition.delay(10, this);
		}
		
	});

})());