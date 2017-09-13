//		widget_factoryJS 0.1.3
//		(c) 2011-2016 Adam Smallcomb
//		Licensed under the MIT license.
//		For all details and documentation:
//		https://github.com/Cloverstone/widget_factory
function widget_factory(options, obj){
	this.loading = false;

	this.getStructure = function(){

		if(!this.loading ){
			if(editor){
				newData = [];
				var list = this.$el.find('.column').each(function(index){
				column = [];
				$(this).find('.widget').each(function(){
					var temp = _.where(wf.widgets, {uuid: $(this).attr('id')});
					// var newTemp = _.clone(temp[0].model.attributes);
					// if(newTemp.loaded){delete newTemp.loaded;}

					// column.push(newTemp);
					var atts = temp[0].model.getAttributes();
					column.push(_.omit(atts, 'loaded'));

				})
				newData.push(column);
				});
				this.data = newData;
				pageData = newData;
				$.ajax(
					{
						url: '/community_pages/' + groupID +'/' + pageID, 
						data: {'content': JSON.stringify(newData)},
						method: 'PUT'
				});
			} else {
				newData = [];
				this.$el.find('.widget').each(function(){
					var temp = _.where(wf.widgets, {uuid: $(this).attr('id')});
					newData.push(_.pick(temp[0].model.attributes, _.union(['guid', 'collapsed', 'scratch'], _.map(temp[0].model.userEdit, function(key){return key.toLowerCase()})) ));
				});
				preferences = newData;
				$.ajax(
					{
						url: '/page_preference/' + pageID, 
						data: {'content': JSON.stringify(newData)},
						method: 'PUT'
				});
			}
		}
	};

	this.load = function(data, layout){

		this.data = data;
		for(var i in this.widgets){
			this.widgets[i].model.off();
			this.widgets[i].view.remove();
			this.widgets[i].edit.remove();
			delete this.widgets[i];
		}
		this.widgets =[];
		if($(".column").hasClass("ui-sortable")){
			$( ".column" ).sortable( "destroy" );
		}


		this.$el.html(this.layouts[(layout||0)].template);
		var columncount = -1;
		this.loading = true;
		for(var i in this.data ){
			if($('.c'+(columncount+1)).length){
				columncount++;
			}
			for( var j in this.data[i]) {
				if(!editor && typeof pagePreferences.content !== 'undefined'){
					this.add(this.data[i][j].widgetType, $.extend({}, this.data[i][j], _.where(JSON.parse(pagePreferences.content), {guid:this.data[i][j]['guid']})[0]), '.c'+columncount)
				}else{
					this.add(this.data[i][j].widgetType, this.data[i][j], '.c'+columncount)
				}
			}
		}

		//var selector = '.c0';
		if(editor){
			//for(var k = 1; k <= columncount; k++){selector += ', .c'+k;}
			$('.column').sortable({
				connectWith: '.column',
				cursor: 'move',
      	handle: ".panel-heading",
				items: ".widget:not(.locked)",
				placeholder: 'cb-placeholder',
				forcePlaceholderSize: true,
				axis: this.options.axis,
				stop: $.proxy(function(event, ui) {
					this.getStructure();
				}, this),
				cancel: '#cb-content li .cobler-li-content, #cb-content li.locked',
				receive: function(e, ui) {
					copyHelper = null;
				}
			});
		}
		this.loading = false;
	};

	this.add = function(name, attributes, target) {
		if(name in widget_factory.types){
			var widget = new widget_factory.types[name](this, (attributes || {}));
			if(widget.validate(widget.defaults, false)){
				this.widgets.push(widget);
				widget.container = target;
				var renderTemplate =  (widget.view.container || 'widgets_container');
				if(editor){
					renderTemplate = (widget.edit.container || 'widgets_edit_container');
				}
				widget.$el = $(widget_factory.render(renderTemplate, $.extend({title: widget.type , id: widget.uuid},widget.model.attributes)));
				widget.$el.on('click', '.actions .wf-edit', $.proxy(function(){
					if(editor){						
							if(this.modal){
								$().berry({name:'modal', model: this.model, fields: this.model.adminEdit, legend: 'Edit '+this.type}).on('cancel', function(){
									this.options.model.trigger('change');
								}).on('saved',function() {
									// if(!this.options.model.hasChanged()) {
										this.options.model.trigger('publish');
									// }
								});
							}else{
								this.$el.find('.collapsible').berry({name:'modal', model: this.model, fields: this.model.adminEdit, legend: false}).on('cancel', function(){
									this.options.model.trigger('change');
								}).on('saved',function() {
									// if(!this.options.model.hasChanged()) {
										this.options.model.trigger('publish');
									// }
								});
							}
					}else{
						$().berry({name:'modal', model: this.model, fields: this.model.userEdit, legend: 'Edit '+this.type}).on('saved',function() {
									// if(!this.options.model.hasChanged()) {
										this.options.model.trigger('publish');
									// }
								});
					}

					}, widget));

					widget.$el.on('click', '.actions .wf-manage', $.proxy(function(){
						$().berry({name: 'modal', model: this.model, legend: 'Visibility',flatten:true, fields:[
							{label: 'Device', name: 'device', type: 'select', value:'widget', choices: [{label: 'All', value:'widget'}, {label: 'Desktop Only', value:'hidden-xs hidden-sm'},{label: 'Tablet and Desktop', value:'hidden-xs'} ,{label: 'Tablet and Phone', value:'hidden-md hidden-lg'} ,{label: 'Phone Only', value:'visible-xs-block'} ] },
							{label: 'Allow Minimization', name: 'enable_min', type: 'checkbox'},
							{label: 'Limit to Group', name: 'limit', type: 'checkbox', show:  {test: function(){return composites.length >0;}} },
						// 	{label: 'Groups', name: 'group', type: 'select', choices: '/groups?composites='+groupID, key: 'name', reference: 'group_id', 'show': {
						// 		matches: {
						// 			name: 'limit',
						// 			value: true
						// 		}
						// 	}
						// },
						{legend: 'Groups', 'show': {
									matches: {
										name: 'limit',
										value: true
									}
								},fields:[
							{label: false, multiple:{duplicate:true}, toArray:true, name: 'group', fields:[
									{label: false, name: 'ids', type: 'select', choices: composites, key: 'name', reference: 'group_id'}
								]
							}]}
					]}).on('saved',function(){
						this.options.model.trigger('publish');
					}).on('change:ids', function(){
						this.fields.limit.trigger('change');
					}).fields.limit.trigger('change');

				}, widget));
				widget.$el.appendTo(this.$el.find(target));
				if(editor){
					widget.$el.prepend(widget.edit.$el);
				}else{
					widget.$el.prepend(widget.view.$el);
				}
				widget.$el.on('click', '.actions .wf-min', $.proxy(function(ui){
					$(ui.currentTarget).parent().parent().find('.panel-heading').css({'border-bottom': 0});
					//var state = !this.model.attributes.colapsed;
					//this.model.set({colapsed: state});
					$(ui.currentTarget).parent().parent().find('.collapsible').toggle(400 , $.proxy(function() {
						this.model.set({collapsed: $(ui.currentTarget).parent().parent().toggleClass('wf-collapsed').hasClass('wf-collapsed') });
						this.model.trigger('publish');
	  			},this) );

				}, widget));


				widget.$el.on('click', '.actions .wf-download', $.proxy(function(ui){
	  			var target = '/preferenceSummary/'+pageID+'/'+this.attributes.guid;
					window.location=target;
				}, widget));
				widget.$el.on('click', '.actions .wf-remove', $.proxy(function(ui){
					if (window.confirm("Are you sure you want to remove this widget")) { 
						// $(ui.currentTarget).parent().parent().remove();
						$(ui.currentTarget).closest('.widget').remove();
						this.getStructure();
					}
				}, this));

				if(editor){
					widget.edit.render(target);
				}else{
					widget.view.render(target);
				}
				if(!this.loading){widget.$el.find('.actions .wf-edit').click();}
			}
			return this;
		}
	};

	this.addTypeDisplay = function(object) {
		if(this.options.types === 'all' || ($.inArray(object.category, this.options.types) > -1)){
			$(widget_factory.render('widget_factory_widget_widget_factory', object )).appendTo('#cb-source');
		}
	}

	var select = function(el) {
		if(!$(el).hasClass('selected')) {
			//self.deselect();
			self.selected = getwidget($(el).attr('id'));
			self.selected.view.$el.addClass('selected');
			if(self.options.autoedit) {
				edit(el);
			}
		}
	};


	var getwidget = function(id) {
		for(var i in self.widgets) {
			if(self.widgets[i].uuid == id) {
				return self.widgets[i];
			}
		}
		return false;
	};

	var getwidgetIndex = function(id) {
		for(var i in self.widgets) {
			if(self.widgets[i].uuid == id) {
				return i;
			}
		}
		return false;
	};

// widget_factory.render('widgets__actions');
// widget_factory.render('widgets__limited_actions');
// widget_factory.render('widgets__header');
	this.options = $.extend({name: widget_factory.getUID(), types: 'all', target: '#content', axis: '', form: '#alt-sidebar', source: '#alt-sidebar', autoedit: true, associative: false, editable: true, activeFormClass: 'active'}, options);
	
	var self = this;
	this.selected = false;
	//this.form = false;
	this.widgets = [];
	this.$el = obj || $(this.options.target);
	//this.clear();

	widget_factory.instances[this.options.name] = this;

}

widget_factory.register = function(object) {
	var extendables = ['view', 'model', 'edit'/*, 'collection'*/];
	for(var ex in extendables){
		if(object[extendables[ex]]) {
			object[extendables[ex]] = widget_factory.widget.prototype[extendables[ex]].extend(object[extendables[ex]]);
		}else{
			object[extendables[ex]] = false;
		}	
	}
	widget_factory.types[object.type] = widget_factory.widget.extend(object);

	if($('#cb-source').length > 0) {
		for(var i in widget_factory.instances){
			widget_factory.instances[i].addTypeDisplay(object);
		}
	}
};

widget_factory.widget = function(owner, initial) {
	this.owner = owner;
	this.attributes = {};
	this.container = '';
	$.extend(true, this.attributes, this.defaults, initial, {widgetType: this.type});

	this.uuid = widget_factory.getUID();
	this.model = new this.model(this.attributes, {widget: this});
	this.model.off();
	this.model.on('publish', function() {
		// debugger;
		// if ( this.changedAttributes() || (!this.preventSave && !this.hasChanged('loaded'))){
		// if(!this.preventSave){
			this.widget.owner.getStructure();
		// }else{
			// this.preventSave = false;
		// }
	})
	this.view = new this.view({model: this.model, attributes: {widget: this}})

	if(!this.edit){
		this.edit = this.view;
	}else{
		this.edit = new this.edit({model: this.model, attributes: {widget: this}})
	}
};

$.extend(widget_factory.widget.prototype, {
	validate: function() {return true;},
	remove: function() {},
	toFORM: function() {
		return {label: this.display, options: {inline: false}, renderer: 'tabs', actions: false, attributes: this.attributes, items:[], fields: this.fields};
	},
	view: Backbone.View.extend({
		template: 'widgets_content',
		initialize: function() {
			this.autoElement();
		}
	}),
	model: Backbone.Model.extend({
		getAttributes: function(){
			return this.attributes;
		},
		schema: {
			Title: {required: true}
		},		
		initialize: function(attributes, options) {
			this.widget = options.widget;
			this.preventSave = false;
		}
	}),
	edit: Backbone.View.extend({
		template: 'widgets_content',
		initialize: function() {
			this.autoElement();
		}
	})
});


widget_factory.types = {};
widget_factory.instances = {};
widget_factory.widget.extend = Backbone.View.extend;
// widget_factory.prototype.events = {initialize: []};
// widget_factory.prototype.addSub = Berry.prototype.addSub;
// widget_factory.prototype.on = Berry.prototype.on;
// widget_factory.prototype.off = Berry.prototype.off;
// widget_factory.prototype.trigger = Berry.prototype.trigger;

widget_factory.render = function(name , data) {
//	return render(name, data);
	return Berry.render(name, data)
};
widget_factory.counter = 0;
widget_factory.getUID = function() {
//	return 'w' + (widget_factory.counter++);
	return generateUUID();
};


widget_factory.changed = false;
window.onbeforeunload = function() {
	if(widget_factory.changed){
		return 'Any changes that you made will be lost.';
	}
};
$((function($){
	$.fn.widget_factory = function(options) {
		return new widget_factory(options, this);
	};
})(jQuery));
$('body').keydown(function(event) {
	switch(event.keyCode) {
		case 27://escape
		for(var i in widget_factory.instances){
			widget_factory.instances[i].deselect();
		}
		break;
	}
});


	widget_factory.prototype.layouts = [
		{
			value: '0',
			classes: 'bu-3-6-3',
			label: '<i title="Wide Middle" class="bu-3-6-3"></i>',
			template: '<div class="col-md-3 col-sm-4 c0 column"></div><div class="col-sm-8 col-md-6 c1 column"></div><div class="col-md-3 col-sm-12 c2 column"></div>'
		},
		{
			value: '1',
			classes: 'bu-6-3-3',
			label: '<i title="Wide Left" class="bu-6-3-3"></i>',
			template: '<div class="col-md-6 col-sm-8 c0 column"></div><div class="col-md-3 col-sm-4 c1 column"></div><div class="col-md-3 col-sm-12 c2 column"></div>'
		},
		{
			value: '2',
			classes: 'bu-4-4-4',
			label: '<i title="Even" class="bu-4-4-4"></i>',
			template: '<div class="col-md-4 col-sm-6 c0 column"></div><div class="col-md-4 col-sm-6 c1 column"></div><div class="col-md-4 col-sm-12 c2 column"></div>'
		},
		{
			value: '3',
			classes: 'bu-6-6',
			label: '<i title="Split" class="bu-6-6"></i>',
			template: '<div class="col-sm-6 c0 column"></div><div class="col-sm-6 c1 column"></div>'
		},
		{
			value: '4',
			classes: 'bu-12-12',
			label: '<i title="Full" class="bu-12-12"></i>',
			template: '<div class="col-lg-12 c0 column"></div>'
		},
		{
			value: '5',
			classes: 'bu-oddshape',
			label: '<i title="Odd Split" class="bu-oddshape"></i>',
			template: '<div class="col-sm-4 c0 column"></div><div class="col-sm-8"><div class="row"><div class="col-sm-12 c1 column"></div></div><div class="row"><div class="col-sm-6 c2 column"></div><div class="col-sm-6 c3 column"></div></div></div>'
		},
		{
			value: '6',
			classes: 'bu-doubledown',
			label: '<i title="Double Down" class="bu-doubledown"></i>',
			template: '<div class="col-sm-12"><div class="row"><div class="col-sm-12 c0 column"></div></div><div class="row"> <div class="col-sm-6 c1 column"></div><div class="col-sm-6 c2 column"></div></div><div class="row"><div class="col-sm-12 c3 column"></div></div></div>'
		},
		{
			value: '7',
			classes: 'bu-3-6-3',
			label: '<i title="Middle Only" class="bu-3-6-3"></i>',
			template: '<div class="col-lg-offset-3 col-md-offset-2  col-lg-6 col-md-8 col-sm-12 c0 column"></div></div>'
		},
	];