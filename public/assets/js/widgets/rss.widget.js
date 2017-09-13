$(function(){
	widget_factory.register({
		type: 'RSS',
		defaults: {
			url:'http://www.binghamton.edu/photos/index.php/feed/', 
			count: 5,
			user_edit: true
		},
		view: {
			template: 'widgets_rss',
			render: function(){
				$.ajax({
				  url      : document.location.protocol + '//ajax.googleapis.com/ajax/services/feed/load?v=1.0&num='+this.model.attributes.count+'&callback=?&q=' + encodeURIComponent(this.model.attributes.url),
				  dataType : 'json',
				  success  : $.proxy(function (data) {
				    if (data.responseData.feed && data.responseData.feed.entries) {
				    	for(var i in data.responseData.feed.entries){
								data.responseData.feed.entries[i].contentSnippet = data.responseData.feed.entries[i].contentSnippet.replace(/&lt;/,"<").replace(/&gt;/,">").replace(/&amp;/,"&");
				    	}
				    	this.model.set({'loaded': data.responseData.feed});
				    }
				  }, this)
				});
			}
		},
		model: {
			schema:{
				'Title': {},
				'Url': {default: '#'},
				'Count': {type: 'number'},
			},
			userEdit: ['Count']
		},
	});
});