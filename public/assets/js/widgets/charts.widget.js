$(function(){
	widget_factory.register({
		type: 'Chart',
		label: 'Chart',
		icon: 'fa fa-check-o',
		defaults: {
			labels: "first,second",
			values: "10,9",
			collections: "{}",
			type: "Pie",
			// user_edit: false
		},
		view: {
			template: 'widgets_chart',
			render: function() {

		  	this.data = []
				//for(var i in this.keys){
				//	this.data.push($.extend(true, {}, chartDesigns[this.data.length], {label: this.labels[i], value: parseInt(appModel.attributes[myApp.fieldmap[this.keys[i]].name], 10)}));
				//}
				var labels = this.model.attributes.labels.split(',');
				var values = this.model.attributes.values.split(',');
				var collections = (JSON.parse(this.model.attributes.collections || '{}'));
				if(this.model.attributes.type == "Pie"){

					for(var i in labels){
						this.data.push($.extend(true, {}, chartDesigns[i], {label: labels[i], value: parseInt(values[i] || 0, 10)}));
					}

				}else{
					this.data = {
						labels: [],
						datasets: []
					};

					for(var i in collections[0]){
						this.data.labels.push(month[i]);
					}
					for(var i in collections){
						this.data.datasets.push($.extend(true, {}, chartDesign[i], {label: labels[i], data: collections[i]}));
					}
				}

				// this.data.push($.extend(true, {}, chartDesigns[0], {label: labels[0], value: 1}));
				// this.data.push($.extend(true, {}, chartDesigns[1], {label: labels[1], value: 2}));

				this.$el.find('.chart').html('<canvas class="mChart" height="200"></canvas>');
				var ctx = this.$el.find('.mChart')[0].getContext("2d");

				// var options = {animateRotate : false};
				this.chart = new Chart(ctx)[this.model.attributes.type](this.data, {});
					this.$el.find('.legend').html(this.chart.generateLegend());
				
			},

			initialize: function(){
				this.autoElement();
			//	appModel.on('change', _.debounce($.proxy(this.render, this), 300));
			}
		},
		model: {
			schema:{
				Title: {},
				Type: {type: 'select', choices: ['Pie', 'Line', 'Bar']},
				Labels: {type: 'tags'},
				Values: {type: 'tags',
					"show": {
						"matches": {
							"name": "type",
							"value": "Pie"
						}
					}
				},
				Collections: {type: 'textarea',
					"show": {
						"not_matches": {
							"name": "type",
							"value": "Pie"
						}
					}
				}
			},
			// userEdit: ['Type', 'Labels', 'Values', 'Collections'],
		},
	});
});
var month = [];
month[0] = "January";
month[1] = "February";
month[2] = "March";
month[3] = "April";
month[4] = "May";
month[5] = "June";
month[6] = "July";
month[7] = "August";
month[8] = "September";
month[9] = "October";
month[10] = "November";
month[11] = "December";
		Chart.defaults.global.responsive = true;
		Chart.defaults.global.maintainAspectRatio = true;
chartDesign = [
	{
		fillColor: "rgba(220,220,220,0.2)",
		strokeColor: "rgba(220,220,220,1)",
		pointColor: "rgba(220,220,220,1)",
		pointStrokeColor: "#fff",
		pointHighlightFill: "#fff",
		pointHighlightStroke: "rgba(220,220,220,1)"
	},
	{
		fillColor: "rgba(151,187,205,0.2)",
		strokeColor: "rgba(151,187,205,1)",
		pointColor: "rgba(151,187,205,1)",
		pointStrokeColor: "#fff",
		pointHighlightFill: "#fff",
		pointHighlightStroke: "rgba(151,187,205,1)"
	},
	{
		fillColor: "rgba(151,17,205,0.2)",
		strokeColor: "rgba(151,17,205,1)",
		pointColor: "rgba(151,17,205,1)",
		pointStrokeColor: "#fff",
		pointHighlightFill: "#fff",
		pointHighlightStroke: "rgba(151,17,205,1)"
	},
	{
		fillColor: "rgba(250,187,20,0.2)",
		strokeColor: "rgba(250,187,20,1)",
		pointColor: "rgba(250,187,20,1)",
		pointStrokeColor: "#fff",
		pointHighlightFill: "#fff",
		pointHighlightStroke: "rgba(250,187,20,1)"
	}
];
chartDesigns = [
	{
		color:"#F7464A",
		highlight: "#FF5A5E",
	},
	{
		color: "#46BFBD",
		highlight: "#5AD3D1"
	},
	{
		color: "#FDB45C",
		highlight: "#FFC870"
	},
	{
		color: "#949FB1",
		highlight: "#A8B3C5"
	},
	{
		color: "#4D5360",
		highlight: "#616774"
	}
];

