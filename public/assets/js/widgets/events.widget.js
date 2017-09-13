$(function(){
	widget_factory.register({
		type: 'Events',
		defaults: {
			url:'http://topaz.binghamton.edu/webapps/index.php/calendar/event/getEventsSearch?categories=10,2690', 
			count: 10,
			user_edit: true
		},
		view: {
			template: 'widgets_events',
			// renderers: {
			// 	ical: function(){

			// 	},
			// 	events: function(){

			// 	}
			// }
			render: function(){
				$.ajax({
				  url      : '/get_remote?q='+ encodeURIComponent(this.model.attributes.url),
				  dataType : 'JSON',
				  success  : $.proxy(function (data) {
						// this.model.preventSave = true;
						this.model.set({'loaded': _.first(data.data,  this.model.attributes.count)});
						//this.model.set({'loaded': data.data});

						this.$el.find("abbr.timeago").each(function(){
						$(this).html(moment($(this).attr('title')*1000).format('hh:mm A'));
					})
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
