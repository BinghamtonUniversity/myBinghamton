<script>

		poll_id = {{$id}} || false;
		var url = '/pollsubmit';

		$.ajax({
			url: url+'/'+poll_id,
			success: function(data){
				render('admin_poll_submission_view');
				$('#content').html(render('admin_poll_submissions_view', {submissions:data}));
			}
		})
</script>