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
                            if(!in_array($valueStatus, $listStatusActive)) {
                                array_push($listStatusActive,$valueStatus);
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
                                            ->where('situacao',true)
                                            ->get();
                
                $queryProccess          =   Chamado::whereIn('id_processo',$listProccess)
                                            ->whereNull('id_responsavel')
                                            ->where('situacao',true)
                                            ->get();
                
                $querySubordinates      =   Chamado::whereIn('id_responsavel',$listUser)
                                            ->where('situacao',true)
                                            ->get();

                # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- #
                
                $returnData =   [];

                foreach ($queryUserResponsible as $keyReturn => $valueReturn) {
                    if(!in_array($valueReturn, $returnData)) {
                        $valueReturn['classCard']   =   'axon-border-indigo';
                        array_push($returnData,$valueReturn);
                    } // if(!in_array($valueReturn, $returnData)) { ... }
                } // foreach ($queryUserResponsible as $keyReturn => $valueReturn) { ... }

                foreach ($queryProccess as $keyReturn => $valueReturn) {
                    if(!in_array($valueReturn, $returnData)) {
                        $valueReturn['classCard']   =   'axon-border-orange';
                        array_push($returnData,$valueReturn);
                    } // if(!in_array($valueReturn, $returnData)) { ... }
                } // foreach ($queryProccess as $keyReturn => $valueReturn) { ... }

                foreach ($querySubordinates as $keyReturn => $valueReturn) {
                    if(!in_array($valueReturn, $returnData)) {
                        $valueReturn['classCard']   =   'axon-border-teal';
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
