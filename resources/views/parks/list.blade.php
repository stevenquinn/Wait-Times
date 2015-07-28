@extends('layouts.default')
@section('content')

@if (!empty($parks))
	<div class="row">
		<div class="col-sm-6 col-sm-offset-3">
			<div class="row">
				@foreach ($parks as $park)
					<div class="col-sm-6">
						<div class="panel panel-default">
							<div class="panel-body">
								<a href="{{ route('park', $park->id) }}">{{ $park->name }}</a>
							</div>
						</div>
					</div>
				@endforeach
			</div>
		</div>
	</div>
@endif

@stop