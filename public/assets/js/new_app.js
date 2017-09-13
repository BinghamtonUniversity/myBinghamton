alert = function(value){ console.log(value); };
Ractive.DEBUG = false;
function render(template, data){
	if(typeof templates[template] === 'undefined'){
		templates[template] =  Hogan.compile($('#'+template).html());
	}
  return templates[template].render(data, templates);
}

function message(options) {
	$.gritter.add($.extend({timeout: 3000, color: '#5F895F'}, options));
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

Date.createFromMysql = function(mysql_string){
   if(typeof mysql_string === 'string') {
      var t = mysql_string.split(/[- :]/);

      //when t[3], t[4] and t[5] are missing they defaults to zero
      return new Date(t[0], t[1] - 1, t[2], t[3] || 0, t[4] || 0, t[5] || 0);
   }

   return null;
};



$('.menu-button').on('click', function(e){
	$('.app>.main-container>.nav-container').toggleClass('showMenu');//.toggleClass('nav-horizontal nav-vertical');
});



$('.smallbarToggle').on('click', function(e){
		updateMenuState(!Lockr.get('menu'))
		$(document).trigger('resize')
});

function updateMenuState(state){
	Lockr.set('menu', state)
	$('body').toggleClass('smallbar', state);
}



$('.view-as').on('click', function(e){
	if($(e.currentTarget).data('group') == 'all'){
		$('body').attr('class','app');
	}else{
		$('body').toggleClass('group_'+$(e.currentTarget).data('group'));
		if(window.getComputedStyle($(".page-menu .page_"+pageID)[0]).display == 'none'){
			console.log('hidden');
			modal({title:'HIDDEN',content:'This Page is not visible to members of this group'})

			$('body').attr('class','app');
		}
	}
})

$('.mobile-layout').on('click', function(e){
	templates.listing = Hogan.compile('<ol class="list-group">{{#widgets}}<li data-guid="{{guid}}" class="list-group-item">{{widgetType}} - {{title}}</li>{{/widgets}}</ol>')
	var tempdata = [].concat.apply([],pageData)
	if(typeof mobile_order !== 'undefined'){
		tempdata = _.sortBy(tempdata, function(o) {
			return mobile_order.indexOf(o.guid);
		})
	}

	mymodal = modal({title: "Mobile Layout", content: templates.listing.render({widgets:tempdata} ), footer: '<div class="btn btn-success save-mobile">Save</div>'})

	Sortable.create($(mymodal.ref).find('.modal-content ol')[0], {draggable:'li'})
		
	$('.save-mobile').on('click', function(e){
		new_mobile_order = _.map($(mymodal.ref).find('.modal-content ol').find('li'), function(item){return item.dataset.guid});
		$.ajax(
			{
				url: '/community_pages/' + groupID +'/' + pageID, 
				data: {'mobile_order': JSON.stringify(new_mobile_order)},
				method: 'PUT',
				success: function(e){
					mobile_order = new_mobile_order;
					mymodal.ref.modal('hide');
				}
		});
	})
})

function renderPage(){
		var template = 'widgets_container'
		var content;
		var layout = pageLayout;
		if(editor){
			template = 'widgets_edit_container';
			content = pageData;
		}else{
			if(fullPage){
				content = [[$.extend(true,{}, fullPage, _.findWhere(JSON.parse(pagePreferences.content || '[]'), {guid: fullPage.guid}))]]
				layout = 4;
			}else{
				content = _.map(pageData, function(column) {
				    return _.map(column, function(el, i) {
							return $.extend(true,{}, el, _.findWhere(this.pref, {guid: el.guid}), {widgetType:el.widgetType});
				    },{pref: this.pref})
				},{pref:JSON.parse(pagePreferences.content || '[]')});
			}
		}
		if(typeof cb !== 'undefined'){
			if(editor){
				pageData = cb.toJSON({editor: true});
			}
			cb.destroy();
			delete cb;
		}
		$('.generated-content').html(Cobler.layouts[layout].template)

		var targets = document.getElementsByClassName('column');
		if(pageData.length > targets.length){
			for(var i = targets.length;i<pageData.length;i++){
				pageData[targets.length-1] = pageData[targets.length-1].concat(pageData[i])
			}
		}

		cb = new Cobler({ 
			disabled: !editor, 
			targets: targets, 
			items: content,
			itemContainer: template,
			itemTarget: 'widget'
		})

		if (editor) {
			cb.on('moved',save);
			cb.on('reorder', save);
			cb.on('remove', save);

			cb.on('manage',function(item){
				$().berry({name: 'modal', attributes: item.get(), legend: 'Visibility',flatten:true, fields:[
					{label: 'Device', name: 'device', type: 'select', value:'widget', choices: [{label: 'All', value:'widget'}, {label: 'Desktop Only', value:'hidden-xs hidden-sm'},{label: 'Tablet and Desktop', value:'hidden-xs'} ,{label: 'Tablet and Phone', value:'hidden-md hidden-lg'} ,{label: 'Phone Only', value:'visible-xs-block'} ] },
					{label: 'Allow Minimization', name: 'enable_min', type: 'checkbox', show:!item.no_minimize},
					{label: 'Limit to Group', name: 'limit', type: 'checkbox', show:  {test: function(){return composites.length >0;}} },
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
				]})
				.on('save',function(){
					// this.set(Berries.modal.toJSON());
					this.container.update(Berries.modal.toJSON(), this);

					Berries.modal.trigger('saved');
					save();
				},item).on('change:ids', function(){
					this.fields.limit.trigger('change');
				}).fields.limit.trigger('change');
			})

		}

		cb.on('min',function(item){
			item.$el.find('.panel-heading').css({'border-bottom': 0});
			item.$el.find('.collapsible').toggle(400 , $.proxy(function() {
				item.set({collapsed: item.$el.find('.widget').toggleClass('wf-collapsed').hasClass('wf-collapsed') });
				save();
			},item) );
		})

		cb.on('user_edit',function(item){
			if(item.get().widgetType == 'Service'){
				item.modal = 
				$().berry({name:'modal',attributes: item.get(), fields: item.userEdit, legend: 'Edit Service'}).on('save',function() {
									// this.modal.trigger('saved')
					Berries.modal.trigger('close')
					this.set(this.modal.toJSON());
					this.container.update({loaded: false}, this)
					save();
				}, item);
			}else{
				item.modal = 
				$().berry({name:'modal',attributes: item.get(), fields: item.userEdit, legend: 'Edit MicroApp'}).on('save',function() {
					Berries.modal.trigger('close')
					var temp = this.modal.toJSON();
					temp.loaded = {data:{options:this.modal.toJSON()}};
					this.set(temp);
					if(typeof this.app !== 'undefined'){
						this.app.trigger('options');
					}
					// this.set(this.modal.toJSON());
					// this.set({loaded:{data:{options:this.modal.toJSON()}}});
					// this.update();
					this.ractive.set($.extend(true, {}, this.get().loaded.data));

					save();
				}, item);
			}
		})

		cb.on('full',function(item){
			var temp = item.toJSON({editor:true})
			if(temp.guid !== fullPage.guid){
				fullPage = temp;
				$('body').addClass('full-page')
			}else{
				fullPage = false;
				$('body').removeClass('full-page');
			}
			renderPage();
		})
		// cb.on('download',function(item){
		// 	window.location='/preferenceSummary/'+pageID+'/'+item.get().guid;
		// })

		cb.on('add', function(item){
			if(item.get().widgetType == 'LinkCollection'){
				$().berry({name: 'modal', fields: {
					Link: { placeholder: 'http://'},
					Title: {},
					Icon: {choices: icons, type: 'select'},
					Image: {},
					Color: {type: 'color'},
					'Default Favorite': {
						type: 'checkbox',
						name: 'favorite'
					},
					guid: {type: 'hidden', value: generateUUID()}
				}, legend: 'Add Link'}).on('save', $.proxy(function(){
					this.set(this.get().links.push(Berries.modal.toJSON()))
					this.container.update(this.get(), this);
					save();
					Berries.modal.trigger('close');	

				}, item ));

			}
		});

}



$('.begin-editing').on('click', function(e){
	if(editUrl !== ''){
		window.location = editUrl;
	}else{
		$('body').attr('class','app');
		editor = (editable && !editor);
		$('body').toggleClass('editing', editor)

		renderPage();





		templates['layout'] = Hogan.compile('<i class="{{classes}}"></i>');
		//$('.layout-area').find('[name="layout"]').html(templates['layout'].render(widget_factory.prototype.layouts[pageLayout]));
		var berry = $('.layout-area').berry({
			// name: 'layout',
			legend: 'Change Layout', 
			renderer: 'popins', 
			inline:false,
			popins: {
				placement: 'bottom',
				container: '.top-nav'
			},
			fields:[
				{
					defaultClass: 'btn-white',
					template:'layout',
					hideLabel: true,
					type:'custom_radio',
					label:'Layout',
					name:'layout',
					classes: Cobler.layouts[pageLayout].classes, 
					choices: Cobler.layouts, 
					value: pageLayout
				}
			]
		}).on('change:layout', function() {
			this.fields.layout.classes = this.fields.layout.choices[this.fields.layout.value].classes;
		}).on('updated',function(){
			pageLayout = this.toJSON().layout;
			// wf.load(wf.data, pageLayout);
			$.ajax(
				{
					url: '/community_pages/'+ groupID + '/' + pageID, 
					data: {'layout': pageLayout},
					method: 'PUT'
			});
			renderPage();
			// this.trigger('saved');
		});
	}
   // $('body').find('.popovers').popover();

})
$('.add-widget').on('click', function(e){
	cb.collections[0].addItem({widgetType: $(e.currentTarget).data('name'), guid: generateUUID()});
})

function save(){
	var url = '/page_preference/' + pageID;
	var content = cb.toJSON({editor: editor});

	if(editor){
		pageData = content;
		url = '/community_pages/' + groupID +'/' + pageID;
	}else{
		content = [].concat.apply([],content);
		pagePreferences = content;
	}
	var data = {'content': JSON.stringify(content)};
	if(authenticated){
		$.ajax({url: url, data: data, method: 'PUT'});
	}else if(!editor){
		Lockr.set(url, data)
	}
}

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
breadViewer = berryTable;

Berry.conditions.test = function(Berry, args, func) {
	return Berry.on('change:' + this.name, $.proxy(function(args, local, topic, token) {
			func.call(this, args(), token);
		}, this, args)
	).lastToken;
}

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

function getCreate(path){
	var path = Hogan.compile(path);
	return function(model){
		$.ajax({
			url: path.render(model.attributes),
			type: 'POST', 
			data: model.attributes,
			success:function(data){

          if(data.error) {
              this.delete();
              this.owner.draw();
              if (data.error.message) {
                  toastr.error(data.error.message, 'ERROR');
              } else {
                  toastr.error(data.error, 'ERROR');
              }
          } else if (typeof data != 'object') {
              this.delete();
              this.owner.draw();
              toastr.error('Creation Failed', 'ERROR')
          } else{
              this.set($.extend({}, this.attributes, data));
              this.owner.draw();
              toastr.success('', 'Successfully Added');
          }
    }.bind(model),
    error:function(e){
	      this.delete();
	    	this.owner.draw();
				toastr.error(e.statusText, 'ERROR');
    	}.bind(model)
    });
  }.bind(this)
}

function getEdit(path){
	var path = Hogan.compile(path);
	return function(model){
		$.ajax({
			url: path.render(model.attributes),
			type: 'PUT', 
			data: model.attributes,
			success: function(data){
              if(data.error){
                  if (data.error.message) {
                      toastr.error(data.error.message, 'ERROR');
                  } else {
                      toastr.error(data.error, 'ERROR');
                  }
                  this.undo();
                  this.owner.draw();
              } else if (typeof data != 'object') {
                  this.undo();
                  this.owner.draw();
                  toastr.error('Edit Failed', 'ERROR')
              }else{
                  this.set(data);
                  this.owner.draw();
                  toastr.success('', 'Successfully Updated');
              }
	    }.bind(model),
	    error:function(e){
              this.undo();
              this.owner.draw();
			toastr.error(e.statusText, 'ERROR');
        }.bind(this)
		});

  }.bind(this)
}

function getDelete(path){
	var path = Hogan.compile(path);
	return function(model){ 
		$.ajax({
			url: path.render(model.attributes),
			type: 'DELETE', 
			data: model.attributes,
			success: function(data){
        if(data.error){
            if (data.error.message) {
                toastr.error(data.error.message, 'ERROR');
            } else {
                toastr.error(data.error, 'ERROR');
            }
        } else{
            toastr.success('', 'Successfully Deleted');
        }
	    },
	    error: function(e){
					toastr.error(e.statusText, 'ERROR');
	      }
	    });

    }.bind(this)
}
