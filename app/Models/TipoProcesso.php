<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class TipoProcesso extends Model
    {
        protected $table        =   'tipo_processo';
        protected $primaryKey   =   'id_tipo_processo';
        protected $fillable     =   ['id_processo','id_situacao_inicial','titulo','subtitulo','sla','situacao'];
        protected $hidden       =   [];
        protected $casts        =   [];
        protected $attributes   =   [
            'situacao'      =>  true,
            'sla'           =>  72,
        ];

        const CREATED_AT        =   'data_cria';
        const UPDATED_AT        =   'data_alt';
    }
