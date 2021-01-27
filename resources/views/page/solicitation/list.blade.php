@extends('layouts.app')

@section('title','Lista')

@section('body')
    <filter-app token="{{ csrf_token() }}" bearer="{{ Auth::user()->token }}" auth="{{ json_encode(Auth::user()) }}"></filter-app>
@endsection