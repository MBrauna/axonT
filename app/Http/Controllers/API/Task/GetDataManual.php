<?php

    namespace App\Http\Controllers\API\Task;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

    use Auth;
    use DB;
    use Carbon\Carbon;

    use App\Models\Chamado;
    use App\Models\ChamadoItem;
    use App\Models\Configuracao;
    use App\Models\Empresa;
    use App\Models\Processo;
    use App\Models\Questao;
    use App\Models\TipoProcesso;
    use App\Models\User;
    use App\Models\UsuarioEmpresa;
    use App\Models\UsuarioPerfil;
    use App\Models\Agendamento;
    use App\Models\AgendamentoItem;

    class GetDataManual extends Controller {
        public function getData(Request $request) {
            try {
                // Uma abertura manual permite gerar para todos da empresa
                $listCompanyPermission  =   UsuarioEmpresa::where('id_usuario',Auth::user()->id)
                                            ->select('id_empresa');

                $companies              =   Empresa::whereIn('id_empresa',$listCompanyPermission)
                                            ->where('situacao',true)
                                            ->orderBy('descricao','asc')
                                            ->orderBy('sigla','asc')
                                            ->get();

                foreach ($companies as $keyCompany => $valueCompany) {
                    // ==================> [DADOS ADICIONAIS DA EMPRESA]
                    $companies[$keyCompany]->respData   =   is_null($valueCompany->id_usuario_responsavel) ? null : User::find($valueCompany->id_usuario_responsavel);
                    // ==================> [DADOS ADICIONAIS DA EMPRESA]


                    // Coleta todos os processos vinculados a esse chamado
                    $companies[$keyCompany]->allProccess    =   Processo::where('id_empresa',$valueCompany->id_empresa)
                                                                ->where('situacao',true)
                                                                ->orderBy('descricao','asc')
                                                                ->orderBy('sigla','asc')
                                                                ->get();

                    foreach ($companies[$keyCompany]->allProccess as $keyProccess => $valueProccess) {
                        // ==================> [DADOS ADICIONAIS DA EMPRESA]
                        $companies[$keyCompany]->allProccess[$keyProccess]->respData        =   is_null($valueProccess->id_usuario_responsavel) ? null : User::find($valueProccess->id_usuario_responsavel);

                        $listUserProccess   =   UsuarioPerfil::where('id_processo',$valueProccess->id_processo)
                                                ->where('situacao',true)
                                                ->distinct()
                                                ->select('id_usuario');

                        $companies[$keyCompany]->allProccess[$keyProccess]->responsible     =   User::whereIn('id',$listUserProccess)
                                                                                                ->orderBy('name','asc')
                                                                                                ->get();
                        // ==================> [DADOS ADICIONAIS DA EMPRESA]

                        $companies[$keyCompany]->allProccess[$keyProccess]->manualType      =   TipoProcesso::where('situacao',true)
                                                                                                ->where('automatico',false)
                                                                                                ->where('id_processo',$valueProccess->id_processo)
                                                                                                ->orderBy('titulo','asc')
                                                                                                ->get();

                        foreach ($companies[$keyCompany]->allProccess[$keyProccess]->manualType as $keyType => $valueType) {
                            $companies[$keyCompany]->allProccess[$keyProccess]->manualType[$keyType]->allQuestions    = Questao::where('situacao',true)
                                                                                                                        ->where('id_tipo_processo',$valueType->id_tipo_processo)
                                                                                                                        ->orderBy('ordem','asc')
                                                                                                                        ->orderBy('titulo','asc')
                                                                                                                        ->get();
                        } // foreach ($companies[$keyCompany]->allProccess[$keyProccess]->manualType as $keyType => $valueType) { ... }

                        $companies[$keyCompany]->allProccess[$keyProccess]->automaticType   =   TipoProcesso::where('situacao',true)
                                                                                                ->where('automatico',true)
                                                                                                ->where('id_processo',$valueProccess->id_processo)
                                                                                                ->orderBy('titulo','asc')
                                                                                                ->get();

                        foreach ($companies[$keyCompany]->allProccess[$keyProccess]->automaticType as $keyType => $valueType) {
                            $companies[$keyCompany]->allProccess[$keyProccess]->automaticType[$keyType]->allQuestions   =   Questao::where('situacao',true)
                                                                                                                            ->where('id_tipo_processo',$valueType->id_tipo_processo)
                                                                                                                            ->orderBy('ordem','asc')
                                                                                                                            ->orderBy('titulo','asc')
                                                                                                                            ->get();
                        } // foreach ($companies[$keyCompany]->allProccess[$keyProccess]->automaticType as $keyType => $valueType) { ... }
                    } // foreach ($companies[$keyCompany]->allProccess as $keyProccess => $valueProccess) { ... }
                } // foreach ($companies as $keyCompany => $valueCompany) { ... }


                return response()->json($companies,200);
            } // try { ... }
            catch(Exception $error) {
                return response()->json([],500);
            } // catch(Exception $error) { ... }
        } // public function getData(Request $request) { ... }

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

            $listPermissions    =   getAccess();
            $listSubordinates   =   getSubordinates();
            $tmpProccess        =   [];
            $tmpSubordinates    =   [];

            foreach ($listPermissions as $keyCompany => $valueCompany) {
                foreach ($valueCompany->proccessData as $keyProccess => $valueProccess) {
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
    } // class GetDataManual extends Controller { ... }
