<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class Chamado extends Model
    {
        protected $table        =   'chamado';
        protected $primaryKey   =   'id_chamado';
        protected $fillable     =   ['id_empresa', 'id_processo', 'id_tipo_processo', 'id_situacao', 'id_agendamento','data_vencimento','data_conclusao', 'id_solicitante', 'id_responsavel','url','titulo', 'situacao'];
        protected $hidden       =   [];
        protected $casts        =   [];
        protected $attributes   =   ['situacao'   =>  true,];

        const CREATED_AT        =   'data_cria';
        const UPDATED_AT        =   'data_alt';


    }
