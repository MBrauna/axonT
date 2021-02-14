@extends('layouts.app')

@section('title','Criar solicitação')

@section('body')
    <create-task token="{{ csrf_token() }}" bearer="{{ Auth::user()->token }}" auth="{{ json_encode(Auth::user()) }}"></create-task>
@endsection