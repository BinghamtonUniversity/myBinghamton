<!DOCTYPE html>
<!--[if lt IE 8]>         <html class="no-js lt-ie8"> <![endif]-->
<!--[if gt IE 8]><!-->
<html><!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Analytics</title>
	<script src="http://code.jquery.com/jquery-2.1.4.min.js" />	
	<script>
		$(document).ready(function(){});
	</script>
	<style> 
		.loading_image {margin:20% 0 0 45%;display:none;}
	</style>
</head>
<body id="page" class="app" data-custom-page="" data-off-canvas-nav="" >
	<form id="myform">
		<p id="update"></p>
		<label for="from">From Date</label>
		<input type="date" name="from" id="from" />
		<label for="to">To Date</label>
		<input type="date" name="to" id="to" />
		<input type="submit" name="submit" value="Generate" />
	</form>
	<img src="assets/img/loading.gif" class="loading_image" />	
	<script>
		$("#myform").submit(function(e) {
			e.preventDefault();
     		console.log("reached");
			$.ajax({
				url: '/analytics_data',
				type: 'get',
				data: $('#myform').serialize(),
				beforeSend: function() {
        			$('.loading_image').css('display','block');
    			},
				success: function(e){
							console.log(e);
							$('.loading_image').css('display','none');
							$("#update").text(e);
						}
				});
		});

		</script>
</body></html>