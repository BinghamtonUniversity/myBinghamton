$(function(){
	widget_factory.register({
		type: 'Endpoint',
		modal: true, 
		view: {
			template: 'widgets_endpoint',
			initialize: function() {				
				this.setElement(render(this.template, this.model.attributes ));
				this.model.on('change:endpoint',$.proxy(function(){
					$.ajax({
						url      : '/endpoints/' + this.model.attributes.endpoint,
						dataType : 'json',
						success  : $.proxy(function (data) {
							this.model.preventSave = true;
							this.model.set({loaded: {choices: JSON.parse(data.content), endpoint_name: data.endpoint_name, shuffle: data.shuffle} })
							this.$el.replaceWith(this.setElement(render(this.template, this.model.attributes )).$el);
							// this.$el.find('.panel-body').html('<h5>'+this.model.attributes.loaded.name+'</h5><div class="endpoint_content"></div>');
							this.$el.find('.endpoint_content').berry({name: this.model.attributes.guid, actions:['save'], fields:[{label:false, name:'choice', type:'radio',choices:_.shuffle(_.pluck(JSON.parse(data.content), 'label'))}]}).on('save',function(){
								$.ajax({
									url      : '/endpointsubmit/' + this.model.attributes.endpoint,
									dataType : 'json',
									method: 'post',
									success  : $.proxy(function (data) {
									}, this)
								});
							});
						}, this)
					});
				},this) );
			},
			generateTable: function(data){
				var temp = {results:[], total: data.total};
				for(var i in data.results){
					temp.results.push({name:i, value: data.results[i], percent: ((data.results[i]/data.total)*100).toFixed(1) })
				}								
				// this.$el.find('.panel-body').html('<h5>'+this.model.attributes.loaded.name+'</h5><div class="endpoint_content"></div>');
				this.$el.find('.endpoint_content').html(render('endpoint_table', temp));
// this/total = x/100

			}, 
			render: function(){
				if(!this.model.attributes.loaded){
					$.ajax({
						url      : '/endpointlive/' + this.model.attributes.endpoint,
						dataType : 'json',
						success  : $.proxy(function (data) {
							this.model.preventSave = true;
							this.model.set({loaded: {choices: JSON.parse(data.content), endpoint_name: data.endpoint_name, shuffle: data.shuffle} })
							this.$el.replaceWith(this.setElement(render(this.template, this.model.attributes )).$el);
							if(!editor && data.results){
								this.generateTable(data);
							} else {
								var choices = _.pluck(JSON.parse(data.content), 'label');
								if(this.model.attributes.loaded.shuffle){
									choices = _.shuffle(choices);
								}
								// debugger;
								// this.$el.find('.panel-body').html('<h5>'+this.model.attributes.loaded.name+'</h5><div class="endpoint_content"></div>');
								this.berry = this.$el.find('.endpoint_content').berry({name: this.model.attributes.guid, actions:['save'], fields:[{label:false, name:'choice', type:'radio',choices: choices}]}).on('save',$.proxy(function(){
									$.ajax({
										url      : '/endpointsubmit',
										dataType : 'json',
										data: {endpoint_id: this.model.attributes.endpoint, choice: this.berry.toJSON().choice},
										method: 'post',
										success  : $.proxy(function (data) {

											this.generateTable(data);
										}, this)
									});
								},this));
							}
						}, this)
					});
				}
			}
		},
		model: {
			schema:{
				Title: {},
				Endpoint: {type: 'select', choices: '/endpoints?group_id='+groupID, label_key: 'endpoint_name'},
			}
		},
	});
});