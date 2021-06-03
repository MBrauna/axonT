<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class UsuarioPerfil extends Model 
    {
        protected $table        =   'usuario_perfil';
        protected $primaryKey   =   'id_usuario_perfil';
        protected $fillable     =   ['id_usuario','id_perfil','id_processo','data_alt','usr_alt'];
        protected $hidden       =   [];
        protected $casts        =   [];
        protected $attributes   =   [];

        const CREATED_AT        =   'data_cria';
        const UPDATED_AT        =   'data_alt';
    }
