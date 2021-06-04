<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class UsuarioEmpresa extends Model 
    {
        protected $table        =   'usuario_empresa';
        protected $primaryKey   =   'id_usuario_empresa';
        protected $fillable     =   ['id_usuario','id_empresa','data_alt','usr_alt'];
        protected $hidden       =   [];
        protected $casts        =   [];
        protected $attributes   =   [];

        const CREATED_AT        =   'data_cria';
        const UPDATED_AT        =   'data_alt';
    }
