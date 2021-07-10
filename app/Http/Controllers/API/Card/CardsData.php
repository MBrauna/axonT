<?php

    namespace App\Http\Controllers\API\Card;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

    use Auth;
    use Carbon\Carbon;
    use Storage;

    use App\Models\Arquivo;
    use App\Models\Chamado;
    use App\Models\ChamadoItem;
    use App\Models\Empresa;
    use App\Models\Fluxo;
    use App\Models\Processo;
    use App\Models\TipoProcesso;
    use App\Models\Situacao;
    use App\Models\User;
    use App\Models\UsuarioPerfil;
    use App\Models\Tarefa;

    class CardsData extends Controller
    {
        public function getCards(Request $request) {
            try {
                $idCompany  =   $request->input('idCompany');
                $idProcess  =   $request->input('idProcess');
                $idType     =   $request->input('idType');

                if($idCompany == 'null' || $idCompany == '' || !is_numeric($idCompany)) {
                    $idCompany  =   null;
                } // if($idCompany == 'null' || $idCompany == '') { ... }
                else {
                    $idCompany  =   intval($idCompany);
                } // else { ... }

                if($idProcess == 'null' || $idProcess == '' || !is_numeric($idProcess)) {
                    $idProcess  =   null;
                } // if($idProcess == 'null' || $idProcess == '') { ... }
                else {
                    $idProcess  =   intval($idProcess);
                } // else { ... }

                if($idType == 'null' || $idType == '' || !is_numeric($idType)) {
                    $idType  =   null;
                } // if($idType == 'null' || $idType == '') { ... }
                else {
                    $idType  =   intval($idType);
                } // else { ... }


                # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- #


                $listProccess   =   [];
                $listPerfil     =   UsuarioPerfil::where('id_usuario',Auth::user()->id)
                                    ->where('situacao',true)
                                    ->select('id_perfil','id_processo')
                                    ->get();
                
                foreach ($listPerfil as $keyPerfil => $valuePerfil) {
                    if(!in_array($valuePerfil->id_processo, $listProccess)) {
                        array_push($listProccess,$valuePerfil->id_processo);
                    } // if(!in_array($valuePerfil->id_processo, $listProccess)) { ... }
                } // foreach ($listPerfil as $keyPerfil => $valuePerfil) { ... }

                $listStatusActive   =   [];
                $listStatus         =   Situacao::whereIn('id_processo',$listProccess)
                                        ->where('conclusiva',false)
                                        ->where('situacao',true)
                                        ->get();

                foreach ($listStatus as $keyStatus => $valueStatus) {
                    $verifyProccess =   Processo::where('id_processo',$valueStatus->id_processo)
                                        ->first();
                    
                    if(isset($verifyProccess->id_processo) && !is_null($verifyProccess) && $verifyProccess->situacao) {
                        $verifyCompany  =   Empresa::where('id_empresa',$verifyProccess->id_empresa)
                                            ->where('situacao',true)
                                            ->first();

                        if(isset($verifyCompany->id_empresa) && !is_null($verifyCompany) && $verifyCompany->situacao) {
                            if(!in_array($valueStatus->id_situacao, $listStatusActive)) {
                                array_push($listStatusActive,$valueStatus->id_situacao);
                            } // if(!in_array($valueStatus->id_situacao, $listStatusActive)) { ... }
                        } // if(isset($verifyCompany->id_empresa) && !is_null($verifyCompany) && $verifyCompany->situacao) { ... }
                    } // if(isset($verifyProccess) && $verifyProccess->situacao) { ... }
                } // foreach ($listStatus as $keyStatus => $valueStatus) { ... }

                $listUser           =   [];
                $listSubordinates   =   getSubordinates();

                foreach ($listSubordinates as $keySub => $valueSub) {
                    if(!in_array($valueSub->id, $tmpSubordinates)) {
                        array_push($tmpSubordinates, $valueSub->id);
                    } // if(!in_array($valueSub->id, $tmpSubordinates)) { ... }
                } // foreach ($listSubordinates as $keySub => $valueSub) { ... }

                $queryUserResponsible   =   Chamado::where('id_responsavel',Auth::user()->id)
                                            ->whereIn('id_situacao',$listStatusActive)
                                            ->where('situacao',true)
                                            ->get();
                
                $queryProccess          =   Chamado::whereIn('id_processo',$listProccess)
                                            ->whereIn('id_situacao',$listStatusActive)
                                            ->whereNull('id_responsavel')
                                            ->where('situacao',true)
                                            ->get();
                
                $querySubordinates      =   Chamado::whereIn('id_responsavel',$listUser)
                                            ->whereIn('id_situacao',$listStatusActive)
                                            ->where('situacao',true)
                                            ->get();

                # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- #

                $returnData =   [];
                $urlChamado =   '/task/';

                foreach ($queryUserResponsible as $keyReturn => $valueReturn) {
                    if(!in_array($valueReturn, $returnData)) {
                        // Coleta subordinados
                        $tmpUserPerfil  =   UsuarioPerfil::where('situacao',true)
                                            ->where('id_processo',$valueReturn->id_processo)
                                            ->select('id_usuario')
                                            ->get();
                        $tmpUserData    =   [];
                        foreach ($tmpUserPerfil as $keyUserPerfil => $valueUserPerfil) {
                            $tmpUserRef =   User::where('id',$valueUserPerfil->id_usuario)->first();

                            if(!in_array($tmpUserRef, $tmpUserData)) {
                                array_push($tmpUserData, $tmpUserRef);
                            } // if(!in_array($valueUserPerfil->id_usuario, $tmpUserData)) { ... }
                        } // foreach ($tmpUserPerfil as $keyUserPerfil => $valueUserPerfil) { ... }

                        // Coleta o fluxo
                        $tmpFluxoSituacao   =   Fluxo::where('id_tipo_processo',$valueReturn->id_tipo_processo)
                                                ->where('id_situacao_ant',$valueReturn->id_situacao)
                                                ->select('id_situacao')
                                                ->get();

                        $tmpFluxoData       =   [];
                        foreach ($tmpFluxoSituacao as $keyFluxo => $valueFluxo) {
                            $tmpSituacaoFluxo   =   Situacao::where('id_situacao',$valueFluxo->id_situacao)->first();
                            $tmpSituacaoFluxo['selectedData']   =   false;

                            if(!in_array($tmpSituacaoFluxo, $tmpFluxoData)) {
                                array_push($tmpFluxoData,$tmpSituacaoFluxo);
                            } // if(!in_array($valueFluxo->id_situacao, $tmpFluxoData)) { ... }
                        } // foreach ($tmpFluxoSituacao as $keyFluxo => $valueFluxo) { ... }

                        $tmpSituacaoFluxo   =   Situacao::where('id_situacao',$valueReturn->id_situacao)->first();
                        $tmpSituacaoFluxo['selectedData']   =   true;
                        if(!in_array($tmpSituacaoFluxo, $tmpFluxoData)) {
                            array_push($tmpFluxoData,$tmpSituacaoFluxo);
                        } // if(!in_array($valueFluxo->id_situacao, $tmpFluxoData)) { ... }



                        $valueReturn['describe']    =   (object)[
                            'lastDate'          =>  Carbon::now()->toDateString(),
                            'file'              =>  false,
                            'company'           =>  Empresa::where('id_empresa',$valueReturn->id_empresa)->first(),
                            'process'           =>  Processo::where('id_processo', $valueReturn->id_processo)->first(),
                            'typeProcess'       =>  TipoProcesso::where('id_tipo_processo',$valueReturn->id_tipo_processo)->first(),
                            'statusData'        =>  Situacao::where('id_situacao',$valueReturn->id_situacao)->first(),
                            'data_vencimento'   =>  Carbon::parse($valueReturn->data_vencimento)->format('d/m/Y H:i'),
                            'data_conclusao'    =>  is_null($valueReturn->data_conclusao) ? 'Não finalizada' : Carbon::parse($valueReturn->data_conclusao)->format('d/m/Y H:i'),
                            'id_solicitante'    =>  User::where('id',$valueReturn->id_solicitante)->first(),
                            'id_responsavel'    =>  is_null($valueReturn->id_responsavel) ? 'Espera entre atividades' : User::where('id',$valueReturn->id_responsavel)->first(),
                            'status'            =>  Carbon::now()->lessThan(Carbon::parse($valueReturn->data_vencimento)) ? 'axon-border-red' : 'axon-border-green',
                            'subordinates'      =>  $tmpUserData,
                            'fluxo'             =>  $tmpFluxoData,
                            'url'               =>  $urlChamado.$valueReturn->id_chamado,
                        ];
                        array_push($returnData,$valueReturn);
                    } // if(!in_array($valueReturn, $returnData)) { ... }
                } // foreach ($queryUserResponsible as $keyReturn => $valueReturn) { ... }

                foreach ($queryProccess as $keyReturn => $valueReturn) {
                    if(!in_array($valueReturn, $returnData)) {
                        // Coleta subordinados
                        $tmpUserPerfil  =   UsuarioPerfil::where('situacao',true)
                                            ->where('id_processo',$valueReturn->id_processo)
                                            ->select('id_usuario')
                                            ->get();
                        $tmpUserData    =   [];
                        foreach ($tmpUserPerfil as $keyUserPerfil => $valueUserPerfil) {
                            $tmpUserRef =   User::where('id',$valueUserPerfil->id_usuario)->first();

                            if(!in_array($tmpUserRef, $tmpUserData)) {
                                array_push($tmpUserData, $tmpUserRef);
                            } // if(!in_array($valueUserPerfil->id_usuario, $tmpUserData)) { ... }
                        } // foreach ($tmpUserPerfil as $keyUserPerfil => $valueUserPerfil) { ... }

                        // Coleta o fluxo
                        $tmpFluxoSituacao   =   Fluxo::where('id_tipo_processo',$valueReturn->id_tipo_processo)
                                                ->where('id_situacao_ant',$valueReturn->id_situacao)
                                                ->select('id_situacao')
                                                ->get();

                        $tmpFluxoData       =   [];
                        foreach ($tmpFluxoSituacao as $keyFluxo => $valueFluxo) {
                            $tmpSituacaoFluxo   =   Situacao::where('id_situacao',$valueFluxo->id_situacao)->first();
                            $tmpSituacaoFluxo['selectedData']   =   false;

                            if(!in_array($tmpSituacaoFluxo, $tmpFluxoData)) {
                                array_push($tmpFluxoData,$tmpSituacaoFluxo);
                            } // if(!in_array($valueFluxo->id_situacao, $tmpFluxoData)) { ... }
                        } // foreach ($tmpFluxoSituacao as $keyFluxo => $valueFluxo) { ... }

                        $tmpSituacaoFluxo   =   Situacao::where('id_situacao',$valueReturn->id_situacao)->first();
                        $tmpSituacaoFluxo['selectedData']   =   true;
                        if(!in_array($tmpSituacaoFluxo, $tmpFluxoData)) {
                            array_push($tmpFluxoData,$tmpSituacaoFluxo);
                        } // if(!in_array($valueFluxo->id_situacao, $tmpFluxoData)) { ... }



                        $valueReturn['describe']    =   (object)[
                            'lastDate'          =>  Carbon::now()->toDateString(),
                            'file'              =>  false,
                            'company'           =>  Empresa::where('id_empresa',$valueReturn->id_empresa)->first(),
                            'process'           =>  Processo::where('id_processo', $valueReturn->id_processo)->first(),
                            'typeProcess'       =>  TipoProcesso::where('id_tipo_processo',$valueReturn->id_tipo_processo)->first(),
                            'statusData'        =>  Situacao::where('id_situacao',$valueReturn->id_situacao)->first(),
                            'data_vencimento'   =>  Carbon::parse($valueReturn->data_vencimento)->format('d/m/Y H:i'),
                            'data_conclusao'    =>  is_null($valueReturn->data_conclusao) ? 'Não finalizada' : Carbon::parse($valueReturn->data_conclusao)->format('d/m/Y H:i'),
                            'id_solicitante'    =>  User::where('id',$valueReturn->id_solicitante)->first(),
                            'id_responsavel'    =>  is_null($valueReturn->id_responsavel) ? 'Espera entre atividades' : User::where('id',$valueReturn->id_responsavel)->first(),
                            'status'            =>  Carbon::now()->lessThan(Carbon::parse($valueReturn->data_vencimento)) ? 'axon-border-red' : 'axon-border-green',
                            'subordinates'      =>  $tmpUserData,
                            'fluxo'             =>  $tmpFluxoData,
                            'url'               =>  $urlChamado.$valueReturn->id_chamado,
                        ];
                        array_push($returnData,$valueReturn);
                    } // if(!in_array($valueReturn, $returnData)) { ... }
                } // foreach ($queryProccess as $keyReturn => $valueReturn) { ... }

                foreach ($querySubordinates as $keyReturn => $valueReturn) {
                    if(!in_array($valueReturn, $returnData)) {
                        // Coleta subordinados
                        $tmpUserPerfil  =   UsuarioPerfil::where('situacao',true)
                                            ->where('id_processo',$valueReturn->id_processo)
                                            ->select('id_usuario')
                                            ->get();
                        $tmpUserData    =   [];
                        foreach ($tmpUserPerfil as $keyUserPerfil => $valueUserPerfil) {
                            $tmpUserRef =   User::where('id',$valueUserPerfil->id_usuario)->first();

                            if(!in_array($tmpUserRef, $tmpUserData)) {
                                array_push($tmpUserData, $tmpUserRef);
                            } // if(!in_array($valueUserPerfil->id_usuario, $tmpUserData)) { ... }
                        } // foreach ($tmpUserPerfil as $keyUserPerfil => $valueUserPerfil) { ... }

                        // Coleta o fluxo
                        $tmpFluxoSituacao   =   Fluxo::where('id_tipo_processo',$valueReturn->id_tipo_processo)
                                                ->where('id_situacao_ant',$valueReturn->id_situacao)
                                                ->select('id_situacao')
                                                ->get();

                        $tmpFluxoData       =   [];
                        foreach ($tmpFluxoSituacao as $keyFluxo => $valueFluxo) {
                            $tmpSituacaoFluxo   =   Situacao::where('id_situacao',$valueFluxo->id_situacao)->first();
                            $tmpSituacaoFluxo['selectedData']   =   false;

                            if(!in_array($tmpSituacaoFluxo, $tmpFluxoData)) {
                                array_push($tmpFluxoData,$tmpSituacaoFluxo);
                            } // if(!in_array($valueFluxo->id_situacao, $tmpFluxoData)) { ... }
                        } // foreach ($tmpFluxoSituacao as $keyFluxo => $valueFluxo) { ... }

                        $tmpSituacaoFluxo   =   Situacao::where('id_situacao',$valueReturn->id_situacao)->first();
                        $tmpSituacaoFluxo['selectedData']   =   true;
                        if(!in_array($tmpSituacaoFluxo, $tmpFluxoData)) {
                            array_push($tmpFluxoData,$tmpSituacaoFluxo);
                        } // if(!in_array($valueFluxo->id_situacao, $tmpFluxoData)) { ... }



                        $valueReturn['describe']    =   (object)[
                            'lastDate'          =>  Carbon::now()->toDateString(),
                            'file'              =>  false,
                            'company'           =>  Empresa::where('id_empresa',$valueReturn->id_empresa)->first(),
                            'process'           =>  Processo::where('id_processo', $valueReturn->id_processo)->first(),
                            'typeProcess'       =>  TipoProcesso::where('id_tipo_processo',$valueReturn->id_tipo_processo)->first(),
                            'statusData'        =>  Situacao::where('id_situacao',$valueReturn->id_situacao)->first(),
                            'data_vencimento'   =>  Carbon::parse($valueReturn->data_vencimento)->format('d/m/Y H:i'),
                            'data_conclusao'    =>  is_null($valueReturn->data_conclusao) ? 'Não finalizada' : Carbon::parse($valueReturn->data_conclusao)->format('d/m/Y H:i'),
                            'id_solicitante'    =>  User::where('id',$valueReturn->id_solicitante)->first(),
                            'id_responsavel'    =>  is_null($valueReturn->id_responsavel) ? 'Espera entre atividades' : User::where('id',$valueReturn->id_responsavel)->first(),
                            'status'            =>  Carbon::now()->lessThan(Carbon::parse($valueReturn->data_vencimento)) ? 'axon-border-red' : 'axon-border-green',
                            'subordinates'      =>  $tmpUserData,
                            'fluxo'             =>  $tmpFluxoData,
                            'url'               =>  $urlChamado.$valueReturn->id_chamado,
                        ];
                        array_push($returnData,$valueReturn);
                    } // if(!in_array($valueReturn, $returnData)) { ... }
                } // foreach ($querySubordinates as $keyReturn => $valueReturn) { ... }

                return response()->json($returnData,200);
                
            }
            catch(Exception $error) {
                return response()->json([],200);
            }
        }
    }
