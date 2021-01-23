<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class UsuarioConfig extends Model
    {
        protected $table        =   'usuario_config';
        protected $primaryKey   =   'id_usuario_config';
        protected $fillable     =   ['id_usuario','id_processo','id_perfil'];
        protected $hidden       =   [];
        protected $casts        =   [];
        protected $attributes   =   [];

        const CREATED_AT        =   'data_cria';
        const UPDATED_AT        =   'data_alt';
    }
