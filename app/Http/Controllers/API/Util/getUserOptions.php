<?php

    namespace App\Http\Controllers\API\Util;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

    use Auth;

    use App\Models\Empresa;
    use App\Models\Processo;
    use App\Models\UsuarioConfig;
    use App\Models\UsuarioEmpresa;
    use App\Models\User;

    class getUserOptions extends Controller
    {
        public function getOptions(Request $request) {
            try {
                $returnData =   UsuarioConfig::where('id_usuario',Auth::user()->id)
                                ->whereIn('id_configuracao',[1,2])
                                ->orderBy('id_configuracao','asc')
                                ->get();

                foreach ($returnData as $keyData => $valueData) {
                    // Verifica se o código é de abertura de troca de objetos, se for ...
                    // Verifica se tem permissão
                    if($valueData->id_configuracao == 2) {
                        // Verifica se o usuário é administrador
                        $adminConfig    =   User::where('id',Auth::user()->id)->get();
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
            } // try { ... }
            catch(Exception $error) {
                return [];
            } // catch(Exception $error) { ... }
        } // public function getOptions(Request $request) { ... }
    }
