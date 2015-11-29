@extends('layouts.default')

@section('jumbotron')
	<div class="jumbotron home">
		<div class="container">
			<h1>Wait Times</h1>
			<p>View current wait times at Disneyland Resort Parks as well as historical wait time data</p>
		</div>
	</div>
@stop

@section('content')

@if (!empty($parks))

	<div class="row">
		<div class="col-sm-6 col-sm-offset-3">
			<div class="row">
				@foreach ($parks as $park)
					<div class="col-sm-6">
						<div class="box padded park-thumb">
							<h3><a href="{{ route('park', $park->id) }}">{{ $park->name }}</a></h3>
						</div>
					</div>
				@endforeach
			</div>
		</div>
	</div>
@endif

@stop