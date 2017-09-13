<script>
group_id = {{$id}} || false;

var url = '/microapps';
if(group_id){
	url+='?group='+group_id
}

$.ajax({
	url: url,
	success: function(data){

		data = _.sortBy(data, 'id');
		this.groups = data;

		var setup ={
			section: 'microapp',
			icon: 'fa fa-cubes',
		}
		if(group_id){
			setup.group ={name:this.groups[0].name, id: group_id}
		}
		$('#content').html(render('admin_panel', setup));

		mydata = [].concat.apply([], _.map(data, function(item) {
			var current = item.name;
			item.microapps = _.map(item.microapps, function(microapp){
				microapp.group = current;
				return microapp;
			})
			return item.microapps;
		}));
		
		this.fields =[
			{label: 'Name', name:'name'},
			{name: 'id', type:'hidden', label:'ID'}
		];
		if(!group_id) {
			this.fields.unshift({label: 'Group', name:'group',type: 'select',  options:_.pluck(data,'name'), enabled:false})
		}
		this.options = _.map(data, function(item){return _.pick(item, 'name', 'id')})
		bt = new berryTable({
			name: 'uapps',
      entries: [10, 25, 50, 100],
      count: 10,
      autoSize: -20,
      container: '#mypanel', 
      schema: this.fields, 
      data: mydata,
      click: function(model){location.assign('/admin/microapps/'+model.attributes.id+'/app');},
      edit: getEdit('/microapps/@{{id}}'),
      delete: getDelete('/microapps/@{{id}}')
		})

		$('#content .btn-success').on('click', function() {
			$().berry({name:'newMicroapp',legend: '<i class="fa fa-cube"></i> Add MicroApp',fields: [
				{label: 'Group', type: 'select', name:'group_id', options: this.options},
				{label: 'Name', name:'name'},
			]}).on('save', function() {

				var temp = Berries.newMicroapp.toJSON();
				temp.group = _.findWhere(this.groups, {id: parseInt(temp.group_id, 10)}).name;
				debugger;
				getCreate('/microapps/')(bt.add(temp))
				
				Berries.newMicroapp.trigger('close');
			},this );
		}.bind(this))

	}
})

</script>