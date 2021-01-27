@extends('layouts.app')

@section('title','Gráficos')

@section('body')
    <filter-app token="{{ csrf_token() }}" bearer="{{ Auth::user()->token }}" auth="{{ json_encode(Auth::user()) }}"></filter-app>
    <performance token="{{ csrf_token() }}" bearer="{{ Auth::user()->token }}" auth="{{ json_encode(Auth::user()) }}"></performance>
@endsection