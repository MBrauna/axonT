@extends('layouts.app')

@section('title','Gráficos')

@section('body')
    <filter-app token="{{ csrf_token() }}" bearer="{{ Auth::user()->token }}"></filter-app>
    <performance token="{{ csrf_token() }}" bearer="{{ Auth::user()->token }}"></performance>
@endsection