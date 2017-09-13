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
$(function(){
	widget_factory.register({
		type: 'Form',
		modal: true, 
		view: {
			load :function(){
				$.ajax({
					url      : '/forms/' + this.model.attributes.form,
					dataType : 'json',
					success  : $.proxy(function (data) {
						this.model.set({loaded: {fields: JSON.parse(data.fields||"{}"),options: JSON.parse(data.options||"{}"), name: data.name} })
						this.$el.replaceWith(this.setElement(render(this.template, this.model.attributes )).$el);
						this.berry = this.$el.find('.form_content').berry({name:this.model.attributes.form ,autoDestroy: false, inline: this.model.attributes.loaded.options.inline , action: '/formsubmit/'+this.model.attributes.form ,actions:['submit'], fields: this.model.attributes.loaded.fields});
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
					}, this)
				});
			},
			template: 'widgets_form_container',
			initialize: function() {				
				if(!this.model.attributes.container){this.template ='widgets_form_container';}
				this.autoElement();
				this.model.on('change',$.proxy(this.load, this) );
			},
			render: function(){
				if(!this.model.attributes.loaded){
					this.load();
				}
			}
		},
		model: {
			schema:{
				Title: {},
				Form: {type: 'select', choices: '/forms?group_id='+groupID, label_key: 'form_name'},
				Container: {label: "Container?", type: 'checkbox'},
			}
		},
	});
});