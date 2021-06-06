<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class Configuracao extends Model
    {
        protected $table        =   'configuracao';
        protected $primaryKey   =   'id_configuracao';
        protected $fillable     =   ['nome', 'titulo', 'id_tipo_configuracao','situacao','data_alt','usr_alt'];
        protected $hidden       =   [];
        protected $casts        =   [];
        protected $attributes   =   [
            'situacao'   =>  true,
        ];

        const CREATED_AT        =   'data_cria';
        const UPDATED_AT        =   'data_alt';
    }
