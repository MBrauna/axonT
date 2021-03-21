<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class ChamadoItem extends Model
    {
        protected $table        =   'chamado_item';
        protected $primaryKey   =   'id_chamado_item';
        protected $fillable     =   ['id_chamado', 'tipo', 'id_questao', 'obrigatorio', 'ordem','questao','resposta','data_alt','usr_alt'];
        protected $hidden       =   [];
        protected $casts        =   [];
        protected $attributes   =   [
            'tipo'          =>  1,
            'obrigatorio'   =>  true,
            'ordem'         =>  999,
        ];

        const CREATED_AT        =   'data_cria';
        const UPDATED_AT        =   'data_alt';
    }
