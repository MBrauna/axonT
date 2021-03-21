<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class Fluxo extends Model
    {
        protected $table        =   'fluxo';
        protected $primaryKey   =   'id_fluxo';
        protected $fillable     =   ['id_tipo_processo','id_situacao','id_situacao_ant','data_cria','data_alt','usr_cria','usr_alt','data_alt','usr_alt'];
        protected $hidden       =   [];
        protected $casts        =   [];
        protected $attributes   =   [];

        const CREATED_AT        =   'data_cria';
        const UPDATED_AT        =   'data_alt';
    }
