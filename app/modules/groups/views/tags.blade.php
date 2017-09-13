<script type="text/javascript" src="/modules/assets/js/groups/resource.js"></script>
<script>

		id={{$id}};
		group_id = id;

		$.ajax({
			url:'/group_tags?group='+id,
			success: function(data){
				$('#content').html('<div class="panel-page"><section class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><span class="fa fa-tags"></span>&nbsp;Tags<span class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add Tag</span></h4></div><div id="mypanel" class="panel-body"></div></section></div>')

				this.fields =[
					{label:'Name', name: 'name', required: false},
					{label:'Value', name: 'value', required: false},
					{name:'group_id', value: group_id, type:'hidden'},
					{name: 'id', type:'hidden'}
				];
				this.options = _.map(data, function(item){return _.pick(item, 'name', 'id')})

				bt = new berryTable({
	        entries: [10, 25, 50, 100],
	        count: 10,
	        autoSize: -20,
	        container: '#mypanel', 
	        schema: this.fields, 
	        data: data,
	        // delete: function(model){ $.ajax({url: '/group_tags/'+model.attributes.id, type: 'DELETE'});}
	        delete: getDelete('/group_tags/@{{id}}')
				})

				$('#content .btn-success').on('click', function() {
					$().berry({name:'newEndpoint',legend: '<i class="fa fa-tag"></i> Add Tag',fields: this.fields}).on('save', function(){

							var temp = Berries.newEndpoint.toJSON();
							temp.group = _.findWhere(this.groups, {id: parseInt(temp.group_id,10)}).name;
							getCreate('/group_tags/')(bt.add(temp))
							// $.ajax({url: '/group_tags/', type: 'POST', data: Berries.newEndpoint.toJSON(), success:function(r){
							// 	bt.add(r);
							// }.bind(this)});
							Berries.newEndpoint.trigger('close');
					},this );
				}.bind(this))

			}
		})
</script>