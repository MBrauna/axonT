<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class Agendamento extends Model
    {
        protected $table        =   'agendamento';
        protected $primaryKey   =   'id_agendamento';
        protected $fillable     =   ['tipo','id_processo_referencia','id_tipo_processo_origem','id_usuario_origem','id_tipo_processo_destino','id_usuario_destino','periodicidade','qtde_periodicidade','data_inicial','data_final','proximo_agendamento','qtde_criado','url','titulo','tipo_objeto','meio','situacao','data_alt','usr_alt'];
        protected $hidden       =   [];
        protected $casts        =   [];
        protected $attributes   =   [];

        const CREATED_AT        =   'data_cria';
        const UPDATED_AT        =   'data_alt';

    }
