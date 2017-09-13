Cobler.types.Content = function(container){
	function render() {
		return templates['widgets_content'].render(get(), templates);
	}
	function get() {
		item.widgetType = 'Content';
		return item;
	}
	function set(newItem) {
		$.extend(true, item, newItem);
	}
	function toJSON(opts){
		if(opts.editor){return get();}

		return {
			guid: item.guid,
			collapsed: item.collapsed
		}
	}
	var item = {
		title: 'This is the title',
		text: 'Here is some text'
	}
	var fields = {
		Title: {},
		Text: {type: 'contenteditable', label: false}
	}
	return {
		fields: fields,
		render: render,
		toJSON: toJSON,
		edit: berryEditor.call(this, container),
		get: get,
		set: set,
		container: container,
		initialize: function(el) {
			this.$el = $(el);
		}
	}
}

Cobler.types.Image = function(container){
	function render() {
		if(!item.container && this.container.owner.options.disabled){
			return templates['widgets_image'].render(get(), templates);
		}else{
			return templates['widgets_image_header'].render(get(), templates);
		}
	}
	function get() {
		item.widgetType = 'Image';
		return item;
	}
	function toJSON(opts){
		if(opts.editor){return get();}
		
		return {
			guid: item.guid,
			collapsed: item.collapsed
		}
	}
	function set(newItem) {
		$.extend(true, item, newItem);
	}
	var item = {
	}
	var fields = {
		Title: {},
		Container: {label: "Container?", type: 'checkbox'},
		Image: {type: 'image_picker', choices: '/images?group_id='+groupID, reference: 'image_filename', value_key: 'image_filename'},
		Text: {label: 'Alt Text', required: true}
	}
	return {
		fields: fields,
		render: render,
		toJSON: toJSON,
		edit: berryEditor.call(this, container),
		get: get,
		set: set,
		container: container,
		initialize: function(el) {
			this.$el = $(el);
		}
	}
}


Cobler.types.Slider = function(container){
	function render() {
		return templates['widgets_slider'].render(get(), templates);
	}
	function get() {
		item.widgetType = 'Slider';
		return item;
	}
	function toJSON(opts) {
		if(opts.editor){return get();}
		
		return {
			guid: item.guid,
			collapsed: item.collapsed
		}
	}
	function set(newItem) {
		$.extend(true, item, newItem);
		if(typeof newItem.images !== 'undefined'){
			item.images = newItem.images;
		}
	}
	var item = {
		images: [{image: '' , url:'', text: '', overlay: ''}]
	}
	var fields = {
		"My Images":{label: false,fields:[
			{type: 'fieldset',name:"images", label: false, multiple: {duplicate: true}, fields: [
				{ name: 'image', type: 'image_picker', choices: '/images?group_id='+groupID, value_key: 'image_filename', label: 'Image'},
				{ name: 'url', label: 'Link', placeholder: 'http://'},
				{ name: 'window', label: 'New Window?', type: 'checkbox'},
				{ name: 'text', label: 'Alt Text', required: true},
				{ name: 'overlay', label: 'Overlay'}
			]}
		]}
	}
	return {
		fields: fields,
		render: render,
		toJSON: toJSON,
		edit: function(){
			return function(){
				this.$el.find('.collapsible').addClass('panel-body');
				this.$el.find('.collapsible').berry({
					attributes: this.get(), 
					fields: this.fields,
					name: this.get().guid
				}).on('save', function(){
					if(Berries[this.get().guid].validate()){
					 	container.update(Berries[this.get().guid].toJSON(), this);
					 	container.deactivate();
					 	save();
					 	Berries[this.get().guid].trigger('saved');
					 }
				}, this).on('cancel',function(){
				 	container.update(this.get(), this)
				 	container.deactivate();
				}, this)
				// return myBerry;
			}
		}.call(this),
		get: get,
		set: set,
		container: container,
		no_minimize:true,
		initialize: function(el){
			this.$el = $(el);
			// if(this.container.owner.options.disabled){
				$(el).find('.slider').nivoSlider({effect: 'fade'});
			// }
				
		}
	}
}

Cobler.types.Html = function(container){
	function render() {
		var temp = get();//$.extend(true, {}, get());
		temp.html = temp.html.replace(/<\\\/script>/g, "</script>");
		if(!item.container && this.container.owner.options.disabled) {
			return templates['widgets_html'].render(temp, templates);
		} else {
			return templates['widgets_html_container'].render(temp, templates);
		}
	}
	function get() {
		item.widgetType = 'Html';
		item.html = item.html.replace(/<\/script>/g, "<\\/script>");

		return item;
	}
	function toJSON(opts){
		if(opts.editor){return get();}
		
		return {
			guid: item.guid,
			collapsed: item.collapsed
		}
	}
	function set(newItem) {
		$.extend(true, item, newItem);
		item.html =  item.html.replace(/<\\\/script>/g, "</script>");
	}
	var item = {
		html: ''
	}
	var fields = {
			Container: {label: "Container?", type: 'checkbox'},
			Title: {},
			HTML: {type: 'ace', label:false}
	}
	return {
		fields: fields,
		render: render,
		toJSON: toJSON,
		edit: berryEditor.call(this, container),
		get: get,
		set: set,
		container: container,
		formTarget: $(),
		formOptions: {legend: 'Edit RAW HTML'},
		initialize: function(el){
				this.$el = $(el);
				$(el).html($(el).html())
				$(el).find('[data-toggle="tooltip"]').tooltip();
	    	$(el).find('[data-toggle="popover"]').popover();
		}
	}
}

Cobler.types.RSS = function(container) {
	// var container = container;
	function render() {	
		var temp = get()
		if(typeof temp.loaded === 'undefined' && temp.count > 0 && temp.url){
				$.ajax({
				  url      : document.location.protocol + '//ajax.googleapis.com/ajax/services/feed/load?v=1.0&num='+temp.count+'&callback=?&q=' + encodeURIComponent(temp.url),
				  dataType : 'json',
				  success  : $.proxy(function (data) {
				  	var feed = data.responseData.feed
				    if (feed && feed.entries) {
				    	for(var i in feed.entries){
								feed.entries[i].contentSnippet = feed.entries[i].contentSnippet.replace(/&lt;/,"<").replace(/&gt;/,">").replace(/&amp;/,"&");
				    	}
				    	var temp = get();
				    	temp.loaded = feed;
				    	container.update(temp, this);
				    }
				  }, this)
				});
			}
		return templates['widgets_rss'].render(temp, templates);
	}
	function get() {
		item.widgetType = 'RSS';
		return item;
	}
	function toJSON(opts){
		if(opts.editor){
			var temp = get();
			delete temp.loaded;
			return temp;
		}
		
		return {
			guid: item.guid,
			collapsed: item.collapsed
		}
	}
	function set(newItem) {
		item = newItem;
	}
	var item = {

	};
	var fields = {
		Title: {},
		Url: {value: 'http://www.binghamton.edu/photos/index.php/feed/'},
		Count: {value: 5, type: 'number'},
	}
	return {
		fields: fields,
		render: render,
		toJSON: toJSON,
		container: container,
		edit: berryEditor.call(this, container),
		get: get,
		set: set,
		initialize: function(el) {
			this.$el = $(el);
		}
	}
}

Cobler.types.Poll = function(container){
	function render() {
		return templates['widgets_poll'].render(get(), templates);
	}
	function get() {
		item.widgetType = 'Poll';
		return item;
	}
	function toJSON(opts){
		if(opts.editor){
			var temp = get();
			delete temp.loaded;
			return temp;
		}
		
		return {
			guid: item.guid,
			collapsed: item.collapsed
		}
	}
	function set(newItem) {
		this.changed = (typeof newItem.poll !== 'undefined' && item.poll !== newItem.poll);
		$.extend(item, newItem);
	}
	var item = {
	}
	var fields = {
		Title: {},
		Poll: {type: 'select', choices: '/polls?group_id='+groupID, label_key: 'poll_name'}
	}
	return {
		fields: fields,
		render: render,
		toJSON: toJSON,
		edit: berryEditor.call(this, container),
		get: get,
		set: set,
		container: container,
		generateTable: function(data){
				var temp = {results:[], total: data.total};
				for(var i in data.results){
					temp.results.push({name:i, value: data.results[i], percent: ((data.results[i]/data.total)*100).toFixed(1) })
				}								
				this.$el.find('.poll_content').html(templates.poll_table.render(temp, templates));
			}, 
		initialize: function(el){
			if(!item.loaded || this.changed){
				$.ajax({
					url      : '/polllive/' + item.poll,
					dataType : 'json',
					success  : $.proxy(function (data) {
					this.data = data;
					this.container.update({loaded: {choices: JSON.parse(data.content), poll_name: data.poll_name, shuffle: data.shuffle} },this);
					this.container.deactivate()
					}, this)
				});
			}
			this.$el = $(el);		
			if(item.loaded){
				if(container.owner.options.disabled && this.data.results){
					this.generateTable(this.data);
				} else {
					var choices = _.pluck(JSON.parse(this.data.content), 'label');
					if(item.loaded.shuffle){
						choices = _.shuffle(choices);
					}
					this.berry = this.$el.find('.poll_content').berry({name: item.guid, actions:['save'], fields:[{label:false, name:'choice', type:'radio',choices: choices}]}).on('save',$.proxy(function(){
						$.ajax({
							url      : '/pollsubmit',
							dataType : 'json',
							data: {poll_id: item.poll, choice: this.berry.toJSON().choice},
							method: 'post',
							success  : $.proxy(function (data) {
								this.generateTable(data);
							}, this)
						});
					},this));
				}
			}
		}
	}
}

Berry.btn.submit= {
		label: 'Submit',
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
Berry.btn.wait= {
		label: 'Submitting',
		icon:'spinner fa-spin',
		id: 'berry-wait',
		modifier: 'warning pull-right',
		click: function() {
		}
	};
Cobler.types.Form = function(container){
	function render() {
		if(item.container){
			return templates['widgets_form_container'].render(get(), templates);
		}else{
			return templates['widgets_form_container'].render(get(), templates);
		}
	}
	function get() {
		item.widgetType = 'Form';
		return item;
	}
	function toJSON(opts){
		if(opts.editor){
			var temp = get();
			delete temp.loaded;
			return temp;
		}
		
		return {
			guid: item.guid,
			collapsed: item.collapsed
		}
	}
	function set(newItem) {
		this.changed = (typeof newItem.form !== 'undefined' && item.form !== newItem.form);

		$.extend(item, newItem);
	}
	var item = {
		title: 'This is the title',
		text: 'Here is some text'
	}
	var fields = {
		Title: {},
		Form: {type: 'select', choices: '/forms?group_id='+groupID, label_key: 'form_name'},
		Container: {label: "Container?", type: 'checkbox'}
	}
	return {
		fields: fields,
		render: render,
		toJSON: toJSON,
		edit: berryEditor.call(this, container),
		get: get,
		set: set,		
		container: container,
		initialize: function(el){
				if(!item.loaded || this.changed){
					$.ajax({
						url      : '/forms/' + item.form,
						dataType : 'json',
						success  : $.proxy(function (data) {
								this.container.update({loaded: {fields: JSON.parse(data.fields||"{}"),options: JSON.parse(data.options||"{}"), name: data.name} },this);
								this.container.deactivate()
						}, this)
					});
				}

				this.$el = $(el);		
				if(typeof item.loaded !== 'undefined' && (typeof item.loaded.fields !== 'undefined' || item.loaded.data_type == 'None' )) {

					this.berry = this.$el.find('.form_content').berry({name:item.form ,autoDestroy: false, inline: item.loaded.options.inline , action: '/formsubmit/'+item.form ,actions:['submit'], fields: item.loaded.fields});
					this.berry.on('saveing', function(){
						this.setActions(['wait']);
					});
					this.berry.on('saved', function(data){
						if(data.success){
							this.berry.destroy();
							this.$el.html('<div class="alert alert-success">Thank you for your submission. It has been successfully logged!</div>')
						}else{
							message({title:'Error', text: 'Form failed to submit', color: '#ff0000'});
							this.berry.setActions(['submit']);
						}
					}, this);
				}
		}
	}
}


// Cobler.types.Links = function(container){
// 	function get() {
// 		item.widgetType = 'Links';
// 		return item;
// 	}
// 	function toJSON(opts){
// 		if(opts.editor){return get();}
		
// 		return {
// 			guid: item.guid,
// 			collapsed: item.collapsed
// 		}
// 	}
// 	var item = {}
// 	var	template = false; 
// 	if(!container.owner.options.disabled){
// 		template = 'widgets_edit_links_container'
// 	}

// 	return {
// 		edit:function(){},
// 		render: function() {
// 			//item.groups[0].links
// 			if(this.container.owner.options.disabled){
// 				return templates['widgets_links'].render(get(), templates);
// 			}else{
// 				return templates['widgets_edit_links'].render(get(), templates);
// 			}
// 		},
// 		template:template,
// 		toJSON: get,
// 		get: get,
// 		container: container,
// 		set: function (newItem) {
// 			newItem = _.pick(newItem, 'widgetType', 'title','limit', 'guid', 'collapse', 'filterable', 'enable_min', 'device', 'collapse', 'groups')
// //			newItem = JSON.parse('{"widgetType":"Links","title":"","limit":false,"guid":"a085561e-7448-4fbf-8031-8e7721b55e24","collapse":[{"title":"favorites","collapse":false},{"title":"links","collapse":false}],"filterable":true,"enable_min":false,"device":[],"groups":[{"label":"My Favorites","title":"favorites","links":[{"title":"B-Engaged","link":"https://binghamton.collegiatelink.net/account/logon","guid":"0e23ef5a-7a93-4ebc-add6-4f13e7e3b116","image":"https://buportalprod.s3.amazonaws.com/assets/b-engaged.jpg","color":"","icon":""},{"title":"TutorTrac","link":"https://tutortrac.binghamton.edu/","guid":"11d3875c-0577-4478-98cc-b615078e2a4c","image":"","color":"","icon":"fa fa-graduation-cap"},{"title":"HireBING","link":"https://binghamton-csm.symplicity.com/students/","guid":"5a45ee9f-d5e7-446f-87d4-c9b9d735c409","image":"","color":"#a86322","icon":"fa fa-briefcase"}],"collapse":false},{"label":"Useful Links","title":"links","links":[{"title":"Academic Calendar","link":"http://www.binghamton.edu/about/university-calendar.html","guid":"74e105f0-845d-4fe6-bbd4-d94bc07f33bc","image":"","color":"","icon":"fa fa-calendar"},{"title":"Academic: Important Dates & Deadlines","link":"http://www2.binghamton.edu/registrar/deadlines.html","guid":"85215459-10e3-41c3-a84d-1b22d5a3f34b","image":"","color":"#f7b636","icon":"fa fa-warning"},{"title":"BMail","link":"http://bmail.binghamton.edu/","guid":"6a3fdc1b-809f-43c4-ae27-e6de5ccb6501","image":"https://ssl.gstatic.com/ui/v1/icons/mail/images/favicon5.ico","color":"","icon":""},{"title":"BMail Calendar","link":"https://www.google.com/calendar/","guid":"2a0fe307-27b8-470e-b57d-fb8c20f1d31d","image":"","color":"#2e88db","icon":"fa fa-calendar"},{"title":"BMail Drive ","link":"https://drive.google.com/?tab=mo&authuser=0","guid":"652eead5-94a4-4d50-84c4-ce7df5142e88","image":"https://ssl.gstatic.com/docs/doclist/images/infinite_arrow_favicon_4.ico","color":"","icon":""},{"title":"binghamton.edu","link":"http://www.binghamton.edu/libraries/","guid":"f212f353-30b4-4e34-a3d6-6d7d9c2e3b87","image":"https://buportalprod.s3.amazonaws.com/assets/B-icon.png","color":"","icon":false},{"title":"Campus Map (pdf)","link":"https://www.binghamton.edu/visiting-campus/pdfs/updated-campus-map.pdf","guid":"f2a49ef0-7b6e-43e7-9493-8891142d5dd0","image":"","color":"","icon":"fa fa-map-o"},{"title":"Directory","link":"http://www.telecom.binghamton.edu/","guid":"f84d5fa9-b276-4a62-9d9b-211778d54eea","image":"","color":"","icon":"fa fa-phone"},{"title":"Libraries","link":"http://www.binghamton.edu/libraries/","guid":"21dbebf2-f9c6-40cf-9f08-190953849be9","image":"","color":"","icon":"fa fa-book"},{"title":"myHealth","link":"https://myhealth.binghamton.edu/","guid":"b002e550-d266-4cdb-ab8b-af8669be1702","image":"","color":"#cc1212","icon":"fa fa-stethoscope"},{"title":"OCCT Bus Locations","link":"http://bupublic.etaspot.net","guid":"d0f7052c-259e-40a2-8529-c628a155efca","image":"","color":"#2b86d9","icon":"fa fa-bus"},{"title":"Parking","link":"https://myparking.binghamton.edu/cmn/auth.aspx","guid":"99ccef3b-fe2d-4491-9dff-fbc62f8e1146","image":"","color":"","icon":"fa fa-car"},{"link":"https://pinnacle.binghamton.edu/","title":"Pinnacle","icon":"fa fa-phone-square","image":"","color":"#56c73a","guid":"74209ab2-7edf-4bf7-ad79-d665232f1567"}],"collapse":false}]}');

// 			$.extend(true, item, newItem);
// 		},
// 		initialize: function(el) {
// 			this.$el = $(el);
// 		}
// 	}
// }




Cobler.types.Links = function(container){
	function get() {
		item.widgetType = 'Links';
		return item;
	}
	function toJSON(opts){
		if(opts.editor){return get();}
		
		return {
			guid: item.guid,
			collapsed: item.collapsed
		}
	}
	var item = {}
	var	template = false; 
	if(!container.owner.options.disabled){
		template = 'widgets_edit_links_container'
	}

	return {
		edit:function(){},
		render: function() {

			if(this.container.owner.options.disabled){
				return templates['widgets_links'].render(get(), templates);
			}else{
				return templates['widgets_edit_links'].render(get(), templates);
			}
		},
		template:template,
		toJSON: get,
		get: get,
		container: container,
		set: function (newItem) {
			newItem = _.pick(newItem, 'widgetType', 'title','limit', 'guid', 'collapse', 'filterable', 'enable_min', 'device', 'collapse', 'groups')

			$.extend(true, item, newItem);
		},
		initialize: function(el) {
			this.$el = $(el);
		}
	}
}





icons = [{"name":"None","value":false}, {"name":"\n       glass\n    ","value":"fa fa-glass"},{"name":"\n       music\n    ","value":"fa fa-music"},{"name":"\n       search\n    ","value":"fa fa-search"},{"name":"\n       envelope-o\n    ","value":"fa fa-envelope-o"},{"name":"\n       heart\n    ","value":"fa fa-heart"},{"name":"\n       star\n    ","value":"fa fa-star"},{"name":"\n       star-o\n    ","value":"fa fa-star-o"},{"name":"\n       user\n    ","value":"fa fa-user"},{"name":"\n       film\n    ","value":"fa fa-film"},{"name":"\n       th-large\n    ","value":"fa fa-th-large"},{"name":"\n       th\n    ","value":"fa fa-th"},{"name":"\n       th-list\n    ","value":"fa fa-th-list"},{"name":"\n       check\n    ","value":"fa fa-check"},{"name":"\n       times\n    ","value":"fa fa-times"},{"name":"\n       search-plus\n    ","value":"fa fa-search-plus"},{"name":"\n       search-minus\n    ","value":"fa fa-search-minus"},{"name":"\n       power-off\n    ","value":"fa fa-power-off"},{"name":"\n       signal\n    ","value":"fa fa-signal"},{"name":"\n       cog\n    ","value":"fa fa-cog"},{"name":"\n       trash-o\n    ","value":"fa fa-trash-o"},{"name":"\n       home\n    ","value":"fa fa-home"},{"name":"\n       file-o\n    ","value":"fa fa-file-o"},{"name":"\n       clock-o\n    ","value":"fa fa-clock-o"},{"name":"\n       road\n    ","value":"fa fa-road"},{"name":"\n       download\n    ","value":"fa fa-download"},{"name":"\n       arrow-circle-o-down\n    ","value":"fa fa-arrow-circle-o-down"},{"name":"\n       arrow-circle-o-up\n    ","value":"fa fa-arrow-circle-o-up"},{"name":"\n       inbox\n    ","value":"fa fa-inbox"},{"name":"\n       play-circle-o\n    ","value":"fa fa-play-circle-o"},{"name":"\n       repeat\n    ","value":"fa fa-repeat"},{"name":"\n       refresh\n    ","value":"fa fa-refresh"},{"name":"\n       list-alt\n    ","value":"fa fa-list-alt"},{"name":"\n       lock\n    ","value":"fa fa-lock"},{"name":"\n       flag\n    ","value":"fa fa-flag"},{"name":"\n       headphones\n    ","value":"fa fa-headphones"},{"name":"\n       volume-off\n    ","value":"fa fa-volume-off"},{"name":"\n       volume-down\n    ","value":"fa fa-volume-down"},{"name":"\n       volume-up\n    ","value":"fa fa-volume-up"},{"name":"\n       qrcode\n    ","value":"fa fa-qrcode"},{"name":"\n       barcode\n    ","value":"fa fa-barcode"},{"name":"\n       tag\n    ","value":"fa fa-tag"},{"name":"\n       tags\n    ","value":"fa fa-tags"},{"name":"\n       book\n    ","value":"fa fa-book"},{"name":"\n       bookmark\n    ","value":"fa fa-bookmark"},{"name":"\n       print\n    ","value":"fa fa-print"},{"name":"\n       camera\n    ","value":"fa fa-camera"},{"name":"\n       font\n    ","value":"fa fa-font"},{"name":"\n       bold\n    ","value":"fa fa-bold"},{"name":"\n       italic\n    ","value":"fa fa-italic"},{"name":"\n       text-height\n    ","value":"fa fa-text-height"},{"name":"\n       text-width\n    ","value":"fa fa-text-width"},{"name":"\n       align-left\n    ","value":"fa fa-align-left"},{"name":"\n       align-center\n    ","value":"fa fa-align-center"},{"name":"\n       align-right\n    ","value":"fa fa-align-right"},{"name":"\n       align-justify\n    ","value":"fa fa-align-justify"},{"name":"\n       list\n    ","value":"fa fa-list"},{"name":"\n       outdent\n    ","value":"fa fa-outdent"},{"name":"\n       indent\n    ","value":"fa fa-indent"},{"name":"\n       video-camera\n    ","value":"fa fa-video-camera"},{"name":"\n       picture-o\n    ","value":"fa fa-picture-o"},{"name":"\n       pencil\n    ","value":"fa fa-pencil"},{"name":"\n       map-marker\n    ","value":"fa fa-map-marker"},{"name":"\n       adjust\n    ","value":"fa fa-adjust"},{"name":"\n       tint\n    ","value":"fa fa-tint"},{"name":"\n       pencil-square-o\n    ","value":"fa fa-pencil-square-o"},{"name":"\n       share-square-o\n    ","value":"fa fa-share-square-o"},{"name":"\n       check-square-o\n    ","value":"fa fa-check-square-o"},{"name":"\n       arrows\n    ","value":"fa fa-arrows"},{"name":"\n       step-backward\n    ","value":"fa fa-step-backward"},{"name":"\n       fast-backward\n    ","value":"fa fa-fast-backward"},{"name":"\n       backward\n    ","value":"fa fa-backward"},{"name":"\n       play\n    ","value":"fa fa-play"},{"name":"\n       pause\n    ","value":"fa fa-pause"},{"name":"\n       stop\n    ","value":"fa fa-stop"},{"name":"\n       forward\n    ","value":"fa fa-forward"},{"name":"\n       fast-forward\n    ","value":"fa fa-fast-forward"},{"name":"\n       step-forward\n    ","value":"fa fa-step-forward"},{"name":"\n       eject\n    ","value":"fa fa-eject"},{"name":"\n       chevron-left\n    ","value":"fa fa-chevron-left"},{"name":"\n       chevron-right\n    ","value":"fa fa-chevron-right"},{"name":"\n       plus-circle\n    ","value":"fa fa-plus-circle"},{"name":"\n       minus-circle\n    ","value":"fa fa-minus-circle"},{"name":"\n       times-circle\n    ","value":"fa fa-times-circle"},{"name":"\n       check-circle\n    ","value":"fa fa-check-circle"},{"name":"\n       question-circle\n    ","value":"fa fa-question-circle"},{"name":"\n       info-circle\n    ","value":"fa fa-info-circle"},{"name":"\n       crosshairs\n    ","value":"fa fa-crosshairs"},{"name":"\n       times-circle-o\n    ","value":"fa fa-times-circle-o"},{"name":"\n       check-circle-o\n    ","value":"fa fa-check-circle-o"},{"name":"\n       ban\n    ","value":"fa fa-ban"},{"name":"\n       arrow-left\n    ","value":"fa fa-arrow-left"},{"name":"\n       arrow-right\n    ","value":"fa fa-arrow-right"},{"name":"\n       arrow-up\n    ","value":"fa fa-arrow-up"},{"name":"\n       arrow-down\n    ","value":"fa fa-arrow-down"},{"name":"\n       share\n    ","value":"fa fa-share"},{"name":"\n       expand\n    ","value":"fa fa-expand"},{"name":"\n       compress\n    ","value":"fa fa-compress"},{"name":"\n       plus\n    ","value":"fa fa-plus"},{"name":"\n       minus\n    ","value":"fa fa-minus"},{"name":"\n       asterisk\n    ","value":"fa fa-asterisk"},{"name":"\n       exclamation-circle\n    ","value":"fa fa-exclamation-circle"},{"name":"\n       gift\n    ","value":"fa fa-gift"},{"name":"\n       leaf\n    ","value":"fa fa-leaf"},{"name":"\n       fire\n    ","value":"fa fa-fire"},{"name":"\n       eye\n    ","value":"fa fa-eye"},{"name":"\n       eye-slash\n    ","value":"fa fa-eye-slash"},{"name":"\n       exclamation-triangle\n    ","value":"fa fa-exclamation-triangle"},{"name":"\n       plane\n    ","value":"fa fa-plane"},{"name":"\n       calendar\n    ","value":"fa fa-calendar"},{"name":"\n       random\n    ","value":"fa fa-random"},{"name":"\n       comment\n    ","value":"fa fa-comment"},{"name":"\n       magnet\n    ","value":"fa fa-magnet"},{"name":"\n       chevron-up\n    ","value":"fa fa-chevron-up"},{"name":"\n       chevron-down\n    ","value":"fa fa-chevron-down"},{"name":"\n       retweet\n    ","value":"fa fa-retweet"},{"name":"\n       shopping-cart\n    ","value":"fa fa-shopping-cart"},{"name":"\n       folder\n    ","value":"fa fa-folder"},{"name":"\n       folder-open\n    ","value":"fa fa-folder-open"},{"name":"\n       arrows-v\n    ","value":"fa fa-arrows-v"},{"name":"\n       arrows-h\n    ","value":"fa fa-arrows-h"},{"name":"\n       bar-chart\n    ","value":"fa fa-bar-chart"},{"name":"\n       twitter-square\n    ","value":"fa fa-twitter-square"},{"name":"\n       facebook-square\n    ","value":"fa fa-facebook-square"},{"name":"\n       camera-retro\n    ","value":"fa fa-camera-retro"},{"name":"\n       key\n    ","value":"fa fa-key"},{"name":"\n       cogs\n    ","value":"fa fa-cogs"},{"name":"\n       comments\n    ","value":"fa fa-comments"},{"name":"\n       thumbs-o-up\n    ","value":"fa fa-thumbs-o-up"},{"name":"\n       thumbs-o-down\n    ","value":"fa fa-thumbs-o-down"},{"name":"\n       star-half\n    ","value":"fa fa-star-half"},{"name":"\n       heart-o\n    ","value":"fa fa-heart-o"},{"name":"\n       sign-out\n    ","value":"fa fa-sign-out"},{"name":"\n       linkedin-square\n    ","value":"fa fa-linkedin-square"},{"name":"\n       thumb-tack\n    ","value":"fa fa-thumb-tack"},{"name":"\n       external-link\n    ","value":"fa fa-external-link"},{"name":"\n       sign-in\n    ","value":"fa fa-sign-in"},{"name":"\n       trophy\n    ","value":"fa fa-trophy"},{"name":"\n       github-square\n    ","value":"fa fa-github-square"},{"name":"\n       upload\n    ","value":"fa fa-upload"},{"name":"\n       lemon-o\n    ","value":"fa fa-lemon-o"},{"name":"\n       phone\n    ","value":"fa fa-phone"},{"name":"\n       square-o\n    ","value":"fa fa-square-o"},{"name":"\n       bookmark-o\n    ","value":"fa fa-bookmark-o"},{"name":"\n       phone-square\n    ","value":"fa fa-phone-square"},{"name":"\n       twitter\n    ","value":"fa fa-twitter"},{"name":"\n       facebook\n    ","value":"fa fa-facebook"},{"name":"\n       github\n    ","value":"fa fa-github"},{"name":"\n       unlock\n    ","value":"fa fa-unlock"},{"name":"\n       credit-card\n    ","value":"fa fa-credit-card"},{"name":"\n       rss\n    ","value":"fa fa-rss"},{"name":"\n       hdd-o\n    ","value":"fa fa-hdd-o"},{"name":"\n       bullhorn\n    ","value":"fa fa-bullhorn"},{"name":"\n       bell\n    ","value":"fa fa-bell"},{"name":"\n       certificate\n    ","value":"fa fa-certificate"},{"name":"\n       hand-o-right\n    ","value":"fa fa-hand-o-right"},{"name":"\n       hand-o-left\n    ","value":"fa fa-hand-o-left"},{"name":"\n       hand-o-up\n    ","value":"fa fa-hand-o-up"},{"name":"\n       hand-o-down\n    ","value":"fa fa-hand-o-down"},{"name":"\n       arrow-circle-left\n    ","value":"fa fa-arrow-circle-left"},{"name":"\n       arrow-circle-right\n    ","value":"fa fa-arrow-circle-right"},{"name":"\n       arrow-circle-up\n    ","value":"fa fa-arrow-circle-up"},{"name":"\n       arrow-circle-down\n    ","value":"fa fa-arrow-circle-down"},{"name":"\n       globe\n    ","value":"fa fa-globe"},{"name":"\n       wrench\n    ","value":"fa fa-wrench"},{"name":"\n       tasks\n    ","value":"fa fa-tasks"},{"name":"\n       filter\n    ","value":"fa fa-filter"},{"name":"\n       briefcase\n    ","value":"fa fa-briefcase"},{"name":"\n       arrows-alt\n    ","value":"fa fa-arrows-alt"},{"name":"\n       users\n    ","value":"fa fa-users"},{"name":"\n       link\n    ","value":"fa fa-link"},{"name":"\n       cloud\n    ","value":"fa fa-cloud"},{"name":"\n       flask\n    ","value":"fa fa-flask"},{"name":"\n       scissors\n    ","value":"fa fa-scissors"},{"name":"\n       files-o\n    ","value":"fa fa-files-o"},{"name":"\n       paperclip\n    ","value":"fa fa-paperclip"},{"name":"\n       floppy-o\n    ","value":"fa fa-floppy-o"},{"name":"\n       square\n    ","value":"fa fa-square"},{"name":"\n       bars\n    ","value":"fa fa-bars"},{"name":"\n       list-ul\n    ","value":"fa fa-list-ul"},{"name":"\n       list-ol\n    ","value":"fa fa-list-ol"},{"name":"\n       strikethrough\n    ","value":"fa fa-strikethrough"},{"name":"\n       underline\n    ","value":"fa fa-underline"},{"name":"\n       table\n    ","value":"fa fa-table"},{"name":"\n       magic\n    ","value":"fa fa-magic"},{"name":"\n       truck\n    ","value":"fa fa-truck"},{"name":"\n       pinterest\n    ","value":"fa fa-pinterest"},{"name":"\n       pinterest-square\n    ","value":"fa fa-pinterest-square"},{"name":"\n       google-plus-square\n    ","value":"fa fa-google-plus-square"},{"name":"\n       google-plus\n    ","value":"fa fa-google-plus"},{"name":"\n       money\n    ","value":"fa fa-money"},{"name":"\n       caret-down\n    ","value":"fa fa-caret-down"},{"name":"\n       caret-up\n    ","value":"fa fa-caret-up"},{"name":"\n       caret-left\n    ","value":"fa fa-caret-left"},{"name":"\n       caret-right\n    ","value":"fa fa-caret-right"},{"name":"\n       columns\n    ","value":"fa fa-columns"},{"name":"\n       sort\n    ","value":"fa fa-sort"},{"name":"\n       sort-desc\n    ","value":"fa fa-sort-desc"},{"name":"\n       sort-asc\n    ","value":"fa fa-sort-asc"},{"name":"\n       envelope\n    ","value":"fa fa-envelope"},{"name":"\n       linkedin\n    ","value":"fa fa-linkedin"},{"name":"\n       undo\n    ","value":"fa fa-undo"},{"name":"\n       gavel\n    ","value":"fa fa-gavel"},{"name":"\n       tachometer\n    ","value":"fa fa-tachometer"},{"name":"\n       comment-o\n    ","value":"fa fa-comment-o"},{"name":"\n       comments-o\n    ","value":"fa fa-comments-o"},{"name":"\n       bolt\n    ","value":"fa fa-bolt"},{"name":"\n       sitemap\n    ","value":"fa fa-sitemap"},{"name":"\n       umbrella\n    ","value":"fa fa-umbrella"},{"name":"\n       clipboard\n    ","value":"fa fa-clipboard"},{"name":"\n       lightbulb-o\n    ","value":"fa fa-lightbulb-o"},{"name":"\n       exchange\n    ","value":"fa fa-exchange"},{"name":"\n       cloud-download\n    ","value":"fa fa-cloud-download"},{"name":"\n       cloud-upload\n    ","value":"fa fa-cloud-upload"},{"name":"\n       user-md\n    ","value":"fa fa-user-md"},{"name":"\n       stethoscope\n    ","value":"fa fa-stethoscope"},{"name":"\n       suitcase\n    ","value":"fa fa-suitcase"},{"name":"\n       bell-o\n    ","value":"fa fa-bell-o"},{"name":"\n       coffee\n    ","value":"fa fa-coffee"},{"name":"\n       cutlery\n    ","value":"fa fa-cutlery"},{"name":"\n       file-text-o\n    ","value":"fa fa-file-text-o"},{"name":"\n       building-o\n    ","value":"fa fa-building-o"},{"name":"\n       hospital-o\n    ","value":"fa fa-hospital-o"},{"name":"\n       ambulance\n    ","value":"fa fa-ambulance"},{"name":"\n       medkit\n    ","value":"fa fa-medkit"},{"name":"\n       fighter-jet\n    ","value":"fa fa-fighter-jet"},{"name":"\n       beer\n    ","value":"fa fa-beer"},{"name":"\n       h-square\n    ","value":"fa fa-h-square"},{"name":"\n       plus-square\n    ","value":"fa fa-plus-square"},{"name":"\n       angle-double-left\n    ","value":"fa fa-angle-double-left"},{"name":"\n       angle-double-right\n    ","value":"fa fa-angle-double-right"},{"name":"\n       angle-double-up\n    ","value":"fa fa-angle-double-up"},{"name":"\n       angle-double-down\n    ","value":"fa fa-angle-double-down"},{"name":"\n       angle-left\n    ","value":"fa fa-angle-left"},{"name":"\n       angle-right\n    ","value":"fa fa-angle-right"},{"name":"\n       angle-up\n    ","value":"fa fa-angle-up"},{"name":"\n       angle-down\n    ","value":"fa fa-angle-down"},{"name":"\n       desktop\n    ","value":"fa fa-desktop"},{"name":"\n       laptop\n    ","value":"fa fa-laptop"},{"name":"\n       tablet\n    ","value":"fa fa-tablet"},{"name":"\n       mobile\n    ","value":"fa fa-mobile"},{"name":"\n       circle-o\n    ","value":"fa fa-circle-o"},{"name":"\n       quote-left\n    ","value":"fa fa-quote-left"},{"name":"\n       quote-right\n    ","value":"fa fa-quote-right"},{"name":"\n       spinner\n    ","value":"fa fa-spinner"},{"name":"\n       circle\n    ","value":"fa fa-circle"},{"name":"\n       reply\n    ","value":"fa fa-reply"},{"name":"\n       github-alt\n    ","value":"fa fa-github-alt"},{"name":"\n       folder-o\n    ","value":"fa fa-folder-o"},{"name":"\n       folder-open-o\n    ","value":"fa fa-folder-open-o"},{"name":"\n       smile-o\n    ","value":"fa fa-smile-o"},{"name":"\n       frown-o\n    ","value":"fa fa-frown-o"},{"name":"\n       meh-o\n    ","value":"fa fa-meh-o"},{"name":"\n       gamepad\n    ","value":"fa fa-gamepad"},{"name":"\n       keyboard-o\n    ","value":"fa fa-keyboard-o"},{"name":"\n       flag-o\n    ","value":"fa fa-flag-o"},{"name":"\n       flag-checkered\n    ","value":"fa fa-flag-checkered"},{"name":"\n       terminal\n    ","value":"fa fa-terminal"},{"name":"\n       code\n    ","value":"fa fa-code"},{"name":"\n       reply-all\n    ","value":"fa fa-reply-all"},{"name":"\n       star-half-o\n    ","value":"fa fa-star-half-o"},{"name":"\n       location-arrow\n    ","value":"fa fa-location-arrow"},{"name":"\n       crop\n    ","value":"fa fa-crop"},{"name":"\n       code-fork\n    ","value":"fa fa-code-fork"},{"name":"\n       chain-broken\n    ","value":"fa fa-chain-broken"},{"name":"\n       question\n    ","value":"fa fa-question"},{"name":"\n       info\n    ","value":"fa fa-info"},{"name":"\n       exclamation\n    ","value":"fa fa-exclamation"},{"name":"\n       superscript\n    ","value":"fa fa-superscript"},{"name":"\n       subscript\n    ","value":"fa fa-subscript"},{"name":"\n       eraser\n    ","value":"fa fa-eraser"},{"name":"\n       puzzle-piece\n    ","value":"fa fa-puzzle-piece"},{"name":"\n       microphone\n    ","value":"fa fa-microphone"},{"name":"\n       microphone-slash\n    ","value":"fa fa-microphone-slash"},{"name":"\n       shield\n    ","value":"fa fa-shield"},{"name":"\n       calendar-o\n    ","value":"fa fa-calendar-o"},{"name":"\n       fire-extinguisher\n    ","value":"fa fa-fire-extinguisher"},{"name":"\n       rocket\n    ","value":"fa fa-rocket"},{"name":"\n       maxcdn\n    ","value":"fa fa-maxcdn"},{"name":"\n       chevron-circle-left\n    ","value":"fa fa-chevron-circle-left"},{"name":"\n       chevron-circle-right\n    ","value":"fa fa-chevron-circle-right"},{"name":"\n       chevron-circle-up\n    ","value":"fa fa-chevron-circle-up"},{"name":"\n       chevron-circle-down\n    ","value":"fa fa-chevron-circle-down"},{"name":"\n       html5\n    ","value":"fa fa-html5"},{"name":"\n       css3\n    ","value":"fa fa-css3"},{"name":"\n       anchor\n    ","value":"fa fa-anchor"},{"name":"\n       unlock-alt\n    ","value":"fa fa-unlock-alt"},{"name":"\n       bullseye\n    ","value":"fa fa-bullseye"},{"name":"\n       ellipsis-h\n    ","value":"fa fa-ellipsis-h"},{"name":"\n       ellipsis-v\n    ","value":"fa fa-ellipsis-v"},{"name":"\n       rss-square\n    ","value":"fa fa-rss-square"},{"name":"\n       play-circle\n    ","value":"fa fa-play-circle"},{"name":"\n       ticket\n    ","value":"fa fa-ticket"},{"name":"\n       minus-square\n    ","value":"fa fa-minus-square"},{"name":"\n       minus-square-o\n    ","value":"fa fa-minus-square-o"},{"name":"\n       level-up\n    ","value":"fa fa-level-up"},{"name":"\n       level-down\n    ","value":"fa fa-level-down"},{"name":"\n       check-square\n    ","value":"fa fa-check-square"},{"name":"\n       pencil-square\n    ","value":"fa fa-pencil-square"},{"name":"\n       external-link-square\n    ","value":"fa fa-external-link-square"},{"name":"\n       share-square\n    ","value":"fa fa-share-square"},{"name":"\n       compass\n    ","value":"fa fa-compass"},{"name":"\n       caret-square-o-down\n    ","value":"fa fa-caret-square-o-down"},{"name":"\n       caret-square-o-up\n    ","value":"fa fa-caret-square-o-up"},{"name":"\n       caret-square-o-right\n    ","value":"fa fa-caret-square-o-right"},{"name":"\n       eur\n    ","value":"fa fa-eur"},{"name":"\n       gbp\n    ","value":"fa fa-gbp"},{"name":"\n       usd\n    ","value":"fa fa-usd"},{"name":"\n       inr\n    ","value":"fa fa-inr"},{"name":"\n       jpy\n    ","value":"fa fa-jpy"},{"name":"\n       rub\n    ","value":"fa fa-rub"},{"name":"\n       krw\n    ","value":"fa fa-krw"},{"name":"\n       btc\n    ","value":"fa fa-btc"},{"name":"\n       file\n    ","value":"fa fa-file"},{"name":"\n       file-text\n    ","value":"fa fa-file-text"},{"name":"\n       sort-alpha-asc\n    ","value":"fa fa-sort-alpha-asc"},{"name":"\n       sort-alpha-desc\n    ","value":"fa fa-sort-alpha-desc"},{"name":"\n       sort-amount-asc\n    ","value":"fa fa-sort-amount-asc"},{"name":"\n       sort-amount-desc\n    ","value":"fa fa-sort-amount-desc"},{"name":"\n       sort-numeric-asc\n    ","value":"fa fa-sort-numeric-asc"},{"name":"\n       sort-numeric-desc\n    ","value":"fa fa-sort-numeric-desc"},{"name":"\n       thumbs-up\n    ","value":"fa fa-thumbs-up"},{"name":"\n       thumbs-down\n    ","value":"fa fa-thumbs-down"},{"name":"\n       youtube-square\n    ","value":"fa fa-youtube-square"},{"name":"\n       youtube\n    ","value":"fa fa-youtube"},{"name":"\n       xing\n    ","value":"fa fa-xing"},{"name":"\n       xing-square\n    ","value":"fa fa-xing-square"},{"name":"\n       youtube-play\n    ","value":"fa fa-youtube-play"},{"name":"\n       dropbox\n    ","value":"fa fa-dropbox"},{"name":"\n       stack-overflow\n    ","value":"fa fa-stack-overflow"},{"name":"\n       instagram\n    ","value":"fa fa-instagram"},{"name":"\n       flickr\n    ","value":"fa fa-flickr"},{"name":"\n       adn\n    ","value":"fa fa-adn"},{"name":"\n       bitbucket\n    ","value":"fa fa-bitbucket"},{"name":"\n       bitbucket-square\n    ","value":"fa fa-bitbucket-square"},{"name":"\n       tumblr\n    ","value":"fa fa-tumblr"},{"name":"\n       tumblr-square\n    ","value":"fa fa-tumblr-square"},{"name":"\n       long-arrow-down\n    ","value":"fa fa-long-arrow-down"},{"name":"\n       long-arrow-up\n    ","value":"fa fa-long-arrow-up"},{"name":"\n       long-arrow-left\n    ","value":"fa fa-long-arrow-left"},{"name":"\n       long-arrow-right\n    ","value":"fa fa-long-arrow-right"},{"name":"\n       apple\n    ","value":"fa fa-apple"},{"name":"\n       windows\n    ","value":"fa fa-windows"},{"name":"\n       android\n    ","value":"fa fa-android"},{"name":"\n       linux\n    ","value":"fa fa-linux"},{"name":"\n       dribbble\n    ","value":"fa fa-dribbble"},{"name":"\n       skype\n    ","value":"fa fa-skype"},{"name":"\n       foursquare\n    ","value":"fa fa-foursquare"},{"name":"\n       trello\n    ","value":"fa fa-trello"},{"name":"\n       female\n    ","value":"fa fa-female"},{"name":"\n       male\n    ","value":"fa fa-male"},{"name":"\n       gratipay\n    ","value":"fa fa-gratipay"},{"name":"\n       sun-o\n    ","value":"fa fa-sun-o"},{"name":"\n       moon-o\n    ","value":"fa fa-moon-o"},{"name":"\n       archive\n    ","value":"fa fa-archive"},{"name":"\n       bug\n    ","value":"fa fa-bug"},{"name":"\n       vk\n    ","value":"fa fa-vk"},{"name":"\n       weibo\n    ","value":"fa fa-weibo"},{"name":"\n       renren\n    ","value":"fa fa-renren"},{"name":"\n       pagelines\n    ","value":"fa fa-pagelines"},{"name":"\n       stack-exchange\n    ","value":"fa fa-stack-exchange"},{"name":"\n       arrow-circle-o-right\n    ","value":"fa fa-arrow-circle-o-right"},{"name":"\n       arrow-circle-o-left\n    ","value":"fa fa-arrow-circle-o-left"},{"name":"\n       caret-square-o-left\n    ","value":"fa fa-caret-square-o-left"},{"name":"\n       dot-circle-o\n    ","value":"fa fa-dot-circle-o"},{"name":"\n       wheelchair\n    ","value":"fa fa-wheelchair"},{"name":"\n       vimeo-square\n    ","value":"fa fa-vimeo-square"},{"name":"\n       try\n    ","value":"fa fa-try"},{"name":"\n       plus-square-o\n    ","value":"fa fa-plus-square-o"},{"name":"\n       space-shuttle\n    ","value":"fa fa-space-shuttle"},{"name":"\n       slack\n    ","value":"fa fa-slack"},{"name":"\n       envelope-square\n    ","value":"fa fa-envelope-square"},{"name":"\n       wordpress\n    ","value":"fa fa-wordpress"},{"name":"\n       openid\n    ","value":"fa fa-openid"},{"name":"\n       university\n    ","value":"fa fa-university"},{"name":"\n       graduation-cap\n    ","value":"fa fa-graduation-cap"},{"name":"\n       yahoo\n    ","value":"fa fa-yahoo"},{"name":"\n       google\n    ","value":"fa fa-google"},{"name":"\n       reddit\n    ","value":"fa fa-reddit"},{"name":"\n       reddit-square\n    ","value":"fa fa-reddit-square"},{"name":"\n       stumbleupon-circle\n    ","value":"fa fa-stumbleupon-circle"},{"name":"\n       stumbleupon\n    ","value":"fa fa-stumbleupon"},{"name":"\n       delicious\n    ","value":"fa fa-delicious"},{"name":"\n       digg\n    ","value":"fa fa-digg"},{"name":"\n       pied-piper\n    ","value":"fa fa-pied-piper"},{"name":"\n       pied-piper-alt\n    ","value":"fa fa-pied-piper-alt"},{"name":"\n       drupal\n    ","value":"fa fa-drupal"},{"name":"\n       joomla\n    ","value":"fa fa-joomla"},{"name":"\n       language\n    ","value":"fa fa-language"},{"name":"\n       fax\n    ","value":"fa fa-fax"},{"name":"\n       building\n    ","value":"fa fa-building"},{"name":"\n       child\n    ","value":"fa fa-child"},{"name":"\n       paw\n    ","value":"fa fa-paw"},{"name":"\n       spoon\n    ","value":"fa fa-spoon"},{"name":"\n       cube\n    ","value":"fa fa-cube"},{"name":"\n       cubes\n    ","value":"fa fa-cubes"},{"name":"\n       behance\n    ","value":"fa fa-behance"},{"name":"\n       behance-square\n    ","value":"fa fa-behance-square"},{"name":"\n       steam\n    ","value":"fa fa-steam"},{"name":"\n       steam-square\n    ","value":"fa fa-steam-square"},{"name":"\n       recycle\n    ","value":"fa fa-recycle"},{"name":"\n       car\n    ","value":"fa fa-car"},{"name":"\n       taxi\n    ","value":"fa fa-taxi"},{"name":"\n       tree\n    ","value":"fa fa-tree"},{"name":"\n       spotify\n    ","value":"fa fa-spotify"},{"name":"\n       deviantart\n    ","value":"fa fa-deviantart"},{"name":"\n       soundcloud\n    ","value":"fa fa-soundcloud"},{"name":"\n       database\n    ","value":"fa fa-database"},{"name":"\n       file-pdf-o\n    ","value":"fa fa-file-pdf-o"},{"name":"\n       file-word-o\n    ","value":"fa fa-file-word-o"},{"name":"\n       file-excel-o\n    ","value":"fa fa-file-excel-o"},{"name":"\n       file-powerpoint-o\n    ","value":"fa fa-file-powerpoint-o"},{"name":"\n       file-image-o\n    ","value":"fa fa-file-image-o"},{"name":"\n       file-archive-o\n    ","value":"fa fa-file-archive-o"},{"name":"\n       file-audio-o\n    ","value":"fa fa-file-audio-o"},{"name":"\n       file-video-o\n    ","value":"fa fa-file-video-o"},{"name":"\n       file-code-o\n    ","value":"fa fa-file-code-o"},{"name":"\n       vine\n    ","value":"fa fa-vine"},{"name":"\n       codepen\n    ","value":"fa fa-codepen"},{"name":"\n       jsfiddle\n    ","value":"fa fa-jsfiddle"},{"name":"\n       life-ring\n    ","value":"fa fa-life-ring"},{"name":"\n       circle-o-notch\n    ","value":"fa fa-circle-o-notch"},{"name":"\n       rebel\n    ","value":"fa fa-rebel"},{"name":"\n       empire\n    ","value":"fa fa-empire"},{"name":"\n       git-square\n    ","value":"fa fa-git-square"},{"name":"\n       git\n    ","value":"fa fa-git"},{"name":"\n       hacker-news\n    ","value":"fa fa-hacker-news"},{"name":"\n       tencent-weibo\n    ","value":"fa fa-tencent-weibo"},{"name":"\n       qq\n    ","value":"fa fa-qq"},{"name":"\n       weixin\n    ","value":"fa fa-weixin"},{"name":"\n       paper-plane\n    ","value":"fa fa-paper-plane"},{"name":"\n       paper-plane-o\n    ","value":"fa fa-paper-plane-o"},{"name":"\n       history\n    ","value":"fa fa-history"},{"name":"\n       circle-thin\n    ","value":"fa fa-circle-thin"},{"name":"\n       header\n    ","value":"fa fa-header"},{"name":"\n       paragraph\n    ","value":"fa fa-paragraph"},{"name":"\n       sliders\n    ","value":"fa fa-sliders"},{"name":"\n       share-alt\n    ","value":"fa fa-share-alt"},{"name":"\n       share-alt-square\n    ","value":"fa fa-share-alt-square"},{"name":"\n       bomb\n    ","value":"fa fa-bomb"},{"name":"\n       futbol-o\n    ","value":"fa fa-futbol-o"},{"name":"\n       tty\n    ","value":"fa fa-tty"},{"name":"\n       binoculars\n    ","value":"fa fa-binoculars"},{"name":"\n       plug\n    ","value":"fa fa-plug"},{"name":"\n       slideshare\n    ","value":"fa fa-slideshare"},{"name":"\n       twitch\n    ","value":"fa fa-twitch"},{"name":"\n       yelp\n    ","value":"fa fa-yelp"},{"name":"\n       newspaper-o\n    ","value":"fa fa-newspaper-o"},{"name":"\n       wifi\n    ","value":"fa fa-wifi"},{"name":"\n       calculator\n    ","value":"fa fa-calculator"},{"name":"\n       paypal\n    ","value":"fa fa-paypal"},{"name":"\n       google-wallet\n    ","value":"fa fa-google-wallet"},{"name":"\n       cc-visa\n    ","value":"fa fa-cc-visa"},{"name":"\n       cc-mastercard\n    ","value":"fa fa-cc-mastercard"},{"name":"\n       cc-discover\n    ","value":"fa fa-cc-discover"},{"name":"\n       cc-amex\n    ","value":"fa fa-cc-amex"},{"name":"\n       cc-paypal\n    ","value":"fa fa-cc-paypal"},{"name":"\n       cc-stripe\n    ","value":"fa fa-cc-stripe"},{"name":"\n       bell-slash\n    ","value":"fa fa-bell-slash"},{"name":"\n       bell-slash-o\n    ","value":"fa fa-bell-slash-o"},{"name":"\n       trash\n    ","value":"fa fa-trash"},{"name":"\n       copyright\n    ","value":"fa fa-copyright"},{"name":"\n       at\n    ","value":"fa fa-at"},{"name":"\n       eyedropper\n    ","value":"fa fa-eyedropper"},{"name":"\n       paint-brush\n    ","value":"fa fa-paint-brush"},{"name":"\n       birthday-cake\n    ","value":"fa fa-birthday-cake"},{"name":"\n       area-chart\n    ","value":"fa fa-area-chart"},{"name":"\n       pie-chart\n    ","value":"fa fa-pie-chart"},{"name":"\n       line-chart\n    ","value":"fa fa-line-chart"},{"name":"\n       lastfm\n    ","value":"fa fa-lastfm"},{"name":"\n       lastfm-square\n    ","value":"fa fa-lastfm-square"},{"name":"\n       toggle-off\n    ","value":"fa fa-toggle-off"},{"name":"\n       toggle-on\n    ","value":"fa fa-toggle-on"},{"name":"\n       bicycle\n    ","value":"fa fa-bicycle"},{"name":"\n       bus\n    ","value":"fa fa-bus"},{"name":"\n       ioxhost\n    ","value":"fa fa-ioxhost"},{"name":"\n       angellist\n    ","value":"fa fa-angellist"},{"name":"\n       cc\n    ","value":"fa fa-cc"},{"name":"\n       ils\n    ","value":"fa fa-ils"},{"name":"\n       meanpath\n    ","value":"fa fa-meanpath"},{"name":"\n       buysellads\n    ","value":"fa fa-buysellads"},{"name":"\n       connectdevelop\n    ","value":"fa fa-connectdevelop"},{"name":"\n       dashcube\n    ","value":"fa fa-dashcube"},{"name":"\n       forumbee\n    ","value":"fa fa-forumbee"},{"name":"\n       leanpub\n    ","value":"fa fa-leanpub"},{"name":"\n       sellsy\n    ","value":"fa fa-sellsy"},{"name":"\n       shirtsinbulk\n    ","value":"fa fa-shirtsinbulk"},{"name":"\n       simplybuilt\n    ","value":"fa fa-simplybuilt"},{"name":"\n       skyatlas\n    ","value":"fa fa-skyatlas"},{"name":"\n       cart-plus\n    ","value":"fa fa-cart-plus"},{"name":"\n       cart-arrow-down\n    ","value":"fa fa-cart-arrow-down"},{"name":"\n       diamond\n    ","value":"fa fa-diamond"},{"name":"\n       ship\n    ","value":"fa fa-ship"},{"name":"\n       user-secret\n    ","value":"fa fa-user-secret"},{"name":"\n       motorcycle\n    ","value":"fa fa-motorcycle"},{"name":"\n       street-view\n    ","value":"fa fa-street-view"},{"name":"\n       heartbeat\n    ","value":"fa fa-heartbeat"},{"name":"\n       venus\n    ","value":"fa fa-venus"},{"name":"\n       mars\n    ","value":"fa fa-mars"},{"name":"\n       mercury\n    ","value":"fa fa-mercury"},{"name":"\n       transgender\n    ","value":"fa fa-transgender"},{"name":"\n       transgender-alt\n    ","value":"fa fa-transgender-alt"},{"name":"\n       venus-double\n    ","value":"fa fa-venus-double"},{"name":"\n       mars-double\n    ","value":"fa fa-mars-double"},{"name":"\n       venus-mars\n    ","value":"fa fa-venus-mars"},{"name":"\n       mars-stroke\n    ","value":"fa fa-mars-stroke"},{"name":"\n       mars-stroke-v\n    ","value":"fa fa-mars-stroke-v"},{"name":"\n       mars-stroke-h\n    ","value":"fa fa-mars-stroke-h"},{"name":"\n       neuter\n    ","value":"fa fa-neuter"},{"name":"\n       genderless\n    ","value":"fa fa-genderless"},{"name":"\n       facebook-official\n    ","value":"fa fa-facebook-official"},{"name":"\n       pinterest-p\n    ","value":"fa fa-pinterest-p"},{"name":"\n       whatsapp\n    ","value":"fa fa-whatsapp"},{"name":"\n       server\n    ","value":"fa fa-server"},{"name":"\n       user-plus\n    ","value":"fa fa-user-plus"},{"name":"\n       user-times\n    ","value":"fa fa-user-times"},{"name":"\n       bed\n    ","value":"fa fa-bed"},{"name":"\n       viacoin\n    ","value":"fa fa-viacoin"},{"name":"\n       train\n    ","value":"fa fa-train"},{"name":"\n       subway\n    ","value":"fa fa-subway"},{"name":"\n       medium\n    ","value":"fa fa-medium"},{"name":"\n       y-combinator\n    ","value":"fa fa-y-combinator"},{"name":"\n       optin-monster\n    ","value":"fa fa-optin-monster"},{"name":"\n       opencart\n    ","value":"fa fa-opencart"},{"name":"\n       expeditedssl\n    ","value":"fa fa-expeditedssl"},{"name":"\n       battery-full\n    ","value":"fa fa-battery-full"},{"name":"\n       battery-three-quarters\n    ","value":"fa fa-battery-three-quarters"},{"name":"\n       battery-half\n    ","value":"fa fa-battery-half"},{"name":"\n       battery-quarter\n    ","value":"fa fa-battery-quarter"},{"name":"\n       battery-empty\n    ","value":"fa fa-battery-empty"},{"name":"\n       mouse-pointer\n    ","value":"fa fa-mouse-pointer"},{"name":"\n       i-cursor\n    ","value":"fa fa-i-cursor"},{"name":"\n       object-group\n    ","value":"fa fa-object-group"},{"name":"\n       object-ungroup\n    ","value":"fa fa-object-ungroup"},{"name":"\n       sticky-note\n    ","value":"fa fa-sticky-note"},{"name":"\n       sticky-note-o\n    ","value":"fa fa-sticky-note-o"},{"name":"\n       cc-jcb\n    ","value":"fa fa-cc-jcb"},{"name":"\n       cc-diners-club\n    ","value":"fa fa-cc-diners-club"},{"name":"\n       clone\n    ","value":"fa fa-clone"},{"name":"\n       balance-scale\n    ","value":"fa fa-balance-scale"},{"name":"\n       hourglass-o\n    ","value":"fa fa-hourglass-o"},{"name":"\n       hourglass-start\n    ","value":"fa fa-hourglass-start"},{"name":"\n       hourglass-half\n    ","value":"fa fa-hourglass-half"},{"name":"\n       hourglass-end\n    ","value":"fa fa-hourglass-end"},{"name":"\n       hourglass\n    ","value":"fa fa-hourglass"},{"name":"\n       hand-rock-o\n    ","value":"fa fa-hand-rock-o"},{"name":"\n       hand-paper-o\n    ","value":"fa fa-hand-paper-o"},{"name":"\n       hand-scissors-o\n    ","value":"fa fa-hand-scissors-o"},{"name":"\n       hand-lizard-o\n    ","value":"fa fa-hand-lizard-o"},{"name":"\n       hand-spock-o\n    ","value":"fa fa-hand-spock-o"},{"name":"\n       hand-pointer-o\n    ","value":"fa fa-hand-pointer-o"},{"name":"\n       hand-peace-o\n    ","value":"fa fa-hand-peace-o"},{"name":"\n       trademark\n    ","value":"fa fa-trademark"},{"name":"\n       registered\n    ","value":"fa fa-registered"},{"name":"\n       creative-commons\n    ","value":"fa fa-creative-commons"},{"name":"\n       gg\n    ","value":"fa fa-gg"},{"name":"\n       gg-circle\n    ","value":"fa fa-gg-circle"},{"name":"\n       tripadvisor\n    ","value":"fa fa-tripadvisor"},{"name":"\n       odnoklassniki\n    ","value":"fa fa-odnoklassniki"},{"name":"\n       odnoklassniki-square\n    ","value":"fa fa-odnoklassniki-square"},{"name":"\n       get-pocket\n    ","value":"fa fa-get-pocket"},{"name":"\n       wikipedia-w\n    ","value":"fa fa-wikipedia-w"},{"name":"\n       safari\n    ","value":"fa fa-safari"},{"name":"\n       chrome\n    ","value":"fa fa-chrome"},{"name":"\n       firefox\n    ","value":"fa fa-firefox"},{"name":"\n       opera\n    ","value":"fa fa-opera"},{"name":"\n       internet-explorer\n    ","value":"fa fa-internet-explorer"},{"name":"\n       television\n    ","value":"fa fa-television"},{"name":"\n       contao\n    ","value":"fa fa-contao"},{"name":"\n       500px\n    ","value":"fa fa-500px"},{"name":"\n       amazon\n    ","value":"fa fa-amazon"},{"name":"\n       calendar-plus-o\n    ","value":"fa fa-calendar-plus-o"},{"name":"\n       calendar-minus-o\n    ","value":"fa fa-calendar-minus-o"},{"name":"\n       calendar-times-o\n    ","value":"fa fa-calendar-times-o"},{"name":"\n       calendar-check-o\n    ","value":"fa fa-calendar-check-o"},{"name":"\n       industry\n    ","value":"fa fa-industry"},{"name":"\n       map-pin\n    ","value":"fa fa-map-pin"},{"name":"\n       map-signs\n    ","value":"fa fa-map-signs"},{"name":"\n       map-o\n    ","value":"fa fa-map-o"},{"name":"\n       map\n    ","value":"fa fa-map"},{"name":"\n       commenting\n    ","value":"fa fa-commenting"},{"name":"\n       commenting-o\n    ","value":"fa fa-commenting-o"},{"name":"\n       houzz\n    ","value":"fa fa-houzz"},{"name":"\n       vimeo\n    ","value":"fa fa-vimeo"},{"name":"\n       black-tie\n    ","value":"fa fa-black-tie"},{"name":"\n       fonticons\n    ","value":"fa fa-fonticons"},{"name":" 500px","value":"fa fa-500px"},{"name":" amazon","value":"fa fa-amazon"},{"name":" balance-scale","value":"fa fa-balance-scale"},{"name":" battery-0 (alias)","value":"fa fa-battery-0"},{"name":" battery-1 (alias)","value":"fa fa-battery-1"},{"name":" battery-2 (alias)","value":"fa fa-battery-2"},{"name":" battery-3 (alias)","value":"fa fa-battery-3"},{"name":" battery-4 (alias)","value":"fa fa-battery-4"},{"name":" battery-empty","value":"fa fa-battery-empty"},{"name":" battery-full","value":"fa fa-battery-full"},{"name":" battery-half","value":"fa fa-battery-half"},{"name":" battery-quarter","value":"fa fa-battery-quarter"},{"name":" battery-three-quarters","value":"fa fa-battery-three-quarters"},{"name":" black-tie","value":"fa fa-black-tie"},{"name":" calendar-check-o","value":"fa fa-calendar-check-o"},{"name":" calendar-minus-o","value":"fa fa-calendar-minus-o"},{"name":" calendar-plus-o","value":"fa fa-calendar-plus-o"},{"name":" calendar-times-o","value":"fa fa-calendar-times-o"},{"name":" cc-diners-club","value":"fa fa-cc-diners-club"},{"name":" cc-jcb","value":"fa fa-cc-jcb"},{"name":" chrome","value":"fa fa-chrome"},{"name":" clone","value":"fa fa-clone"},{"name":" commenting","value":"fa fa-commenting"},{"name":" commenting-o","value":"fa fa-commenting-o"},{"name":" contao","value":"fa fa-contao"},{"name":" creative-commons","value":"fa fa-creative-commons"},{"name":" expeditedssl","value":"fa fa-expeditedssl"},{"name":" firefox","value":"fa fa-firefox"},{"name":" fonticons","value":"fa fa-fonticons"},{"name":" genderless","value":"fa fa-genderless"},{"name":" get-pocket","value":"fa fa-get-pocket"},{"name":" gg","value":"fa fa-gg"},{"name":" gg-circle","value":"fa fa-gg-circle"},{"name":" hand-grab-o (alias)","value":"fa fa-hand-grab-o"},{"name":" hand-lizard-o","value":"fa fa-hand-lizard-o"},{"name":" hand-paper-o","value":"fa fa-hand-paper-o"},{"name":" hand-peace-o","value":"fa fa-hand-peace-o"},{"name":" hand-pointer-o","value":"fa fa-hand-pointer-o"},{"name":" hand-rock-o","value":"fa fa-hand-rock-o"},{"name":" hand-scissors-o","value":"fa fa-hand-scissors-o"},{"name":" hand-spock-o","value":"fa fa-hand-spock-o"},{"name":" hand-stop-o (alias)","value":"fa fa-hand-stop-o"},{"name":" hourglass","value":"fa fa-hourglass"},{"name":" hourglass-1 (alias)","value":"fa fa-hourglass-1"},{"name":" hourglass-2 (alias)","value":"fa fa-hourglass-2"},{"name":" hourglass-3 (alias)","value":"fa fa-hourglass-3"},{"name":" hourglass-end","value":"fa fa-hourglass-end"},{"name":" hourglass-half","value":"fa fa-hourglass-half"},{"name":" hourglass-o","value":"fa fa-hourglass-o"},{"name":" hourglass-start","value":"fa fa-hourglass-start"},{"name":" houzz","value":"fa fa-houzz"},{"name":" i-cursor","value":"fa fa-i-cursor"},{"name":" industry","value":"fa fa-industry"},{"name":" internet-explorer","value":"fa fa-internet-explorer"},{"name":" map","value":"fa fa-map"},{"name":" map-o","value":"fa fa-map-o"},{"name":" map-pin","value":"fa fa-map-pin"},{"name":" map-signs","value":"fa fa-map-signs"},{"name":" mouse-pointer","value":"fa fa-mouse-pointer"},{"name":" object-group","value":"fa fa-object-group"},{"name":" object-ungroup","value":"fa fa-object-ungroup"},{"name":" odnoklassniki","value":"fa fa-odnoklassniki"},{"name":" odnoklassniki-square","value":"fa fa-odnoklassniki-square"},{"name":" opencart","value":"fa fa-opencart"},{"name":" opera","value":"fa fa-opera"},{"name":" optin-monster","value":"fa fa-optin-monster"},{"name":" registered","value":"fa fa-registered"},{"name":" safari","value":"fa fa-safari"},{"name":" sticky-note","value":"fa fa-sticky-note"},{"name":" sticky-note-o","value":"fa fa-sticky-note-o"},{"name":" television","value":"fa fa-television"},{"name":" trademark","value":"fa fa-trademark"},{"name":" tripadvisor","value":"fa fa-tripadvisor"},{"name":" tv (alias)","value":"fa fa-tv"},{"name":" vimeo","value":"fa fa-vimeo"},{"name":" wikipedia-w","value":"fa fa-wikipedia-w"},{"name":" y-combinator","value":"fa fa-y-combinator"},{"name":" yc (alias)","value":"fa fa-yc"},{"name":" adjust","value":"fa fa-adjust"},{"name":" anchor","value":"fa fa-anchor"},{"name":" archive","value":"fa fa-archive"},{"name":" area-chart","value":"fa fa-area-chart"},{"name":" arrows","value":"fa fa-arrows"},{"name":" arrows-h","value":"fa fa-arrows-h"},{"name":" arrows-v","value":"fa fa-arrows-v"},{"name":" asterisk","value":"fa fa-asterisk"},{"name":" at","value":"fa fa-at"},{"name":" automobile (alias)","value":"fa fa-automobile"},{"name":" balance-scale","value":"fa fa-balance-scale"},{"name":" ban","value":"fa fa-ban"},{"name":" bank (alias)","value":"fa fa-bank"},{"name":" bar-chart","value":"fa fa-bar-chart"},{"name":" bar-chart-o (alias)","value":"fa fa-bar-chart-o"},{"name":" barcode","value":"fa fa-barcode"},{"name":" bars","value":"fa fa-bars"},{"name":" battery-0 (alias)","value":"fa fa-battery-0"},{"name":" battery-1 (alias)","value":"fa fa-battery-1"},{"name":" battery-2 (alias)","value":"fa fa-battery-2"},{"name":" battery-3 (alias)","value":"fa fa-battery-3"},{"name":" battery-4 (alias)","value":"fa fa-battery-4"},{"name":" battery-empty","value":"fa fa-battery-empty"},{"name":" battery-full","value":"fa fa-battery-full"},{"name":" battery-half","value":"fa fa-battery-half"},{"name":" battery-quarter","value":"fa fa-battery-quarter"},{"name":" battery-three-quarters","value":"fa fa-battery-three-quarters"},{"name":" bed","value":"fa fa-bed"},{"name":" beer","value":"fa fa-beer"},{"name":" bell","value":"fa fa-bell"},{"name":" bell-o","value":"fa fa-bell-o"},{"name":" bell-slash","value":"fa fa-bell-slash"},{"name":" bell-slash-o","value":"fa fa-bell-slash-o"},{"name":" bicycle","value":"fa fa-bicycle"},{"name":" binoculars","value":"fa fa-binoculars"},{"name":" birthday-cake","value":"fa fa-birthday-cake"},{"name":" bolt","value":"fa fa-bolt"},{"name":" bomb","value":"fa fa-bomb"},{"name":" book","value":"fa fa-book"},{"name":" bookmark","value":"fa fa-bookmark"},{"name":" bookmark-o","value":"fa fa-bookmark-o"},{"name":" briefcase","value":"fa fa-briefcase"},{"name":" bug","value":"fa fa-bug"},{"name":" building","value":"fa fa-building"},{"name":" building-o","value":"fa fa-building-o"},{"name":" bullhorn","value":"fa fa-bullhorn"},{"name":" bullseye","value":"fa fa-bullseye"},{"name":" bus","value":"fa fa-bus"},{"name":" cab (alias)","value":"fa fa-cab"},{"name":" calculator","value":"fa fa-calculator"},{"name":" calendar","value":"fa fa-calendar"},{"name":" calendar-check-o","value":"fa fa-calendar-check-o"},{"name":" calendar-minus-o","value":"fa fa-calendar-minus-o"},{"name":" calendar-o","value":"fa fa-calendar-o"},{"name":" calendar-plus-o","value":"fa fa-calendar-plus-o"},{"name":" calendar-times-o","value":"fa fa-calendar-times-o"},{"name":" camera","value":"fa fa-camera"},{"name":" camera-retro","value":"fa fa-camera-retro"},{"name":" car","value":"fa fa-car"},{"name":" caret-square-o-down","value":"fa fa-caret-square-o-down"},{"name":" caret-square-o-left","value":"fa fa-caret-square-o-left"},{"name":" caret-square-o-right","value":"fa fa-caret-square-o-right"},{"name":" caret-square-o-up","value":"fa fa-caret-square-o-up"},{"name":" cart-arrow-down","value":"fa fa-cart-arrow-down"},{"name":" cart-plus","value":"fa fa-cart-plus"},{"name":" cc","value":"fa fa-cc"},{"name":" certificate","value":"fa fa-certificate"},{"name":" check","value":"fa fa-check"},{"name":" check-circle","value":"fa fa-check-circle"},{"name":" check-circle-o","value":"fa fa-check-circle-o"},{"name":" check-square","value":"fa fa-check-square"},{"name":" check-square-o","value":"fa fa-check-square-o"},{"name":" child","value":"fa fa-child"},{"name":" circle","value":"fa fa-circle"},{"name":" circle-o","value":"fa fa-circle-o"},{"name":" circle-o-notch","value":"fa fa-circle-o-notch"},{"name":" circle-thin","value":"fa fa-circle-thin"},{"name":" clock-o","value":"fa fa-clock-o"},{"name":" clone","value":"fa fa-clone"},{"name":" close (alias)","value":"fa fa-close"},{"name":" cloud","value":"fa fa-cloud"},{"name":" cloud-download","value":"fa fa-cloud-download"},{"name":" cloud-upload","value":"fa fa-cloud-upload"},{"name":" code","value":"fa fa-code"},{"name":" code-fork","value":"fa fa-code-fork"},{"name":" coffee","value":"fa fa-coffee"},{"name":" cog","value":"fa fa-cog"},{"name":" cogs","value":"fa fa-cogs"},{"name":" comment","value":"fa fa-comment"},{"name":" comment-o","value":"fa fa-comment-o"},{"name":" commenting","value":"fa fa-commenting"},{"name":" commenting-o","value":"fa fa-commenting-o"},{"name":" comments","value":"fa fa-comments"},{"name":" comments-o","value":"fa fa-comments-o"},{"name":" compass","value":"fa fa-compass"},{"name":" copyright","value":"fa fa-copyright"},{"name":" creative-commons","value":"fa fa-creative-commons"},{"name":" credit-card","value":"fa fa-credit-card"},{"name":" crop","value":"fa fa-crop"},{"name":" crosshairs","value":"fa fa-crosshairs"},{"name":" cube","value":"fa fa-cube"},{"name":" cubes","value":"fa fa-cubes"},{"name":" cutlery","value":"fa fa-cutlery"},{"name":" dashboard (alias)","value":"fa fa-dashboard"},{"name":" database","value":"fa fa-database"},{"name":" desktop","value":"fa fa-desktop"},{"name":" diamond","value":"fa fa-diamond"},{"name":" dot-circle-o","value":"fa fa-dot-circle-o"},{"name":" download","value":"fa fa-download"},{"name":" edit (alias)","value":"fa fa-edit"},{"name":" ellipsis-h","value":"fa fa-ellipsis-h"},{"name":" ellipsis-v","value":"fa fa-ellipsis-v"},{"name":" envelope","value":"fa fa-envelope"},{"name":" envelope-o","value":"fa fa-envelope-o"},{"name":" envelope-square","value":"fa fa-envelope-square"},{"name":" eraser","value":"fa fa-eraser"},{"name":" exchange","value":"fa fa-exchange"},{"name":" exclamation","value":"fa fa-exclamation"},{"name":" exclamation-circle","value":"fa fa-exclamation-circle"},{"name":" exclamation-triangle","value":"fa fa-exclamation-triangle"},{"name":" external-link","value":"fa fa-external-link"},{"name":" external-link-square","value":"fa fa-external-link-square"},{"name":" eye","value":"fa fa-eye"},{"name":" eye-slash","value":"fa fa-eye-slash"},{"name":" eyedropper","value":"fa fa-eyedropper"},{"name":" fax","value":"fa fa-fax"},{"name":" feed (alias)","value":"fa fa-feed"},{"name":" female","value":"fa fa-female"},{"name":" fighter-jet","value":"fa fa-fighter-jet"},{"name":" file-archive-o","value":"fa fa-file-archive-o"},{"name":" file-audio-o","value":"fa fa-file-audio-o"},{"name":" file-code-o","value":"fa fa-file-code-o"},{"name":" file-excel-o","value":"fa fa-file-excel-o"},{"name":" file-image-o","value":"fa fa-file-image-o"},{"name":" file-movie-o (alias)","value":"fa fa-file-movie-o"},{"name":" file-pdf-o","value":"fa fa-file-pdf-o"},{"name":" file-photo-o (alias)","value":"fa fa-file-photo-o"},{"name":" file-picture-o (alias)","value":"fa fa-file-picture-o"},{"name":" file-powerpoint-o","value":"fa fa-file-powerpoint-o"},{"name":" file-sound-o (alias)","value":"fa fa-file-sound-o"},{"name":" file-video-o","value":"fa fa-file-video-o"},{"name":" file-word-o","value":"fa fa-file-word-o"},{"name":" file-zip-o (alias)","value":"fa fa-file-zip-o"},{"name":" film","value":"fa fa-film"},{"name":" filter","value":"fa fa-filter"},{"name":" fire","value":"fa fa-fire"},{"name":" fire-extinguisher","value":"fa fa-fire-extinguisher"},{"name":" flag","value":"fa fa-flag"},{"name":" flag-checkered","value":"fa fa-flag-checkered"},{"name":" flag-o","value":"fa fa-flag-o"},{"name":" flash (alias)","value":"fa fa-flash"},{"name":" flask","value":"fa fa-flask"},{"name":" folder","value":"fa fa-folder"},{"name":" folder-o","value":"fa fa-folder-o"},{"name":" folder-open","value":"fa fa-folder-open"},{"name":" folder-open-o","value":"fa fa-folder-open-o"},{"name":" frown-o","value":"fa fa-frown-o"},{"name":" futbol-o","value":"fa fa-futbol-o"},{"name":" gamepad","value":"fa fa-gamepad"},{"name":" gavel","value":"fa fa-gavel"},{"name":" gear (alias)","value":"fa fa-gear"},{"name":" gears (alias)","value":"fa fa-gears"},{"name":" gift","value":"fa fa-gift"},{"name":" glass","value":"fa fa-glass"},{"name":" globe","value":"fa fa-globe"},{"name":" graduation-cap","value":"fa fa-graduation-cap"},{"name":" group (alias)","value":"fa fa-group"},{"name":" hand-grab-o (alias)","value":"fa fa-hand-grab-o"},{"name":" hand-lizard-o","value":"fa fa-hand-lizard-o"},{"name":" hand-paper-o","value":"fa fa-hand-paper-o"},{"name":" hand-peace-o","value":"fa fa-hand-peace-o"},{"name":" hand-pointer-o","value":"fa fa-hand-pointer-o"},{"name":" hand-rock-o","value":"fa fa-hand-rock-o"},{"name":" hand-scissors-o","value":"fa fa-hand-scissors-o"},{"name":" hand-spock-o","value":"fa fa-hand-spock-o"},{"name":" hand-stop-o (alias)","value":"fa fa-hand-stop-o"},{"name":" hdd-o","value":"fa fa-hdd-o"},{"name":" headphones","value":"fa fa-headphones"},{"name":" heart","value":"fa fa-heart"},{"name":" heart-o","value":"fa fa-heart-o"},{"name":" heartbeat","value":"fa fa-heartbeat"},{"name":" history","value":"fa fa-history"},{"name":" home","value":"fa fa-home"},{"name":" hotel (alias)","value":"fa fa-hotel"},{"name":" hourglass","value":"fa fa-hourglass"},{"name":" hourglass-1 (alias)","value":"fa fa-hourglass-1"},{"name":" hourglass-2 (alias)","value":"fa fa-hourglass-2"},{"name":" hourglass-3 (alias)","value":"fa fa-hourglass-3"},{"name":" hourglass-end","value":"fa fa-hourglass-end"},{"name":" hourglass-half","value":"fa fa-hourglass-half"},{"name":" hourglass-o","value":"fa fa-hourglass-o"},{"name":" hourglass-start","value":"fa fa-hourglass-start"},{"name":" i-cursor","value":"fa fa-i-cursor"},{"name":" image (alias)","value":"fa fa-image"},{"name":" inbox","value":"fa fa-inbox"},{"name":" industry","value":"fa fa-industry"},{"name":" info","value":"fa fa-info"},{"name":" info-circle","value":"fa fa-info-circle"},{"name":" institution (alias)","value":"fa fa-institution"},{"name":" key","value":"fa fa-key"},{"name":" keyboard-o","value":"fa fa-keyboard-o"},{"name":" language","value":"fa fa-language"},{"name":" laptop","value":"fa fa-laptop"},{"name":" leaf","value":"fa fa-leaf"},{"name":" legal (alias)","value":"fa fa-legal"},{"name":" lemon-o","value":"fa fa-lemon-o"},{"name":" level-down","value":"fa fa-level-down"},{"name":" level-up","value":"fa fa-level-up"},{"name":" life-bouy (alias)","value":"fa fa-life-bouy"},{"name":" life-buoy (alias)","value":"fa fa-life-buoy"},{"name":" life-ring","value":"fa fa-life-ring"},{"name":" life-saver (alias)","value":"fa fa-life-saver"},{"name":" lightbulb-o","value":"fa fa-lightbulb-o"},{"name":" line-chart","value":"fa fa-line-chart"},{"name":" location-arrow","value":"fa fa-location-arrow"},{"name":" lock","value":"fa fa-lock"},{"name":" magic","value":"fa fa-magic"},{"name":" magnet","value":"fa fa-magnet"},{"name":" mail-forward (alias)","value":"fa fa-mail-forward"},{"name":" mail-reply (alias)","value":"fa fa-mail-reply"},{"name":" mail-reply-all (alias)","value":"fa fa-mail-reply-all"},{"name":" male","value":"fa fa-male"},{"name":" map","value":"fa fa-map"},{"name":" map-marker","value":"fa fa-map-marker"},{"name":" map-o","value":"fa fa-map-o"},{"name":" map-pin","value":"fa fa-map-pin"},{"name":" map-signs","value":"fa fa-map-signs"},{"name":" meh-o","value":"fa fa-meh-o"},{"name":" microphone","value":"fa fa-microphone"},{"name":" microphone-slash","value":"fa fa-microphone-slash"},{"name":" minus","value":"fa fa-minus"},{"name":" minus-circle","value":"fa fa-minus-circle"},{"name":" minus-square","value":"fa fa-minus-square"},{"name":" minus-square-o","value":"fa fa-minus-square-o"},{"name":" mobile","value":"fa fa-mobile"},{"name":" mobile-phone (alias)","value":"fa fa-mobile-phone"},{"name":" money","value":"fa fa-money"},{"name":" moon-o","value":"fa fa-moon-o"},{"name":" mortar-board (alias)","value":"fa fa-mortar-board"},{"name":" motorcycle","value":"fa fa-motorcycle"},{"name":" mouse-pointer","value":"fa fa-mouse-pointer"},{"name":" music","value":"fa fa-music"},{"name":" navicon (alias)","value":"fa fa-navicon"},{"name":" newspaper-o","value":"fa fa-newspaper-o"},{"name":" object-group","value":"fa fa-object-group"},{"name":" object-ungroup","value":"fa fa-object-ungroup"},{"name":" paint-brush","value":"fa fa-paint-brush"},{"name":" paper-plane","value":"fa fa-paper-plane"},{"name":" paper-plane-o","value":"fa fa-paper-plane-o"},{"name":" paw","value":"fa fa-paw"},{"name":" pencil","value":"fa fa-pencil"},{"name":" pencil-square","value":"fa fa-pencil-square"},{"name":" pencil-square-o","value":"fa fa-pencil-square-o"},{"name":" phone","value":"fa fa-phone"},{"name":" phone-square","value":"fa fa-phone-square"},{"name":" photo (alias)","value":"fa fa-photo"},{"name":" picture-o","value":"fa fa-picture-o"},{"name":" pie-chart","value":"fa fa-pie-chart"},{"name":" plane","value":"fa fa-plane"},{"name":" plug","value":"fa fa-plug"},{"name":" plus","value":"fa fa-plus"},{"name":" plus-circle","value":"fa fa-plus-circle"},{"name":" plus-square","value":"fa fa-plus-square"},{"name":" plus-square-o","value":"fa fa-plus-square-o"},{"name":" power-off","value":"fa fa-power-off"},{"name":" print","value":"fa fa-print"},{"name":" puzzle-piece","value":"fa fa-puzzle-piece"},{"name":" qrcode","value":"fa fa-qrcode"},{"name":" question","value":"fa fa-question"},{"name":" question-circle","value":"fa fa-question-circle"},{"name":" quote-left","value":"fa fa-quote-left"},{"name":" quote-right","value":"fa fa-quote-right"},{"name":" random","value":"fa fa-random"},{"name":" recycle","value":"fa fa-recycle"},{"name":" refresh","value":"fa fa-refresh"},{"name":" registered","value":"fa fa-registered"},{"name":" remove (alias)","value":"fa fa-remove"},{"name":" reorder (alias)","value":"fa fa-reorder"},{"name":" reply","value":"fa fa-reply"},{"name":" reply-all","value":"fa fa-reply-all"},{"name":" retweet","value":"fa fa-retweet"},{"name":" road","value":"fa fa-road"},{"name":" rocket","value":"fa fa-rocket"},{"name":" rss","value":"fa fa-rss"},{"name":" rss-square","value":"fa fa-rss-square"},{"name":" search","value":"fa fa-search"},{"name":" search-minus","value":"fa fa-search-minus"},{"name":" search-plus","value":"fa fa-search-plus"},{"name":" send (alias)","value":"fa fa-send"},{"name":" send-o (alias)","value":"fa fa-send-o"},{"name":" server","value":"fa fa-server"},{"name":" share","value":"fa fa-share"},{"name":" share-alt","value":"fa fa-share-alt"},{"name":" share-alt-square","value":"fa fa-share-alt-square"},{"name":" share-square","value":"fa fa-share-square"},{"name":" share-square-o","value":"fa fa-share-square-o"},{"name":" shield","value":"fa fa-shield"},{"name":" ship","value":"fa fa-ship"},{"name":" shopping-cart","value":"fa fa-shopping-cart"},{"name":" sign-in","value":"fa fa-sign-in"},{"name":" sign-out","value":"fa fa-sign-out"},{"name":" signal","value":"fa fa-signal"},{"name":" sitemap","value":"fa fa-sitemap"},{"name":" sliders","value":"fa fa-sliders"},{"name":" smile-o","value":"fa fa-smile-o"},{"name":" soccer-ball-o (alias)","value":"fa fa-soccer-ball-o"},{"name":" sort","value":"fa fa-sort"},{"name":" sort-alpha-asc","value":"fa fa-sort-alpha-asc"},{"name":" sort-alpha-desc","value":"fa fa-sort-alpha-desc"},{"name":" sort-amount-asc","value":"fa fa-sort-amount-asc"},{"name":" sort-amount-desc","value":"fa fa-sort-amount-desc"},{"name":" sort-asc","value":"fa fa-sort-asc"},{"name":" sort-desc","value":"fa fa-sort-desc"},{"name":" sort-down (alias)","value":"fa fa-sort-down"},{"name":" sort-numeric-asc","value":"fa fa-sort-numeric-asc"},{"name":" sort-numeric-desc","value":"fa fa-sort-numeric-desc"},{"name":" sort-up (alias)","value":"fa fa-sort-up"},{"name":" space-shuttle","value":"fa fa-space-shuttle"},{"name":" spinner","value":"fa fa-spinner"},{"name":" spoon","value":"fa fa-spoon"},{"name":" square","value":"fa fa-square"},{"name":" square-o","value":"fa fa-square-o"},{"name":" star","value":"fa fa-star"},{"name":" star-half","value":"fa fa-star-half"},{"name":" star-half-empty (alias)","value":"fa fa-star-half-empty"},{"name":" star-half-full (alias)","value":"fa fa-star-half-full"},{"name":" star-half-o","value":"fa fa-star-half-o"},{"name":" star-o","value":"fa fa-star-o"},{"name":" sticky-note","value":"fa fa-sticky-note"},{"name":" sticky-note-o","value":"fa fa-sticky-note-o"},{"name":" street-view","value":"fa fa-street-view"},{"name":" suitcase","value":"fa fa-suitcase"},{"name":" sun-o","value":"fa fa-sun-o"},{"name":" support (alias)","value":"fa fa-support"},{"name":" tablet","value":"fa fa-tablet"},{"name":" tachometer","value":"fa fa-tachometer"},{"name":" tag","value":"fa fa-tag"},{"name":" tags","value":"fa fa-tags"},{"name":" tasks","value":"fa fa-tasks"},{"name":" taxi","value":"fa fa-taxi"},{"name":" television","value":"fa fa-television"},{"name":" terminal","value":"fa fa-terminal"},{"name":" thumb-tack","value":"fa fa-thumb-tack"},{"name":" thumbs-down","value":"fa fa-thumbs-down"},{"name":" thumbs-o-down","value":"fa fa-thumbs-o-down"},{"name":" thumbs-o-up","value":"fa fa-thumbs-o-up"},{"name":" thumbs-up","value":"fa fa-thumbs-up"},{"name":" ticket","value":"fa fa-ticket"},{"name":" times","value":"fa fa-times"},{"name":" times-circle","value":"fa fa-times-circle"},{"name":" times-circle-o","value":"fa fa-times-circle-o"},{"name":" tint","value":"fa fa-tint"},{"name":" toggle-down (alias)","value":"fa fa-toggle-down"},{"name":" toggle-left (alias)","value":"fa fa-toggle-left"},{"name":" toggle-off","value":"fa fa-toggle-off"},{"name":" toggle-on","value":"fa fa-toggle-on"},{"name":" toggle-right (alias)","value":"fa fa-toggle-right"},{"name":" toggle-up (alias)","value":"fa fa-toggle-up"},{"name":" trademark","value":"fa fa-trademark"},{"name":" trash","value":"fa fa-trash"},{"name":" trash-o","value":"fa fa-trash-o"},{"name":" tree","value":"fa fa-tree"},{"name":" trophy","value":"fa fa-trophy"},{"name":" truck","value":"fa fa-truck"},{"name":" tty","value":"fa fa-tty"},{"name":" tv (alias)","value":"fa fa-tv"},{"name":" umbrella","value":"fa fa-umbrella"},{"name":" university","value":"fa fa-university"},{"name":" unlock","value":"fa fa-unlock"},{"name":" unlock-alt","value":"fa fa-unlock-alt"},{"name":" unsorted (alias)","value":"fa fa-unsorted"},{"name":" upload","value":"fa fa-upload"},{"name":" user","value":"fa fa-user"},{"name":" user-plus","value":"fa fa-user-plus"},{"name":" user-secret","value":"fa fa-user-secret"},{"name":" user-times","value":"fa fa-user-times"},{"name":" users","value":"fa fa-users"},{"name":" video-camera","value":"fa fa-video-camera"},{"name":" volume-down","value":"fa fa-volume-down"},{"name":" volume-off","value":"fa fa-volume-off"},{"name":" volume-up","value":"fa fa-volume-up"},{"name":" warning (alias)","value":"fa fa-warning"},{"name":" wheelchair","value":"fa fa-wheelchair"},{"name":" wifi","value":"fa fa-wifi"},{"name":" wrench","value":"fa fa-wrench"},{"name":" hand-grab-o (alias)","value":"fa fa-hand-grab-o"},{"name":" hand-lizard-o","value":"fa fa-hand-lizard-o"},{"name":" hand-o-down","value":"fa fa-hand-o-down"},{"name":" hand-o-left","value":"fa fa-hand-o-left"},{"name":" hand-o-right","value":"fa fa-hand-o-right"},{"name":" hand-o-up","value":"fa fa-hand-o-up"},{"name":" hand-paper-o","value":"fa fa-hand-paper-o"},{"name":" hand-peace-o","value":"fa fa-hand-peace-o"},{"name":" hand-pointer-o","value":"fa fa-hand-pointer-o"},{"name":" hand-rock-o","value":"fa fa-hand-rock-o"},{"name":" hand-scissors-o","value":"fa fa-hand-scissors-o"},{"name":" hand-spock-o","value":"fa fa-hand-spock-o"},{"name":" hand-stop-o (alias)","value":"fa fa-hand-stop-o"},{"name":" thumbs-down","value":"fa fa-thumbs-down"},{"name":" thumbs-o-down","value":"fa fa-thumbs-o-down"},{"name":" thumbs-o-up","value":"fa fa-thumbs-o-up"},{"name":" thumbs-up","value":"fa fa-thumbs-up"},{"name":" ambulance","value":"fa fa-ambulance"},{"name":" automobile (alias)","value":"fa fa-automobile"},{"name":" bicycle","value":"fa fa-bicycle"},{"name":" bus","value":"fa fa-bus"},{"name":" cab (alias)","value":"fa fa-cab"},{"name":" car","value":"fa fa-car"},{"name":" fighter-jet","value":"fa fa-fighter-jet"},{"name":" motorcycle","value":"fa fa-motorcycle"},{"name":" plane","value":"fa fa-plane"},{"name":" rocket","value":"fa fa-rocket"},{"name":" ship","value":"fa fa-ship"},{"name":" space-shuttle","value":"fa fa-space-shuttle"},{"name":" subway","value":"fa fa-subway"},{"name":" taxi","value":"fa fa-taxi"},{"name":" train","value":"fa fa-train"},{"name":" truck","value":"fa fa-truck"},{"name":" wheelchair","value":"fa fa-wheelchair"},{"name":" genderless","value":"fa fa-genderless"},{"name":" intersex (alias)","value":"fa fa-intersex"},{"name":" mars","value":"fa fa-mars"},{"name":" mars-double","value":"fa fa-mars-double"},{"name":" mars-stroke","value":"fa fa-mars-stroke"},{"name":" mars-stroke-h","value":"fa fa-mars-stroke-h"},{"name":" mars-stroke-v","value":"fa fa-mars-stroke-v"},{"name":" mercury","value":"fa fa-mercury"},{"name":" neuter","value":"fa fa-neuter"},{"name":" transgender","value":"fa fa-transgender"},{"name":" transgender-alt","value":"fa fa-transgender-alt"},{"name":" venus","value":"fa fa-venus"},{"name":" venus-double","value":"fa fa-venus-double"},{"name":" venus-mars","value":"fa fa-venus-mars"},{"name":" file","value":"fa fa-file"},{"name":" file-archive-o","value":"fa fa-file-archive-o"},{"name":" file-audio-o","value":"fa fa-file-audio-o"},{"name":" file-code-o","value":"fa fa-file-code-o"},{"name":" file-excel-o","value":"fa fa-file-excel-o"},{"name":" file-image-o","value":"fa fa-file-image-o"},{"name":" file-movie-o (alias)","value":"fa fa-file-movie-o"},{"name":" file-o","value":"fa fa-file-o"},{"name":" file-pdf-o","value":"fa fa-file-pdf-o"},{"name":" file-photo-o (alias)","value":"fa fa-file-photo-o"},{"name":" file-picture-o (alias)","value":"fa fa-file-picture-o"},{"name":" file-powerpoint-o","value":"fa fa-file-powerpoint-o"},{"name":" file-sound-o (alias)","value":"fa fa-file-sound-o"},{"name":" file-text","value":"fa fa-file-text"},{"name":" file-text-o","value":"fa fa-file-text-o"},{"name":" file-video-o","value":"fa fa-file-video-o"},{"name":" file-word-o","value":"fa fa-file-word-o"},{"name":" file-zip-o (alias)","value":"fa fa-file-zip-o"},{"name":" circle-o-notch","value":"fa fa-circle-o-notch"},{"name":" cog","value":"fa fa-cog"},{"name":" gear (alias)","value":"fa fa-gear"},{"name":" refresh","value":"fa fa-refresh"},{"name":" spinner","value":"fa fa-spinner"},{"name":" check-square","value":"fa fa-check-square"},{"name":" check-square-o","value":"fa fa-check-square-o"},{"name":" circle","value":"fa fa-circle"},{"name":" circle-o","value":"fa fa-circle-o"},{"name":" dot-circle-o","value":"fa fa-dot-circle-o"},{"name":" minus-square","value":"fa fa-minus-square"},{"name":" minus-square-o","value":"fa fa-minus-square-o"},{"name":" plus-square","value":"fa fa-plus-square"},{"name":" plus-square-o","value":"fa fa-plus-square-o"},{"name":" square","value":"fa fa-square"},{"name":" square-o","value":"fa fa-square-o"},{"name":" cc-amex","value":"fa fa-cc-amex"},{"name":" cc-diners-club","value":"fa fa-cc-diners-club"},{"name":" cc-discover","value":"fa fa-cc-discover"},{"name":" cc-jcb","value":"fa fa-cc-jcb"},{"name":" cc-mastercard","value":"fa fa-cc-mastercard"},{"name":" cc-paypal","value":"fa fa-cc-paypal"},{"name":" cc-stripe","value":"fa fa-cc-stripe"},{"name":" cc-visa","value":"fa fa-cc-visa"},{"name":" credit-card","value":"fa fa-credit-card"},{"name":" google-wallet","value":"fa fa-google-wallet"},{"name":" paypal","value":"fa fa-paypal"},{"name":" area-chart","value":"fa fa-area-chart"},{"name":" bar-chart","value":"fa fa-bar-chart"},{"name":" bar-chart-o (alias)","value":"fa fa-bar-chart-o"},{"name":" line-chart","value":"fa fa-line-chart"},{"name":" pie-chart","value":"fa fa-pie-chart"},{"name":" bitcoin (alias)","value":"fa fa-bitcoin"},{"name":" btc","value":"fa fa-btc"},{"name":" cny (alias)","value":"fa fa-cny"},{"name":" dollar (alias)","value":"fa fa-dollar"},{"name":" eur","value":"fa fa-eur"},{"name":" euro (alias)","value":"fa fa-euro"},{"name":" gbp","value":"fa fa-gbp"},{"name":" gg","value":"fa fa-gg"},{"name":" gg-circle","value":"fa fa-gg-circle"},{"name":" ils","value":"fa fa-ils"},{"name":" inr","value":"fa fa-inr"},{"name":" jpy","value":"fa fa-jpy"},{"name":" krw","value":"fa fa-krw"},{"name":" money","value":"fa fa-money"},{"name":" rmb (alias)","value":"fa fa-rmb"},{"name":" rouble (alias)","value":"fa fa-rouble"},{"name":" rub","value":"fa fa-rub"},{"name":" ruble (alias)","value":"fa fa-ruble"},{"name":" rupee (alias)","value":"fa fa-rupee"},{"name":" shekel (alias)","value":"fa fa-shekel"},{"name":" sheqel (alias)","value":"fa fa-sheqel"},{"name":" try","value":"fa fa-try"},{"name":" turkish-lira (alias)","value":"fa fa-turkish-lira"},{"name":" usd","value":"fa fa-usd"},{"name":" won (alias)","value":"fa fa-won"},{"name":" yen (alias)","value":"fa fa-yen"},{"name":" align-center","value":"fa fa-align-center"},{"name":" align-justify","value":"fa fa-align-justify"},{"name":" align-left","value":"fa fa-align-left"},{"name":" align-right","value":"fa fa-align-right"},{"name":" bold","value":"fa fa-bold"},{"name":" chain (alias)","value":"fa fa-chain"},{"name":" chain-broken","value":"fa fa-chain-broken"},{"name":" clipboard","value":"fa fa-clipboard"},{"name":" columns","value":"fa fa-columns"},{"name":" copy (alias)","value":"fa fa-copy"},{"name":" cut (alias)","value":"fa fa-cut"},{"name":" dedent (alias)","value":"fa fa-dedent"},{"name":" eraser","value":"fa fa-eraser"},{"name":" file","value":"fa fa-file"},{"name":" file-o","value":"fa fa-file-o"},{"name":" file-text","value":"fa fa-file-text"},{"name":" file-text-o","value":"fa fa-file-text-o"},{"name":" files-o","value":"fa fa-files-o"},{"name":" floppy-o","value":"fa fa-floppy-o"},{"name":" font","value":"fa fa-font"},{"name":" header","value":"fa fa-header"},{"name":" indent","value":"fa fa-indent"},{"name":" italic","value":"fa fa-italic"},{"name":" link","value":"fa fa-link"},{"name":" list","value":"fa fa-list"},{"name":" list-alt","value":"fa fa-list-alt"},{"name":" list-ol","value":"fa fa-list-ol"},{"name":" list-ul","value":"fa fa-list-ul"},{"name":" outdent","value":"fa fa-outdent"},{"name":" paperclip","value":"fa fa-paperclip"},{"name":" paragraph","value":"fa fa-paragraph"},{"name":" paste (alias)","value":"fa fa-paste"},{"name":" repeat","value":"fa fa-repeat"},{"name":" rotate-left (alias)","value":"fa fa-rotate-left"},{"name":" rotate-right (alias)","value":"fa fa-rotate-right"},{"name":" save (alias)","value":"fa fa-save"},{"name":" scissors","value":"fa fa-scissors"},{"name":" strikethrough","value":"fa fa-strikethrough"},{"name":" subscript","value":"fa fa-subscript"},{"name":" superscript","value":"fa fa-superscript"},{"name":" table","value":"fa fa-table"},{"name":" text-height","value":"fa fa-text-height"},{"name":" text-width","value":"fa fa-text-width"},{"name":" th","value":"fa fa-th"},{"name":" th-large","value":"fa fa-th-large"},{"name":" th-list","value":"fa fa-th-list"},{"name":" underline","value":"fa fa-underline"},{"name":" undo","value":"fa fa-undo"},{"name":" unlink (alias)","value":"fa fa-unlink"},{"name":" angle-double-down","value":"fa fa-angle-double-down"},{"name":" angle-double-left","value":"fa fa-angle-double-left"},{"name":" angle-double-right","value":"fa fa-angle-double-right"},{"name":" angle-double-up","value":"fa fa-angle-double-up"},{"name":" angle-down","value":"fa fa-angle-down"},{"name":" angle-left","value":"fa fa-angle-left"},{"name":" angle-right","value":"fa fa-angle-right"},{"name":" angle-up","value":"fa fa-angle-up"},{"name":" arrow-circle-down","value":"fa fa-arrow-circle-down"},{"name":" arrow-circle-left","value":"fa fa-arrow-circle-left"},{"name":" arrow-circle-o-down","value":"fa fa-arrow-circle-o-down"},{"name":" arrow-circle-o-left","value":"fa fa-arrow-circle-o-left"},{"name":" arrow-circle-o-right","value":"fa fa-arrow-circle-o-right"},{"name":" arrow-circle-o-up","value":"fa fa-arrow-circle-o-up"},{"name":" arrow-circle-right","value":"fa fa-arrow-circle-right"},{"name":" arrow-circle-up","value":"fa fa-arrow-circle-up"},{"name":" arrow-down","value":"fa fa-arrow-down"},{"name":" arrow-left","value":"fa fa-arrow-left"},{"name":" arrow-right","value":"fa fa-arrow-right"},{"name":" arrow-up","value":"fa fa-arrow-up"},{"name":" arrows","value":"fa fa-arrows"},{"name":" arrows-alt","value":"fa fa-arrows-alt"},{"name":" arrows-h","value":"fa fa-arrows-h"},{"name":" arrows-v","value":"fa fa-arrows-v"},{"name":" caret-down","value":"fa fa-caret-down"},{"name":" caret-left","value":"fa fa-caret-left"},{"name":" caret-right","value":"fa fa-caret-right"},{"name":" caret-square-o-down","value":"fa fa-caret-square-o-down"},{"name":" caret-square-o-left","value":"fa fa-caret-square-o-left"},{"name":" caret-square-o-right","value":"fa fa-caret-square-o-right"},{"name":" caret-square-o-up","value":"fa fa-caret-square-o-up"},{"name":" caret-up","value":"fa fa-caret-up"},{"name":" chevron-circle-down","value":"fa fa-chevron-circle-down"},{"name":" chevron-circle-left","value":"fa fa-chevron-circle-left"},{"name":" chevron-circle-right","value":"fa fa-chevron-circle-right"},{"name":" chevron-circle-up","value":"fa fa-chevron-circle-up"},{"name":" chevron-down","value":"fa fa-chevron-down"},{"name":" chevron-left","value":"fa fa-chevron-left"},{"name":" chevron-right","value":"fa fa-chevron-right"},{"name":" chevron-up","value":"fa fa-chevron-up"},{"name":" exchange","value":"fa fa-exchange"},{"name":" hand-o-down","value":"fa fa-hand-o-down"},{"name":" hand-o-left","value":"fa fa-hand-o-left"},{"name":" hand-o-right","value":"fa fa-hand-o-right"},{"name":" hand-o-up","value":"fa fa-hand-o-up"},{"name":" long-arrow-down","value":"fa fa-long-arrow-down"},{"name":" long-arrow-left","value":"fa fa-long-arrow-left"},{"name":" long-arrow-right","value":"fa fa-long-arrow-right"},{"name":" long-arrow-up","value":"fa fa-long-arrow-up"},{"name":" toggle-down (alias)","value":"fa fa-toggle-down"},{"name":" toggle-left (alias)","value":"fa fa-toggle-left"},{"name":" toggle-right (alias)","value":"fa fa-toggle-right"},{"name":" toggle-up (alias)","value":"fa fa-toggle-up"},{"name":" arrows-alt","value":"fa fa-arrows-alt"},{"name":" backward","value":"fa fa-backward"},{"name":" compress","value":"fa fa-compress"},{"name":" eject","value":"fa fa-eject"},{"name":" expand","value":"fa fa-expand"},{"name":" fast-backward","value":"fa fa-fast-backward"},{"name":" fast-forward","value":"fa fa-fast-forward"},{"name":" forward","value":"fa fa-forward"},{"name":" pause","value":"fa fa-pause"},{"name":" play","value":"fa fa-play"},{"name":" play-circle","value":"fa fa-play-circle"},{"name":" play-circle-o","value":"fa fa-play-circle-o"},{"name":" random","value":"fa fa-random"},{"name":" step-backward","value":"fa fa-step-backward"},{"name":" step-forward","value":"fa fa-step-forward"},{"name":" stop","value":"fa fa-stop"},{"name":" youtube-play","value":"fa fa-youtube-play"},{"name":" 500px","value":"fa fa-500px"},{"name":" adn","value":"fa fa-adn"},{"name":" amazon","value":"fa fa-amazon"},{"name":" android","value":"fa fa-android"},{"name":" angellist","value":"fa fa-angellist"},{"name":" apple","value":"fa fa-apple"},{"name":" behance","value":"fa fa-behance"},{"name":" behance-square","value":"fa fa-behance-square"},{"name":" bitbucket","value":"fa fa-bitbucket"},{"name":" bitbucket-square","value":"fa fa-bitbucket-square"},{"name":" bitcoin (alias)","value":"fa fa-bitcoin"},{"name":" black-tie","value":"fa fa-black-tie"},{"name":" btc","value":"fa fa-btc"},{"name":" buysellads","value":"fa fa-buysellads"},{"name":" cc-amex","value":"fa fa-cc-amex"},{"name":" cc-diners-club","value":"fa fa-cc-diners-club"},{"name":" cc-discover","value":"fa fa-cc-discover"},{"name":" cc-jcb","value":"fa fa-cc-jcb"},{"name":" cc-mastercard","value":"fa fa-cc-mastercard"},{"name":" cc-paypal","value":"fa fa-cc-paypal"},{"name":" cc-stripe","value":"fa fa-cc-stripe"},{"name":" cc-visa","value":"fa fa-cc-visa"},{"name":" chrome","value":"fa fa-chrome"},{"name":" codepen","value":"fa fa-codepen"},{"name":" connectdevelop","value":"fa fa-connectdevelop"},{"name":" contao","value":"fa fa-contao"},{"name":" css3","value":"fa fa-css3"},{"name":" dashcube","value":"fa fa-dashcube"},{"name":" delicious","value":"fa fa-delicious"},{"name":" deviantart","value":"fa fa-deviantart"},{"name":" digg","value":"fa fa-digg"},{"name":" dribbble","value":"fa fa-dribbble"},{"name":" dropbox","value":"fa fa-dropbox"},{"name":" drupal","value":"fa fa-drupal"},{"name":" empire","value":"fa fa-empire"},{"name":" expeditedssl","value":"fa fa-expeditedssl"},{"name":" facebook","value":"fa fa-facebook"},{"name":" facebook-f (alias)","value":"fa fa-facebook-f"},{"name":" facebook-official","value":"fa fa-facebook-official"},{"name":" facebook-square","value":"fa fa-facebook-square"},{"name":" firefox","value":"fa fa-firefox"},{"name":" flickr","value":"fa fa-flickr"},{"name":" fonticons","value":"fa fa-fonticons"},{"name":" forumbee","value":"fa fa-forumbee"},{"name":" foursquare","value":"fa fa-foursquare"},{"name":" ge (alias)","value":"fa fa-ge"},{"name":" get-pocket","value":"fa fa-get-pocket"},{"name":" gg","value":"fa fa-gg"},{"name":" gg-circle","value":"fa fa-gg-circle"},{"name":" git","value":"fa fa-git"},{"name":" git-square","value":"fa fa-git-square"},{"name":" github","value":"fa fa-github"},{"name":" github-alt","value":"fa fa-github-alt"},{"name":" github-square","value":"fa fa-github-square"},{"name":" gittip (alias)","value":"fa fa-gittip"},{"name":" google","value":"fa fa-google"},{"name":" google-plus","value":"fa fa-google-plus"},{"name":" google-plus-square","value":"fa fa-google-plus-square"},{"name":" google-wallet","value":"fa fa-google-wallet"},{"name":" gratipay","value":"fa fa-gratipay"},{"name":" hacker-news","value":"fa fa-hacker-news"},{"name":" houzz","value":"fa fa-houzz"},{"name":" html5","value":"fa fa-html5"},{"name":" instagram","value":"fa fa-instagram"},{"name":" internet-explorer","value":"fa fa-internet-explorer"},{"name":" ioxhost","value":"fa fa-ioxhost"},{"name":" joomla","value":"fa fa-joomla"},{"name":" jsfiddle","value":"fa fa-jsfiddle"},{"name":" lastfm","value":"fa fa-lastfm"},{"name":" lastfm-square","value":"fa fa-lastfm-square"},{"name":" leanpub","value":"fa fa-leanpub"},{"name":" linkedin","value":"fa fa-linkedin"},{"name":" linkedin-square","value":"fa fa-linkedin-square"},{"name":" linux","value":"fa fa-linux"},{"name":" maxcdn","value":"fa fa-maxcdn"},{"name":" meanpath","value":"fa fa-meanpath"},{"name":" medium","value":"fa fa-medium"},{"name":" odnoklassniki","value":"fa fa-odnoklassniki"},{"name":" odnoklassniki-square","value":"fa fa-odnoklassniki-square"},{"name":" opencart","value":"fa fa-opencart"},{"name":" openid","value":"fa fa-openid"},{"name":" opera","value":"fa fa-opera"},{"name":" optin-monster","value":"fa fa-optin-monster"},{"name":" pagelines","value":"fa fa-pagelines"},{"name":" paypal","value":"fa fa-paypal"},{"name":" pied-piper","value":"fa fa-pied-piper"},{"name":" pied-piper-alt","value":"fa fa-pied-piper-alt"},{"name":" pinterest","value":"fa fa-pinterest"},{"name":" pinterest-p","value":"fa fa-pinterest-p"},{"name":" pinterest-square","value":"fa fa-pinterest-square"},{"name":" qq","value":"fa fa-qq"},{"name":" ra (alias)","value":"fa fa-ra"},{"name":" rebel","value":"fa fa-rebel"},{"name":" reddit","value":"fa fa-reddit"},{"name":" reddit-square","value":"fa fa-reddit-square"},{"name":" renren","value":"fa fa-renren"},{"name":" safari","value":"fa fa-safari"},{"name":" sellsy","value":"fa fa-sellsy"},{"name":" share-alt","value":"fa fa-share-alt"},{"name":" share-alt-square","value":"fa fa-share-alt-square"},{"name":" shirtsinbulk","value":"fa fa-shirtsinbulk"},{"name":" simplybuilt","value":"fa fa-simplybuilt"},{"name":" skyatlas","value":"fa fa-skyatlas"},{"name":" skype","value":"fa fa-skype"},{"name":" slack","value":"fa fa-slack"},{"name":" slideshare","value":"fa fa-slideshare"},{"name":" soundcloud","value":"fa fa-soundcloud"},{"name":" spotify","value":"fa fa-spotify"},{"name":" stack-exchange","value":"fa fa-stack-exchange"},{"name":" stack-overflow","value":"fa fa-stack-overflow"},{"name":" steam","value":"fa fa-steam"},{"name":" steam-square","value":"fa fa-steam-square"},{"name":" stumbleupon","value":"fa fa-stumbleupon"},{"name":" stumbleupon-circle","value":"fa fa-stumbleupon-circle"},{"name":" tencent-weibo","value":"fa fa-tencent-weibo"},{"name":" trello","value":"fa fa-trello"},{"name":" tripadvisor","value":"fa fa-tripadvisor"},{"name":" tumblr","value":"fa fa-tumblr"},{"name":" tumblr-square","value":"fa fa-tumblr-square"},{"name":" twitch","value":"fa fa-twitch"},{"name":" twitter","value":"fa fa-twitter"},{"name":" twitter-square","value":"fa fa-twitter-square"},{"name":" viacoin","value":"fa fa-viacoin"},{"name":" vimeo","value":"fa fa-vimeo"},{"name":" vimeo-square","value":"fa fa-vimeo-square"},{"name":" vine","value":"fa fa-vine"},{"name":" vk","value":"fa fa-vk"},{"name":" wechat (alias)","value":"fa fa-wechat"},{"name":" weibo","value":"fa fa-weibo"},{"name":" weixin","value":"fa fa-weixin"},{"name":" whatsapp","value":"fa fa-whatsapp"},{"name":" wikipedia-w","value":"fa fa-wikipedia-w"},{"name":" windows","value":"fa fa-windows"},{"name":" wordpress","value":"fa fa-wordpress"},{"name":" xing","value":"fa fa-xing"},{"name":" xing-square","value":"fa fa-xing-square"},{"name":" y-combinator","value":"fa fa-y-combinator"},{"name":" y-combinator-square (alias)","value":"fa fa-y-combinator-square"},{"name":" yahoo","value":"fa fa-yahoo"},{"name":" yc (alias)","value":"fa fa-yc"},{"name":" yc-square (alias)","value":"fa fa-yc-square"},{"name":" yelp","value":"fa fa-yelp"},{"name":" youtube","value":"fa fa-youtube"},{"name":" youtube-play","value":"fa fa-youtube-play"},{"name":" youtube-square","value":"fa fa-youtube-square"},{"name":" ambulance","value":"fa fa-ambulance"},{"name":" h-square","value":"fa fa-h-square"},{"name":" heart","value":"fa fa-heart"},{"name":" heart-o","value":"fa fa-heart-o"},{"name":" heartbeat","value":"fa fa-heartbeat"},{"name":" hospital-o","value":"fa fa-hospital-o"},{"name":" medkit","value":"fa fa-medkit"},{"name":" plus-square","value":"fa fa-plus-square"},{"name":" stethoscope","value":"fa fa-stethoscope"},{"name":" user-md","value":"fa fa-user-md"},{"name":" wheelchair","value":"fa fa-wheelchair"}];

Cobler.types.LinkCollection = function(container){
	function render() {
		if(this.container.owner.options.disabled){
			return templates['widgets_link_collection'].render(get(), templates);
		}else{
			return templates['widgets_edit_links'].render(get(), templates);
		}
	}
	function get() {
		item.widgetType = 'LinkCollection';
		return item;
	}
	function set(newItem) {
			$.extend(true, item, newItem);
			item.links = _.sortBy(item.links, 'title');
			item.groups[0].links = _.where(item.links, {favorite: true});
			item.groups[1].links = _.where(item.links, {favorite: false});
			item.groups[0].collapse = item.collapse[0];
			item.groups[1].collapse = item.collapse[1];
	}
	function toJSON(opts){
		if(opts.editor){
			return _.pick(item, 'links', 'widgetType','filterable', 'guid', 'device', 'group', 'limit');
		}
		return {
			guid: item.guid,
			collapse: item.collapse,
			links: _.map(item.links, function(item){return _.pick(item, 'favorite') })
		}
	}
	var item = {
			groups:[{label: 'My Favorites', title:'favorites', collapse: false, favorites:true},{label: 'Useful Links', title:'links',collapse: false}],
			collapse: [false,false],
			links:[]
		}
	var	template = false; 
	if(!container.owner.options.disabled){
		template = 'widgets_edit_links_container'
	}

	var fields = {
			Filterable: {type: 'checkbox'}
		}
	return {
		fields: fields,
		render: render,
		toJSON: toJSON,
		edit: berryEditor.call(this, container),
		get: get,
		set: set,
		container: container,
		template:template,
		no_minimize:true,
		formTarget: $(),
		formOptions: {legend: 'Edit Link Collection Options'},
		initialize: function(el) {

			this.$el = $(el);
			this.$el.on('click', '.list-group .fa-times',	function(e) {
				e.stopPropagation();
				var guid = $(e.currentTarget).parent().data('guid');
				var item = this.get();
				item.links = _.without(item.links, _.findWhere(item.links, {guid: guid}));
				this.container.update(item, this);
				save();
			}.bind(this));


			this.$el.find('.panel-collapse ').on('hidden.bs.collapse', function(){
				this.set({collapse:[!this.$el.find('#favorites'+this.get().guid).hasClass('in'),!this.$el.find('#links'+this.get().guid).hasClass('in')]});
				save();
			}.bind(this)).on('shown.bs.collapse', function(){
				this.set({collapse:[!this.$el.find('#favorites'+this.get().guid).hasClass('in'),!this.$el.find('#links'+this.get().guid).hasClass('in')]});
				save();
			}.bind(this));

			this.$el.find('.favorites').on('click', function(e){
				e.preventDefault();
				e.stopPropagation();
				var index = -1;
				index = _.indexOf(item.links, _.findWhere(item.links, {guid: $(e.currentTarget).parent().data('guid')}))
				if(index != -1){
					item.links[index].favorite = !item.links[index].favorite;
				}
				this.container.update(item, this);
				save();

			}.bind(this))

			if(!this.container.owner.options.disabled){
				this.$el.on('click', '.list-group .list-group-item',function(e) {
					var item = this.get();
					$().berry({name: 'modal', attributes:
						_.findWhere(item.links, {guid: $(e.currentTarget).data('guid')})
	 				,fields: {
						Link: { placeholder: 'http://'},
						Title: {},
						Icon: {choices: icons, type: 'select'},
						Image: {},
						Color: {type: 'color'},
						'Default Favorite': {
							type: 'checkbox',
							name: 'favorite'
						},
						guid: {type: 'hidden'}
					}, legend: 'Edit Link'}).on('save', function(){

						var index = -1;
						index = _.indexOf(item.links, _.findWhere(item.links, {guid: $(e.currentTarget).data('guid')}))
						if(index != -1){
							item.links.splice(index, 1, Berries.modal.toJSON());
						}
						this.container.update(item, this);
						save();
						Berries.modal.trigger('close');
					}, this );
				}.bind(this));
			}

		}
	}
}
