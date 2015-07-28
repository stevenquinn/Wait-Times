@extends('layouts.default')
@section('content')

<h1>{{ $ride->api_name }}</h1>
<p>Park: <a href="{{ route('park', $ride->park->id) }}">{{ $ride->park->name }}</a></p>
<p>Current Wait Time: {{ $ride->wait() }} min</p>

@if (!empty($ride->avgWait()))
	<p>Average Wait Today: {{ $ride->avgWait() }} min</p>
@endif

@stop