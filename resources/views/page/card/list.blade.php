@extends('layouts.app')

@section('title','Lista de cartões para execuções')

@section('body')
    <filter-app token="{{ csrf_token() }}" bearer="{{ Auth::user()->token }}"></filter-app>
    <list-card token="{{ csrf_token() }}" bearer="{{ Auth::user()->token }}"></list-card>
@endsection