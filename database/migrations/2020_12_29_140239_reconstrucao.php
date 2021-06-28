<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Reconstrucao extends Migration
{
    public function up()
    {
        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->boolean('administrador')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->dateTime('last_login')->nullable();
            $table->text('token')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('empresa', function (Blueprint $table) {
            $table->bigIncrements('id_empresa');
            $table->string('descricao');
            $table->string('sigla');
            $table->text('imagem')->nullable();
            $table->integer('id_usuario_responsavel')->nullable();
            $table->dateTime('vencimento_contrato')->nullable();
            $table->integer('maximo_arquivos')->nullable();
            $table->boolean('situacao')->default(false);
            $table->dateTime('data_cria');
            $table->dateTime('data_alt');
            $table->integer('usr_cria');
            $table->integer('usr_alt');

            $table->index('sigla');
            $table->index('id_usuario_responsavel');
            $table->index('vencimento_contrato');
            $table->index('maximo_arquivos');
            $table->index('situacao');

            $table->unique('descricao');

            $table->foreign('id_usuario_responsavel')->references('id')->on('users');
        });

        Schema::create('empresa_vinculo', function (Blueprint $table) {
            $table->bigIncrements('id_empresa_vinculo');
            $table->integer('id_empresa');
            $table->integer('id_empresa_relacao');
            $table->dateTime('data_cria');
            $table->dateTime('data_alt');
            $table->integer('usr_cria');
            $table->integer('usr_alt');

            $table->index('id_empresa');
            $table->index('id_empresa_relacao');
            $table->unique(['id_empresa','id_empresa_relacao']);

            $table->foreign('id_empresa')->references('id_empresa')->on('empresa');
            $table->foreign('id_empresa_relacao')->references('id_empresa')->on('empresa');
        });
        
        Schema::create('processo', function (Blueprint $table) {
            $table->bigIncrements('id_processo');
            $table->integer('id_empresa');
            $table->text('descricao');
            $table->text('sigla')->nullable();
            $table->text('icone')->nullable();
            $table->integer('id_usuario_responsavel')->nullable();
            $table->boolean('situacao')->default(true);
            $table->dateTime('data_cria');
            $table->dateTime('data_alt');
            $table->integer('usr_cria');
            $table->integer('usr_alt');

            $table->index(['id_empresa']);
            $table->index(['descricao']);
            $table->index(['icone']);
            $table->index(['id_usuario_responsavel']);
            $table->index(['situacao']);

            $table->unique(['id_empresa','sigla']);

            $table->foreign('id_usuario_responsavel')->references('id')->on('users');
            $table->foreign('id_empresa')->references('id_empresa')->on('empresa');
        });

        Schema::create('perfil', function (Blueprint $table) {
            $table->bigIncrements('id_perfil');
            $table->integer('id_empresa');
            $table->text('descricao');
            $table->boolean('situacao')->default(true);
            $table->dateTime('data_cria');
            $table->dateTime('data_alt');
            $table->integer('usr_cria');
            $table->integer('usr_alt');

            $table->index(['id_empresa']);
            $table->index(['descricao']);
            $table->index(['situacao']);

            $table->unique(['id_empresa','descricao']);

            $table->foreign('id_empresa')->references('id_empresa')->on('empresa');
        });

        Schema::create('usuario_empresa', function (Blueprint $table) {
            $table->bigIncrements('id_usuario_empresa');
            $table->integer('id_usuario');
            $table->integer('id_empresa');
            $table->dateTime('data_cria');
            $table->dateTime('data_alt');
            $table->integer('usr_cria');
            $table->integer('usr_alt');

            $table->index(['id_usuario']);
            $table->index(['id_empresa']);

            $table->unique(['id_usuario','id_empresa']);

            $table->foreign('id_usuario')->references('id')->on('users');
            $table->foreign('id_empresa')->references('id_empresa')->on('empresa');
        });

        Schema::create('tipo_configuracao', function (Blueprint $table) {
            $table->bigIncrements('id_tipo_configuracao');
            $table->text('nome');
            $table->boolean('situacao')->default(true);
            $table->dateTime('data_cria');
            $table->dateTime('data_alt');
            $table->integer('usr_cria');
            $table->integer('usr_alt');

            $table->index(['nome']);
            $table->index(['situacao']);
        });


        Schema::create('configuracao', function (Blueprint $table) {
            $table->bigIncrements('id_configuracao');
            $table->text('nome');
            $table->text('titulo')->nullable();
            $table->integer('id_tipo_configuracao')->nullable();
            $table->boolean('situacao')->default(true);
            $table->dateTime('data_cria');
            $table->dateTime('data_alt');
            $table->integer('usr_cria');
            $table->integer('usr_alt');

            $table->index(['nome']);
            $table->index(['situacao']);
        });

        Schema::create('usuario_config', function (Blueprint $table) {
            $table->bigIncrements('id_usuario_config');
            $table->integer('id_usuario');
            $table->integer('id_configuracao');
            $table->dateTime('data_cria');
            $table->dateTime('data_alt');
            $table->integer('usr_cria');
            $table->integer('usr_alt');

            $table->index(['id_usuario']);
            $table->index(['id_configuracao']);
    
            $table->unique(['id_usuario','id_configuracao']);

            $table->foreign('id_usuario')->references('id')->on('users');
            $table->foreign('id_configuracao')->references('id_configuracao')->on('configuracao');
        });


        Schema::create('usuario_perfil', function (Blueprint $table) {
            $table->bigIncrements('id_usuario_perfil');
            $table->integer('id_usuario');
            $table->integer('id_perfil');
            $table->integer('id_superior')->nullable();
            $table->integer('id_processo');
            $table->boolean('situacao')->default(false);
            $table->dateTime('data_cria');
            $table->dateTime('data_alt');
            $table->integer('usr_cria');
            $table->integer('usr_alt');

            $table->index(['id_usuario']);
            $table->index(['id_perfil']);
            $table->index(['id_superior']);
            $table->index(['id_processo']);
            $table->index(['situacao']);
    
            $table->unique(['id_usuario','id_superior','id_perfil','id_processo']);

            $table->foreign('id_usuario')->references('id')->on('users');
            $table->foreign('id_superior')->references('id')->on('users');
            $table->foreign('id_perfil')->references('id_perfil')->on('perfil');
            $table->foreign('id_processo')->references('id_processo')->on('processo');
        });


        Schema::create('situacao', function (Blueprint $table) {
            $table->bigIncrements('id_situacao');
            $table->integer('id_processo');
            $table->text('descricao');
            $table->boolean('envia_email')->default(false);
            $table->boolean('envia_mensagem')->default(false);
            $table->boolean('tarefa_solicitante')->default(false);
            $table->boolean('marcar_responsavel')->default(false);
            $table->boolean('limpar_responsavel')->default(false);
            $table->boolean('data_vencimento')->default(false);
            $table->boolean('conclusiva')->default(true);
            $table->boolean('situacao')->default(true);
            $table->dateTime('data_cria');
            $table->dateTime('data_alt');
            $table->integer('usr_cria');
            $table->integer('usr_alt');

            $table->index(['descricao']);
            $table->index(['id_processo']);
            $table->index(['tarefa_solicitante']);
            $table->index(['marcar_responsavel']);
            $table->index(['limpar_responsavel']);
            $table->index(['data_vencimento']);
            $table->index(['situacao']);
            $table->index(['conclusiva']);
    
            $table->unique(['id_processo','descricao']);

            $table->foreign('id_processo')->references('id_processo')->on('processo');
        });

        Schema::create('tipo_processo', function (Blueprint $table) {
            $table->bigIncrements('id_tipo_processo');
            $table->integer('id_processo');
            $table->integer('id_situacao_inicial')->nullable();
            $table->boolean('automatico')->default(false);
            $table->text('titulo');
            $table->text('subtitulo');
            $table->integer('sla')->default(72);
            $table->boolean('situacao')->default(true);
            $table->dateTime('data_cria');
            $table->dateTime('data_alt');
            $table->integer('usr_cria');
            $table->integer('usr_alt');

            $table->index(['id_processo']);
            $table->index(['id_situacao_inicial']);
            $table->index(['sla']);
            $table->index(['situacao']);
            $table->unique(['id_processo','titulo']);

            $table->foreign('id_situacao_inicial')->references('id_situacao')->on('situacao');
            $table->foreign('id_processo')->references('id_processo')->on('processo');
        });


        Schema::create('questao', function (Blueprint $table) {
            $table->bigIncrements('id_questao');
            $table->integer('id_tipo_processo');
            $table->text('tipo')->default('texto');
            $table->text('titulo');
            $table->text('placeholder')->nullable();
            $table->boolean('obrigatorio')->default(true);
            $table->integer('ordem')->default(999);
            $table->boolean('alt_data_vencimento')->default(true);
            $table->boolean('situacao')->default(true);
            $table->dateTime('data_cria');
            $table->dateTime('data_alt');
            $table->integer('usr_cria');
            $table->integer('usr_alt');

            $table->index(['id_tipo_processo']);
            $table->index(['tipo']);
            $table->index(['ordem']);
            $table->index(['situacao']);
            $table->unique(['id_tipo_processo','titulo']);

            $table->foreign('id_tipo_processo')->references('id_tipo_processo')->on('tipo_processo');
        });

        Schema::create('situacao_abertura_processo', function (Blueprint $table) {
            $table->bigIncrements('id_situacao_abertura_processo');
            $table->integer('id_situacao');
            $table->integer('id_tipo_processo');
            $table->integer('ordem')->default(999);
            $table->boolean('situacao')->default(true);
            $table->dateTime('data_cria');
            $table->dateTime('data_alt');
            $table->integer('usr_cria');
            $table->integer('usr_alt');

            $table->index(['id_situacao']);
            $table->index(['id_tipo_processo']);
            $table->unique(['id_situacao','id_tipo_processo']);

            $table->foreign('id_situacao')->references('id_situacao')->on('situacao');
            $table->foreign('id_tipo_processo')->references('id_tipo_processo')->on('tipo_processo');
        });

        Schema::create('fluxo', function(Blueprint $table){
            $table->bigIncrements('id_fluxo');
            $table->integer('id_tipo_processo');
            $table->integer('id_situacao');
            $table->integer('id_situacao_ant')->nullable();
            $table->dateTime('data_cria');
            $table->dateTime('data_alt');
            $table->integer('usr_cria');
            $table->integer('usr_alt');

            $table->unique(['id_tipo_processo','id_situacao','id_situacao_ant']);
            $table->index(['id_tipo_processo']);
            $table->index(['id_situacao']);
            $table->index(['id_situacao_ant']);

            $table->foreign('id_tipo_processo')->references('id_tipo_processo')->on('tipo_processo');
            $table->foreign('id_situacao')->references('id_situacao')->on('situacao');
            $table->foreign('id_situacao_ant')->references('id_situacao')->on('situacao');
        });

        Schema::create('agendamento', function (Blueprint $table) {
            $table->bigIncrements('id_agendamento');
            $table->integer('tipo')->default(1); // [1] - Entrada, [2] - Saída
            $table->integer('id_processo_referencia')->nullable();
            $table->integer('id_processo_origem');
            $table->integer('id_tipo_processo_origem');
            $table->integer('id_usuario_origem')->nullable();
            $table->integer('id_solicitante')->nullable();
            $table->integer('id_processo_destino')->nullable();
            $table->integer('id_tipo_processo_destino')->nullable();
            $table->integer('id_usuario_destino')->nullable();
            $table->integer('periodicidade')->default(1); // [1] - Dias, [2] - Semanas, [3] - Quinzena, [4] - Meses, [5] - Bimestre, [6] - Semestre, [7] - Anos
            $table->integer('qtde_periodicidade')->default(1);
            $table->dateTime('data_inicial');
            $table->dateTime('data_final')->nullable();
            $table->dateTime('proximo_agendamento');
            $table->integer('qtde_criado')->default(0);
            $table->boolean('aprova_origem')->default(false);
            $table->boolean('aprova_destino')->default(false);
            $table->text('url');
            $table->text('titulo');
            $table->integer('tipo_objeto')->nullable();
            $table->integer('meio')->nullable();
            $table->boolean('situacao')->default(true);
            $table->dateTime('data_cria');
            $table->dateTime('data_alt');
            $table->integer('usr_cria');
            $table->integer('usr_alt');

            $table->index(['tipo']);
            $table->index(['id_processo_referencia']);
            $table->index(['id_processo_origem']);
            $table->index(['id_processo_destino']);
            $table->index(['id_tipo_processo_origem']);
            $table->index(['id_usuario_origem']);
            $table->index(['id_tipo_processo_destino']);
            $table->index(['id_usuario_destino']);
            $table->index(['periodicidade']);
            $table->index(['data_inicial']);
            $table->index(['proximo_agendamento']);
            $table->index(['url']);
            $table->index(['tipo_objeto']);
            $table->index(['meio']);
            $table->index(['situacao']);

            $table->foreign('id_tipo_processo_origem')->references('id_tipo_processo')->on('tipo_processo');
            $table->foreign('id_tipo_processo_destino')->references('id_tipo_processo')->on('tipo_processo');
            $table->foreign('id_processo_referencia')->references('id_processo')->on('processo');
            $table->foreign('id_processo_origem')->references('id_processo')->on('processo');
            $table->foreign('id_processo_destino')->references('id_processo')->on('processo');
            $table->foreign('id_usuario_origem')->references('id')->on('users');
            $table->foreign('id_usuario_destino')->references('id')->on('users');
        });

        Schema::create('agendamento_item', function (Blueprint $table) {
            $table->bigIncrements('id_agendamento_item');
            $table->integer('id_agendamento');
            $table->text('tipo')->default('texto');
            $table->integer('id_questao')->nullable();
            $table->boolean('obrigatorio')->default(true);
            $table->text('questao');
            $table->text('resposta')->nullable();
            $table->text('original')->nullable();
            $table->integer('ordem')->default(999);
            $table->boolean('situacao')->default(true);
            $table->dateTime('data_cria');
            $table->dateTime('data_alt');
            $table->integer('usr_cria');
            $table->integer('usr_alt');

            $table->index(['id_agendamento']);
            $table->index(['tipo']);
            $table->index(['id_questao']);
            $table->index(['ordem']);
            $table->index(['situacao']);
            $table->index(['obrigatorio']);

            $table->foreign('id_agendamento')->references('id_agendamento')->on('agendamento');
            $table->foreign('id_questao')->references('id_questao')->on('questao');
        });

        Schema::create('chamado', function (Blueprint $table) {
            $table->bigIncrements('id_chamado');
            $table->integer('id_empresa');
            $table->integer('id_processo');
            $table->integer('id_tipo_processo');
            $table->integer('id_situacao');
            $table->integer('id_agendamento')->nullable();
            $table->dateTime('data_vencimento')->nullable();
            $table->dateTime('data_conclusao')->nullable();
            $table->integer('id_solicitante');
            $table->integer('id_responsavel')->nullable();
            $table->text('url');
            $table->text('titulo');
            $table->boolean('situacao')->default(true);
            $table->dateTime('data_cria');
            $table->dateTime('data_alt');
            $table->integer('usr_cria');
            $table->integer('usr_alt');

            $table->index(['id_situacao']);
            $table->index(['id_agendamento']);
            $table->index(['id_empresa']);
            $table->index(['id_processo']);
            $table->index(['id_tipo_processo']);
            $table->index(['data_vencimento']);
            $table->index(['data_conclusao']);
            $table->index(['id_solicitante']);
            $table->index(['id_responsavel']);
            $table->index(['url']);
            $table->index(['situacao']);
            $table->index(['data_cria']);
            $table->index(['data_alt']);

            $table->foreign('id_situacao')->references('id_situacao')->on('situacao');
            $table->foreign('id_agendamento')->references('id_agendamento')->on('agendamento');
            $table->foreign('id_empresa')->references('id_empresa')->on('empresa');
            $table->foreign('id_processo')->references('id_processo')->on('processo');
            $table->foreign('id_tipo_processo')->references('id_tipo_processo')->on('tipo_processo');
            $table->foreign('id_solicitante')->references('id')->on('users');
            $table->foreign('id_responsavel')->references('id')->on('users');
        });

        Schema::create('chamado_item', function (Blueprint $table) {
            $table->bigIncrements('id_chamado_item');
            $table->integer('id_chamado');
            $table->text('tipo')->default('texto');
            $table->integer('id_questao')->nullable();
            $table->boolean('obrigatorio')->default(true);
            $table->integer('ordem')->default(999);
            $table->text('questao');
            $table->text('resposta')->nullable();
            $table->text('original')->nullable();
            $table->dateTime('data_cria');
            $table->dateTime('data_alt');
            $table->integer('usr_cria');
            $table->integer('usr_alt');

            $table->index(['id_chamado']);
            $table->index(['tipo']);
            $table->index(['id_questao']);
            $table->index(['obrigatorio']);
            $table->index(['ordem']);
        });

        Schema::create('tarefa', function (Blueprint $table) {
            $table->bigIncrements('id_tarefa');
            $table->integer('id_chamado');
            $table->text('conteudo')->nullable();
            $table->integer('id_situacao_anterior');
            $table->integer('id_situacao_atribuida');
            $table->integer('id_usuario_anterior')->nullable();
            $table->integer('id_usuario_atribuido')->nullable();
            $table->dateTime('data_venc_anterior')->nullable();
            $table->dateTime('data_venc_atribuida')->nullable();
            $table->dateTime('data_cria');
            $table->dateTime('data_alt');
            $table->integer('usr_cria');
            $table->integer('usr_alt');

            $table->index(['id_chamado']);
            $table->index(['id_situacao_anterior']);
            $table->index(['id_situacao_atribuida']);
            $table->index(['id_usuario_anterior']);
            $table->index(['id_usuario_atribuido']);
            $table->index(['data_venc_anterior']);
            $table->index(['data_venc_atribuida']);
            $table->index(['data_cria']);
        });

        Schema::create('arquivo', function (Blueprint $table) {
            $table->bigIncrements('id_arquivo');
            $table->integer('id_usuario');
            $table->integer('id_usuario_alvo')->nullable();
            $table->integer('id_chamado')->nullable();
            $table->integer('id_agendamento')->nullable();
            $table->integer('id_tarefa')->nullable();
            $table->text('nome_servidor')->nullable();
            $table->text('nome_arquivo');
            $table->text('extensao')->nullable();
            $table->text('mime')->nullable();
            $table->double('tamanho',12,2)->nullable();
            $table->dateTime('data_cria');
            $table->dateTime('data_alt');
            $table->integer('usr_cria');
            $table->integer('usr_alt');

            $table->index(['id_usuario']);
            $table->index(['id_usuario_alvo']);
            $table->index(['id_chamado']);
            $table->index(['id_agendamento']);
            $table->index(['id_tarefa']);
            $table->index(['nome_arquivo']);
            $table->index(['extensao']);
            $table->index(['tamanho']);

            $table->foreign('id_usuario')->references('id')->on('users');
            $table->foreign('id_usuario_alvo')->references('id')->on('users');
            $table->foreign('id_chamado')->references('id_chamado')->on('chamado');
            $table->foreign('id_agendamento')->references('id_agendamento')->on('agendamento');
            $table->foreign('id_tarefa')->references('id_tarefa')->on('tarefa');
        });
    }

    public function down()
    {
        Schema::dropIfExists('password_resets');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('users');
        Schema::dropIfExists('empresa');
        Schema::dropIfExists('empresa_vinculo');
        Schema::dropIfExists('processo');
        Schema::dropIfExists('situacao');
        Schema::dropIfExists('perfil');
        Schema::dropIfExists('usuario_config');
        Schema::dropIfExists('tipo_processo');
        Schema::dropIfExists('questao');
        Schema::dropIfExists('situacao_abertura_processo');
        Schema::dropIfExists('fluxo');
        Schema::dropIfExists('agendamento');
        Schema::dropIfExists('agendamento_item');
        Schema::dropIfExists('chamado');
        Schema::dropIfExists('chamado_item');
        Schema::dropIfExists('tarefa');
        Schema::dropIfExists('arquivo');
    }
}
