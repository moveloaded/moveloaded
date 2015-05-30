
((function(){

this.Progress = new Class({
	
	Implements: [Options, Events],
	
	options: {
		color: '#7f7f7f',
		bg: true,
		bgColor: '#fff'
	},
	
	initialize: function(element, options){
		if (Browser.ie8 || Browser.ie7) return;
		
		var canvas = this.canvas = document.getElement(element);
		if (!canvas) return;
		
		this.ctx = canvas.getContext('2d');
		
		this.setOptions(options);
		
		this.size = {x: canvas.get('width').toInt(), y: canvas.get('height').toInt()};
		Object.append(this.size, {halfx: this.size.x / 2, halfy: this.size.y / 2});
		
		this.start = 0 - Math.PI / 2;
		this.end = this.start + ((Math.PI * 2) * 0 / 100);
		this.ctx.fillStyle = this.options.color;
		
		if (this.options.bg) this.createBackground();
	},
	
	createBackground: function(){
		if (Browser.ie8 || Browser.ie7) return;
		var cls = this.canvas.className.trim().clean();
		var canvas = new Element('canvas.' + cls + '-bg', {width: this.size.x, height: this.size.y}).inject(this.canvas, 'after'),
			ctx = canvas.getContext('2d');
		
		ctx.fillStyle = this.options.bgColor;
		ctx.strokeStyle = this.options.color;
		ctx.beginPath();
		ctx.arc(this.size.halfx, this.size.halfy, this.size.halfx - 1, 0, Math.PI * 2, true);
		ctx.closePath();
		ctx.fill();
		ctx.stroke();
	},
	
	set: function(pc){
		if (Browser.ie8 || Browser.ie7) return;
		pc = pc.toInt();
		this.end = this.start + ((Math.PI * 2) * pc / 100);
		this.ctx.clearRect(0, 0, this.size.x, this.size.y);
		this.ctx.moveTo(this.size.halfx, this.size.halfy);
		this.ctx.beginPath();
		this.ctx.arc(this.size.halfx, this.size.halfy, this.size.halfx - 1, this.start, this.end, false);
		this.ctx.lineTo(this.size.halfx, this.size.halfy);
		this.ctx.fill();
		this.ctx.closePath();
	},
	
	reset: function(){
		this.set(0);
	}
});

})());