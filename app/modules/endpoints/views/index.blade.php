<script>
		group_id = {{$id}} || false;
		var url = '/endpoints';
		if(group_id){
			url+='?group='+group_id
		}

		$.ajax({
			url: url,
			success: function(data){
				//$('#content').html('<div class="panel-page"><section class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><span class="fa fa-server"></span>&nbsp;Endpoints<span class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add Endpoint</span></h4></div><div id="mypanel" class="panel-body"></div></section></div>')
				data = _.sortBy(data, 'id');

				this.groups = data;
				var setup ={
					section: 'endpoint',
					icon: 'fa fa-crosshairs',
				}
				if(group_id){
					setup.group ={name:this.groups[0].name, id: group_id}
				}
				$('#content').html(render('admin_panel', setup));


				mydata = [].concat.apply([],_.map(data,function(item){
					var current = item.name;
					item.endpoints = _.map(item.endpoints, function(endpoint){
						endpoint.group = current;
						return endpoint;
					})
					return item.endpoints;
				}));
				
				this.fields =[
					// {label: 'Group', name:'group',type: 'select',  options:_.pluck(data,'name'),enabled:false},
					{label: 'Endpoint Name', name: 'name'},
					{label: 'Path', name: 'target'},
					{label: 'Auth Type', name: 'authtype', options:['HTTPS'], type:'radio'},
					{label: 'Username' ,autocomplete: false, show:{not_matches:{name:'authtype', value:'None'}} },
					{label: 'Password', type:'text', name: 'targetpassword', default:'*****', autocomplete: false, show:{not_matches: {name: 'authtype', value: 'None'}}},
					{name: 'id', type:'hidden'}
				];
				this.filters = [
						// {label: 'Group',type: 'select', name:'group', options:_.pluck(data,'name')},
						{label: 'Endpoint Name', name: 'name'},
						{label: 'Path', name: 'target'}
					]
				if(!group_id){
					this.fields.unshift({label: 'Group', name:'group',type: 'select',  options:_.pluck(data,'name'),enabled:false});
					this.filters.unshift({label: 'Group',type: 'select', name:'group', options:_.pluck(data,'name')});

				}
				this.options = _.map(data, function(item){return _.pick(item, 'name', 'id')})

				bt = new berryTable({
					name: 'endpoints',
	        entries: [10, 25, 50, 100],
	        count: 10,
	        autoSize: -20,
	        container: '#mypanel', 
	        schema: this.fields, 
	        data: mydata,
	        filters: this.filters,

	        // click: function(model){myrouter.navigate('/endpoint/'+model.attributes.id, { trigger: true });},
	        // edit: function(model){$.ajax({url: '/endpoints/'+model.attributes.id, type: 'PUT', data: model.attributes});},
	        // delete: function(model){ $.ajax({url: '/endpoints/'+model.attributes.id, type: 'DELETE'});}
		      edit: getEdit('/endpoints/@{{id}}'),
		      delete: getDelete('/endpoints/@{{id}}')
				})

				$('#content .btn-success').on('click', function() {
					$().berry({name:'newEndpoint',legend: '<i class="fa fa-sitemap"></i> Add Endpoint',fields: [
						{label: 'Group', type: 'select', name:'group_id', options: this.options},
						{label: 'Endpoint Name', name: 'name'},
						{label: 'Path', name: 'target'},
						{label: 'Auth Type', name: 'authtype', options:['HTTPS'], type:'radio'},
						{label: 'Username' ,autocomplete: false, show:{not_matches:{name:'authtype', value:'None'}} },
						{label: 'Password', type:'text', name: 'targetpassword', default:'*****', autocomplete: false, show:{not_matches: {name: 'authtype', value: 'None'}}}
					]}).on('save', function(){

							var temp = Berries.newEndpoint.toJSON();
							temp.group = _.findWhere(this.groups, {id: parseInt(temp.group_id,10)}).name;
							getCreate('/endpoints/')(bt.add(temp))

							// $.ajax({url: '/endpoints/', type: 'POST', data: Berries.newEndpoint.toJSON(), success:function(r){
							// 	r.group = _.findWhere(this.groups, {id: parseInt(r.group_id,10)}).name;
							// 	bt.add(r);
							// }.bind(this)});
							Berries.newEndpoint.trigger('close');
					},this );
				}.bind(this))

			}
		})


</script>