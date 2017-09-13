<script>
		poll_id = {{$id}} || false;
		var url = '/pollresults';

		$.ajax({
			url: url+'/'+poll_id,
			success: function(data){

				data.totals = [];
				for(var i in data.results){
					data.totals.push({name: i, value: data.results[i]});
				}
				$('#content').html(render('admin_poll_graphs_view', data));

				colors = [
					{
							color: "#F7464A",
							highlight: "#FF5A5E",
					},
					{
							color: "#46BFBD",
							highlight: "#5AD3D1",
					},
					{
							color: "#FDB45C",
							highlight: "#FFC870",
					},
					{
							color: "#949FB1",
							highlight: "#A8B3C5",
					},
					{
							highlight: "#616774",
							label: "Dark Grey"
					}
				];

				function labelFormatter(label, series) {
					return "<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
				}
				var data = _.map(data.results, function(num, key){ 
					return {label:key,
									data:num,
								 //  labelColor : 'white',
								 //  labelFontSize : '16',
									// color:colors[count].color,
									// highlight: colors[count++].hightlight,
								}; 
				});
				this.plot = $('#content').find('.graph').plot(data, {
					series: {
							pie: {
									show: true,
									radius: 1,
									label: {
											show: true,
											radius: 2/3,
											formatter: labelFormatter,
											threshold: 0.1
									}
							}
					},
					legend: {
							show: false
					}
				}).data("plot");


			}
		})
</script>