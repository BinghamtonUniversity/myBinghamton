$(function(){
	widget_factory.register({
		type: 'Html',
		defaults: {
			// title: 'hello',
			html: '',
		},
		view: {
			template: 'widgets_html',
			render: function(){
				this.$el.find('[data-toggle="tooltip"]').tooltip();
	    	this.$el.find('[data-toggle="popover"]').popover();
			},
			initialize: function() {
				if(this.model.attributes.container){this.template ='widgets_html_container';}
				this.model.attributes.html =  this.model.attributes.html.replace(/<\\\/script>/g, "</script>");
				this.setElement(render(this.template, this.model.attributes ));
				this.model.on('change', $.proxy(function() {
					this.model.attributes.html =  this.model.attributes.html.replace(/<\\\/script>/g, "</script>");//.split("<\\\/script>").join('</script>').split("<\/script>").join('</script>');
					this.$el.replaceWith(this.setElement(render(this.template, this.model.attributes )).$el);
					this.render()
					this.editing = false;
					this.trigger('rendered');
				}), this);
			}
		},
		edit: {
			template: 'widgets_html_container',
			render: function(){
				this.$el.find('[data-toggle="tooltip"]').tooltip();
	    	this.$el.find('[data-toggle="popover"]').popover();
			},		
			initialize: function() {
				this.autoElement();
			}
		},
		model: {
			getAttributes: function(){
				this.attributes.html = this.attributes.html.replace(/<\/script>/g, "<\\/script>");
				return this.attributes;
			},
			schema:{
				Container: {label: "Container?", type: 'checkbox'},
				Title: {},
				HTML: {type: 'ace', label:false},
			},
		},
		collection: {
			model: this.model,
			url: '/',
		}
	});
});

