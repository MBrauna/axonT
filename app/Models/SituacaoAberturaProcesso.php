<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class SituacaoAberturaProcesso extends Model
    {
        protected $table        =   'situacao_abertura_processo';
        protected $primaryKey   =   'id_situacao_abertura_processo';
        protected $fillable     =   ['id_situacao','id_tipo_processo','ordem','situacao','data_alt','usr_alt'];
        protected $hidden       =   [];
        protected $casts        =   [];
        protected $attributes   =   [
            'situacao'              =>  true,
            'ordem'                 =>  999,
        ];

        const CREATED_AT        =   'data_cria';
        const UPDATED_AT        =   'data_alt';
    }
