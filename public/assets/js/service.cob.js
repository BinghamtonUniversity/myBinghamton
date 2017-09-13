Cobler.types.Service = function(container) {
	function load(data) {
		$('body').append('<style>'+data.css+'</style>');
		templates['service_'+data.name] =  Hogan.compile(data.template);
		// this.raw_template = data.template;
		var fields = $.extend(true, {},this.fields);
		fields.Service.enabled = false;

		this.userEdit = [];
		this.userEditFields = [];

		if(data.form && data.form.length){
			for(var i in data.form){
				fields[data.form[i].name] = data.form[i];
				if(data.form[i].userEdit){
					this.userEdit.push(data.form[i]);
					this.userEditFields.push(data.form[i].name);
				}
			}
		}
		set({user_edit: (this.userEditFields.length > 0)})
		fields = _.reduceRight(fields, function(result, field){
			result.push(field.name);
		 	return result; 
		}, []);

		var temp = this.get();
		data.data.form = _.pick(temp, fields);
		data.data.scratch = temp.scratch;
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
		if(typeof data.form  == 'object'){
			this.fields['Custom Options'].fields = data.form;

			this.fields['Custom Options'].show = (typeof data.form !== 'undefined' && data.form !== null && data.form.length >0);
		}else{
			this.fields['Custom Options'].show = false;
		}
		this.set({loaded: {data: data.data, data_type: data.data_type, name: data.name} });
		this.container.update(get(),this);
		this.container.deactivate()
	}

	function buildContextTools() {
		function defaultCallback (data){
			if(data.error){
        if (data.error.message) {
          modal({title: "ERROR",content: data.error.message, modal:{header_class:"bg-danger"}});
        } else {
          modal({title: "ERROR",content: data.error, modal:{header_class:"bg-danger"}});
        }
      }else{
				item.loaded.data[name] = data;
				this.container.update(this.get(), this)
      }
    }
		function post(name, data, callback){
			var callback = callback || defaultCallback;
			$.post('/post_service/'+item.service+'/'+name, $.extend({}, item.loaded.data.form, {resource:data||{}}), callback.bind(this));
		}
		function get(name, data, callback){
			var callback = callback || defaultCallback;
			
			$.post('/get_service/'+item.service+'/'+name, $.extend({}, item.loaded.data.form, {resource:data||{}}), callback.bind(this));
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
			var fields = _.filter(this.fields, function(item){
				return item.userEdit;
			})

			this.modal = $().berry($.extend(true, {}, {fields: fields}, options)).on('save', function(){
				callback.call(this, this.modal.toJSON());
			}, this).on('save', function(){this.trigger('saved');});
		}
		function refresh(){
			this.set({loaded:false});
			this.container.update(this.get(),this);
		}
		function redraw(){
			item.loaded.data = this.data;
			// this.ractive.set($.extend(true, {}, item.loaded.data, {scratch: item.scratch}));
			this.container.update(this.get(),this);
		}
		function scratch(values){
			var temp = this.get()
			if(typeof values !== 'undefined'){
				this.container.update({scratch:$.extend({}, temp.scratch, values)},this);
			}
			return temp.scratch
		}
		function click(selector, callback){
			this.$el.find(selector).on('click', $.proxy(callback, this));
		}
		return {
			post: post.bind(this),
			get: get.bind(this),
			router: router.bind(this),
			defaultCallback: defaultCallback,
			form :{
				get: form_get.bind(this),
				clear: form_clear.bind(this),
				set: form_set.bind(this),
				modal: modal.bind(this)
			},
			refresh: refresh.bind(this),
			redraw: redraw.bind(this),
			scratch: scratch.bind(this),
			click: click.bind(this),
		}
	}

	function render(){
		var template = 'widgets_service';
		if(!item.container && this.container.owner.options.disabled){template ='widgets_empty_service';}
		return templates[template].render($.extend({title: 'Service'},item),templates)
	}

	function initialize(el) {
		this.$el = $(el);
		if(typeof item.service == 'undefined')return;


		if(typeof item.loaded !== 'undefined' && (typeof item.loaded.data !== 'undefined' || item.loaded.data_type == 'None' )) {
				this.$el.find('.collapsible').html(templates['service_'+item.loaded.name].render($.extend(true, {}, item.loaded.data, {scratch: item.scratch}),templates));

	   	// this.ractive = new Ractive({el: this.$el.find('.collapsible')[0], template: this.raw_template, data: $.extend(true, {}, item.loaded.data, {scratch: item.scratch})});

			fields.Service.enabled = false;

			this.$el.find('[data-toggle="tooltip"]').tooltip();
			this.$el.find('[data-toggle="popover"]').popover();

			if(this.$el.find('[data-inline]').length > 0 && this.userEdit.length > 0){
			this.inline = this.$el.find('.collapsible').berry({attributes:item,renderer:'inline', fields:this.userEdit, legend: 'Edit '+this.type}).on('save',function() {
				this.set(this.inline.toJSON());
				this.container.update({loaded: false}, this)
				save();
			},this);
			this.$el.find('form').on('submit', $.proxy(function(e){
				e.preventDefault();
				this.inline.trigger('save');
			}, this) );

			this.$el.find('[data-inline="submit"]').on('click', $.proxy(function(){
				this.inline.trigger('save');
			}, this) );
			}
			if(typeof item.loaded.data.callback === 'function') {
				this.data = item.loaded.data;
				this.service = buildContextTools.call(this)
				item.loaded.data.callback.call(this, this.data);
			}
		}else if(!item.loaded && typeof services[item.service] !== 'undefined'){
			load.call(this, services[item.service]);
		}else if(!item.loaded && !services[item.service]){
			$.ajax({
				url      : '/get_service/' + item.service,// + '?rand='+ Berry.getUID(),
				dataType : 'json',
				type: 'POST',
				data: item,
				error: function (data) {
					if(typeof item.loaded === 'undefined'){
						if(item.container){this.$el.find('.collapsible').html('Failed to load');}
					}else 
					if(typeof item.loaded.data !== 'undefined') {
						this.$el.find('.collapsible').html('Timed out attempting to load data from an external data source');
					}
				}.bind(this),
				success  : load.bind(this)
			});
		}
	}
	function get() {
		item.widgetType = 'Service';
		return item;
	}
	function toJSON(opts){
		if(opts.editor){
			var temp = get();
			delete temp.loaded;
			return temp;
		}
		return _.pick(item, _.union(['guid', 'collapsed', 'scratch'], _.map(this.userEditFields, function(key){return key.toLowerCase()})))
	}
	function set(newItem) {
		$.extend(true, item, newItem);
	}

	var item = {
		user_edit: false,
		loaded:false
	};
	var fields = {
			Service: {type: 'select', choices: '/services?group_id='+groupID, key: 'name'},
			Title: {},
			Container: {label: "Container?", type: 'checkbox'},
			"Custom Options": {show:false}
	}

	return {
		fields: fields,
		render: render,
		toJSON: toJSON,
		service: function(){},
		edit: berryEditor.call(this, container),
		get: get,
		set: set,
		initialize: initialize,
		container: container,
	}
}