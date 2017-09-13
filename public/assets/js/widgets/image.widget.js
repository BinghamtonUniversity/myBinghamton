$(function(){
	widget_factory.register({
		type: 'Image',
		label: 'Image',
		icon: 'fa fa-photo',
		view: {
			template: 'widgets_image',
			initialize: function() {
				if(this.model.attributes.container){
					this.template = 'widgets_image_header';
				}
				this.autoElement();
			}
		},
		edit: {
			template: 'widgets_image_header',
			initialize: function() {
				this.autoElement();
			}
		},
		model: {
			schema:{
				Title: {},
				Container: {label: "Container?", type: 'checkbox'},
				Image: {type: 'image_picker', choices: '/images?group_id='+groupID, reference: 'image_filename', value_key: 'image_filename'},
				Text: {label: 'Alt Text', required: true}
			},
		},
	});
});