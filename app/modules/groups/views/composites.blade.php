<script type="text/javascript" src="/modules/assets/js/groups/resource.js"></script>
<script>

		id={{$id}};
		group_id = id;

		$.ajax({
			url:'/group_composites?group='+id,
			success: function(data){
		$.ajax({
			url:'/groups',
			success: function(newData){
				Berry.collection.add('/groups', newData);

				$('#content').html('<div class="panel-page"><section class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><span class="fa fa-cubes"></span>&nbsp;Composites<span class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add Composite</span></h4></div><div id="mypanel" class="panel-body"></div></section></div>')
					data = _.map(data, function(item){
						item.group = _.findWhere(Berry.collection.get('/groups'),{id:item.composite_id}).name;
						return item;
					})

				this.fields =[
					{type: 'hidden', name: 'group_id', value: id},
					{type: 'select', label: 'Composite Group', choices: '/groups', name: 'composite_id'}

				];
				this.options = _.map(data, function(item){return _.pick(item, 'name', 'id')})

				bt = new berryTable({
	        entries: [10, 25, 50, 100],
	        count: 10,
	        autoSize: -20,
	        container: '#mypanel', 
	        schema: this.fields, 
	        data: data,
	        // delete: function(model){ $.ajax({url: '/group_composites/'+model.attributes.group_id+'/'+model.attributes.composite_id, type: 'DELETE'});}
	        delete: getDelete('/microapps/@{{group_id}}/@{{composite_id}}')

				})

				$('#content .btn-success').on('click', function() {
					$().berry({name:'newEndpoint',legend: '<i class="fa fa-tag"></i> Add Composite',fields: this.fields}).on('save', function(){
						

							var temp = Berries.newEndpoint.toJSON();
							temp.group = _.findWhere(this.groups, {id: parseInt(temp.group_id,10)}).name;
							getCreate('/group_composites/')(bt.add(temp))
							// $.ajax({url: '/group_composites', type: 'POST', data: Berries.newEndpoint.toJSON(), success:function(r){
							// 	r.group = _.findWhere(Berry.collection.get('/groups'), {id: parseInt(r.composite_id,10)}).name;
							// 	bt.add(r);
							// }.bind(this)});
							Berries.newEndpoint.trigger('close');
					},this );
				}.bind(this))

			}})

			}
		})
</script>