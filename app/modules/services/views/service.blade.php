<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/backbone.js/1.3.3/backbone-min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-sortable/0.9.13/jquery-sortable-min.js"></script>

<script type="text/javascript">
alert = function(value){ console.log(value); };
function render(template, data){
	if(typeof templates[template] === 'undefined'){
		templates[template] =  Hogan.compile($('#'+template).html());
	}
  return templates[template].render(data, templates);
}

$(function() {

	contentManager = new RegionManager();
	sidebarManager = new RegionManager({el: '#sidebar'});

	var Workspace = Backbone.Router.extend({
		route: function(route, name, callback) {
			return Backbone.Router.prototype.route.call(this, route, name, function() {
				this.trigger('beforeRoute');
				if (!callback) callback = this[name];
				return callback.apply(this, arguments);
			});
		},
		initialize: function(options) {
			this.history = [];
			return this.on("beforeRoute", this.storeRoute);
		},
		storeRoute: function() {
			return this.history.push(Backbone.history.fragment);
		},
		previousFragment: function() {
			return this.history[this.history.length - 2];
		},
		changeAndStoreFragment: function(fragment) {
			this.navigate(fragment);
			return this.storeRoute();
		},
		previous: function(trigger) {
			if (this.history.length > 1) {
				if(typeof trigger === 'undefined') { trigger = true; }
				return this.navigate(this.history[this.history.length - 2], {trigger: trigger});
			}
		},
		// routes: {
		// 	":path(/:optional)(/:path2)(/:optional2)": "default",
		// 	"": 'default'
		// },
		// default: function(path1, optional, path2, optional2) {
		// 	var path = path1 || 'Apps';

		// 	if(typeof routes[path] === 'undefined'){
		// 		path = 'undefined';
		// 	}
		// 	if(typeof routes[path] !== 'undefined'){
		// 		// $('.logo i').addClass('fa-spin');

		// 		/* Check here if resource not loading when expected*/
		// 		if(routes[path].resource.length > 0){
		// 			$.ajax({
		// 				url: '/modules/assets/js/' + routes[path].resource + '/resource.js',
		// 				dataType: "script",
		// 				cache: true,
		// 				success: function(){
		// 					// $('.logo i').removeClass('fa-spin');
		// 					if(typeof routes[path] !== 'undefined') {
		// 						var activeLink = $("[href='#/" + path + "']");
		// 						var parent = activeLink.closest(".menu-item").find(".menu-item-parent").html();
		// 						$('.sidebar-menu a').removeClass('active');
		// 						activeLink.closest('a').addClass('active');
		// 						if(typeof parent == 'undefined'){parent = path;}
		// 						if(parent != path){
		// 							$('.breadcrumb').append('<li>' + path + '</li>');
		// 						}
		// 						if(typeof path2 !== 'undefined' && path2 !== null){
		// 							routes[path + '_' + path2].init(optional,optional2);
		// 						}else{
		// 							routes[path].init(optional);
		// 						}
		// 					}
		// 				}, error:function(){
		// 					alert('Bad Response');
		// 					// $('.logo i').removeClass('fa-spin');
		// 					if(typeof routes[path] !== 'undefined') {
		// 						var activeLink = $("[href='#/" + path + "']");
		// 						var parent = activeLink.closest(".menu-item").find(".menu-item-parent").html();
		// 						$('.sidebar-menu a').removeClass('active');
		// 						activeLink.closest('a').addClass('active');
		// 						if(typeof parent == 'undefined'){parent = path;}
		// 						if(parent != path){
		// 							$('.breadcrumb').append('<li>' + path + '</li>');
		// 						}
		// 						if(typeof path2 !== 'undefined' && path2 !== null){
		// 							routes[path + '_' + path2].init(optional,optional2);
		// 						}else{
		// 							routes[path].init(optional);
		// 						}
		// 					}
		// 				}
		// 			});
		// 		}else{
		// 			// $('.logo i').removeClass('fa-spin');
		// 			if(typeof routes[path] !== 'undefined'){
		// 				var activeLink = $("[href='#/" + path + "']");
		// 				var parent = activeLink.closest(".menu-item").find(".menu-item-parent").html();
		// 				$('.sidebar-menu a').removeClass('active');
		// 				activeLink.closest('a').addClass('active');
		// 				if(typeof parent == 'undefined'){parent = path;}
		// 				if(parent != path){
		// 					$('.breadcrumb').append('<li>' + path + '</li>');
		// 				}
		// 				if(typeof path2 !== 'undefined' && path2 !== null){
		// 					routes[path + '_' + path2].init(optional,optional2);
		// 				}else{
		// 					routes[path].init(optional);
		// 				}
		// 			}
		// 		}
		// 	}else{alert('Route Not Supported');}
		// }
	});

	myrouter = new Workspace();
	Backbone.history.start();
});

/* Tools */
RegionManager = function(defaults) {
	this.currentView = undefined;
	defaults = $.extend({el:"#content"}, defaults);
	var el = defaults.el;

	var closeView = function (view) {
		for(var i in Berry.instances){
			Berry.instances[i].destroy();
		}
		if (view && view.close) {
			view.close();
		}
	};

	var openView = function (view) {
		view.render();
		$(el).html(view.el);
		if (view.onShow) {
			view.onShow();
		}
		$('html, body').animate({ scrollTop: 0 }, 'fast');
		$(el).find('.tooltips').tooltip();
    $(el).find('.popovers').popover();
	};

	this.show = function (view) {
		var r = true;
		// if(widget_factory.changed){
		// 	r = confirm("Any changes that you made will be lost.\n\nAre you sure you want to leave this page?");
		// }
		if (r == true) {
			// widget_factory.changed = false;
			closeView(this.currentView);
			if(view){
				this.currentView = view;
				openView(this.currentView);
			}else{
				this.currentView = undefined;
			}
		} else {
			myrouter.previous(false);
			myrouter.history.pop();
		}
	};
};

$(function() {
	Backbone.View.prototype.close = function() {
		this.remove();
		this.unbind();
		if (this.onClose){
			this.onClose();
		}
	};
	Backbone.View.prototype.form = function(options) {
		options = options || {target: this.formTarget};
		this.berry = $(this.formTarget || options.target).berry($.extend({model: this.model, legend: this.legend, fields: this.fields }, options));
		return this.berry;
	};
	Backbone.View.prototype.destroy = function(e) {
		e.stopPropagation();
		if(confirm('Are you sure you want to delete?')) {
			this.$el.fadeOut('fast', $.proxy(function() {
				this.model.destroy({success: function(model, response) {
					alert('here');
				}});
				this.remove();
			}, this));
		}
	};
	Backbone.Model.prototype.alert = function(keys) {
		$.gritter.add(
			$.extend(
				{title: 'Success!', text: 'Successfully updated' + this.model.attributes['name'], timeout: 3000, color: "#5F895F", icon: "fa fa-user"},
				(this.alert || {})
			)
		);
	};
	Backbone.Model.prototype.unauthorized = function(alert) {
		$.gritter.add(
			$.extend(
				{title: 'Unauthorized!', text: 'You are not authorized to perform this action', timeout: 3000, color: "#5F895F", icon: "fa fa-user"},
				(alert || this.alert || {})
			)
		);
	};

	Backbone.Model.prototype.fields = function(keys) {
		return containsKey(this.schema,keys);
	};
	Backbone.View.prototype.replace = function() {
			this.$el.replaceWith(this.setElement(render(this.template, this.model.attributes )).$el);
			this.render()
			this.editing = false;
			this.trigger('rendered');
		};
	Backbone.View.prototype.autoElement = function(options) {
		options = $.extend({append: true}, options);
		this.setElement(render(this.template, this.model.attributes ));
		this.model.off('change', Backbone.View.prototype.replace, this);
		this.model.on('change', Backbone.View.prototype.replace, this);
		if(options.append !== false){
			$(this.target).append(this.$el);
		}
	};
	Backbone.View.prototype.autoAdd = function(options) {
		this.collection.on('add', $.proxy(function(){ contentManager.show( new this.constructor({ collection: this.collection }))}, this) );
	};

	Backbone.ItemView = Backbone.View.extend({
		initialize: function() {
			this.autoElement();
		},
		edit: function(e) {
			e.stopPropagation();
			this.form();
		}
	});

	Backbone.ListView = Backbone.View.extend({
		add: function() {
			$().berry({context: this, legend: this.legend, model: new this.collection.model(), fields: this.fields}).on('completed', function(){
				if(this.closeAction === 'save'){
					this.options.context.collection.add(this.options.model);
					new this.options.context.modelView({'model': this.options.model});
				}
			});
		},
		onShow: function() {
			_.each(this.collection.models, function(model) {
				new this.modelView({'model': model});
			}, this);
		},
		initialize: function() {
			this.fields = this.fields || this.modelView.prototype.fields;
			this.setElement(render(this.template, this.collection ));
		},
	});

});

function message(options) {
	$.gritter.add($.extend({timeout: 3000, color: '#5F895F'}, options));
}
function rating(selector, rated, container) {
	container.find(selector + ' .fa-star:lt('+parseInt(rated ,10)+')').removeClass('fa-star-o');
	var temp = Math.floor(rated);
	if(rated - temp >= 0.5){
		container.find(selector + ' .fa-star:nth-child(' + (temp + 1) + ')').addClass('fa-star-half-full');
	}
}

modal = function(options) {
	$('#myModal').remove();
	this.ref = $(render('modal', options));

	options.legendTarget = this.ref.find('.modal-title');
	options.actionTarget = this.ref.find('.modal-footer');

	$(this.ref).appendTo('body');

	if(options.content) {
		$('.modal-body').html(options.content);
		options.legendTarget.html(options.legend);
	}else{
		options.autoDestroy = true;
		var myform = this.ref.find('.modal-body').berry(options).on('destroy', $.proxy(function(){
			this.ref.modal('hide');
		},this));

		this.ref.on('shown.bs.modal', $.proxy(function () {
			this.$el.find('.form-control:first').focus();
		},myform));
	}
	if(options.onshow){
		this.ref.on('shown.bs.modal', options.onshow);
	}  
	this.ref.modal();
	return this;
};


function containsKey( list , keys ){
	var returnArray = {};
	for (var key in keys) {
		if(typeof list[keys[key]] !== 'undefined'){
			returnArray[keys[key]] = list[keys[key]];
		}
	}
	return returnArray;
}

function createChildren(original,name,source){
	for(var j in original) {
		original[j][name] = {};
		temp = source.get(original[j][name + '_id']);
		if(typeof temp != 'undefined'){
			original[j][name] = temp.attributes;
		}
	}
}

Date.createFromMysql = function(mysql_string){
   if(typeof mysql_string === 'string') {
      var t = mysql_string.split(/[- :]/);

      //when t[3], t[4] and t[5] are missing they defaults to zero
      return new Date(t[0], t[1] - 1, t[2], t[3] || 0, t[4] || 0, t[5] || 0);
   }

   return null;
};

//mymodal = new modal({body:"newbod",'footer':'<button type="button" class="btn btn-primary" id="save">Save changes</button>'});


$('.menu-button').on('click', function(e){
	$('.app>.main-container>.nav-container').toggleClass('showMenu');//.toggleClass('nav-horizontal nav-vertical');
});




// routes = {
// 	'': {
// 		init: function() {
// // 		$('.panel-default .panel-body').empty().cobler({target: '.page', axis: 'y', types: 'none'})
// // 			.add('Content')
// // 			.add('select');
// // //			$('.page').berry({fields:[{label: 'Nope', name: 'nope', type: 'text', placeholder: 'stuff'}]});
//  		},
// 		resource: ''
// 	},
// 	'look': {
// 		init: function() {


// 			myProfile = new configModel({ id : 0 });
// 			myProfile.fetch( { success: function() {
// 				contentManager.show( new configView( { model: myProfile } ) );
// 				sidebarManager.show();
// 			}});
// // 		$('.panel-default .panel-body').empty().cobler({target: '.page', axis: 'y', types: 'none'})
// // 			.add('Content')
// // 			.add('select');
// // //			$('.page').berry({fields:[{label: 'Nope', name: 'nope', type: 'text', placeholder: 'stuff'}]});
//  		},
// 		resource: ''
// 	},	
		

// };

configView = Backbone.View.extend({
	events:{
		'click #save': 'save',
	},
	template: 'portal_config_view',
	onShow:function() {
		this.editor = ace.edit("itemcontainer");
    this.editor.setTheme("ace/theme/chrome");
    this.editor.getSession().setMode("ace/mode/css");
    this.editor.focus();
    this.editor.setValue(this.model.attributes.css); // or session.setValue
	},
	save: function(){
		this.model.save({css: this.editor.getValue()},{patch:true});
	},
	initialize: function() {
		this.setElement(render(this.template));
	}
});


// configModel = Backbone.Model.extend({
// 	urlRoot: '/portalconfig',
//   schema:{
// 		Name: {name: 'name', required: true},
// 	},
// 	initialize: function(){
// 		//if(this.collection){ this.attributes.group_id = this.collection.id; }
// 		this.bind('change', function(){ if(this.collection){ this.save(); }});
// 	}
// });


// Automatically cancel unfinished ajax requests 
// when the user navigates elsewhere.
(function($) {
  var xhrPool = [];
  $(document).ajaxSend(function(e, jqXHR, options){
    xhrPool.push(jqXHR);
  });
  $(document).ajaxComplete(function(e, jqXHR, options) {
    xhrPool = $.grep(xhrPool, function(x){return x!=jqXHR});
  });
  var abort = function() {
    $.each(xhrPool, function(idx, jqXHR) {
      jqXHR.abort();
    });
  };

  var oldbeforeunload = window.onbeforeunload;
  window.onbeforeunload = function() {
    abort();
    return oldbeforeunload ? oldbeforeunload() : undefined;
  }
})(jQuery);

function generateUUID(){
    var d = new Date().getTime();
    var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        var r = (d + Math.random()*16)%16 | 0;
        d = Math.floor(d/16);
        return (c=='x' ? r : (r&0x3|0x8)).toString(16);
    });
    return uuid;
};


Berry.conditions.test = function(Berry, args, func) {
	// debugger;
		return Berry.on('change:' + this.name, $.proxy(function(args, local, topic, token) {
				func.call(this, args(), token);
			}, this, args)
		).lastToken;
	}

// servicesView = Backbone.View.extend({
// 	template: 'services_view' ,
// 	onShow: function() {
// 		_.each(myServices.models, function(model) {
// 			var temp = new serviceGroupView({'model': model});
// 		});
// 	},
// 	render: function() {
// 		this.setElement(render(this.template, myServices));
// 	},
// });

// serviceView = Backbone.View.extend({
// 	events: {
// 		'click': 'edit_css',
// 		'click .fa-pencil': 'edit',
// 		'click .fa-times': 'destroy'
// 	},
// 	edit_css: function(){
// 		myrouter.navigate('/service/'+this.model.id, { trigger: true });
// 	},
// 	template: 'service_view',
// 	target: '.group-list',
// 	initialize: function() {
// 		this.target = this.target+this.model.attributes.group_id
// 		this.autoElement();
// 	}
// });

// serviceGroupView = Backbone.View.extend({
// 	events: {
// 		'click .fa-plus': 'addService',
// 		'click .fa-bolt': 'importService'
// 	},
// 	addService: function() {
// 		this.form({legend: '<i class="fa fa-photo"></i> Add Service',fields: ['Name'], model: new serviceModel({group_id: this.model.attributes.id})}).on('completed', function(){
// 			// if(this.closeAction === 'save'){
				
// 			if(!this.options.model.isNew()) {
// 				$('.empty-list').remove();
// 				myServices.add(this.options.model);
// 				new serviceView({model: this.options.model});
// 			}
// 		} );
// 	},
// 	importService: function() {
// 		$().berry({inline: true, legend: '<i class="fa fa-photo"></i> Import Service',fields: [	{label: 'Descriptor', type: 'textarea'},{name: 'group_id', value: this.model.attributes.id, type: 'hidden'} ]}).on('save', function(){
// 			var model = JSON.parse(this.toJSON().descriptor);
// 			model.group_id = this.toJSON().group_id;
// 			this.trigger('saved');
// 			var sModel = new serviceModel(model);
// 			sModel.save();
// 			new serviceView({model: sModel});
// 		});
// 	},
// 	render: function() {
// 		$('.group-list'+this.model.attributes.group_id).empty();
// 		_.each(this.model.attributes.services, function(model) {
// 			var temp = new serviceView({'model': new serviceModel(model)});
// 		});
// 	},
// 	template: 'service_group_view',
// 	target: '.page',
// 	initialize: function() {
// 		this.setElement(render(this.template, this.model.attributes ));
// 		$(this.target).append(this.$el);
// 		this.render();
// 	}
// });

editServiceView = Backbone.View.extend({
	template: 'edit_service_view',
	events:{
		'click #save': 'save',
		'click #update': 'update',
		'click #toggle_template_editor': 'toggle_template_editor',
		'click #new_js': 'addJS',
		'click #edit_js': 'editJS',
		'click #delete_js': 'deleteJS',
	},
	toggle_template_editor: function(){
		if(this.template_editor == 'ace'){
			this.template = this.editor.getValue();

			this.editor.destroy()
			var oldDiv = this.editor.container;
			var newDiv = oldDiv.cloneNode(false);
			// newDiv.textContent = this.template;
			oldDiv.parentNode.replaceChild(newDiv, oldDiv);

			this.template_editor = 'tinyMCE'
			this.t_berry = $('#templatecontainer').berry({actions:false,attributes:{template: this.template},fields:[{type:'contenteditable',label:false, name: 'template'}]})
		}else{
			this.template_editor = 'ace';
			this.template = this.t_berry.toJSON('template');
			this.t_berry.destroy();
			this.editor = ace.edit("templatecontainer");
			this.editor.setTheme("ace/theme/chrome");
			this.editor.getSession().setMode("ace/mode/handlebars");
			this.editor.focus();
			this.editor.setValue(this.template); // or session.setValue
		}
	},
	deleteJS:function(){
			this.fixScripts();
			if(this.activeScript !== 'main'){
				if(confirm("Are you sure you want to delete this file? \nThis operation can not be undone.\n\n" )) {
					this.model.attributes.script = _.filter(this.model.attributes.script,function(item){return (item.name !== this.activeScript)}.bind(this))
				}
				$('[data-file="'+this.activeScript+'"]').parent().remove();
				$('[data-file="main"]').tab('show');
			}
	},
	editJS:function(){
		this.fixScripts();			
		if(this.activeScript !== 'main'){
			$().berry({name:'modal',legend:'Edit Script File',fields:{'Filename':{value: this.activeScript}}}).on('save',function(){			
				var filename = Berries.modal.toJSON().filename;
				$('[data-file="'+this.activeScript+'"]').html(filename);
				_.findWhere(this.model.attributes.script, {name: this.activeScript}).name = filename;
				this.activeScript = filename;
				Berries.modal.trigger('saved');
			},this)
		}
	},
	addJS:function(){
		this.fixScripts();
		$().berry({name:'modal',legend:'New Script File',fields:{'Filename':''}}).on('save',function(){			
			var filename = Berries.modal.toJSON().filename;
			this.model.attributes.script.push({name:filename,content:''})
			$('#script ul.nav-tabs').append('<li role="presentation" class=""><a data-file="'+filename+'" href="#js_filename" aria-controls="js_Main" role="tab" data-toggle="tab"></span>'+filename+'</a></li>')
				.find('a[data-toggle="tab"]').on('shown.bs.tab', this.script_page.bind(this)).click();
				Berries.modal.trigger('saved');
		},this)
	},
	script_page: function(e){
			this.fixScripts();
			var old = _.findWhere(this.model.attributes.script, {name: this.activeScript})
			if(typeof old != 'undefined'){
				old.content = this.script_editor.getValue()
			}
			this.activeScript = e.target.dataset.file;
			this.script_editor.setValue(_.findWhere(this.model.attributes.script, {name: this.activeScript}).content); // or session.setValue
	},
	onShow:function() {
		// debugger;
		$('.panel-body > div').height($('#content').height()-10)
		this.editor = ace.edit("templatecontainer");
		this.editor.setTheme("ace/theme/chrome");
		this.editor.getSession().setMode("ace/mode/handlebars");
		this.editor.focus();
		this.editor.setValue(this.model.attributes.template); // or session.setValue
		this.template_editor = 'ace';

		this.css_editor = ace.edit("csscontainer");
		this.css_editor.setTheme("ace/theme/chrome");
		this.css_editor.getSession().setMode("ace/mode/css");
		this.css_editor.focus();
		this.css_editor.setValue(this.model.attributes.css); // or session.setValue

		this.script_editor = ace.edit("scriptcontainer");
		this.script_editor.setTheme("ace/theme/chrome");
		this.script_editor.getSession().setMode("ace/mode/javascript");
		this.script_editor.focus();
		this.script_editor.setValue(this.model.attributes.script[0].content); // or session.setValue
		this.activeScript = 'main';
		this.model.attributes.sources = this.model.attributes.sources||[{"map":"","data_type":"None","path":""}];

		$('#script a[data-toggle="tab"]').on('shown.bs.tab', this.script_page.bind(this))
		$('[data-file="main"]').tab('show');


		//hack to cause each data_type element to reload and trigger change
		// delete	Berry.collections[this.model.schema['My Sources'].fields.Sources.fields.Type.choices];

		this.settingsForm = $('#settingscontainer').berry({legend: false, model: this.model, actions: false, flatten: true}).on('change:data_type', function(field){
			var element = this.findByID(field.id);
			var value = field.value;
			if(!isNaN(value)){
				value = parseInt(value);
			}
			//var temp = _.findWhere(Berry.collections['/endpoints/plus/'+this.options.model.attributes.group_id], {value: value});
			var temp = _.findWhere(Berry.collection.get('/endpoints/plus/'+this.options.model.attributes.group_id), {value: value});

			if(typeof temp !== 'undefined'){
				element.parent.children.path.update({pre: temp.target}, true);
			}else{
				element.parent.children.path.update({pre: false}, true);
			}
		});
// debugger;
		cb = new cobler({target: '#editor', types: ['widget']});
		cb.load(this.model.attributes.form || {});
	},
	update: function(){
		this.updateB = $().berry({inline: true, legend: '<i class="fa fa-photo"></i> Import Service',fields: [	{label: 'Descriptor', type: 'textarea'},{name: 'group_id', value: this.model.attributes.id, type: 'hidden'} ]}).on('save', function(){
				this.model.set(JSON.parse(this.updateB.toJSON().descriptor));
				window.location.reload();
		}, this);
	},
	save: function(){
		if(this.template_editor == 'ace'){
			this.template = this.editor.getValue();
		}else{
			this.template = this.t_berry.toJSON('template');
		}
		var successCompile = true;
		// try{Ractive.parse(this.template);successCompile=true;}catch(e){modal({headerClass:'danger' ,title: e.name,content:$("<div>").text(e.message).html()})}
		if(successCompile){
			this.fixScripts();
			_.findWhere(this.model.attributes.script, {name: this.activeScript}).content = this.script_editor.getValue();

			this.model.set($.extend({template: this.template, css: this.css_editor.getValue(), script: JSON.stringify(this.model.attributes.script), form: cb.toJSON()}, this.settingsForm.toJSON()));
		}

	},
	fixScripts: function(){
		if(typeof this.model.attributes.script == 'string'){
			try{
				var temp = JSON.parse(this.model.attributes.script);
				this.model.attributes.script = temp;
			}catch(e){}
			if(typeof this.model.attributes.script !== 'object'){
				this.model.attributes.script = [{name:'main', content: this.model.attributes.script}] ;
			}
		}
	},
	initialize: function() {

		if(this.model.attributes.script == null){
			this.model.attributes.script = "";
		}
		this.fixScripts();
		this.model.schema['My Sources'].fields.Sources.fields.Type.choices += '/'+this.model.attributes.group_id
		this.setElement(render(this.template, this.model.attributes));

		$(document).keydown($.proxy(function(e) {
			if ((e.which == '115' || e.which == '83' ) && (e.ctrlKey || e.metaKey))
			{
					e.preventDefault();
					this.save();
			}
			return true;
		}, this) ); 
	}
});
serviceModel = Backbone.Model.extend({
	schema:{
		Name: {required: true, columns: 12},
		'n1': {parsable: false, label: false, type: 'raw', columns: 2, value :'<b>Map to</b>'},
		'n2': {parsable: false, label: false, type: 'raw', columns: 2, value :'<b>Data Type</b>'},
		'n3': {parsable: false, label: false, type: 'raw', columns: 1, value :'<b>Cache</b>'},
		'n4': {parsable: false, label: false, type: 'raw', columns: 1, value :'<b>Auto Fetch</b>'},
		'n5': {parsable: false, label: false, type: 'raw', columns: 6, value :'<b>Path</b>'},
		"My Sources":{label: false,fields:{
			Sources: {type: 'fieldset', name: 'sources', label: false, inline: true,  multiple: {duplicate: true}, fields: {
				Map: {columns: 2, label: false,},
				Type: {columns: 2, label: false, name:'data_type', type: 'select', choices: '/endpoints/plus'},
				Cache: {columns: 1, label: false, type: 'checkbox','alt-display': 'ui-switch ui-switch-success', 'container': 'i'},
				Fetch: {columns: 1, label: false, type: 'checkbox', value: true,'alt-display': 'ui-switch ui-switch-success', 'container': 'i'},
				Path: {columns: 6, required: true, label: false}
			}}
		}}
	},
	idAttribute: 'id',
	urlRoot: '/services',
	initialize: function() {
		this.bind('change', function() {
			if(!this.hasChanged('updated_at') && !this.hasChanged('id')) {
				this.save(); 
			}
		});
	}
});
// serviceCollection = Backbone.Collection.extend({
// 	model: serviceModel,
// 	url: '/services',
// });

</script>
<script>

		myService = new serviceModel({ 'id' : {{$id}} });
		myService.fetch( { success: function() {
			contentManager.show( new editServiceView( { model: myService } ) );
		}});		
</script>