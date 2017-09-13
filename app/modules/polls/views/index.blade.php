<!-- <script type="text/javascript" src="/modules/assets/js/polls/resource.js"></script> -->
<script>
		group_id = {{$id}} || false;

		var url = '/polls';
		if(group_id){
			url+='?group='+group_id
		}
		$.ajax({
			url: url,
			success: function(data){
				// $('#content').html('<div class="panel-page"><section class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><span class="fa fa-list-ol"></span>&nbsp;polls<span class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add Poll</span></h4></div><div id="mypanel" class="panel-body"></div></section></div>')

				data = _.sortBy(data, 'id');

				this.groups = data;

				var setup ={
					section: 'poll',
					icon: 'fa fa-list-ol',
					// sort:true
				}
				if(group_id){
					setup.group ={name:this.groups[0].name, id: group_id}
				}
				$('#content').html(render('admin_panel', setup));

				mydata = [].concat.apply([],_.map(data,function(item){
					var current = item.name;
					item.polls = _.map(item.polls, function(endpoint){
						endpoint.group = current;
						return endpoint;
					})
					return item.polls;
				}));
				
				this.fields =[
					// {label: 'Group', name:'group',type: 'select',  options:_.pluck(data,'name'), enabled:false},
					{label: 'Poll Name', name: 'poll_name'},
					{label: 'Shuffle', name:'shuffle', type: 'checkbox', 'alt-display': 'ui-switch ui-switch-success', 'container': 'i'},
					{name: 'id', type:'hidden'}
				];
				if(!group_id){
					this.fields.unshift({label: 'Group', name:'group',type: 'select',  options:_.pluck(data,'name'),enabled:false})
				}
				this.options = _.map(data, function(item){return _.pick(item, 'name', 'id')})

				bt = new berryTable({
					name: 'polls',
	        entries: [10, 25, 50, 100],
	        count: 10,
	        autoSize: -20,
	        container: '#mypanel', 
	        schema: this.fields, 
	        data: mydata,				
	        events:[
						{'name': 'chart', 'label': '<i class="fa fa-pie-chart"></i> Charts', callback: function(model){
							location.assign('/admin/polls/'+model.attributes.id+'/graphs');
							// myrouter.navigate('/poll_graphs/'+item.attributes.id, { trigger: true });
						}},
						{'name': 'submissions', 'label': '<i class="fa fa-circle"></i> Submissions', callback: function(model){
							location.assign('/admin/polls/'+model.attributes.id+'/submissions');
							// myrouter.navigate('/poll_submissions/'+item.attributes.id, { trigger: true });
						}}
					],
	        click: function(model){location.assign('/admin/polls/'+model.attributes.id+'/poll');},
	        // edit: function(model){$.ajax({url: '/polls/'+model.attributes.id, type: 'PUT', data: model.attributes});},
	        // delete: function(model){ $.ajax({url: '/polls/'+model.attributes.id, type: 'DELETE'});}
    		  edit: getEdit('/polls/@{{id}}'),
		      delete: getDelete('/polls/@{{id}}')
				})

				$('#content .btn-success').on('click', function() {
					$().berry({name:'newpoll',legend: '<i class="fa fa-check-square-o"></i> Add poll',fields: [
						{label: 'Group', type: 'select', name:'group_id', options: this.options},
						{label: 'Poll Name', name: 'poll_name'},
						{label: 'Shuffle', name:'shuffle', type: 'checkbox', 'alt-display': 'ui-switch ui-switch-success', 'container': 'i'}
					]}).on('save', function(){
						
							var temp = Berries.newpoll.toJSON();
							temp.group = _.findWhere(this.groups, {id: parseInt(temp.group_id,10)}).name;
							getCreate('/polls/')(bt.add(temp))
							// $.ajax({url: '/polls/', type: 'POST', data: Berries.newpoll.toJSON(), success:function(r){
							// 	r.group = _.findWhere(this.groups, {id: parseInt(r.group_id,10)}).name;
							// 	bt.add(r);
							// }.bind(this)});
							Berries.newpoll.trigger('close');
					},this );
				}.bind(this))

			}
		})



</script>