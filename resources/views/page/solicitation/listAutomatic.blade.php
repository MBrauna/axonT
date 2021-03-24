@extends('layouts.app')

@section('title','Troca de objetos')

@section('body')
    <filter-app token="{{ csrf_token() }}" bearer="{{ Auth::user()->token }}"></filter-app>

    <list-automatic token="{{ csrf_token() }}" bearer="{{ Auth::user()->token }}"></list-automatic>
@endsection