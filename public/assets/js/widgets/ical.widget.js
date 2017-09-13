$(function(){
	widget_factory.register({
		type: 'Ical',
		defaults: {
			url:'https://www.google.com/calendar/ical/asmallco%40binghamton.edu/private-31391c88a53d30addf93ad7dbf196bb8/basic.ics', 
			count: 10,
			user_edit: true
		},
		view: {
			template: 'widgets_ical',
			render: function(){
				$.ajax({
				  url      : '/get_remote?q='+ encodeURIComponent(this.model.attributes.url),
				  success  : $.proxy(function (data) {
						icalParser.parseIcal(data);
						var events = [];
						for(var i in icalParser.icals[0].events){
							//icalParser.icals[0].events[i].dtstart[0].value = icalParser.icals[0].events[i].dtstart[0].value.replace(/([a-z0-9]{4})(\d{2})([T0-9]{5})(\d{2})/, "$1-$2-$3:$4:");
							events.push({
								summary: icalParser.icals[0].events[i].summary[0].value,
								start: icalParser.icals[0].events[i].dtstart[0].value.replace(/([a-z0-9]{4})(\d{2})([T0-9]{5})(\d{2})/, "$1-$2-$3:$4:")
							});
						}
						// this.model.preventSave = true;
						this.model.set({'loaded': _.first(events, this.model.attributes.count)});

						//this.model.set({'loaded': events});
					//jQuery.timeago.settings.allowFuture = true;
					//jQuery("abbr.timeago").timeago();
					this.$el.find("abbr.timeago").each(function(){
						$(this).html(moment($(this).attr('title')).format('hh:mm A'));
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
			userEdit: ['Count'],
		},
	});
});
