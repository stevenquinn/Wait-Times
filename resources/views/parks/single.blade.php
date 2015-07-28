@extends('layouts.default');
@section('content')

<p><a href="{{ route('home') }}">Home</a></p>

<h1>{{ $park->name }}</h1>
<h3>Today's Hours</h3>
<p>{{ $parkOpen }} to {{ $parkClose }}</p>

@if (!empty($rides))

<ul class="list-group">
	@foreach ($rides as $ride)
		<li class="list-group-item row">
			<a href="{{ route('ride', $ride->id) }}">
				<div class="col-sm-8">
					{{ $ride->api_name }}
				</div>
				<div class="col-sm-2">
					@if ($ride->open())
						<span class="label label-success">Open</span>
					@else
						<span class="label label-danger">Closed</span>
					@endif
				</div>
				<div class="col-sm-2">
					<span class="badge">{{ $ride->wait() }} min</span>
				</div>
			</a>
		</li>
	@endforeach
</ul>

@endif

@stop