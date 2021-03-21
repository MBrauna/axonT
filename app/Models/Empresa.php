<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class Empresa extends Model
    {
        protected $table        =   'empresa';
        protected $primaryKey   =   'id_empresa';
        protected $fillable     =   ['descricao','sigla','imagem','id_usuario_responsavel','vencimento_contrato','maximo_arquivos','situacao','data_alt','usr_alt'];
        protected $hidden       =   [];
        protected $casts        =   [];
        protected $attributes   =   [
            'situacao'      =>  true,
        ];

        const CREATED_AT        =   'data_cria';
        const UPDATED_AT        =   'data_alt';

    }
