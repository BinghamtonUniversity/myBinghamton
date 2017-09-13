<script>
		group_id = {{$id}} || false;
		var url = '/forms';
		if(group_id){
			url+='?group='+group_id
		}
		$.ajax({
			url: url,
			success: function(data){
				//$('#content').html('<div class="panel-page"><section class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><span class="fa fa-check-square-o"></span>&nbsp;forms<span class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add Form</span></h4></div><div id="mypanel" class="panel-body"></div></section></div>')
				data = _.sortBy(data, 'id');

				this.groups = data;
				var setup ={
					section: 'form',
					icon: 'fa fa-check-square-o',
				}
				if(group_id){
					setup.group ={name:this.groups[0].name, id: group_id}
				}
				$('#content').html(render('admin_panel', setup));

				mydata = [].concat.apply([],_.map(data,function(item){
					var current = item.name;
					item.forms = _.map(item.forms, function(endpoint){
						endpoint.group = current;
						return endpoint;
					})
					return item.forms;
				}));
				
				this.fields =[
					// {label: 'Group', name:'group',type: 'select',  options:_.pluck(data,'name'),enabled:false},
					{label: 'Form Name', name: 'name'},
					{name: 'id', type:'hidden'}
				];

				if(!group_id){
					this.fields.unshift({label: 'Group', name:'group',type: 'select',  options:_.pluck(data,'name'),enabled:false})
				}
				this.options = _.map(data, function(item){return _.pick(item, 'name', 'id')})

				bt = new berryTable({
					name: 'forms',
	        entries: [10, 25, 50, 100],
	        count: 10,
	        autoSize: -20,
	        container: '#mypanel', 
	        schema: this.fields, 
	        data: mydata,

	        click: function(model){location.assign('/admin/forms/'+model.attributes.id+'/form'); },
	        // edit: function(model){$.ajax({url: '/forms/'+model.attributes.id, type: 'PUT', data: model.attributes});},
	        // delete: function(model){ $.ajax({url: '/forms/'+model.attributes.id, type: 'DELETE'});}
		      edit: getEdit('/forms/@{{id}}'),
		      delete: getDelete('/forms/@{{id}}')
				})

				$('#content .btn-success').on('click', function() {
					$().berry({name:'newForm',legend: '<i class="fa fa-check-square-o"></i> Add Form',fields: [
						{label: 'Group', type: 'select', name:'group_id', options: this.options},
						{label: 'Form Name', name: 'name'},
					]}).on('save', function(){

							var temp = Berries.newForm.toJSON();
							temp.group = _.findWhere(this.groups, {id: parseInt(temp.group_id,10)}).name;
							getCreate('/forms/')(bt.add(temp))
				
							// $.ajax({url: '/forms/', type: 'POST', data: Berries.newForm.toJSON(), success:function(r){
							// 	r.group = _.findWhere(this.groups, {id: parseInt(r.group_id,10)}).name;
							// 	bt.add(r);
							// }.bind(this)});
							Berries.newForm.trigger('close');
					},this );
				}.bind(this))

			}
		})
</script>