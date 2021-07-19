@extends('layouts.app')

@section('title','Cadastro de empresas')

@section('body')
    <admin-company token="{{ csrf_token() }}" bearer="{{ Auth::user()->token }}"></admin-company>
@endsection