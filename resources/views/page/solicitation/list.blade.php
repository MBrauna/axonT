@extends('layouts.app')

@section('title','Lista')

@section('body')
    <filter-app token="{{ csrf_token() }}" bearer="{{ Auth::user()->token }}"></filter-app>

    <list-manual token="{{ csrf_token() }}" bearer="{{ Auth::user()->token }}"></list-manual>
@endsection