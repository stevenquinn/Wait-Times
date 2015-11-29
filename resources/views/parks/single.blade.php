@extends('layouts.default');

@section('jumbotron')
	<div class="jumbotron">
		<div class="container">
			<h1>{{ $park->name }}</h1>
			<div class="attributes">
				<ul>
					@if (!empty($parkOpen && !empty($parkClose)))
						<li>Today's Hours {{ $parkOpen }} to {{ $parkClose }}</li>
					@endif
				</ul>
			</div>
		</div>
	</div>
@stop

@section('content')


@if (!empty($rides))

	<div class="row">
		<div class="col-md-9">
			<div class="box padded">
				<h3>Rides</h3>
				<div class="table-responsive">
					<table class="table table-striped">
						@foreach ($rides as $ride)
							<tr>
								<td><a href="{{ route('ride', $ride->id) }}">@if (!empty($ride->name)){{ $ride->name }}@else{{ $ride->api_name }}@endif</a></td>
								<td>
									@if ($ride->open())
										<span class="label label-success">Open</span>
									@else
										<span class="label label-danger">Closed</span>
									@endif
								</td>
								<td><span class="badge">{{ $ride->wait() }} min</span></td>
								<td width="250">
									<div class="progress">
										<div class="progress-bar @if ($ride->wait() < 20) progress-bar-success @elseif ($ride->wait() < 45) progress-bar-warning @else progress-bar-danger @endif" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: {{ $ride->wait() / $waitMax * 100 }}%"></div>
									</div>
								</td>
							</tr>
						@endforeach
					</table>
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="box padded">
				<h3 style="text-align: center;">Current Wait Time Distribution</h3>
				<div id="chartdiv" style="width: 100%; height: 400px;"></div>
			</div>
			
			<script src="{{ asset('/assets/js/vendor/amcharts/amcharts.js') }}" type="text/javascript"></script>
	        <script src="{{ asset('/assets/js/vendor/amcharts/pie.js') }}" type="text/javascript"></script>
	
	        <script>
	            var chart;
	            var legend;
	
	            var chartData = [
	                {
	                    "category": "0 - 19 min",
	                    "value": {{ $waitDist->low }},
	                    'color': '#62B321'
	                },
	                {
	                    "category": "20 - 44 min",
	                    "value": {{ $waitDist->med }},
	                    'color': '#FEC239'
	                },
	                {
	                    "category": "45+ min",
	                    "value": {{ $waitDist->high }},
	                    'color': '#CA0813'
	                },
	                {
	                    "category": "Closed",
	                    "value": {{ $waitDist->closed }},
	                    'color': '#000'
	                }
	            ];
	
	            AmCharts.ready(function () {
	                // PIE CHART
	                chart = new AmCharts.AmPieChart();
	                chart.dataProvider = chartData;
	                chart.titleField = "category";
	                chart.valueField = "value";
	                chart.colorField = 'color';
	                chart.labelsEnabled = false;
	                chart.autoMargins = false;
	                chart.marginTop = 0;
	                chart.marginBottom = 0;
	                chart.marginLeft = 0;
	                chart.marginRight = 0;
	
	                // LEGEND
	                legend = new AmCharts.AmLegend();
	                legend.align = "center";
	                legend.markerType = "circle";
	                chart.balloonText = "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>";
	                chart.addLegend(legend);
	
	                // WRITE
	                chart.write("chartdiv");
	            });
	
	            // changes label position (labelRadius)
	            function setLabelPosition() {
	                if (document.getElementById("rb1").checked) {
	                    chart.labelRadius = 30;
	                    chart.labelText = "[[title]]: [[value]]";
	                } else {
	                    chart.labelRadius = -30;
	                    chart.labelText = "[[percents]]%";
	                }
	                chart.validateNow();
	            }	
	           
	        </script>
			
		</div>
	</div>


@endif

@stop