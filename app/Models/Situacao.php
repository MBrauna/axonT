<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class Situacao extends Model
    {
        protected $table        =   'situacao';
        protected $primaryKey   =   'id_situacao';
        protected $fillable     =   ['id_tipo_processo','descricao','envia_email','envia_mensagem','tarefa_solicitante','marcar_responsavel','limpar_responsavel','data_vencimento','conclusiva','situacao'];
        protected $hidden       =   [];
        protected $casts        =   [];
        protected $attributes   =   [
            'envia_email'           =>  false,
            'envia_mensagem'        =>  false,
            'tarefa_solicitante'    =>  false,
            'marcar_responsavel'    =>  false,
            'limpar_responsavel'    =>  false,
            'data_vencimento'       =>  false,
            'conclusiva'            =>  true,
            'situacao'              =>  true,
        ];

        const CREATED_AT        =   'data_cria';
        const UPDATED_AT        =   'data_alt';
    }
