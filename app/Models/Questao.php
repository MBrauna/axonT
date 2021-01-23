<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class Questao extends Model
    {
        protected $table        =   'questao';
        protected $primaryKey   =   'id_questao';
        protected $fillable     =   ['id_tipo_processo','tipo','titulo','placeholder','obrigatorio','ordem','situacao'];
        protected $hidden       =   [];
        protected $casts        =   [];
        protected $attributes   =   [
            'situacao'      =>  true,
            'tipo'          =>  1,
            'obrigatorio'   =>  true,
            'ordem'         =>  999,
        ];

        const CREATED_AT        =   'data_cria';
        const UPDATED_AT        =   'data_alt';
    }
