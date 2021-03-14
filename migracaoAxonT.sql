-- Users -  Migração para usuários
insert into public.users
select id,
		name,
		email,
		administrador,
		email_verified_at,
		password,
		null,
		remember_token,
		created_at,
		updated_at
  from backup.users;


-- Empresa - Migração para a empresa
insert into public.empresa(id_empresa,
        descricao,
        sigla,
        situacao,
        data_cria,
        data_alt,
        usr_cria,
        usr_alt)
select id_empresa,
        descricao,
        sigla,
        situacao,
        data_cria,
        data_alt,
        usr_cria,
        usr_alt
from backup.empresa;

insert into public.processo
select * from backup.processo;

insert into public.situacao
select id_situacao,
		id_processo,
		descricao,
		true,
		true,
		tarefa_solicitante,
		marca_responsavel,
		limpar_responsavel,
		alterar_data_vencimento,
		conclusiva,
		situacao,
		data_cria,
		data_alt,
		usr_cria,
		usr_alt
  from backup.situacao;


insert into public.tipo_processo(id_tipo_processo, id_processo, id_situacao_inicial, titulo, subtitulo, sla, situacao, data_cria, data_alt, usr_cria, usr_alt, automatico)
select id_tipo_processo,
		id_processo,
		id_situacao,
		descricao,
		questao,
		sla,
		true,
		data_cria,
		data_alt,
		usr_cria,
		usr_alt,
		automatico
  from backup.tipo_processo
 where situacao = true
 order by id_tipo_processo;



insert into public.chamado
select id_chamado,
	   id_empresa,
	   id_processo,
	   id_tipo_processo,
	   id_situacao,
	   null,
	   data_vencimento,
	   data_conclusao,
	   id_solicitante,
	   id_responsavel,
	   url,
	   titulo,
	   true,
	   data_cria,
	   data_alt,
	   usr_cria,
	   usr_alt
  from backup.chamado;


insert into public.perfil(id_perfil, id_empresa, descricao, situacao, data_cria, data_alt, usr_cria, usr_alt)
select distinct
	   bp.id_perfil
	  ,vpro.id_empresa
	  ,trim(bp.descricao)
	  ,true
	  ,bp.data_cria
	  ,bp.data_alt
	  ,bp.usr_cria
	  ,bp.usr_alt
  from backup.perfil bp
  	   inner join backup.perfil_acesso bpa
	   	       on bpa.id_perfil = bp.id_perfil
	   inner join backup.processo vpro
	   	       on vpro.id_processo = bpa.id_processo
 where bp.situacao = true
    and vpro.situacao = true
	and bpa.id_perfil_acesso = (select min(pera.id_perfil_acesso)
							      from backup.perfil_acesso pera
							     where pera.id_perfil = bp.id_perfil
							   );


insert into public.usuario_config(id_usuario, id_processo, id_superior, id_perfil, data_cria, data_alt, usr_cria, usr_alt)
select distinct bpu.id_usuario,
 		bpa.id_processo,
		bpu.id_superior,
		bp.id_perfil,
		current_timestamp,
		current_timestamp,
		1,
		1
  from backup.perfil                    bp
  	   inner join backup.perfil_acesso   bpa
	           on bpa.id_perfil          = bp.id_perfil
	   inner join backup.perfil_usuario  bpu
	           on bp.id_perfil 			 = bp.id_perfil
where bp.situacao = true
	and bpa.id_perfil_acesso = (select min(pera.id_perfil_acesso)
							      from backup.perfil_acesso pera
							     where pera.id_perfil = bp.id_perfil
							   )
 order by bpu.id_usuario, bpa.id_processo, bp.id_perfil, bpu.id_superior;



insert into public.questao(id_questao, id_tipo_processo, tipo, titulo, placeholder, obrigatorio, ordem, situacao, alt_data_vencimento, data_cria, data_alt, usr_cria, usr_alt)
select id_pergunta_tipo,
		id_tipo_processo,
		tipo,
		descricao,
		descricao,
		true,
		ordem,
		situacao,
		alt_data_vencimento,
		data_cria,
		data_alt,
		usr_cria,
		usr_alt
  from backup.pergunta_tipo
 order by id_pergunta_tipo asc;