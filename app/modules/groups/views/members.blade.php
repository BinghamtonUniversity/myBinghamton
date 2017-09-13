<script>
		id={{$id}};

		group_id = id;

		$.ajax({
			url:'/group_members?group='+id,
			success: function(data){
				$('#content').html('<div class="panel-page"><section class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><span class="fa fa-group"></span>&nbsp;Group Members (manually added only)<span class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add Member</span></h4></div><div id="mypanel" class="panel-body"></div></section></div>')
				
				this.fields =[
					{label: 'First Name', name: 'first_name'},
					{label: 'Last Name', name: 'last_name'},					
					{label: 'Pidm', name: 'pidm'},
					{name: 'id', type:'hidden'}
				];
				this.options = _.map(data, function(item){return _.pick(item, 'name', 'id')})
				
				bt = new berryTable({
	        entries: [10, 25, 50, 100],
	        count: 10,
	        autoSize: -20,
	        container: '#mypanel', 
	        schema: this.fields, 
	        data: _.pluck(data, 'user'),
	        delete: getDelete('/group_members/'+id+'/@{{pidm}}')
				})

				$('#content .btn-success').on('click', function() {
					mymodal = new modal({ 
						onshow:
						function () {
							$('#myModal .modal-search').berry({actions: false, fields: {'First Name': {name:'first'}, 'Last Name': {name:'last'},Email: {}}}).delay('change', function(){
								if(this.fields.first.value.length > 2 || this.fields.last.value.length > 2 || this.fields.email.value.length > 2){
									$.get('/query?'+$.param(this.toJSON()), function(data) {
											$('.list').html(render('user_list', {items:data}));
										}
									)
								}
							});	
						},
						content: '<div class="modal-search"></div><div class="list"></div>', title: 'Add Member'}
					);

					mymodal.ref.on('click', '.filterable', function(e){
						$.post('/group_members', {'group_id': group_id, 'pidm': e.currentTarget.dataset.pidm}, function(data) {
								mymodal.ref.modal('hide');
								bt.add(data);
							}
						);
					})

				}.bind(this))

			}
		})
</script>