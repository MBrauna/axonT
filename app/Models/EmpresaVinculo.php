<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class EmpresaVinculo extends Model
    {
        protected $table        =   'empresa_vinculo';
        protected $primaryKey   =   'id_empresa_vinculo';
        protected $fillable     =   ['id_empresa','id_empresa_relacao'];
        protected $hidden       =   [];
        protected $casts        =   [];
        protected $attributes   =   [];

        const CREATED_AT        =   'data_cria';
        const UPDATED_AT        =   'data_alt';
    }
