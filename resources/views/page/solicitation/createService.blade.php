@extends('layouts.app')

@section('title','Criar solicitação de serviço')

@section('body')
    <form method="GET">
        <button class="btn btn-sm btn-block btn-primary" type="submit">
            <i class="fas fa-reply-all"></i>
            <span>Desfazer seleção</span>
            <i class="fas fa-reply-all"></i>
        </button>
    </form>

    <create-task-manual
        token="{{ csrf_token() }}"
        bearer="{{ Auth::user()->token }}"
    ></create-task-manual>
@endsection