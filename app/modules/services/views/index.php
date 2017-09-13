<script>
		$.ajax({
			url:'/services',
			success: function(data){
				// $('#content').html('<div class="panel-page"><section class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><span class="fa fa-cubes"></span>&nbsp;microApps</h4></div><div id="mypanel" class="panel-body"></div></section></div>')

				$('#content').html('<div class="panel-page"><section class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><span class="fa fa-ban"></span>&nbsp;Services<span class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add Service</span></h4></div><div id="mypanel" class="panel-body"></div></section></div>')
				data = _.sortBy(data, 'id');
				this.groups = data;
				mydata = [].concat.apply([],_.map(data,function(item){
					var current = item.name;
					item.services = _.map(item.services, function(service){
						service.group = current;
						return service;
					})
					return item.services;
				}));
				
				this.fields =[
					{label: 'Group', name:'group',type: 'select',  options:_.pluck(data,'name'),enabled:false},
					{label: 'Name', name:'name'},
					{name: 'id', type:'hidden'}
				];
				this.options = _.map(data, function(item){return _.pick(item, 'name', 'id')})

				bt = new berryTable({
	        entries: [10, 25, 50, 100],
	        count: 10,	        
	        autoSize: -20,
	        container: '#mypanel', 
	        schema: this.fields, 
	        data: mydata,
	        click: function(model){location.assign('/admin/services/'+model.attributes.id+'/service');},
	        edit: function(model){$.ajax({url: '/services/'+model.attributes.id, type: 'PUT', data: model.attributes});},
	        delete: function(model){ $.ajax({url: '/services/'+model.attributes.id, type: 'DELETE'});}
				})

				$('#content .btn-success').on('click', function() {
					$().berry({name:'newService',legend: '<i class="fa fa-cubes"></i> Add Service',fields: [
						{label: 'Group', type: 'select', name:'group_id', options: this.options},
						{label: 'Name', name:'name'},
					]}).on('save', function(){
							$.ajax({url: '/services/', type: 'POST', data: Berries.newService.toJSON(), success:function(r){
								r.group = _.findWhere(this.groups, {id: parseInt(r.group_id,10)}).name;

								bt.add(r);
							}.bind(this)});
							Berries.newService.trigger('close');
					},this );
				}.bind(this))

			}
		})
</script>