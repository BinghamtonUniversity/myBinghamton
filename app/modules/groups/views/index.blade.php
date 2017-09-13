<script>
		
	$(function() {
		data = {{ $groups }};
		var setup ={
			section: 'group',
			icon: 'fa fa-users',
			sort:true,
		}
		$('#content').html(render('admin_panel', setup));

		this.fields =[
			{required: true, label: 'Name', name: 'name'},
			{label: 'Slug', name: 'slug', required: true, max: 50, type:'text'},
			{label: 'Priority', name:'Priority', type: 'custom_radio', value: '0', options: [ {label: 'Primary', value: '1'}, {label: 'Secondary', value: '0'}]},
			{label: 'Type', name: 'type', type: 'select', value: 'Public', options: [ 'Private', 'Public', 'Closed']},
			{label: 'Community', name: 'community_flag', type: 'custom_radio', options: [{label: 'No', value: 0}, {label: 'Yes', value: 1}]},
			{name: 'id', type:'hidden'}

		];
		this.filters =[
			{required: true, label: 'Name', name: 'name'},
			{label: 'Slug', name: 'slug', required: true, type:'text'},
			{label: 'Priority', name:'Priority', type: 'radio', value: '0', options: [ {label: 'Primary', value: '1'}, {label: 'Secondary', value: '0'}],showColumn:false},
			{label: 'Type', name: 'type', type: 'select', value: 'Public', options: [ 'Private', 'Public', 'Closed'],showColumn:false},
			{label: 'Community', name: 'community_flag', type: 'radio', options: [{label: 'No', value: 0}, {label: 'Yes', value: 1}]}
		];
		this.options = _.map(data, function(item){return _.pick(item, 'name', 'id')})

		bt = new berryTable({
			name: 'groups',
      entries: [10, 25, 50, 100],
      count: 10,
      autoSize: -20,
      container: '#mypanel', 
      schema: this.fields, 
      filters: this.filters, 
      data: data,
      click: function(model){location.assign('/admin/groups/'+model.attributes.id);},
      edit: getEdit('/groups/@{{id}}'),
      delete: getDelete('/groups/@{{id}}')
		})

		$('#content .btn-success').on('click', function() {
			$().berry({name:'newGroup',legend: '<i class="fa fa-cube"></i> Add Group',fields: this.fields}).on('save', function() {
				if(Berries.newGroup.validate()) {
					getCreate('/groups/')(bt.add(Berries.newGroup.toJSON()))
					Berries.newGroup.trigger('close');
				}
			},this );
		}.bind(this))
})


$('body').on('click', '.sort',function(e){
	render('group_view');
	templates.listing = Hogan.compile('<ol id="sorter" class="list-group" style="margin: -15px;">@{{#items}}@{{>group_view}}@{{/items}}</ol>');
	var tempdata = _.map(bt.models, function(item){return item.attributes});//[].concat.apply([],pageData)

	tempdata = _.sortBy(tempdata, 'order');
	mymodal = modal({title: "Sort Groups", content: templates.listing.render({items:tempdata},templates ), footer: '<div class="btn btn-success save-sort">Save</div>'});

	Sortable.create($(mymodal.ref).find('.modal-content ol')[0], {draggable:'li'});
		
	$('.save-sort').on('click', function(e) {
			var results = _.map($('#sorter .filterable'), function(item, index){
				return {order: index, key: item.dataset.id }
			})
			$.ajax({
				url: '/groups'+'/order',
				type: 'post',
				data: {results: results}
			});
			mymodal.ref.modal('hide');
	})
})

</script>