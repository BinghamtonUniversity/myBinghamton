<script>
$(function() {
	model = {{ $group }}
	model.path = '/'+model.slug+'/';
	$('#content').html(render('group_summary_view', model));

	$('#content').on('click', '#options',function(){
			$().berry({
				legend: 'Group Options',
				attributes: model,
				fields: [
					{required: true, label: 'Name', name: 'name'},
					{label: 'Slug', name: 'slug', required: true, max: 50, type:'text'},
					{label: 'Priority', name:'Priority', type: 'custom_radio', value: '0', options: [ {label: 'Primary', value: '1'}, {label: 'Secondary', value: '0'}]},
					{label: 'Type', name: 'type', type: 'select', value: 'Public', options: [ 'Private', 'Public', 'Closed']},
					{label: 'Community', name: 'community_flag', type: 'custom_radio', options: [{label: 'No', value: 0}, {label: 'Yes', value: 1}]}
				]}).on('save',function() {
				$.extend(model, this.toJSON());
				$.ajax({url: '/groups/'+model.id, type: 'PUT', data: this.toJSON()});
				$('#content').html(render('group_summary_view', model));
				this.trigger('close');
			});
	});


})
</script>