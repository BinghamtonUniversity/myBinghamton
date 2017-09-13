$(function(){
	widget_factory.register({
		type: 'Poll',
		modal: true, 
		view: {
			template: 'widgets_poll',
			initialize: function() {				
				this.setElement(render(this.template, this.model.attributes ));
				this.model.on('change:poll',$.proxy(function(){
					$.ajax({
						url      : '/polls/' + this.model.attributes.poll,
						dataType : 'json',
						success  : $.proxy(function (data) {
							this.model.preventSave = true;
							this.model.set({loaded: {choices: JSON.parse(data.content), poll_name: data.poll_name, shuffle: data.shuffle} })
							this.$el.replaceWith(this.setElement(render(this.template, this.model.attributes )).$el);
							// debugger;
							// this.$el.find('.panel-body').html('<h5>'+this.model.attributes.loaded.name+'</h5><div class="poll_content"></div>');
							this.$el.find('.poll_content').berry({name: this.model.attributes.guid, actions:['save'], fields:[{label:false, name:'choice', type:'radio',choices:_.shuffle(_.pluck(JSON.parse(data.content), 'label'))}]}).on('save',function(){
								$.ajax({
									url      : '/pollsubmit/' + this.model.attributes.poll,
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
				// this.$el.find('.panel-body').html('<h5>'+this.model.attributes.loaded.name+'</h5><div class="poll_content"></div>');
				this.$el.find('.poll_content').html(render('poll_table', temp));
// this/total = x/100

			}, 
			render: function(){
				if(!this.model.attributes.loaded){
					$.ajax({
						url      : '/polllive/' + this.model.attributes.poll,
						dataType : 'json',
						success  : $.proxy(function (data) {
							this.model.preventSave = true;
							this.model.set({loaded: {choices: JSON.parse(data.content), poll_name: data.poll_name, shuffle: data.shuffle} })
							this.$el.replaceWith(this.setElement(render(this.template, this.model.attributes )).$el);
							if(!editor && data.results){
								this.generateTable(data);
							} else {
								var choices = _.pluck(JSON.parse(data.content), 'label');
								if(this.model.attributes.loaded.shuffle){
									choices = _.shuffle(choices);
								}
								// debugger;
								// this.$el.find('.panel-body').html('<h5>'+this.model.attributes.loaded.name+'</h5><div class="poll_content"></div>');
								this.berry = this.$el.find('.poll_content').berry({name: this.model.attributes.guid, actions:['save'], fields:[{label:false, name:'choice', type:'radio',choices: choices}]}).on('save',$.proxy(function(){
									$.ajax({
										url      : '/pollsubmit',
										dataType : 'json',
										data: {poll_id: this.model.attributes.poll, choice: this.berry.toJSON().choice},
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
				Poll: {type: 'select', choices: '/polls?group_id='+groupID, label_key: 'poll_name'},
			}
		},
	});
});