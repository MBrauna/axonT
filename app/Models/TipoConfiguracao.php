<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class TipoConfiguracao extends Model
    {
        protected $table        =   'tipo_configuracao';
        protected $primaryKey   =   'id_tipo_configuracao';
        protected $fillable     =   ['nome', 'situacao','data_alt','usr_alt'];
        protected $hidden       =   [];
        protected $casts        =   [];
        protected $attributes   =   [
            'situacao'   =>  true,
        ];

        const CREATED_AT        =   'data_cria';
        const UPDATED_AT        =   'data_alt';
    }
