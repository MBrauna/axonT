@extends('layouts.app')

@section('title','Criar solicitação')

@section('body')
    @isset($data)
        @if($success)
            <alert-axont
                type="success"
                data="Seu chamado de ID {{ $data->idChamado }} foi gerado com sucesso!">
            </alert-axont>
        @else
            <alert-axont
                type="error"
                data="{{ $data->message }}">
            </alert-axont>
        @endif
    @endisset

    <create-task token="{{ csrf_token() }}" bearer="{{ Auth::user()->token }}"></create-task>
@endsection