<?php

    namespace App\Http\Controllers\API\Util;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

    use Auth;

    use App\Models\Configuracao;
    use App\Models\Empresa;
    use App\Models\Processo;
    use App\Models\UsuarioConfig;
    use App\Models\UsuarioEmpresa;
    use App\Models\User;

    class GetUserOptions extends Controller
    {
        public function getOptions(Request $request) {
            try {
                $returnData =   UsuarioConfig::where('id_usuario',Auth::user()->id)
                                ->whereIn('id_configuracao',[1,2])
                                ->orderBy('id_configuracao','asc')
                                ->get();

                foreach ($returnData as $keyData => $valueData) {
                    $returnData[$keyData]->configData   =   Configuracao::where('id_configuracao',$valueData->id_configuracao)->first();

                    // Verifica se o código é de abertura de troca de objetos, se for ...
                    // Verifica se tem permissão
                    if($valueData->id_configuracao == 2) {
                        // Verifica se o usuário é administrador
                        $adminConfig    =   User::where('id',Auth::user()->id)->first();
                        $adminConfig    =   $adminConfig->administrador;

                        // Verifica se o usuário é dono de alguma empresa
                        $qtdeEmpresa    =   Empresa::where('situacao',true)
                                            ->where('id_usuario_responsavel',Auth::user()->id)
                                            ->count();
                        $qtdeEmpresa    =   ($qtdeEmpresa <= 0) ? false : true;

                        // Verifica se o usuário é o dono de algum processo
                        $qtdeProcesso   =   Processo::where('situacao',true)
                                            ->where('id_usuario_responsavel',Auth::user()->id)
                                            ->count();
                        $qtdeProcesso   =   ($qtdeProcesso <= 0) ? false : true;

                        if($adminConfig || $qtdeEmpresa || $qtdeProcesso) {
                            $returnData[$keyData]->permission   =   true;
                        } // if($adminConfig || $qtdeEmpresa || $qtdeProcesso) { ... }
                        else {
                            $returnData[$keyData]->permission   =   false;
                        } // else { ... }
                    } // if($valueData->id_configuracao == 2) { ... }
                    else {
                        // Caso contrário, apenas em ter a permissão já libera
                        $returnData[$keyData]->permission   =   true;
                    }
                } // foreach ($returnData as $keyData => $valueData) { ... }

                return response()->json($returnData,200);
            } // try { ... }
            catch(Exception $error) {
                return response()->json([],500);
            } // catch(Exception $error) { ... }
        } // public function getOptions(Request $request) { ... }



        public function getPermission(Request $request) {
            try {
                // Coleta os dados da requisição - Coleta o ID da configuração
                $idConfig   =   $request->input('id_configuracao');

                if(is_null($idConfig)) return response()->json(['permission'    =>  false,],200);
                $idConfig   =   intval($idConfig);

                switch ($idConfig) {
                    case 1:
                        // Código para Criação de solicitação de serviço
                        $userPerm   =   UsuarioConfig::where('id_usuario',Auth::user()->id)
                                        ->where('id_configuracao',1) // [1] - Solicitação de serviço
                                        ->count();
                        // Verifica se a solicitação de serviço está disponível para o usuário
                        if($userPerm <= 0) {
                            return response()->json(['permission'    =>  false,],200);
                        } // if($userPerm <= 0) { ... }
                        else {
                            return response()->json(['permission'    =>  true,],200);
                        } // else { ... }

                        break;
                    case 2:
                        // Código para criação de troca de objetos
                        $userPerm   =   UsuarioConfig::where('id_usuario',Auth::user()->id)
                                        ->where('id_configuracao',2) // [2] - Troca de objetos
                                        ->count();
                        
                        // Verifica se a solicitação de serviço está disponível para o usuário
                        if($userPerm <= 0) {
                            return response()->json(['permission'    =>  false,],200);
                        } // if($userPerm <= 0) { ... }
                        else {
                            /*$adminConfig    =   User::where('id',Auth::user()->id)->first();
                            $adminConfig    =   $adminConfig->administrador;

                            // Verifica se o usuário é dono de alguma empresa
                            $qtdeEmpresa    =   Empresa::where('situacao',true)
                                                ->where('id_usuario_responsavel',Auth::user()->id)
                                                ->count();
                            $qtdeEmpresa    =   ($qtdeEmpresa <= 0) ? false : true;

                            // Verifica se o usuário é o dono de algum processo
                            $qtdeProcesso   =   Processo::where('situacao',true)
                                                ->where('id_usuario_responsavel',Auth::user()->id)
                                                ->count();
                            $qtdeProcesso   =   ($qtdeProcesso <= 0) ? false : true;

                            if($adminConfig || $qtdeEmpresa || $qtdeProcesso) {
                                return response()->json(['permission'    =>  true,],200);
                            } // if($adminConfig || $qtdeEmpresa || $qtdeProcesso) { ... }
                            else {
                                return response()->json(['permission'    =>  false,],200);
                            } // else { ... }*/
                            return response()->json(['permission'    =>  true,],200);
                        } // else { ... }

                        break;
                    default:
                        return response()->json(['permission'    =>  false,],200);
                        break;
                } // switch ($idConfig) { ... }

            } // try { ... }
            catch(Exception $error) {
                return response()->json(['permission'    =>  false,],500);
            } // catch(Exception $error) { ... }
        } // public function getPermission(Request $request) { ... }


        /*
         * 
         * if($adminConfig) {
                                

                                // Coleta todos os dados das empresas que possui acesso
                                $listCompanyUser    =   UsuarioEmpresa::where('id_usuario',Auth::user()->id)
                                                        ->select('id_empresa');

                                // Coleta as empresas e todos os processos referentes a elas.
                                $returnData[$keyData]->listAccess   =   Empresa::whereIn('id_empresa',$listCompanyUser)
                                                                        ->where('situacao',true)
                                                                        ->get();

                                foreach ($returnData[$keyData]->listAccess as $keyCompany => $valueCompany) {
                                    $returnData[$keyData]->listAccess[$keyCompany]  =
                                } // foreach ($returnData[$keyData]->listAccess as $keyCompany => $valueCompany) { ... }

                                
                            } // if($adminConfig) { ... }
                            // Se for o responsável pela empresa terá acesso a todos os processos.
                            elseif ($qtdeEmpresa) {
                                $returnData[$keyData]->permission   =   true;
                            } // elseif ($qtdeEmpresa) { ... }
                            // Se for o responsável por processo terá acesso a apenas aqueles processos.
                            elseif ($qtdeProcesso) {
                                $returnData[$keyData]->permission   =   true;
                            } // elseif ($qtdeProcesso) {
                            // Se não tiver nada ele retorna um array vazio
                            else {
                                $returnData[$keyData]->permission   =   true;
                                $returnData[$keyData]->listAccess   =   [];
                            }
         * 
         */
    }
