<!DOCTYPE html>
<!--[if lt IE 8]>         <html class="no-js lt-ie8"> <![endif]-->
<!--[if gt IE 8]><!-->
<html><!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Analytics Group</title>
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
		<label for="gid">Group ID</label>
		<input type="number" name="gid" id="gid" required />
		<label for="dateFrom">From Date</label>
		<input type="date" name="dateFrom" id="dateFrom" />
		<label for="dateTo">To Date</label>
		<input type="date" name="dateTo" id="dateTo" />
		<label for="type">Generate CSV</label>
		<input type="checkbox" name="type" id="type" value="csv" />
		<input type="submit" name="submit" value="Generate" />
	</form>
	<p id="update"></p>
	<img src="assets/img/loading.gif" class="loading_image" />	
	<script>
		$("#myform").submit(function(e) {
			e.preventDefault();
     		$.ajax({
				url: '/analytics_get_collective_data',
				type: 'get',
				data: $('#myform').serialize(),
				beforeSend: function() {
        			$('.loading_image').css('display','block');
    			},
				success: function(e){
							//console.log(e);
							$('.loading_image').css('display','none');
							$("#update").empty();
							if($("#type").is(":checked")) {
        						$('#update').append($('<a href="output.csv"> Download File </a>'));
        					} else {
        						$.each(e, function(index, element) {
            						$('#update').append($('<div>', {
                						text: JSON.stringify(element)
            						}));
        						});	
        					}
						}
				});
		});

		</script>
</body></html>