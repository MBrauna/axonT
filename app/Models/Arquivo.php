<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class Arquivo extends Model
    {
        protected $table        =   'arquivo';
        protected $primaryKey   =   'id_arquivo';
        protected $fillable     =   ['id_usuario','id_usuario_alvo','id_chamado','id_tarefa','nome_servidor','nome_arquivo','extensao','mime','tamanho','data_alt','usr_alt'];
        protected $hidden       =   [];
        protected $casts        =   [];
        protected $attributes   =   [];

        const CREATED_AT        =   'data_cria';
        const UPDATED_AT        =   'data_alt';
    }
