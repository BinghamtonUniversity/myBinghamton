<script>

		group_id = {{$id}} || false;
		var url = '/images';
		if(group_id){
			url+='?group='+group_id
		}
		$.ajax({
			url: url,
			success: function(data){
				// $('#content').html('<div class="panel-page"><section class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><span class="fa fa-photo"></span>&nbsp;images<span class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add Images</span></h4></div><div id="mypanel" class="panel-body"></div></section></div>')
				data = _.sortBy(data, 'id');

				this.groups = data;
				var setup ={
					section: 'image',
					icon: 'fa fa-photo',
				}
				if(group_id){
					setup.group ={name:this.groups[0].name, id: group_id}
				}
				$('#content').html(render('admin_panel', setup));

				mydata = [].concat.apply([],_.map(data,function(item){
					var current = item.name;
					item.images = _.map(item.images, function(endpoint){
						endpoint.group = current;
						return endpoint;
					})
					return item.images;
				}));
				
				this.fields =[
					{label: 'Image',type:'raw', name:'image_filename',enabled:false, template: '<div style="width:150px;margin:0 auto;"><img style="max-width:150px;max-height:50px" src="@{{value}}"/></div>'},
					// {label: 'Group', name:'group', type: 'select', options:_.pluck(data,'name'),enabled:false},
					{label: 'Name', name: 'name'},
					{name: 'id', type:'hidden'}
				];

				if(!group_id){
					this.fields.unshift({label: 'Group', name:'group',type: 'select',  options:_.pluck(data,'name'),enabled:false})
				}
				this.options = _.map(data, function(item){return _.pick(item, 'name', 'id')})

				bt = new berryTable({
					name: 'images',
	        entries: [10, 25, 50, 100],
	        count: 10,
	        autoSize: -20,
	        container: '#mypanel', 
	        schema: this.fields, 
	        data: mydata,
	        click: function(model){
						new modal({legend: model.attributes.name, content:'<div style="text-align:center"><img style="max-width:100%" src="'+model.attributes.image_filename+'"/></div>'});

	        },
	        // edit: function(model){$.ajax({url: '/images/'+model.attributes.id, type: 'PUT', data: model.attributes});},
	        // delete: function(model){ $.ajax({url: '/images/'+model.attributes.id, type: 'DELETE'});}
		      edit: getEdit('/images/@{{id}}'),
		      delete: getDelete('/images/@{{id}}')
				})

				$('#content .btn-success').on('click', function() {
							$().berry({name:'newimage',actions:['cancel'],legend: 'Add Image(s)', fields:[
								{label: 'Group', name:'group_id', type: 'select', options: this.options, required: true, default: {label:"Choose a group", value:'-'}},
								{show:{"not_matches": {"name": "group_id","value": "-"}},type: 'upload', label: false, path: '/images?group_id=', name: 'image_filename'}]}).on('uploaded:image_filename', $.proxy(function(){
										var temp = Berries.newimage.fields.image_filename.value;
										temp.group = _.findWhere(this.groups,{id:parseInt(Berries.newimage.fields.image_filename.value.group_id, 10)}).name;
										bt.add(temp);
										Berries.newimage.trigger('close');
							}, this) ).on('change:group_id', function(){
									var groupid =this.fields.group_id.toJSON();
								this.fields.image_filename.update({path:'/images?group_id='+groupid}, true)
							});
				}.bind(this))


			}
		})
</script>