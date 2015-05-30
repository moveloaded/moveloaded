
((function(){

	this.Marquee.Crop = new Class({

		Extends: Marquee,

		options: {
			autoHide: false,
			cropMode: true,
			contain: true,
			handleSize: 8,
			preset: false,
			noHandlers: false,
			handleStyle: { 'border': '1px solid #000', 'background-color': '#ccc' , opacity: 0.75  }
		},

		initialize: function(img, options){

			this.img = document.id(img);
			if(this.img.get('tag') != 'img') return false;

			var coords = this.img.getCoordinates();
			this.container = new Element('div',{
				'styles': { 'position': 'relative', 'margin': '0 auto', 'width': coords.width, 'height': coords.height} // 'background': 'url('+this.img.get('src')+') no-repeat'
			}).inject(this.img,'after');

			this.img.setStyle('display','none');

			options.p = this.container;

			this.imgClip = new Element('img',{
				'src': this.img.get('src'),
				styles: { 'position': 'absolute', 'top': 0, 'left': 0, 'width': coords.width, 'height': coords.height, 'padding': 0, 'margin': 0, 'z-index': 1 }
			}).inject(this.container /*document.body*/);

			this.crop = new Element('img',{
				'src': this.img.get('src'),
				styles: { 'position': 'absolute', 'top': 0, 'left': 0, 'width': coords.width, 'height': coords.height, 'padding': 0, 'margin': 0, 'z-index': this.options.zindex-1 }
			}).inject(this.container /*document.body*/);

			this.parent(options);

			this.binds.handleMove = this.handleMove.bind(this);
			this.binds.handleEnd = this.handleEnd.bind(this);
			this.binds.handles = {};

			this.handles = {}; // stores resize handler elements
			// important! this setup a matrix for each handler, patterns emerge when broken into 3x3 grid. Faster/easier processing.
			this.handlesGrid = { 'NW':[0,0], 'N':[0,1], 'NE':[0,2], 'W':[1,0], 'E':[1,2], 'SW':[2,0], 'S':[2,1], 'SE':[2,2] };
			// this could be more elegant!
			['NW','N','NE','W','E','SW','S','SE'].each(function(handle){
				var grid = this.handlesGrid[handle]; // grab location in matrix
				this.binds.handles[handle] = function(event){
					this.handleStart(handle, grid[0], grid[1], event); // bind
				}.bind(this);

				this.handles[handle] = new Element("div", {
				'styles': Object.merge({ 'position': 'absolute',
							'display': 'block',
							'visibility': 'hidden',
							'width': this.options.handleSize,
							'height': this.options.handleSize,
							'overflow': 'hidden',
							'cursor': (handle.toLowerCase()+'-resize'),
							'z-index': this.options.zindex+2
						},this.options.handleStyle)
				}).inject(this.box,'bottom');
			},this);

			this.binds.drag = function(event){
				this.handleStart('DRAG', 1, 1, event);
			}.bind(this);

			this.attach();

			this.setDefault();

			this.fireEvent('init');

			return this;
		},

		attach: function(){
			this.trigger.addEvent('mousedown', this.binds.start);
			this.overlay.addEvent('mousedown', this.binds.drag);

			['NW','N','NE','W','E','SW','S','SE'].each(function(handle){
				this.handles[handle].addEvent('mousedown', this.binds.handles[handle]);
			}, this);

			this.detached = false;
		},

		detach: function(){
			if (this.active) this.handleEnd();

			this.trigger.removeEvent('mousedown', this.binds.start);
			this.overlay.removeEvent('mousedown', this.binds.drag);

			['NW','N','NE','W','E','SW','S','SE'].each(function(handle){
				this.handles[handle].removeEvent('mousedown', this.binds.handles[handle]);
			}, this);

			this.detached = true;
		},

		setDefault: function(){
			if(!this.options.preset) return this.resetCoords();
			this.getContainCoords();
			this.getRelativeOffset();
			var c = this.coords.container, d = this.options.preset;
			this.coords.start = { x: d[0], y: d[1]};
			this.active = true;
			this.move({ page: { x: d[2]+this.offset.left, y: d[3]+this.offset.top}});
			this.active = false;

			return this;
		},

		setCoords: function(coords){
			// coords => x, y, w, h

			coords.each(function(coord, i){
				coords[i] = coords[i].toInt();
			});

			this.getContainCoords();
			this.getRelativeOffset();
			var c = this.coords.container;
			this.coords.start = { x: coords[0], y: coords[1]};
			this.active = true;
			this.move({ page: { x: coords[2] + coords[0] + this.offset.left, y: coords[3] + coords[1] + this.offset.top}});
			this.active = false;

			return this;
		},

		handleStart: function(handle, row, col, event){
			this.getContainCoords();
			this.getRelativeOffset();

			this.currentHandle = { 'handle': handle, 'row': row, 'col': col}; // important! used for easy matrix transforms.
			document.addEvents({'mousemove': this.binds.handleMove, 'mouseup': this.binds.handleEnd});
			// had to merge because we don't want to effect the class instance of box. we want to record it
			event.page.y -= this.offset.top; event.page.x -= this.offset.left;
			this.coords.hs = { 's': event.page, 'b': Object.merge(Object.clone(this.coords.box)) }; // handler start (used for 'DRAG')
			this.active = true;

			if (this.scroller) this.scroller.start().attach();
		},

		handleMove: function(event){
			var box = this.coords.box, c = this.coords.container, m = event.page, cur = this.currentHandle, s = this.coords.start;
			m.y -= this.offset.top; m.x -= this.offset.left;
			if(cur.handle == 'DRAG'){ // messy? could probably be optimized.
				var hs = this.coords.hs, xm = m.x - hs.s.x, ym = m.y - hs.s.y, diff;
				box.y[0] = hs.b.y[0] + ym; box.y[1] = hs.b.y[1] + ym;
				box.x[0] = hs.b.x[0] + xm; box.x[1] = hs.b.x[1] + xm;
				if((diff = box.y[0] - c.y[0]) < 0) { box.y[0] -= diff; box.y[1] -= diff; } // constrains drag North
				if((diff = box.y[1] - c.y[1]) > 0) { box.y[0] -= diff; box.y[1] -= diff; } // constrains drag South
				if((diff = box.x[0] - c.x[0]) < 0) { box.x[0] -= diff; box.x[1] -= diff; } // constrains drag West
				if((diff = box.x[1] - c.x[1]) > 0) { box.x[0] -= diff; box.x[1] -= diff; } // constrains drag East

				this.removeDOMSelection();

	 			return this.refresh();
			}

			// handles flipping ( nw handle behaves like a se when past the orgin )
			if(cur.row == 0 && box.y[1] < m.y){ cur.row = 2; } 		// fixes North passing South
			if(cur.row == 2 && box.y[0] > m.y){ cur.row = 0; } 		// fixes South passing North
			if(cur.col == 0 && box.x[1] < m.x){ cur.col = 2; } 		// fixes West passing East
			if(cur.col == 2 && box.x[0] > m.x){ cur.col = 0; } 		// fixes East passing West

			if(cur.row == 0 || cur.row == 2){ // if top or bottom row ( center e,w are special case)
				s.y = (cur.row) ? box.y[0]: box.y[1]; 				// set start.y opposite of current direction ( anchor )
				if(cur.col == 0){ s.x = box.x[1]; } 				// if West side anchor East
				if(cur.col == 1){ s.x = box.x[0]; m.x = box.x[1]; } // if center lock width
				if(cur.col == 2){ s.x = box.x[0]; } 				// if East side anchor West
			}

			if(!this.options.ratio){ // these handles only apply when ratios are not in effect. center handles don't makes sense on ratio
				if(cur.row == 1){ // sanity check make sure we are dealing with the right handler
					if(cur.col == 0){ s.y = box.y[0]; m.y = box.y[1]; s.x = box.x[1]; }		// if West lock height anchor East
					else if(cur.col == 2){ s.y = box.y[0]; m.y = box.y[1]; s.x = box.x[0]; }// if East lock height anchor West
				}
			}

			m.y += this.offset.top; m.x += this.offset.left;
			this.move(event); // now that we manipulated event pass it to move to manage.

			return this;
		},

		handleEnd: function(event){
			document.removeEvents({'mousemove': this.binds.handleMove, 'mouseup': this.binds.handleEnd});
			this.active = false;
			this.currentHandle = false;
			if(this.options.min && (this.coords.w < this.options.min[0] || this.coords.h < this.options.min[1])){
				if(this.options.preset) this.setDefault();
				else this.resetCoords();
			}

			if (this.scroller) this.scroller.stop();
		},

		end: function(event){
			if(!this.parent(event)) return false;
			if(this.options.min && (this.coords.w < this.options.min[0] || this.coords.h < this.options.min[1])){
				this.setDefault();
			}

			if (this.scroller) this.scroller.stop();

			return this;
		},

		resetCoords: function(){
			this.parent();
			this.coords.box = { x: [0,0], y: [0,0]};
			this.hideHandlers();
			this.crop.setStyle('clip', 'rect(0px 0px 0px 0px)');
		},

		showHandlers: function(){
			var box = this.coords.box;

			if(this.options.min && (this.coords.w < this.options.min[0] || this.coords.h < this.options.min[1])) this.hideHandlers();

			else {
				var tops = [], lefts = [], pxdiff = (this.options.handleSize / 2)+1; // used to store location of handlers
				for(var cell = 0, cells = 2; cell <= cells; cell++ ){  // using matrix again
					tops[cell] = ( (cell == 0) ? 0: ((cell == 2) ? box.y[1] - box.y[0]: (box.y[1] - box.y[0])/2  ) ) - pxdiff;
					lefts[cell] = ( (cell == 0) ? 0: ((cell == 2) ? box.x[1] - box.x[0]: (box.x[1] - box.x[0])/2 ) ) - pxdiff;
				}

				for(var handleID in this.handlesGrid){ // get each handler's matrix location
					var grid = this.handlesGrid[handleID], handle = this.handles[handleID];
					if(!this.options.ratio || (grid[0] != 1 && grid[1] != 1)){ // if no ratio or not N,E,S,W show
						if(this.options.min && this.options.max){
							if((this.options.min[0] == this.options.max[0]) && (grid[1] % 2) == 0) continue; // turns off W&E since width is set
							if(this.options.min[1] == this.options.max[1] && (grid[0] % 2) == 0) continue;  // turns off N&S since height is set
						}
						handle.setStyles({'visibility': 'visible', 'top': tops[grid[0]], 'left': lefts[grid[1]] }); // just grab from grid
					}
				}
		   }
		},

		hideHandlers: function(){
			for(handle in this.handles){ this.handles[handle].setStyle('visibility','hidden'); }
		},

		refresh: function(fast){
			this.parent(fast);
			var box = this.coords.box, cc = this.coords.container;

			if(Browser.Engine.trident && Browser.Engine.version < 5 && this.currentHandle && this.currentHandle.col === 1)
					this.overlay.setStyle('width' , '100.1%').setStyle('width','100%');

			this.crop.setStyle('clip' , 'rect('+(box.y[0])+'px '+(box.x[1])+'px '+(box.y[1])+'px '+(box.x[0])+'px )' );
			if (!this.options.noHandlers) this.showHandlers();
		},

		refreshClip: function(width, height, fast){
			var coords = {width: width, height: height};

			if (!width || !height) coords = {width: this.img.get('width'), height: this.img.get('height')};

			$$([this.imgClip, this.crop]).set('width', coords.width).set('height', coords.height);
			$$([this.container, this.imgClip, this.crop, this.crop.getParent()]).setStyles({'width': coords.width, 'height': coords.height});

			this.refresh(fast);
		}

	});

})());
