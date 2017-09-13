<script>
	var original;

$(function() {

	render('pages_tabpanel');
	render('pages_listgroupitem');
	render('pages')
	$.ajax({
		url:'/microapps/{{$id}}',
		success: function(model){
			// original = $.extend(true, {}, model);
			original = JSON.stringify(_.pick(model, 'sources', 'template', 'script', 'css', 'options' ))
			setInterval(function(){ 
		    $.ajax({
		    url:'/groups/'+model.group_id,
		    error:function() {
		        toastr.error("Your Session has timed out; please refresh your page.", 'ERROR',{"showDuration": "30000"});
		    }
		    })
			}, 30000);

			$.ajax({
				url:'/endpoints/list/'+model.group_id,
				success: function(eps){
					// console.log(model);
					Berry.collection.add('/endpoints/list/'+model.group_id, eps)

					$('#content').html(render('edit_microapp_view_new', model));
		  		$('.nav-tabs').stickyTabs();

				  var temp = $(window).height() - $('.nav-tabs').offset().top -66;

				  $('body').append('<style name="ace_style">.ace_editor { height: '+temp+'px; }</style>')

					/* Setup form Builder */
					/**********************/
					templates['itemContainer'] = Hogan.compile(
						'<div class="cobler-li-content"></div><div class="btn-group parent-hover"><span class="remove-item btn btn-danger fa fa-trash-o" data-title="Remove"></span><span class="duplicate-item btn btn-default fa fa-copy" data-title="Duplicate"></span></div>'
					);

					var items = {};
					if(typeof model.options !== 'undefined' &&  model.options !== null){
						items = model.options.fields || {};
					}
					if(typeof cb !== 'undefined') {
						cb.destroy();
						delete cb;
					}
		      cb = new Cobler({formTarget:$('#form'), disabled: false, targets: [document.getElementById('editor')], items: [items]})
		      list = document.getElementById('sortableList');
		      cb.addSource(list);
		      cb.on('activate', function() {
		        if(list.className.indexOf('hidden') == -1){
		          list.className += ' hidden';
		        }
		        $('#form, .reset-form-view').removeClass('hidden');
		      })
		      cb.on('deactivate', function() {
		        list.className = list.className.replace('hidden', '');
		        $('#form, .reset-form-view').addClass('hidden');
		      })
		      cb.on('remove', function() {
		      	cb.deactivate();
		      })
		      document.getElementById('sortableList').addEventListener('click', function(e) {
		        cb.collections[0].addItem(e.target.dataset.type);
		      })

					/* end form builder*/

		      /* Setup Table */
					/**********************/
					fields = [
						{label: 'Name', name: 'name'},
						{label: 'Endpoint', name:'endpoint', default: {name: 'External (no auth)', value: 'External', target:''}, type: 'select', choices: '/endpoints/list/'+model.group_id},
						{label: 'Modifier', name:'modifier', type: 'select', options: [{name: 'Default', value: 'none'}, {'label': 'XML/RSS > JSON', 'value': 'xml'}, {'label': 'CSV > JSON', 'value': 'CSV'}, {'label': 'Include as CSS', 'value': 'CSS'}, {'label': 'Include as JS', 'value': 'JS'}]},
						{label: 'Cache', name: 'cache', type: 'checkbox','alt-display': 'ui-switch ui-switch-success', 'container': 'i'},
						{label: 'Fetch', name: 'fetch', type: 'checkbox', value: true,'alt-display': 'ui-switch ui-switch-success', 'container': 'i'},
						{required: true, label: 'Path', name: 'path', template:template}
					];

					bt = new berryTable({
						filter: false,search: true,columns: false,upload:false,download:false,
						multiEdit:['endpoint', 'fetch', 'cache', 'modifier'],
		        entries: [],
		        // autoSize: 22,
		        // inlineEdit:true,
		        container: '#resourcescontainer', 
		        schema: fields, 
		        data: model.sources,
		        edit:true,
		        preDraw:function(item){			        	
		        	var temp = _.findWhere(Berry.collection.get('/endpoints/list/'+model.group_id), {value: parseInt(item.attributes.endpoint, 10)});
		        	if(typeof temp !== 'undefined'){
								item.target = temp.target;
							}else{
								item.target = '';
							}
		        },
		        click: function(model, view){model.toggle();},
		        add: true,
		        delete: true
					})

		      /* Setup Editors */
					/**********************/
				  // templatePage = new paged('.templates',{items:attributes.code.templates});

				  var scripts = JSON.parse(model.script) || [{name:'main', content: ''}];
				  scripts[0].disabled = true;
				  scriptPage = new paged('#scriptcontainer',{items: scripts, mode:'ace/mode/javascript', label: 'Script'});

				  var partials = JSON.parse(model.template) ||  [{name:'main', content: ''}];
				  partials[0].disabled = true;
				  templatePage = new paged('#templatecontainer',{items: partials, mode:'ace/mode/handlebars', label: 'Template'});

				  cssPage = new paged('#csscontainer', {items:[{name:'Style', editable: false, content: model.css || ''}], mode:'ace/mode/css'});

				  /* Events */
				  $('#update').on('click', function(){
						$().berry({name: 'update', inline: true, legend: '<i class="fa fa-photo"></i> Update Microapp',fields: [	{label: 'Descriptor', type: 'textarea'},{name: 'group_id', value: model.id, type: 'hidden'} ]}).on('save', function(){
								$.ajax({
									url: '/microapps/'+model.id,
									data: $.extend({force: true, updated_at:''}, JSON.parse(Berries.update.toJSON().descriptor)),
									method: 'PUT',
									success: function(){
										Berries.update.trigger('close');
										window.location.reload();
									}
								})
						});
				  })
				  $('#view').on('click', function(){
						$.get('/microapp/used/'+model.id, function(data){
							if(data.length > 0){
								modal({title:'This uApp was found on the following pages', content:viewTemplate.render({items:data})});
							}else{
								modal({title: 'No pages Found', content:'This uApp is not currently placed on any pages.'});
							}
						})
					})			  
				  this.model = model;


					$('#save').on('click', function(){
						template_errors = templatePage.errors();
						script_errors =scriptPage.errors();
						// debugger;
						css_errors = [];
						if(cssPage.toJSON()[0].content.length>0){
							css_errors =cssPage.errors();
						}
						console.log(css_errors);

						// var compilefail = false;
						// var errors = [];
						_.each(templatePage.toJSON(), function(partial) {
							try{
								Ractive.parse(partial.content);
							}catch(e){
								template_errors.push({
									type: e.name,
									name: partial.name,
									message: e.message
								});
							}
						})
						// template_errors+=errors.length;
						var errorCount = template_errors.length+ script_errors.length+ css_errors.length

														// modal({headerClass:'danger' ,title: e.name+': '+partial.name,content:$('<div>').html(e.message).html()})


						if(!errorCount){
							this.model.sources = _.map(bt.models, function(item){return item.attributes});
							this.model.template =  JSON.stringify(templatePage.toJSON());
							this.model.script = JSON.stringify(scriptPage.toJSON());
							this.model.css = cssPage.toJSON()[0].content;
							this.model.options = {fields: cb.toJSON({})[0]} ;
							// this.model.updated_at = model.updated_at;

							$.ajax({
								url:'/microapps/{{$id}}',
								data: this.model,
								method:'PUT',
								success: function(model){
									this.model = model;
									original = JSON.stringify(_.pick(model, 'sources', 'template', 'script', 'css', 'options', 'updated_at' ))

									toastr.success(model.name +' has been successfully saved.', 'Success!')

								}.bind(this),
								error: function(e){
									toastr.error(e, 'Error on save')
								},
								statusCode: {
							    404: function() {
										toastr.error('You are no longer logged in', 'Logged Out')
							    },
							    409: function(error) {
							    	// debugger;
							    	test = JSON.parse(JSON.parse(error.responseText).error.message);
										toastr.warning('conflict detected', 'NOT SAVED')


										conflictResults = {};

										conflictResults.sources = (JSON.stringify(test.sources) !== JSON.stringify(this.model.sources));
										conflictResults.css = (JSON.stringify(test.css) !== JSON.stringify(this.model.css));
										conflictResults.options = (JSON.stringify(test.options) !== JSON.stringify(this.model.options));
										conflictResults.scripts = (JSON.stringify(test.script) !== JSON.stringify(this.model.script));
										conflictResults.template = (JSON.stringify(test.template) !== JSON.stringify(this.model.template));

										modal({headerClass:'bg-danger' ,title: 'Conflict(s) detected', content: render('conflict', conflictResults)})//, footer:'<div class="btn btn-danger">Force Save</div>'})

							    }.bind(this),
							    401: function() {
										toastr.error('You are not authorized to perform this action', 'Not Authorized')
							    }
							  }
							})
						}else{
							toastr.error('Please correct the compile/syntax errors ('+ errorCount +')', 'Errors Found')
							modal({headerClass:'danger' ,title: 'Syntax Error(s)', content: render('error', {count:errorCount, temp: template_errors, script: script_errors, css: css_errors})})//, footer:'<div class="btn btn-danger">Force Save</div>'})
						}
					}.bind(this))


					$('.logo').after( '<a id="editName" href="javascript:void(0);" style="position:absolute;left:220px;line-height:50px;font-size:18px"><b>'+model.name+'</b> <i style="font-size:16px" class="fa fa-pencil"></i></a>');
					$('#editName').on('click',function(){
						$().berry({
							name: 'options',
							legend:'Options',
							attributes: this.model,
							fields:{
									Name: {required: true,},
									Public: {type:'checkbox','alt-display': 'ui-switch ui-switch-success', 'container': 'i'}
							}
						}).on('save',function() {
							this.model.name = Berries.options.toJSON().name;
							this.model.public = Berries.options.toJSON().public;
							Berries.options.trigger('close');
						}.bind(this))
					}.bind(this))
				}
			})
	}})
})






$(document).keydown(function(e) {
	if ((e.which == '115' || e.which == '83' ) && (e.ctrlKey || e.metaKey))
	{
			e.preventDefault();
			$('#save').click();
	}
	return true;
});


$(window).on('resize orientationChange', function(){
	var temp = $(window).height() - $('.nav-tabs').offset().top -110;
	$('body [name="ace_style"]').html('.ace_editor { height: '+temp+'px; }')
});
		// changed = false
     window.onbeforeunload = function() {

    	var currentObj = {};
			currentObj.sources = _.map(bt.models, function(item){return item.attributes});
			currentObj.template =  JSON.stringify(templatePage.toJSON());
			currentObj.script = JSON.stringify(scriptPage.toJSON());
			currentObj.css = cssPage.toJSON()[0].content;
			currentObj.options = {fields: cb.toJSON({})[0]} ;



    	if(original !== JSON.stringify(currentObj)  ){
      	return true;
      // debugger;
    	}	
    }   

Berry.btn.save= {
		label: 'Update',
		icon:'check',
		id: 'berry-submit',
		modifier: 'success pull-right',
		click: function() {
			if(this.options.autoDestroy) {
				this.on('saved', this.destroy);
			}
			this.trigger('save');
		}
	};


(function(b, $){
	b.register({
		type: 'ace',
		create: function() {
				return b.render('berry_ace', this);
			},
		setup: function() {
			this.$el = this.self.find('.formcontrol > div');
			this.$el.off();
			if(this.onchange !== undefined) {
				this.$el.on('input', this.onchange);
			}
			this.$el.on('input', $.proxy(function(){this.trigger('change');},this));

			this.editor = ace.edit(this.id+"container");
	    this.editor.setTheme(this.item.theme || "ace/theme/chrome");
	    this.editor.getSession().setMode(this.item.mode || "ace/mode/handlebars");

		},
		setValue: function(value){
			if(typeof this.lastSaved === 'undefined'){
				this.lastSaved = value;
			}
			this.value = value;
			this.editor.session.setValue(value);
			// this.editor.session.getUndoManager().reset();
			return this.$el;
		},
		getValue: function(){
			return this.editor.getValue()
		},
		// destroy: function(){
		// 	this.editor.destroy();
		// }
		focus: function(){
			this.editor.focus();
		}
	});
})(Berry,jQuery);

/**
 * jQuery Plugin: Sticky Tabs
 *
 * @author Aidan Lister <aidan@php.net>
 * @version 1.2.0
 */
(function ( $ ) {
    $.fn.stickyTabs = function( options ) {
        var context = this

        var settings = $.extend({
            getHashCallback: function(hash, btn) { return hash }
        }, options );

        // Show the tab corresponding with the hash in the URL, or the first tab.
        var showTabFromHash = function() {
          var hash = window.location.hash;
          var selector = hash ? 'a[href="' + hash + '"]' : 'li.active > a';
          $(selector, context).tab('show');
        }

        // We use pushState if it's available so the page won't jump, otherwise a shim.
        var changeHash = function(hash) {
          if (history && history.pushState) {
            history.pushState(null, null, '#' + hash);
          } else {
            scrollV = document.body.scrollTop;
            scrollH = document.body.scrollLeft;
            window.location.hash = hash;
            document.body.scrollTop = scrollV;
            document.body.scrollLeft = scrollH;
          }
        }

        // Set the correct tab when the page loads
        showTabFromHash(context)

        // Set the correct tab when a user uses their back/forward button
        $(window).on('hashchange', showTabFromHash);

        // Change the URL when tabs are clicked
        $('a', context).on('click', function(e) {
          var hash = this.href.split('#')[1];
          var adjustedhash = settings.getHashCallback(hash, this);
          changeHash(adjustedhash);
        });

        return this;
    };
}( jQuery ));

toastr.options = {
  "closeButton": false,
  "debug": false,
  "newestOnTop": false,
  "progressBar": false,
  "positionClass": "toast-bottom-right",
  "preventDuplicates": true,
  "onclick": null,
  "showDuration": "300",
  "hideDuration": "1000",
  "timeOut": "5000",
  "extendedTimeOut": "1000",
  "showEasing": "swing",
  "hideEasing": "linear",
  "showMethod": "fadeIn",
  "hideMethod": "fadeOut"
}



var paged = function(selector, options){
  this.$el = $(selector);
  options.hasextra  = (typeof options.extra == 'function');
  options.u_id =  Berry.getUID();
  options.label = options.label || 'Section';
  options.items = _.map(options.items, function(item) {
    item.key = options.u_id+item.name.toLowerCase().replace(/ /g,"_").split('.').join('_');
    return item;
  })
  options.fields = _.map(options.items, function(item) {
        return {fieldset:item.key,name:item.key};        
  })  
  options.actions = false;
  options.attributes  = {};
  _.map(options.items, function(item) {
    if(typeof item.content == 'object'){
      item.content = JSON.stringify(item.content)
    }
    options.attributes[item.key] = item.content; 
  })
  options.default ={label: false,type:'ace',mode:options.mode || 'ace/mode/handlebars'}
  this.options = $.extend(true,{editable: true,},options);
  $(selector).html(templates.pages.render(this.options,templates));
  this.berry = $(selector+' .dummyTarget').berry(this.options);

  this.render = function(){
    $(selector+' .list-group').empty().html(templates.pages_listgroupitem.render(this.options));
    $('[href="#'+this.active+'"]').click();
  }

  $(selector+' .actions .pages_delete,'+selector+' .actions .pages_edit,'+selector+' .actions .pages_new,'+selector+' .pages_extra').on('click', function(e){
    var currentItem = _.findWhere(this.options.items, {key: this.active});
    if($(e.currentTarget).hasClass('pages_delete') && !currentItem.disabled){
      currentItem.removed = true;
      this.render();
    }else{
      if($(e.currentTarget).hasClass('pages_edit') && !currentItem.disabled){
        $().berry({name:'page_name', legend: 'Edit '+options.label, attributes: {name: currentItem.name},fields: {'Name': {}}}).on('save', function(){
          _.findWhere(this.options.items, {key:this.active}).name = Berries.page_name.toJSON().name;
          this.render();
          Berries.page_name.trigger('close');
        }, this);
      }else{
        if($(e.currentTarget).hasClass('pages_new')){
          $().berry({name:'page_name', legend: 'New '+options.label,fields: {'Name': {}}}).on('save', function(){
            var name = Berries.page_name.toJSON().name;
            var key = this.options.u_id+name.toLowerCase().replace(/ /g,"_").split('.').join('_');;

            this.options.items.push({name: name,key:key, content:""})
            this.active = key;
            this.$el.find('.tab-content').append(templates.pages_tabpanel.render({name: name,key:key, content:""}));
            this.berry.createField($.extend({name:key},this.berry.options.default), this.$el.find('.tab-content').find('#'+key),null)
            this.render();

            Berries.page_name.trigger('close');
          }, this);
        }else{
          if($(e.currentTarget).hasClass('pages_extra')) {
            this.options.extra.call(this, currentItem);
          }else{
          	if(currentItem.disabled){
          		toastr.error('This action is disabled for this item','Disabled')
          	}
          }
        }
      }
    }
  }.bind(this));

  $(selector).on('click','.list-group-item.tab',function(e){
    $(e.currentTarget).parent().find('.list-group-item').removeClass('active');
    $(e.currentTarget).addClass('active');
    this.active = $(e.currentTarget).attr('aria-controls');
		$(e.currentTarget).parent().parent().find('button.dropdown-toggle').prop('disabled', 
			_.findWhere(this.options.items, {key: this.active}).disabled || false
		);
    this.berry.fields[this.active].editor.clearSelection();//.instances[0]
 		this.berry.fields[this.active].focus();
  }.bind(this))
  $(selector).find('.list-group-item.tab').first().click();

  return {
    toJSON:function(){
      var options = this.options;
      var temp = _.map(this.berry.toJSON(),function(item,i){
        var cachedItem = _.findWhere(options.items, {key:i});
        
        if(typeof cachedItem !== 'undefined' && !cachedItem.removed){
          return {name: _.findWhere(options.items, {key:i}).name, content: item};
        }else{
          return false;
        }
      });
      return _.filter(temp, function(item){return item;});
    }.bind(this),
    getCurrent:function(){
      return _.findWhere(this.options.items, {key: this.active});
    }.bind(this),
    update:function(key,value){
      this.berry.fields[key].setValue(value)
    }.bind(this),
    errors: function(){
    	// var errors = 0;
    	var errors = [];
    	_.each(this.options.items, function(item){
    		var items = _.where(this.berry.fields[item.key].editor.session.getAnnotations(), {type:"error"});
    		for(var i in items){
    			items[i].name = item.name;
    		}
				errors = errors.concat(items);
    		// var count = _.where(this.berry.fields[item.key].editor.session.getAnnotations(), {type:"error"}).length;
    		// errors+=items.length;

    		var content = item.name;

    		if(items.length){
    			content = '<span class="badge badge-danger">'+items.length+ '</span> '+ item.name;
				}
    		if(item.disabled){
    			content = '<i class="fa fa-ban"></i> ' +content;
    		}
				this.$el.find('.list-group-item.tab[name="'+item.key+'"]').html(content);
    		// console.log(_.where(this.berry.fields[item.key].editor.session.getAnnotations(), {type:"error"}).length)
    	}.bind(this))

    	return errors;
    }.bind(this)
  }
}
template = '<span class="text-muted">@{{target}}</span>@{{value}}'
viewTemplate = Hogan.compile('<div class="list-group">@{{#items}}<div class="list-group-item"><a target="_blank" href="/community/@{{group_id}}/@{{slug}}">@{{name}}</a></div>@{{/items}}</div>');


</script>