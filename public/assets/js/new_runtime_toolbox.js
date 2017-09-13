alert = function(value){ console.log(value); };
function render(template, data){
	if(typeof templates[template] === 'undefined'){
		templates[template] =  Hogan.compile($('#'+template).html());
	}
  return templates[template].render(data, templates);
}

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