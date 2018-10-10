<p>
	<span>Panel:</span>
	<select id="serialSelect">
		<?php
			$panels = \App\Models\Panel::all();
			foreach($panels as $panel) {
				echo "<option>" . $panel->serial . "</option>";
			}
		?>
	</select>
</p>	

<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

@section('scripts')

	<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
	<script src="https://code.highcharts.com/highcharts.js"></script>
	<script src="https://code.highcharts.com/modules/data.js"></script>
	<script src="https://code.highcharts.com/modules/series-label.js"></script>
	<script src="https://code.highcharts.com/modules/exporting.js"></script>
	<script src="https://code.highcharts.com/modules/export-data.js"></script>

	<!-- Additional files for the Highslide popup effect -->
	<script src="https://www.highcharts.com/media/com_demo/js/highslide-full.min.js"></script>
	<script src="https://www.highcharts.com/media/com_demo/js/highslide.config.js" charset="utf-8"></script>
	<link rel="stylesheet" type="text/css" href="https://www.highcharts.com/media/com_demo/css/highslide.css" />

	<script>

		$('#serialSelect').on('change', function() {
		  	loadChart(this.value);
		});

		loadChart($('#serialSelect').val())

		function loadChart(panelId) {

			var processed_json_for_sum = new Array();
			var processed_json_for_min = new Array();
			var processed_json_for_max = new Array();
			var processed_json_for_average = new Array();

	        $.getJSON('/api/one_day_electricities?panel_serial=' + panelId, function(data) {
	                for (datum in data){
	            		var datumObj = data[datum];
	                    processed_json_for_sum.push([datumObj.day, datumObj.sum]);
	                    processed_json_for_min.push([datumObj.day, datumObj.min]);
	                    processed_json_for_max.push([datumObj.day, datumObj.max]);
	                    processed_json_for_average.push([datumObj.day, datumObj.average]);
	                }
	        	}).done(function() {
	        		options.series[0].data = processed_json_for_sum;
	        		options.series[1].data = processed_json_for_min;
	        		options.series[1].data = processed_json_for_max;
	        		options.series[1].data = processed_json_for_average;
	        		var chart = new Highcharts.Chart('container', options);
			    });

		    var options = {

			    chart: {
			        scrollablePlotArea: {
			            minWidth: 700
			        }
			    },

			    title: {
			        text: 'Daily electricity consumption'
			    },

			    xAxis: {
			        title: {
			            text: "Days"
			        },
			        tickInterval: 7 * 24 * 3600 * 1000, // one week
			        tickWidth: 0,
			        gridLineWidth: 1,
			        labels: {
			            align: 'left',
			            x: 3,
			            y: -3
			        }
			    },

			    yAxis: [{ // left y axis
			        title: {
			            text: "Kilowatt Hour"
			        },
			        labels: {
			            align: 'left',
			            x: 3,
			            y: 16,
			            format: '{value:.,0f}'
			        },
			        showFirstLabel: false
			    }, { // right y axis
			        linkedTo: 0,
			        gridLineWidth: 0,
			        opposite: true,
			        title: {
			            text: null
			        },
			        labels: {
			            align: 'right',
			            x: -3,
			            y: 16,
			            format: '{value:.,0f}'
			        },
			        showFirstLabel: false
			    }],

			    tooltip: {
			        shared: true,
			        crosshairs: true
			    },

			    series: [{
			        name: 'Sum',
			        lineWidth: 4,
			        data: processed_json_for_sum,
			        marker: {
			            radius: 4
			        }
			    }, {
			        name: 'Min',
			        lineWidth: 4,
			        data: processed_json_for_min,
			        marker: {
			            radius: 4
			        }
			    }, {
			        name: 'Max',
			        lineWidth: 4,
			        data: processed_json_for_max,
			        marker: {
			            radius: 4
			        }
			    }, {
			        name: 'Average',
			        lineWidth: 4,
			        data: processed_json_for_average,
			        marker: {
			            radius: 4
			        }
			    }]
			};
		}
	</script>

@endsection