@extends('layouts.app')

@section('title','Gráficos')

@section('body')
    <performance token="{{ csrf_token() }}" bearer="{{ Auth::user()->token }}" auth="{{ json_encode(Auth::user()) }}"></performance>
@endsection