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
  from backup.situacao bs
where not exists (select null from backup.situacao bss where bss.id_processo = bs.id_processo and bss.descricao = bs.descricao and bss.id_situacao != bs.id_situacao);


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


insert into public.usuario_perfil(id_usuario, id_processo,id_superior, id_perfil, data_cria, data_alt, usr_cria, usr_alt)
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
	and 0 < (select count(1) from perfil where perfil.id_perfil = bp.id_perfil)
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


 insert into public.agendamento(id_agendamento, tipo, id_processo_referencia, id_processo_origem, id_tipo_processo_origem, id_usuario_origem,
								id_solicitante, id_processo_destino, id_tipo_processo_destino, id_usuario_destino, 
								periodicidade, qtde_periodicidade, data_inicial, data_final, proximo_agendamento, qtde_criado, aprova_origem, 
								aprova_destino, url, titulo, tipo_objeto, meio, situacao, data_cria, data_alt, usr_cria, usr_alt
							)
 select id_entrada_solicitacao,
		tipo,
		coalesce(id_processo_referencia,1),
		id_processo_origem,
		id_tipo_processo_origem,
		id_responsavel_origem,
		id_solicitante,
		id_processo_destino,
		id_tipo_processo_destino,
		id_responsavel_destino,
		periodicidade,
		coalesce(qtde_chamado,9999),
		data_criacao,
		null,
		data_proximo_agendamento,
		coalesce(qtde_chamado,0),
		sla_cliente,
		sla_fornecedor,
		url,
		titulo,
		tipo_objeto,
		meio,
		situacao,
		data_cria,
		data_alt,
		usr_cria,
		usr_alt
   from backup.entrada_solicitacao;



insert into usuario_perfil(id_usuario, id_perfil,id_processo, data_cria, data_alt, usr_cria, usr_alt, id_superior, situacao)
select distinct
		pu.id_usuario, ua.id_perfil, p.id_processo,
		current_timestamp,
		current_timestamp,1,1,
		pu.id_superior, true
   from backup.perfil_acesso ua
        inner join backup.perfil_usuario pu on pu.id_perfil = ua.id_perfil
   	    inner join backup.processo p on p.id_processo = ua.id_processo
		inner join perfil pp on pp.id_perfil = ua.id_perfil
 where pp.situacao = true;

insert into configuracao values(1,'SS','Solicitação de serviço',1,TRUE,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1,1);
insert into configuracao values(2,'OBJ','Troca de objetos',1,TRUE,CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1,1);


insert into usuario_config(id_usuario, id_configuracao, data_cria, data_alt, usr_cria, usr_alt)
SELECT ID,1,current_timestamp, current_timestamp, 1,1
  FROM backup.users
UNION ALL
SELECT ID,2,current_timestamp, current_timestamp, 1,1
  FROM backup.users;


insert into usuario_empresa(id_usuario, id_empresa, data_cria, data_alt, usr_cria, usr_alt)
select distinct
		pu.id_usuario, p.id_empresa,
		current_timestamp,
		current_timestamp,1,1
   from backup.perfil_acesso ua
        inner join backup.perfil_usuario pu on pu.id_perfil = ua.id_perfil
   	    inner join backup.processo p on p.id_processo = ua.id_processo;