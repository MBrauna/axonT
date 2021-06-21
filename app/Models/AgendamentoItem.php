<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class AgendamentoItem extends Model
    {
        protected $table        =   'agendamento_item';
        protected $primaryKey   =   'id_agendamento_item';
        protected $fillable     =   ['id_agendamento','tipo','id_questao','obrigatorio','questao','resposta','original','ordem','situacao','data_alt','usr_alt'];
        protected $hidden       =   [];
        protected $casts        =   [];
        protected $attributes   =   [
            'tipo'          =>  1,
            'obrigatorio'   =>  true,
            'ordem'         =>  999,
            'situacao'      =>  true,
        ];

        const CREATED_AT        =   'data_cria';
        const UPDATED_AT        =   'data_alt';
    }