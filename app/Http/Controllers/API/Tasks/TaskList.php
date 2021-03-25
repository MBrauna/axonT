<?php

    namespace App\Http\Controllers\API\Tasks;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

    use DB;
    use Auth;
    use Carbon\Carbon;

    use App\Models\Situacao;
    use App\Models\Processo;
    use App\Models\TipoProcesso;
    use App\Models\User;

    class TaskList extends Controller
    {
        public function list(Request $request) {
            $retorno    =   [];
            $idCompany  =   $request->input('idCompany');
            $idProccess =   $request->input('idProccess');
            $idType     =   $request->input('idType');

            if($idCompany == 'null') {
                $idCompany  =   null;
            } // if($idCompany == 'null') { ... }

            if($idProccess == 'null') {
                $idProccess =   null;
            } // if($idProccess == 'null') { ... } 

            if($idType == 'null') {
                $idType =   null;
            } // if($idType == 'null') { ... }

            $listPermissions    =   getCompanyPermission();
            $listSubordinates   =   getSubordinates();
            $tmpProccess        =   [];
            $tmpSubordinates    =   [];

            foreach ($listPermissions as $keyCompany => $valueCompany) {
                foreach ($valueCompany->processos as $keyProccess => $valueProccess) {
                    if(!in_array($valueProccess->id_processo, $tmpProccess)) {
                        array_push($tmpProccess, $valueProccess->id_processo);
                    } // if(!in_array($valueProccess->id_processo, $tmpProccess)) { ... }
                } // foreach ($valueCompany->processos as $keyProccess => $valueProccess) { ... }
            } // foreach ($listPermissions as $keyCompany => $valueCompany) { ... }

            foreach ($listSubordinates as $keySub => $valueSub) {
                if(!in_array($valueSub->id, $tmpSubordinates)) {
                    array_push($tmpSubordinates, $valueSub->id);
                }
            } // foreach ($listSubordinates as $keySub => $valueSub) { ... }

            $perfilSituacao =    DB::table('situacao')
                                ->where('situacao',true)
                                ->where('conclusiva',false)
                                ->whereIn('id_processo',$tmpProccess)
                                ->select('id_situacao');

            $chamadoProcesso    =   DB::table('chamado')
                                    ->join('empresa','empresa.id_empresa','chamado.id_empresa')
                                    ->join('processo','processo.id_processo','chamado.id_processo')
                                    ->where('empresa.situacao',true)
                                    ->where('processo.situacao',true)
                                    ->whereIn('chamado.id_processo',$tmpProccess)
                                    ->whereIn('chamado.id_situacao',$perfilSituacao)
                                    ->select('chamado.*');

            $chamadoSubordinado =   DB::table('chamado')
                                    ->join('empresa','empresa.id_empresa','chamado.id_empresa')
                                    ->join('processo','processo.id_processo','chamado.id_processo')
                                    ->where('empresa.situacao',true)
                                    ->where('processo.situacao',true)
                                    ->whereIn('chamado.id_solicitante',$tmpSubordinates)
                                    ->whereIn('chamado.id_situacao',$perfilSituacao)
                                    ->select('chamado.*');

            $chamadoUsuario     =   DB::table('chamado')
                                    ->join('empresa','empresa.id_empresa','chamado.id_empresa')
                                    ->join('processo','processo.id_processo','chamado.id_processo')
                                    ->where('empresa.situacao',true)
                                    ->where('processo.situacao',true)
                                    ->where('chamado.id_solicitante',Auth::user()->id)
                                    ->whereIn('chamado.id_situacao',$perfilSituacao)
                                    ->union($chamadoProcesso)
                                    ->union($chamadoSubordinado)
                                    ->orderBy('data_vencimento','asc')
                                    ->orderBy('titulo','asc')
                                    ->select('chamado.*')
                                    ->distinct()
                                    ->get();

            foreach ($chamadoUsuario as $conteudo) {

                // Para filtros
                // por ID
                //if(!is_null($idCompany) && $conteudo->id_empresa != intval($idCompany)) continue;
                // por titulo
                //if(!is_null($idProccess) && !strpos($conteudo->titulo, $titulo)) continue;
                // Para empresa
                if(!is_null($idCompany) && $conteudo->id_empresa != intval($idCompany)) continue;
                // Para processo
                if(!is_null($idProccess) && $conteudo->id_processo != intval($idProccess)) continue;
                // Para filtros
                if(!is_null($idType) && $conteudo->id_tipo_processo != intval($idType)) continue;
                // Para solicitante
                //if(!is_null($idSolicitante) && $conteudo->id_solicitante != intval($idSolicitante)) continue;
                // Para responsavel
                //if(!is_null($idResponsavel) && $conteudo->id_responsavel != intval($idResponsavel)) continue;
                // Para filtros

                $tmpRetorno                     =   [];
                $tmpSolicitante                 =   DB::table('users')->where('id',$conteudo->id_solicitante)->first();
                $tmpSituacao                    =   DB::table('situacao')->where('id_situacao',$conteudo->id_situacao)->first();
                $tmpEmpresa                     =   DB::table('empresa')->where('id_empresa',$conteudo->id_empresa)->first();
                $tmpProcesso                    =   DB::table('processo')->where('id_processo',$conteudo->id_processo)->first();
                $tmpTipoProcesso                =   DB::table('tipo_processo')->where('id_tipo_processo',$conteudo->id_tipo_processo)->first();

                if(isset($conteudo->id_responsavel) && !is_null($conteudo->id_responsavel)) {
                    $tmpResponsavel             =   DB::table('users')->where('id',$conteudo->id_responsavel)->first();
                }
                elseif(isset($tmpProcesso->id_usr_responsavel) && !is_null($tmpProcesso->id_usr_responsavel)) {
                    $tmpResponsavel             =   null; //consulta_usuario($tmpProcesso->id_usr_responsavel);
                }
                else {
                    $tmpResponsavel             =   null;
                }
                /*
                if(is_null($tmpSolicitante)) {
                    $tmpNomeSolicitante =   ['Não atribuído'];
                }
                else {
                    $tmpNomeSolicitante =  explode(' ', trim($tmpSolicitante->name));
                }
                if(is_null($tmpResponsavel)) {
                    $tmpNomeResponsavel =   'Não atribuído';
                }
                else {
                    $tmpNomeResponsavel =   explode(' ', trim($tmpResponsavel->name));
                    $tmpNomeResponsavel =   $tmpNomeResponsavel[0].(count($tmpNomeResponsavel) > 1 ? ' '.$tmpNomeResponsavel[1] : '').(count($tmpNomeResponsavel) > 2 ? ' '.$tmpNomeResponsavel[2] : '');
                }*/

                $tmpRetorno['id']               =   '<a href="/solicitacao/'.$conteudo->id_chamado.'">#'.$conteudo->id_chamado.'</a>';
                $tmpRetorno['titulo']           =   '<a href="/solicitacao/'.$conteudo->id_chamado.'">'.$conteudo->titulo.'</a>'; #.( strlen($conteudo->titulo) <= 30 ? $conteudo->titulo : substr($conteudo->titulo,0,30).'...' ).'</a>';
                $tmpRetorno['solicitante']      =   $tmpSolicitante->name ?? 'Não atribuído';//$tmpNomeSolicitante[0].(count($tmpNomeSolicitante) > 1 ? ' '.$tmpNomeSolicitante[1] : '').(count($tmpNomeSolicitante) > 2 ? ' '.$tmpNomeSolicitante[2] : '');
                $tmpRetorno['situacao']         =   $tmpSituacao->descricao ?? '';#(is_null($tmpSituacao)) ? '' : (strlen($tmpSituacao->descricao) <= 30 ? $tmpSituacao->descricao : substr($tmpSituacao->descricao,0,30).'...');
                $tmpRetorno['responsavel']      =   is_null($tmpResponsavel) ? '<b style="color: #fa9016;">Espera entre atividades</b>' : ('<span>'.$tmpResponsavel->name.'</span>' ?? '<b style="color: #fa9016;">Espera entre atividades</b>');
                $tmpRetorno['empresa']          =   trim($tmpEmpresa->sigla);
                $tmpRetorno['processo']         =   $tmpProcesso->descricao;
                $tmpRetorno['dataSolicitacao']  =   Carbon::parse($conteudo->data_cria);//->format('d/m/Y h:i');
                $tmpRetorno['dataVencimento']   =   Carbon::parse($conteudo->data_vencimento);//->format('d/m/Y h:i');
                $tmpRetorno['dataConclusao']    =   is_null($conteudo->data_conclusao) ? '' : Carbon::parse($conteudo->data_conclusao)->format('d/m/Y h:i');
                $tmpRetorno['prazoContratado']  =   Carbon::parse($conteudo->data_cria)->diff(Carbon::parse($conteudo->data_cria)->addMinutes($tmpTipoProcesso->sla))->format('%ya %mm %dd %H:%I:%S');
                $tmpRetorno['prazoAtribuido']   =   Carbon::parse($conteudo->data_cria)->diff(Carbon::parse($conteudo->data_vencimento))->format('%ya %mm %dd %H:%I:%S');
                $tmpRetorno['prazo']            =   (is_null($conteudo->data_conclusao) ? (Carbon::now()->greaterThan(Carbon::parse($conteudo->data_vencimento)) ? '<b class="text-danger">'.Carbon::now()->diff(Carbon::parse($conteudo->data_vencimento))->format('%Ya %mm %dd %H:%I:%S').'</a>' : '<b class="text-success">'.Carbon::now()->diff(Carbon::parse($conteudo->data_vencimento))->format('%ya %mm %dd %H:%I:%S').'</a>') : '<b class="text-success">'.Carbon::parse($conteudo->data_cria)->diff(Carbon::parse($conteudo->data_conclusao))->format('%ya %mm %dd %H:%I:%S').'</a>');

                array_push($retorno,$tmpRetorno);
            } // foreach ($chamadoUsuario as $conteudo) { ... }

            return response()->json($retorno,200);
        } // public function list(Request $request) { ... }

        public function listAutomatic(Request $request) {
            $retorno    =   [];
            $idCompany  =   $request->input('idCompany');
            $idProccess =   $request->input('idProccess');
            $idType     =   $request->input('idType');

            if($idCompany == 'null') {
                $idCompany  =   null;
            } // if($idCompany == 'null') { ... }

            if($idProccess == 'null') {
                $idProccess =   null;
            } // if($idProccess == 'null') { ... } 

            if($idType == 'null') {
                $idType =   null;
            } // if($idType == 'null') { ... }

            $listPermissions    =   getCompanyPermission();
            $listSubordinates   =   getSubordinates();
            $tmpProccess        =   [];
            $tmpSubordinates    =   [];

            foreach ($listPermissions as $keyCompany => $valueCompany) {
                foreach ($valueCompany->processos as $keyProccess => $valueProccess) {
                    if(!in_array($valueProccess->id_processo, $tmpProccess)) {
                        array_push($tmpProccess, $valueProccess->id_processo);
                    } // if(!in_array($valueProccess->id_processo, $tmpProccess)) { ... }
                } // foreach ($valueCompany->processos as $keyProccess => $valueProccess) { ... }
            } // foreach ($listPermissions as $keyCompany => $valueCompany) { ... }

            foreach ($listSubordinates as $keySub => $valueSub) {
                if(!in_array($valueSub->id, $tmpSubordinates)) {
                    array_push($tmpSubordinates, $valueSub->id);
                }
            } // foreach ($listSubordinates as $keySub => $valueSub) { ... }

            $listaEntradaSub=   DB::table('agendamento')
                                ->whereIn('id_usuario_origem',$tmpSubordinates)
                                ->where('situacao',true)
                                ->orderBy('tipo','asc')
                                ->orderBy('titulo','asc');

            $listaSaidaSub  =   DB::table('agendamento')
                                ->whereIn('id_usuario_destino',$tmpSubordinates)
                                ->where('situacao',true)
                                ->orderBy('tipo','asc')
                                ->orderBy('titulo','asc');

            $listaEntrada   =   DB::table('agendamento')
                                ->where('id_usuario_origem',Auth::user()->id)
                                ->where('situacao',true)
                                ->orderBy('tipo','asc')
                                ->orderBy('titulo','asc');

            $listaSaida     =   DB::table('agendamento')
                                ->where('id_usuario_destino',Auth::user()->id)
                                ->where('situacao',true)
                                ->orderBy('tipo','asc')
                                ->orderBy('titulo','asc');

            $lista  =   DB::table('agendamento')
                        ->where('id_solicitante',Auth::user()->id)
                        ->where('situacao',true)
                        ->union($listaEntrada)
                        ->union($listaSaida)
                        ->union($listaEntradaSub)
                        ->union($listaSaidaSub)
                        ->orderBy('tipo','asc')
                        ->orderBy('titulo','asc')
                        ->distinct()
                        ->get();

            foreach ($lista as $conteudoLista) {
                if(!is_null($idProccess) && (($conteudoLista->id_processo_referencia != intval($idProccess)) || ($conteudoLista->id_processo_origem != intval($idProccess)) || ($conteudoLista->id_processo_destino != intval($idProccess)))) continue;
                if(!is_null($idType) && (($conteudoLista->id_tipo_processo_origem != intval($idType)) || ($conteudoLista->id_tipo_processo_destino != intval($idType)))) continue;

                // ID
                $conteudoLista->idDesc          =   '#'.str_pad($conteudoLista->id_agendamento,4,'0',STR_PAD_LEFT);

                // Descrição do tipo
                if($conteudoLista->tipo == 1) {
                    $conteudoLista->tipoDesc    =   'Entrada';
                }
                else {
                    $conteudoLista->tipoDesc    =   'Saída';
                }

                $conteudoLista->procRef         =   (Processo::where('id_processo',$conteudoLista->id_processo_referencia)->first())->descricao ?? 'Não identificado';
                $conteudoLista->procOrigem      =   (Processo::where('id_processo',$conteudoLista->id_processo_origem)->first())->descricao ?? 'Não identificado';
                $conteudoLista->procDestino     =   (Processo::where('id_processo',$conteudoLista->id_processo_destino)->first())->descricao ?? 'Não identificado';

                $conteudoLista->tipoOrigem      =   (TipoProcesso::where('id_tipo_processo',$conteudoLista->id_tipo_processo_origem)->first())->titulo ?? 'Não identificado';
                $conteudoLista->tipoDestino     =   (TipoProcesso::where('id_tipo_processo',$conteudoLista->id_tipo_processo_destino)->first())->titulo ?? 'Não identificado';

                $conteudoLista->periodicDesc    =   $conteudoLista->qtde_periodicidade.' '.getPeriodic($conteudoLista->periodicidade);

                $conteudoLista->usuarioOrigem   =   (User::find($conteudoLista->id_usuario_origem))->name ?? '';
                $conteudoLista->usuarioDestino  =   (User::find($conteudoLista->id_usuario_destino))->name ?? '';

                $conteudoLista->dataIniDesc     =   Carbon::parse($conteudoLista->data_inicial)->format('d/m/Y h:i') ?? '';
                $conteudoLista->dataFimDesc     =   Carbon::parse($conteudoLista->data_final)->format('d/m/Y h:i') ?? '';
                $conteudoLista->proxAgendDesc   =   Carbon::parse($conteudoLista->proximo_agendamento)->format('d/m/Y h:i') ?? '';

                $conteudoLista->btnAprovacao    =   (object)[
                    'id'        =>  $conteudoLista->id_agendamento,
                    'tipo'      =>  $conteudoLista->tipo,
                    'trust'     =>  (object)[
                        'origem'    =>  $conteudoLista->aprova_origem,
                        'destino'   =>  $conteudoLista->aprova_destino,
                    ],
                    'permission'=>  (object)[
                        'origem'    =>  (Processo::where('id_processo',$conteudoLista->id_processo_origem)->where('id_usuario_responsavel',Auth::user()->id)->count() <= 0) ? false : true,
                        'destino'   =>  (Processo::where('id_processo',$conteudoLista->id_processo_destino)->where('id_usuario_responsavel',Auth::user()->id)->count() <= 0) ? false : true,
                    ]

                ];

                $conteudoLista->listaQuestao    =   DB::table('agendamento_item')
                                                    ->where('id_agendamento',$conteudoLista->id_agendamento)
                                                    ->orderBy('ordem','asc')
                                                    ->orderBy('id_agendamento_item','asc')
                                                    ->get();

                array_push($retorno, $conteudoLista);
            } // foreach ($lista as $conteudoLista) { ... }

            return response()->json($retorno,200);
        } // public function listAutomatic(Request $request) { ... }

        public function changeAutomaticStatus(Request $request) {
            try {
                
            } // try { ... }
            catch(Exception $error) {

            } // catch(Exception $error) { ... }
        } // public function changeAutomaticStatus(Request $request) { ... }
    }
