alert = function(value){ console.log(value); };
function render(template, data){
	if(typeof templates[template] === 'undefined'){
		templates[template] =  Hogan.compile($('#'+template).html());
	}
  return templates[template].render(data, templates);
}


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




function generateUUID(){
    var d = new Date().getTime();
    var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        var r = (d + Math.random()*16)%16 | 0;
        d = Math.floor(d/16);
        return (c=='x' ? r : (r&0x3|0x8)).toString(16);
    });
    return uuid;
};




// var urlParams;
var hashParams
var QueryStringToHash = function QueryStringToHash  (query) {
  var query_string = {};
  var vars = query.split("&");
  for (var i=0;i<vars.length;i++) {
    var pair = vars[i].split("=");
    pair[0] = decodeURIComponent(pair[0]);
    pair[1] = decodeURIComponent((pair[1] || "").split('+').join(' '));
      // If first entry with this name
    if (typeof query_string[pair[0]] === "undefined") {
      query_string[pair[0]] = pair[1];
      // If second entry with this name
    } else if (typeof query_string[pair[0]] === "string") {
      var arr = [ query_string[pair[0]], pair[1] ];
      query_string[pair[0]] = arr;
      // If third or later entry with this name
    } else {
      query_string[pair[0]].push(pair[1]);
    }
  } 
  return query_string;
};