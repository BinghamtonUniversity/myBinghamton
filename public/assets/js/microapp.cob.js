Cobler.types.Microapp = function(container) {
	function load(data) {
		$('body').append('<style>'+data.css+'</style>');
		// templates['microapp_'+data.name] =  Hogan.compile(data.template);
		var parsed_template = JSON.parse(data.template);
		// this.raw_template = parsed_template[0].content;
		this.partials = {};
		for(var i in parsed_template){
			this.partials[parsed_template[i].name] = parsed_template[i].content;
		}
		var fields = $.extend(true, {},this.fields);
		fields.Microapp.enabled = false;

		this.userEdit = [];
		this.userEditFields = [];
		if(data.options && data.options.fields && data.options.fields.length){
			for(var i in data.options.fields){
				fields[data.options.fields[i].name] = data.options.fields[i];
				if(data.options.fields[i].userEdit === true || data.options.fields[i].userEdit === 'true'){
					this.userEdit.push(data.options.fields[i]);
					this.userEditFields.push(data.options.fields[i].name);
				}
			}
		}
		set({user_edit: (this.userEditFields.length > 0)})
		fields = _.reduceRight(fields, function(result, field){
			result.push(field.name);
		 	return result; 
		}, []);

		var temp = this.get();
		data.data.options = _.pick(temp, fields);
		data.data.tags = tags;

		try{
			var temp = JSON.parse(data.script);
			data.script = temp;
		}catch(e){}
		if(typeof data.script == 'object'){
			data.script = _.pluck(data.script, 'content').join(';');
		}


		var myStuff = (function(data, $el, script){
			try{			
				eval('function pf(){'+script+';return this;}');
				return pf.call({data:data});
			}catch(e) {
				console.error(e);
				return undefined;
			}
		})(data.data, this.$el, data.script)
		if(myStuff !== undefined){
			data.data= myStuff.data;
			data.callback = myStuff.callback;
		}
		if(typeof data.options  == 'object' && data.options !== null){
			this.fields['Custom Options'].fields = data.options.fields;

			this.fields['Custom Options'].show = (typeof data.options !== 'undefined' && data.options !== null && typeof data.options.fields !== 'undefined' && data.options.fields !== null && data.options.fields.length >0);
		}else{
			this.fields['Custom Options'].show = false;
		}
		this.set({loaded: {callback: data.callback, data: data.data, data_type: data.data_type, name: data.name} });
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
		function post(name, data, callback, error) {
			var callback = callback || defaultCallback;
			$.ajax({
			  type: "POST",
			  url: '/post_microapp/' + item.microapp + '/' + name,
			  data: $.extend({}, item.loaded.data.options, {request:data||{}}),
			  success: callback.bind(this),
			  error: (error || function(){}).bind(this)
			});
		}
		function get(name, data, callback, error) {
			var callback = callback || defaultCallback;
			$.ajax({
			  type: "POST",
			  url: '/get_microapp/' + item.microapp + '/' + name,
			  data: $.extend({}, item.loaded.data.options, {request:data||{}}),
			  success: callback.bind(this),
			  error: (error || function(){}).bind(this)
			});
		}
		function put(name, data, callback, error) {
			var callback = callback || defaultCallback;
			$.ajax({
			  type: "POST",
			  url: '/put_microapp/' + item.microapp + '/' + name,
			  data: $.extend({}, item.loaded.data.options, {request:data||{}}),
			  success: callback.bind(this),
			  error: (error || function(){}).bind(this)
			});
		}
		function mydelete(name, data, callback, error) {
			var callback = callback || defaultCallback;
			$.ajax({
			  type: "POST",
			  url: '/delete_microapp/' + item.microapp + '/' + name,
			  data: $.extend({}, item.loaded.data.options, {request:data||{}}),
			  success: callback.bind(this),
			  error: (error || function(){}).bind(this)
			});
		}

		function router(verb, name, callback) {
  		var temp = _.partial(this.microapp[verb], name);
  		return function(model, lateCallback){
  			if(typeof model.toJSON !== 'undefined'){model = model.toJSON();}
  			temp(model, callback || lateCallback || function(){})
  		};
		}
		function form_get() {
			return this.inline.toJSON();
		}
		function form_clear() {
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
		function refetch() {
			this.fetch(function(newData) {
				item.loaded.data = $.extend(true, this.data, newData.data || {});
				this.ractive.set($.extend(true, {}, item.loaded.data ));
				this.app.trigger('updated')
				this.app.trigger('fetch')
			});
		}
		function redraw() {
			if(typeof this.inline == 'object' && this.inline instanceof Berry){
				this.inline.destroy();
			}
			this.ractive.teardown();
			this.draw();
		}
		function refresh() {
			this.set({loaded:false});
			this.container.update(this.get(), this);
			this.app.trigger('refreshed')
		}
		function update(newData) {
			item.loaded.data = $.extend(true, this.data, newData || {});
			this.ractive.set($.extend(true, {}, item.loaded.data ));
			this.app.trigger('updated')
		}
		function click(selector, callback){
			// this.$el.find(selector).off('click', callback);
			this.$el.find(selector).on('click', callback.bind(this));
		}

		this.events = {initialize: []};
		this.addSub = Berry.prototype.addSub;

		return {
			post: post.bind(this),
			get: get.bind(this),
			put: put.bind(this),
			delete: mydelete.bind(this),
			router: router.bind(this),

			defaultCallback: defaultCallback,

			options: {
				get: form_get.bind(this),
				clear: form_clear.bind(this),
				set: form_set.bind(this),
				modal: modal.bind(this)
			},
			refetch: refetch.bind(this),
			redraw: redraw.bind(this),
			refresh: refresh.bind(this),
			update: update.bind(this),
			click: click.bind(this),
			on: Berry.prototype.on.bind(this),
			off: Berry.prototype.off.bind(this),
			trigger: Berry.prototype.trigger.bind(this),
			$el: this.$el

		}
	}

	function render(){
		var template = 'widgets_microapp';
		if(!item.container && this.container.owner.options.disabled){template ='widgets_empty_microapp';}
		return templates[template].render($.extend({title: 'Microapp'},item),templates)
	}

	function draw(){
		this.ractive = new Ractive({el: this.$el.find('.collapsible')[0], template: this.partials.main, data: $.extend(true, {}, item.loaded.data), partials: this.partials});

		fields.Microapp.enabled = false;

		this.$el.find('[data-toggle="tooltip"]').tooltip();
		this.$el.find('[data-toggle="popover"]').popover();

		if(this.$el.find('[data-inline]').length > 0 && this.userEdit.length > 0){
		this.inline = this.$el.find('.collapsible').berry({attributes:item,renderer:'inline', fields: this.userEdit, legend: 'Edit '+this.type}).on('save',function() {

			this.set(this.inline.toJSON());
			this.app.trigger('options')

			this.ractive.set($.extend(true, {}, this.get().loaded.data, {options: this.inline.toJSON()} ));
			save();
		},this);
		this.$el.find('form').on('submit', $.proxy(function(e){
			e.preventDefault();
		}, this) );

		this.$el.find('[data-inline="submit"]').on('click', $.proxy(function(){
			this.inline.trigger('save');
		}, this) );
		}
		if(typeof item.loaded.callback === 'function') {
			this.data = item.loaded.data;
			if(typeof this.app == 'undefined'){
				this.app = buildContextTools.call(this)
			}
			item.loaded.callback.call(this);
		}
	}
	function fetch(callback, data){
		$.ajax({
			url      : '/get_microapp/' + item.microapp,// + '?rand='+ Berry.getUID(),
			dataType : 'json',
			type: 'POST',
			data: this.toJSON({editor:true}),
			error: function (data) {
				if(typeof item.loaded === 'undefined'){
					if(item.container){this.$el.find('.collapsible').html('Failed to load');}
				}else 
				if(typeof item.loaded.data !== 'undefined') {
					this.$el.find('.collapsible').html('Timed out attempting to load data from an external data source');
				}
			}.bind(this),
			success  : callback.bind(this)
		});
	}
	function initialize(el) {
		this.$el = $(el);
		if(typeof item.microapp == 'undefined')return;
		if(typeof item.loaded !== 'undefined' && (typeof item.loaded.data !== 'undefined' || item.loaded.data_type == 'None' )) {
			this.draw();
		}else if(!item.loaded && typeof microapps[item.microapp] !== 'undefined'){
			load.call(this, microapps[item.microapp]);
		}else if(!item.loaded && !microapps[item.microapp]){
			this.fetch(load);
		}
	}
	function get() {
		item.widgetType = 'Microapp';
		return item;
	}
	function toJSON(opts){
		if(opts.editor){
			var temp = $.extend({},get());
			delete temp.loaded;
			return temp;
		}
		return _.pick(item, _.union(['guid', 'collapsed'], _.map(this.userEditFields, function(key){return key.toLowerCase()})))
	}
	function set(newItem) {
		$.extend(true, item, newItem);
		if(typeof this.app !== 'undefined'){
			this.app.trigger('set');
		}
		if(typeof this.inline == 'object' && this.inline instanceof Berry){
			this.inline.populate(item);
		}
	}

	var item = {
		user_edit: false,
		loaded:false
	};
	var fields = {
		Microapp: {type: 'select', choices: '/microapps?group_id='+groupID, key: 'name'},
		Title: {},
		Container: {label: "Container?", type: 'checkbox'},
		"Custom Options": {show:false}
	}

	return {
		fields: fields,
		render: render,
		toJSON: toJSON,
		microapp: function(){},
		edit: berryEditor.call(this, container),
		get: get,
		set: set,
		fetch: fetch,
		initialize: initialize,
		draw: draw,
		container: container,
	}
}