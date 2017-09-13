<script>
	group_id = ({{$id}} == 0)?0:{{$id}} || false;
	var url = '/community_pages';
	var fields;
	$.ajax({
		url: '/group_composites/'+group_id,
		type: 'get',
		success: function(data){
			//dont love it
			composites = data;

			fields = {
				Name: {name: 'name', required: true, max: 50},					
				Slug: {label: 'Slug', name: 'slug', required: true, max: 50},
				'id': {type:'hidden'},
				'group_id': {type:'hidden'},
				'Keep Unlisted': {name: 'unlist', type: 'checkbox', truestate: 1, falsestate: 0},
				'Limit Device': {name: 'device', value_key:'index', value:0, options: ['All', 'Desktop Only', 'Tablet and Desktop', 'Tablet and Phone', 'Phone Only']},
				'Public': {name: 'public', type: 'checkbox', truestate: 1, falsestate: 0, show: {matches:{name:'limit', value: false}}},

				'Limit to Group': {name: 'limit', type: 'checkbox', show:  {matches:{name:'public', value: 0},test: function(form){return composites.length >0;debugger;}} },
				'Groups':{legend: 'Groups', 'show': {
						matches: {
							name: 'limit',
							value: true
						}
					},fields:[
						{label: false, multiple:{duplicate:true}, toArray:true, name: 'group', fields:[
							{label: false, name: 'ids', type: 'select', options: composites}
						]}
					]
				}
			}

			//Load page and initialize
			$.ajax({
				url: url+'?group_id='+group_id,
				success: function(data){
					pages = data;
					render('community_page_view');
					$('#content').html(render('community_pages_view', {models:pages}));


					Sortable.create($('#group-list')[0], {draggable:'li', handle: '.handle', onSort: function () {
						var results = _.map($('#group-list li'), function(item, index){
								return {order: index, key: item.dataset.id }
							})
							$.ajax({
								url: url+'/order',
								type: 'post',
								data: {results: results},
								success: function(){
          				toastr.success('', 'Order Updated');
								},
								error: function(){
          				toastr.error('Failed to re-order', 'ERROR')
								}
							});
			    }})

				}
			})
		}
	});

	var upsert = function (arr, key, newval) {
	    var match = _.findWhere(arr, key);
	    if(match){
	        var index = _.indexOf(arr, match);
	        arr.splice(index, 1, newval);
	    } else {
	        arr.push(newval);
	    }
	};
	function get(id){
		if(typeof id == 'string')id = parseInt(id,10);
		return _.findWhere(pages, {id:id})
	}
	function set(attributes){
      if(attributes.error) {
          if (data.error.message) {
              toastr.error(data.error.message, 'ERROR');
          } else {
              toastr.error(data.error, 'ERROR');
          }
      } else if (typeof attributes != 'object') {
          toastr.error('Action Failed', 'ERROR')
      } else{
          toastr.success('', 'Success');

					if(typeof attributes.id == 'string')attributes.id = parseInt(attributes.id,10);
					if(typeof attributes.unlist == 'string')attributes.unlist = parseInt(attributes.unlist,10);

					upsert(pages, {id:attributes.id}, attributes)

					var old = $('#list_'+attributes.id);
					var newItem = render('community_page_view', attributes);
					if(old.length){
						old.replaceWith(newItem)
					}else{
						$('#group-list').append(newItem)
					}	
      }
	}




	$('#content').on('click', '.btn-success', function() {
			$().berry({legend: '<i class="fa fa-cogs"></i> New Page', fields: fields, attributes: {'group_id': group_id}}).on('save', function(){
				$.ajax({
					url: '/community_pages/'+this.toJSON().group_id+'/',
					type: 'POST',
					data: this.toJSON(),
					success: set,
			    error:function(e){
						toastr.error(e.statusText, 'ERROR');
			    }
				})
				this.trigger('close');
		})
	}.bind(this))

	$('#content').on('click', '.fa-pencil', function(e) {
		e.stopPropagation();
		page = get(e.currentTarget.parentElement.parentElement.dataset.id);

		var tempGroup = page.groups.split(',');
		page.limit = (tempGroup.length >0 && tempGroup[0] !=='' );
		page.group = {ids: tempGroup}

		$().berry({legend: '<i class="fa fa-cogs"></i> Edit Page', fields: fields, attributes: page}).on('save', function() {
			$.ajax({
				url: '/community_pages/'+this.toJSON().group_id+'/'+this.toJSON().id,
				type: 'PUT',
				data: this.toJSON(),
				success: set,
    		error:function(e){
					toastr.error(e.statusText, 'ERROR');
    		}
			})
			this.trigger('close');
		})
	}.bind(this))

	$('#content').on('click', '.fa-times', function(e) {
		e.stopPropagation();
		page = get(e.currentTarget.parentElement.parentElement.dataset.id);
		$.ajax({
			url: '/community_pages/'+page.id,
			type: 'DELETE',
			success: function(page){
				toastr.success('', 'Successfully Removed');
				$('#list_'+page.id).remove();
			},
    	error:function(e){
				toastr.error(e.statusText, 'ERROR');
    	}
		})
	}.bind(this))

</script>