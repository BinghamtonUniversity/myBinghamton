$(function(){
	widget_factory.register({
		type: 'Content',
		defaults: {
			title: 'This is the title',
			text: 'Here is some text'
		},
		view: {
			template: 'widgets_content'
		},
		edit: {
			initialize: function() {
				this.autoElement();
			}
		},
		model: {
			schema:{
				Title: {},
				Text: {type: 'contenteditable', label: false}
			},
		},
		collection: {
			model: this.model,
			url: '/',
		}
	});
});