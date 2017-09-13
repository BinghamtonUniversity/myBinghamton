<script>

	poll_id = {{$id}} || false;
	var url = '/polls';
	var fields;
	var poll;

	//Load page and initialize
	$.ajax({
		url: url+'/'+poll_id,
		success: function(data){
			poll = data;
			poll.content = JSON.parse(poll.content || '[]');
			// poll = data[0];
			render('admin_edit_poll_item_view');
			$('#content').html(render('admin_edit_poll_view', poll));
			Sortable.create($('#group-list')[0], {draggable:'li', handle: '.handle', onSort: save})
		}
	})


	fields = {
		'Choice': {name:'label'}
	}

	function save(){
		var results = _.map($('#group-list li'), function(item, index){
			return {label: item.innerText }
		})
		poll.content = JSON.stringify(results);


			$.ajax({
				url: url+'/'+poll_id,
				type: 'PUT',
				data: poll,
				// success: set
			})

		poll.content = JSON.parse(poll.content);
	}

	$('#content').on('click', '.btn-success', function() {
			$().berry({legend: '<i class="fa fa-cogs"></i> New Poll Item', fields: fields}).on('save', function(){
				$('#group-list').append(render('admin_edit_poll_item_view', this.toJSON()))
				save();
				this.trigger('close')
		})
	})


	$('#content').on('click', '.fa-times', function(e) {
		e.stopPropagation();
		$('[data-label="'+e.currentTarget.parentElement.parentElement.dataset.label+'"]').remove();
		save();
	})


</script>