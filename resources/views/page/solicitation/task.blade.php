@extends('layouts.app')

@section('title','Solicitação de serviço #'.$idTask)

@section('body')
    <id-task token="{{ csrf_token() }}" bearer="{{ Auth::user()->token }}" id="{{ $idTask }}"></filter-app>
@endsection