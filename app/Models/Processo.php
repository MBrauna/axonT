<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class Processo extends Model
    {
        protected $table        =   'processo';
        protected $primaryKey   =   'id_processo';
        protected $fillable     =   ['id_empresa','descricao','sigla','icone','id_usuario_responsavel','situacao','data_alt','usr_alt'];
        protected $hidden       =   [];
        protected $casts        =   [];
        protected $attributes   =   [
            'situacao'      =>  true,
        ];

        const CREATED_AT        =   'data_cria';
        const UPDATED_AT        =   'data_alt';
    }
