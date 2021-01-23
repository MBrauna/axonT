<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class Perfil extends Model
    {
        protected $table        =   'perfil';
        protected $primaryKey   =   'id_perfil';
        protected $fillable     =   ['id_empresa','descricao','situacao'];
        protected $hidden       =   [];
        protected $casts        =   [];
        protected $attributes   =   [
            'situacao'      =>  true,
        ];

        const CREATED_AT        =   'data_cria';
        const UPDATED_AT        =   'data_alt';
    }
