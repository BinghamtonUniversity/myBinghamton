$(function(){
	widget_factory.register({
		type: 'Slider',
		defaults: {
			images: [{image: '' , url:'', text: '', overlay: ''}]
		},
		view: {
			template: 'widgets_slider',
			render: function(){
				$('.slider').nivoSlider({effect: 'fade'});
			},
			initialize: function() {
				this.model.on('change:images',function(){

					this.trigger('publish');

				})
				this.autoElement();
			}
		},
		model: {
			schema:{
			//	Image: {type: 'custom_select', choices: '/images', reference: 'name'},
				"My Images":{label: false,fields:[
				 {type: 'fieldset',name:"images", label: false, multiple: {duplicate: true}, fields: [
					{ name: 'image', type: 'image_picker', choices: '/images?group_id='+groupID, value_key: 'image_filename', label: 'Image'},
					{ name: 'url', label: 'Link', placeholder: 'http://'},
					{ name: 'window', label: 'New Window?', type: 'checkbox'},
					{ name: 'text', label: 'Alt Text', required: true},
					{ name: 'overlay', label: 'Overlay'}
				]}
				]}
			},
		},
	});
});