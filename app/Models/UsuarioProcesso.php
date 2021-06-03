<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class UsuarioProcesso extends Model 
    {
        protected $table        =   'usuario_processo';
        protected $primaryKey   =   'id_usuario_processo';
        protected $fillable     =   ['id_usuario','id_processo','data_alt','usr_alt'];
        protected $hidden       =   [];
        protected $casts        =   [];
        protected $attributes   =   [];

        const CREATED_AT        =   'data_cria';
        const UPDATED_AT        =   'data_alt';
    }
