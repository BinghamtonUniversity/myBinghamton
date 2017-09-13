$(function(){
	widget_factory.register({
		type: 'Service',
		defaults: {
			path:'',
			user_edit: false
		},
		view: {
			scratch: function(values){
				if(typeof values !== 'undefined'){
					this.model.attributes.scratch =	$.extend({}, this.model.attributes.scratch, values);
					this.model.trigger('publish');
					this.render();
				}
				return this.model.attributes.scratch

			},
			template: 'widgets_empty_service',
			load: function (data) {

				$('body').append('<style>'+data.css+'</style>');
				templates['service_'+data.name] =  Hogan.compile(data.template);

				this.model.schema = $.extend({},this.model.original);
				this.model.schema.Service.enabled = false;

				this.model.userEdit = [];
				if(data.form && data.form.length){
					for(var i in data.form){
						this.model.schema[data.form[i].name] = data.form[i];
						if(data.form[i].userEdit){
							this.model.userEdit.push(data.form[i].name);
						}
					}
				}

				var fields = _.reduceRight(this.model.schema, function(result, field){
					result.push(field.name);
				 return result; 
				}, []);

				data.data.form = _.pick(this.model.attributes, fields);
				data.data.scratch = this.model.attributes.scratch;
				data.data.tags = tags;


		try{
			var temp = JSON.parse(data.script);
			data.script = temp;
		}catch(e){}
		if(typeof data.script == 'object'){
			data.script = _.pluck(data.script, 'content').join(';');
		}

				var myStuff = (function(data, $el, script){
					var wf=null;
					eval('function pf(){'+script+';}');
					try{
						return pf.call(data);
					}catch(e) {
						return undefined;
					}
				})(data.data, this.$el, data.script)
				if(myStuff !== undefined){
					data.data = myStuff;
				}

				this.model.set({user_edit: (this.model.userEdit.length > 0)});
				// this.model.trigger('publish');

				this.model.set({loaded: {data: data.data, data_type: data.data_type, name: data.name, path:(this.model.path || data.path)} });
				this.trigger('loaded');

			},

			// service: ,
			render: function(){
				if(!this.model.attributes.loaded && !services[this.model.attributes.service]){
					$.ajax({
						url      : '/get_service/' + this.model.attributes.service,// + '?rand='+ Berry.getUID(),
						dataType : 'json',
						type: 'POST',
						data: this.model.attributes,
						// async: false,
						error    : $.proxy(function (data) {
							if(typeof this.model.attributes.loaded === 'undefined'){
								if(this.model.attributes.container){this.$el.find('.collapsible').html('Failed to load');}
							}else 
							if(typeof this.model.attributes.loaded.data !== 'undefined') {
								this.$el.find('.collapsible').html('Timed out attempting to load data from an external data source');
							}
						}, this),
						success  : $.proxy(this.load, this)
					});
				}else{
					if(!this.model.attributes.loaded && typeof services[this.model.attributes.service] !== 'undefined'){
						this.load(services[this.model.attributes.service]);
						//this.model.attributes.loaded = services[this.model.attributes.service];
					}else{
						if(typeof this.model.attributes.loaded.data !== 'undefined' || this.model.attributes.loaded.data_type == 'None' ) {
							this.$el.find('.collapsible').html(templates['service_'+this.model.attributes.loaded.name].render($.extend(true, {}, this.model.attributes.loaded.data, {scratch: this.model.attributes.scratch}),templates));
							this.$el.find('[data-toggle="tooltip"]').tooltip();
							this.$el.find('[data-toggle="popover"]').popover();
							this.inline = this.$el.find('.collapsible').berry({renderer:'inline', model: this.model, fields: this.model.userEdit, legend: 'Edit '+this.type}).on('saved',function() {
									// if(!this.options.model.hasChanged()) {
										this.options.model.trigger('publish');
									// }
								});
								this.$el.find('form').on('submit', $.proxy(function(e){
									e.preventDefault();
									this.inline.trigger('save');
									this.model.attributes.loaded = false;
									this.render();
								}, this) );

							this.$el.find('[data-inline="submit"]').on('click', $.proxy(function(){
								this.inline.trigger('save');
								this.model.attributes.loaded = false;
								this.render();
							}, this) );
							if(typeof this.model.attributes.loaded.data.callback === 'function'){
								this.data = this.model.attributes.loaded.data;
								this.model.attributes.loaded.data.callback.call(this);
							}
						}
					}

				}
			},
			initialize: function() {
				// this.data = {};
				this.service = function() {
					function callback (data){
							if(data.error){
				          if (data.error.message) {
		                modal({title: "ERROR",content: data.error.message, modal:{header_class:"bg-danger"}});
		              } else {
		                modal({title: "ERROR",content: data.error, modal:{header_class:"bg-danger"}});
		              }
	              }else{
									//this.model.set({name:data});
									this.model.attributes.loaded.data[name] = data;
									// this.model.trigger('changed');
									this.render();
	              }
	            }
					function post(name, data, callback){
						var callback = callback || callback;
						$.post('/post_service/'+this.model.attributes.service+'/'+name, $.extend({}, this.model.attributes.loaded.data.form, {postable:data||{}}), callback.bind(this));
					}
					function get(name, data, callback){
						var callback = callback || callback;
						
						$.post('/get_service/'+this.model.attributes.service+'/'+name, $.extend({}, this.model.attributes.loaded.data.form, {postable:data||{}}), callback.bind(this));
					}
    			function router(verb, route, callback){
        		var temp = _.partial(this.service[verb], route);
        		return function(model, lateCallback){
        			if(typeof model.toJSON !== 'undefined'){model = model.toJSON();}
        			temp(model, callback || lateCallback || function(){})
        		};
    			}
					function form_get(){
						return this.inline.toJSON();
					}
					function form_clear(){
						return this.inline.clear();
					}
					function form_set(data) {
						return this.inline.load(data);
					}
					function modal(options, callback) {


						var fields = _.filter(this.model.schema, function(item){
							return item.userEdit;
						})

						this.modal = $().berry($.extend(true, {}, {fields: fields}, options)).on('save', function(){
							callback.call(this, this.modal.toJSON());
						}, this).on('save', function(){this.trigger('saved');});
						// return this.inline.load(data);
					}
					function refresh(){
						this.model.attributes.loaded = false;

						this.inline.destroy();
						this.$el.find('.collapsible').html('<center><i class="fa fa-spinner fa-spin" style="font-size:60px;margin:40px auto;color:#eee"></i></center>');
						this.once('loaded', this.render);

						this.render();
					}
					function redraw(){
						this.model.attributes.loaded.data = this.data;
						this.render();
					}
					function scratch(values){
						if(typeof values !== 'undefined'){
							this.model.attributes.scratch =	$.extend({}, this.model.attributes.scratch, values);
							this.model.trigger('publish');
							this.render();
						}
						return this.model.attributes.scratch

					}
					function click(selector, callback){
						this.$el.find(selector).on('click', $.proxy(callback, this));
					}
					return {
						post: post.bind(this),
						get: get.bind(this),
						router: router.bind(this),
						defaultCallback: callback,
						// get: get.bind(this),
						form :{
							get: form_get.bind(this),
							clear: form_clear.bind(this),
							set: form_set.bind(this),
							modal: modal.bind(this)
						},
						refresh: refresh.bind(this),
						redraw: redraw.bind(this),
						scratch: scratch.bind(this),
						click: click.bind(this)
						// data: function(){return this.model.attributes.loaded.data}.bind(this)
					}
				}.call(this)

				if(this.model.attributes.container){this.template ='widgets_service';}
				this.model.schema = $.extend({},this.model.original);
				this.model.schema.Service.enabled = !(this.model.attributes.Service);
				this.autoElement();
			}
		},
		edit:{
			template: 'widgets_service',
			render: function() {

				if(!this.model.attributes.loaded) {
					$.ajax({
						url      : '/get_service/' + this.model.attributes.service,// + '?rand='+ Berry.getUID(),
						dataType : 'json',
						type: 'POST',
						data: this.model.attributes,

						error    : $.proxy(function (data) {
							if(typeof this.model.attributes.loaded === 'undefined'){
								if(this.model.attributes.container){this.$el.find('.collapsible').html('Failed to load');}
							}else 
							if(typeof this.model.attributes.loaded.data !== 'undefined') {
								this.$el.find('.collapsible').html('Timed out attempting to load data from an external data source');
							}
						}, this),
						success  : $.proxy(function (data) {				
							$('body').append('<style>'+data.css+'</style>');
							templates['service_'+data.name] =  Hogan.compile(data.template);
							this.model.schema = $.extend({},this.model.original);
							this.model.schema.Service.enabled = false;
							this.model.userEdit = [];
							if(data.form && data.form.length){
								for(var i in data.form){
									this.model.schema[data.form[i].name] = data.form[i];
									if(data.form[i].userEdit){
										this.model.userEdit.push(data.form[i].name);
									}
								}
							}

							var fields = _.reduceRight(this.model.schema, function(result, field){
								result.push(field.name);
								return result;
							}, []);
							data.data.form = _.pick(this.model.attributes, fields);
							data.data.scratch = this.model.attributes.scratch;
							data.data.tags = tags;
							data.data.service = 'service object';


							var myStuff = (function(data, $el, script){
								var wf=null;
								eval('function pf(){'+script+'}');
								try{
									return pf.call(data);
								}catch(e){
									return undefined;
								}
							})(data.data, this.$el, data.script)
							if(myStuff !== undefined){
								data.data = myStuff;
							}

							this.model.set({user_edit: (this.model.userEdit.length > 0), loaded: {data: data.data, data_type: data.data_type, name: data.name, path:(this.model.path || data.path)} })

							this.model.trigger('publish');

						}, this)
					});
				}else{
					if(typeof this.model.attributes.loaded.data !== 'undefined' || this.model.attributes.loaded.data_type == 'None' ) {
						this.$el.find('.collapsible').html(templates['service_'+this.model.attributes.loaded.name].render(this.model.attributes.loaded.data));
						this.$el.find('[data-toggle="tooltip"]').tooltip();
						this.$el.find('[data-toggle="popover"]').popover();
						if(typeof this.model.attributes.loaded.data.callback === 'function'){
							this.model.attributes.loaded.data.callback.call(this);
						}
					}
				}
			},
			// initialize: function() {
			// 	this.model.on('change:service',function(){
			// 		this.model.attributes.loaded = false;
			// 	}, this);
			// 	this.autoElement();
			// }
		},
		model: {
			original:{
				Service: {type: 'select', choices: '/services?group_id='+groupID, key: 'name'},
				Title: {},
				Container: {label: "Container?", type: 'checkbox'},
			}
		},
	});
});
