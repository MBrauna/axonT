<?php

    namespace App\Http\Controllers\API\Util;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Auth;

    use App\Models\Empresa;
    use App\Models\Processo;
    use App\Models\UsuarioConfig;
    use App\Models\Perfil;

    class Company extends Controller {
        public function getCompany(Request $request) {
            try {
                $userConfig =   UsuarioConfig::where('id_usuario', Auth::user()->id)
                                ->select('id_perfil');

                
            } // try { ... }
            catch(Exception $error) {
                return response()->json([
                    'Company'  =>  []
                ],200);
            } // catch(Exception $error) { ... }
        } // public function getCompany(Request $request) { ... }
    } // class Company extends Controller { ... }
