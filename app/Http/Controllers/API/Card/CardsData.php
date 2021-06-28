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

                $perfilProcesso =   UsuarioPerfil::where('id_usuario',Auth::user()->id)
                                    ->where('situacao',true)
                                    ->select('id_perfil','id_processo')
                                    ->get();

                $listPermissions    =   getAccess();
                $listSubordinates   =   getSubordinates();
                $tmpProccess        =   [];
                $tmpSubordinates    =   [];
                $tmpPerfil          =   [];

                foreach ($listPermissions as $keyCompany => $valueCompany) {
                    foreach ($valueCompany->proccessData as $keyProccess => $valueProccess) {
                        if(!in_array($valueProccess->id_processo, $tmpProccess)) {
                            array_push($tmpProccess, $valueProccess->id_processo);
                        } // if(!in_array($valueProccess->id_processo, $tmpProccess)) { ... }
                    } // foreach ($valueCompany->processos as $keyProccess => $valueProccess) { ... }
                } // foreach ($listPermissions as $keyCompany => $valueCompany) { ... }

                $perfilSituacao =   Situacao::where('situacao',true)
                                    ->where('conclusiva',false)
                                    ->whereIn('id_processo',$tmpProccess)
                                    ->select('id_situacao');

                foreach ($listSubordinates as $keySub => $valueSub) {
                    if(!in_array($valueSub->id, $tmpSubordinates)) {
                        array_push($tmpSubordinates, $valueSub->id);
                    }
                } // foreach ($listSubordinates as $keySub => $valueSub) { ... }

                foreach($perfilProcesso as $keyData => $valueData) {
                    /*$tmpInfoPerfil  =   (object)[
                        'id_perfil'     =>  $valueData->id_perfil,
                        'id_processo'   =>  $valueData->id_processo,
                    ];*/

                    if(!in_array($valueData->id_processo,$tmpPerfil)) {
                        array_push($tmpPerfil,$valueData->id_processo);
                    } // if(!in_array($tmpInfoPerfil,$tmpPerfil)) { ... }
                } // foreach($perfilProcesso as $keyData => $valueData) { ... }

                // Tudo que é para o usuário e que ele esteja vinculado
                $taskPerfilProcessUser  =   Chamado::where('situacao',true)
                                            ->whereIn('id_processo',$tmpPerfil)

                
            }
            catch(Exception $error) {

            }
        }
    }
