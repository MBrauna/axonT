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
                $filtro     =   is_null($request->filter) ? false : $request->filter;

                $userConfig =   UsuarioConfig::where('id_usuario', Auth::user()->id)->select('id_perfil');
                $perfil     =   Perfil::whereIn('id_perfil',$userConfig)->select('id_empresa');
                $empresa    =   Empresa::whereIn('id_empresa',$perfil)
                                ->where('situacao',true)
                                ->orderBy('descricao','asc');

                // Inicia os dados dos filtros
                if(!is_null($request->idCompany) && $filtro) {
                    $empresa    =   $empresa->where('id_empresa',$request->idCompany);
                } // if(isset($request->idCompany) && !is_null($request->idCompany) && $filtro) { ... }


                $empresa    =   $empresa->get();
                                
                return response()->json($empresa, 200);
                
            } // try { ... }
            catch(Exception $error) {
                return response()->json([
                    'Company'  =>  []
                ],200);
            } // catch(Exception $error) { ... }
        } // public function getCompany(Request $request) { ... }
    } // class Company extends Controller { ... }
