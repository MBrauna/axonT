<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class Tarefa extends Model
    {
        protected $table        =   'tarefa';
        protected $primaryKey   =   'id_tarefa';
        protected $fillable     =   ['id_tarefa','id_chamado','conteudo','id_situacao_anterior','id_situacao_atribuida','id_usuario_anterior','id_usuario_atribuido','data_venc_anterior','data_venc_atribuida','data_alt','usr_alt'];
        protected $hidden       =   [];
        protected $casts        =   [];
        protected $attributes   =   [];

        const CREATED_AT        =   'data_cria';
        const UPDATED_AT        =   'data_alt';
    }
