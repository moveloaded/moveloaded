
((function(){

	this.Job = new Class({

		Implements: [Options, Events],

		options:{
			url: '',
			id: null/*,
			onBeforeCreate: 	function(){},
			onCreate: 			function(response, id){},
			onBeforeReady: 		function(){},
			onReady: 			function(response, id){},
			onBeforeProcess:	function(){},
			onProcess:			function(response, id){},
			onBeforeStatus: 	function(){},
			onStatus: 			function(response, id){},
			onBeforePause: 		function(){},
			onPause: 			function(response, id){},
			onBeforeResume: 	function(){},
			onResume: 			function(response, id){},
			onBeforeCancel: 	function(){},
			onCancel: 			function(response, id){},
			onBeforeDelete: 	function(){},
			onDelete: 			function(response, id){},

			onComplete: 		function(){},
			onError: 			function(message){}*/
		},

		initialize: function(options){
			this.setOptions(options);
			this.id = this.options.id;
			this.queue = null;

			this.request = new Request({url: this.options.url, onSuccess: this.success.bind(this)});
			this.processRequest = new Request({url: this.options.url, onSuccess: this.success.bind(this)});

			['create', 'ready', 'process', 'status', 'pause', 'resume', 'cancel', 'delete'].each(function(action){
				this.job(action);
			}, this);

		},

		success: function(response){
			if (!JSON.validate(response)) return this.error('Invalid JSON response.');

			response = JSON.decode(response);

			if (response.status != 'success') return this.error(response.message);

			if (this.action == 'create') this.id = response.payload.job;

			this.fireEvent(this.action, [response, this.id]);

			if (this.queue){
				this[this.queue].delay(10, this);
				this.queue = null;

				return this;
			}

			this.done(response);

			return this;
		},

		start: function(){
			this.fireEvent('start', this.id);
		},

		complete: function(){
			this.fireEvent('complete', this.id);
		},

		done: function(response){
			this.fireEvent('done', [this.id, response]);
		},

		error: function(message){
			this.fireEvent('error', message);
		},

		job: function(action){
			this[action] = function(){
				if (!this.id && action != 'create'){
					this.queue = action;
					return this.create();
				}

				this.start();

				this.fireEvent('before' + action.capitalize());
				this.action = action;

				var data = {};
				data[RokGallery.ajaxVars.model] = 'job';
				data[RokGallery.ajaxVars.action] = action;

				if (action != 'create') data.params = JSON.encode({id: this.id});

				(action == 'process' ? this.request : this.processRequest)['send']({data: data});

				return this;
			};
		}.protect()
	});

})());
