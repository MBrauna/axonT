@extends('layouts.app')

@section('title','Criar troca de objetos')

@section('body')
    <form method="POST">
        @csrf
        <button class="btn btn-sm btn-block btn-primary" type="submit">
            <i class="fas fa-reply-all"></i>
            <span>Desfazer seleção</span>
            <i class="fas fa-reply-all"></i>
        </button>
    </form>

    <create-task-automatic
        token="{{ csrf_token() }}"
        bearer="{{ Auth::user()->token }}"
        greatehour="{{ \Carbon\Carbon::now()->toDateString() }}"
    ></create-task-automatic>
@endsection