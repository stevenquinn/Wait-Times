@extends('layouts.default')
@section('jumbotron')
	<div class="jumbotron">
		<div class="container">
			<div class="row">
				<div class="col-md-8">
					<h1>@if (!empty($ride->name)){{ $ride->name }}@else{{ $ride->api_name }}@endif</h1>
					<h4 class="park"><a href="{{ route('park', $ride->park->id) }}">{{ $ride->park->name }}</a></h4>
					
					<div class="attributes">
						<ul>
							<li>
								Current Wait Time 
								@if (!empty($ride->wait()))
									<span class="label @if ($ride->wait() < 20) label-success @elseif ($ride->wait() < 45) label-warning @else label-danger @endif">
										{{ $ride->wait() }} min
									</span>
								@else
									<span class="label label-default">Closed</span>
								@endif
							</li>
							@if (!empty($ride->avgWait))
								<li>
									Average Wait Today 
									<span class="label @if ($ride->avgWait() < 20) label-success @elseif ($ride->avgWait() < 45) label-warning @else label-danger @endif">
										{{ $ride->avgWait() }} min
									</span>
								</li>
							@endif
						</ul>
					</div>
				</div>
				<div class="col-md-4">
					<div id="gauge_chart" style="width:200px; height:200px;"></div>
				</div>
			</div>
		</div>
	</div>
@stop

@section('content')

<div class="ride-single">
	
	<div class="row">
		@if (!empty($waittimes->daysOfWeek))
			<div class="col-md-5">
				<div class="box padded">
					<h3>Avg. Wait by Day Of Week (min)</h3>
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Su</th>
									<th>M</th>
									<th>T</th>
									<th>W</th>
									<th>Th</th>
									<th>F</th>
									<th>S</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									@foreach ($waittimes->daysOfWeek as $day)
										<td>{{ number_format($day) }}</td>
									@endforeach
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		@endif
		
		@if (!empty($waittimes->months))
			<div class="col-md-7">
				<div class="box padded">
					<h3>Avg. Wait By Month (min)</h3>
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									@foreach ($waittimes->months as $key => $value)
										<th>{{ $key }}</th>
									@endforeach
								</tr>
							</thead>
							<tbody>
								<tr>
									@foreach ($waittimes->months as $key => $value)
										<td>{{ number_format($value) }}</td>
									@endforeach
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		@endif
	</div>
	
	@if (!empty($waittimes->hours))
		<div class="box padded">
			<h3>Avg. Wait by Hour of Day</h3>
			<div class="row">
				<div class="col-md-12">
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									@foreach ($waittimes->hours as $key => $value)
										<th>@if ($key > 12) {{ $key - 12 }}pm @else {{$key}}am @endif</th>
									@endforeach
								</tr>
							</thead>
							<tbody>
								<tr>
									@foreach ($waittimes->hours as $key => $value)
										<td>{{ number_format($value->wait) }}</td>
									@endforeach
								</tr>
							</tbody>
						</table>
					</div>		
				</div>
			</div>
		</div>
	@endif
	
	
	
	<div class="box padded">
		<h3>All Data</h3>
		<div id="chartdiv" style="width: 100%; height: 400px;"></div>
	</div>
	
	<script src="{{ asset('/assets/js/vendor/amcharts/amcharts.js') }}"></script>
	<script src="{{ asset('/assets/js/vendor/amcharts/serial.js') }}"></script>
	<script src="{{ asset('/assets/js/vendor/amcharts/gauge.js') }}"></script>
	
	<script>
	    var chart;
	    var chartData = [];
	    var chartCursor;
	
	
	
	    AmCharts.ready(function () {
	        // generate some data first
	        generateChartData();
	
	        // SERIAL CHART
	        chart = new AmCharts.AmSerialChart();
	
	        chart.dataProvider = chartData;
	        chart.categoryField = "date";
	        chart.balloon.bulletSize = 5;
	        chart.dataDateFormat = "YYYY-MM-DD JJ:NN:SS";
	
	        // listen for "dataUpdated" event (fired when chart is rendered) and call zoomChart method when it happens
	        chart.addListener("dataUpdated", zoomChart);
	
	        // AXES
	        // category
	        var categoryAxis = chart.categoryAxis;
	        categoryAxis.parseDates = true; // as our data is date-based, we set parseDates to true
	        categoryAxis.minPeriod = "mm"; // our data is daily, so we set minPeriod to DD
	        categoryAxis.dashLength = 1;
	        categoryAxis.minorGridEnabled = true;
	        categoryAxis.twoLineMode = true;
	        categoryAxis.dateFormats = [{
	            period: 'fff',
	            format: 'JJ:NN:SS'
	        }, {
	            period: 'ss',
	            format: 'JJ:NN:SS'
	        }, {
	            period: 'mm',
	            format: 'JJ:NN'
	        }, {
	            period: 'hh',
	            format: 'JJ:NN'
	        }, {
	            period: 'DD',
	            format: 'DD'
	        }, {
	            period: 'WW',
	            format: 'DD'
	        }, {
	            period: 'MM',
	            format: 'MMM'
	        }, {
	            period: 'YYYY',
	            format: 'YYYY'
	        }];
	
	        categoryAxis.axisColor = "#DADADA";
	
	        // value
	        var valueAxis = new AmCharts.ValueAxis();
	        valueAxis.axisAlpha = 0;
	        valueAxis.dashLength = 1;
	        chart.addValueAxis(valueAxis);
	
	        // GRAPH
	        var graph = new AmCharts.AmGraph();
	        graph.id ="g1";
	        graph.title = "red line";
	        graph.valueField = "wait";
	        graph.bullet = "round";
	        graph.bulletBorderColor = "#FFFFFF";
	        graph.bulletBorderThickness = 2;
	        graph.bulletBorderAlpha = 1;
	        graph.lineThickness = 2;
	        graph.fillColorsField = "lineColor";
	        graph.fillAlphas = 0.3;
	        graph.lineColorField = "lineColor";
	        graph.hideBulletsCount = 50; // this makes the chart to hide bullets when there are more than 50 series in selection
	        chart.addGraph(graph);
	
	        // CURSOR
	        chartCursor = new AmCharts.ChartCursor();
	        chartCursor.cursorPosition = "mouse";
	        chartCursor.pan = true; // set it to fals if you want the cursor to work in "select" mode
	        chart.addChartCursor(chartCursor);
	
	        // SCROLLBAR
	        var chartScrollbar = new AmCharts.ChartScrollbar();
	        chartScrollbar.graph = "g1";
	        chartScrollbar.autoGridCount = true;
	        chart.addChartScrollbar(chartScrollbar);
	
	        chart.creditsPosition = "bottom-right";
	
	        // WRITE
	        chart.write("chartdiv");
	    });
	
	    // generate some random data, quite different range
	    function generateChartData() {
		    
		    @foreach ($waittimes->all as $time)
			    chartData.push({
	                date: "{{ Carbon::createFromFormat('Y-m-d H:i:s', $time->created_at)->format('Y-m-d H:i:s') }}",
	                wait: {{ $time->wait }},
	                lineColor: '@if ($time->wait == 0) #000000 @elseif ($time->wait < 20) #62B321 @elseif ($time->wait < 45) #FEC239  @else #CA0813 @endif'
	            });
		    @endforeach
		    
	    }
	
	    // this method is called when chart is first inited as we listen for "dataUpdated" event
	    function zoomChart() {
	        // different zoom methods can be used - zoomToIndexes, zoomToDates, zoomToCategoryValues
	        chart.zoomToIndexes(chartData.length - 200, chartData.length - 1);
	    }
	
	    // changes cursor mode from pan to select
	    function setPanSelect() {
	        if (document.getElementById("rb1").checked) {
	            chartCursor.pan = false;
	            chartCursor.zoomable = true;
	        } else {
	            chartCursor.pan = true;
	        }
	        chart.validateNow();
	    }
	
	</script>
	
	<script>
        var chart;
        var arrow;
        var axis;

        AmCharts.ready(function () {
            // create angular gauge
            chart = new AmCharts.AmAngularGauge();
            chart.addTitle("Current Wait");

            // create axis
            axis = new AmCharts.GaugeAxis();
            axis.startValue = 0;
			axis.axisThickness = 1;
            axis.valueInterval = 10;
            axis.endValue = 120;
            // color bands
            var band1 = new AmCharts.GaugeBand();
            band1.startValue = 0;
            band1.endValue = 19;
            band1.color = "#00CC00";

            var band2 = new AmCharts.GaugeBand();
            band2.startValue = 21;
            band2.endValue = 44;
            band2.color = "#ffac29";

            var band3 = new AmCharts.GaugeBand();
            band3.startValue = 45;
            band3.endValue = 120;
            band3.color = "#ea3838";
            band3.innerRadius = "95%";

            axis.bands = [band1, band2, band3];

            // bottom text
            axis.bottomTextYOffset = -20;
            axis.setBottomText("0 km/h");
            chart.addAxis(axis);

            // gauge arrow
            arrow = new AmCharts.GaugeArrow();
            chart.addArrow(arrow);

            chart.write("gauge_chart");
            arrow.setValue('{{ $ride->wait() }}');
            axis.setBottomText('{{ $ride->wait() }} min');
        });


    </script>
	
	
	
</div>



@stop