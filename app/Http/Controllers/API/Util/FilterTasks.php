<?php

    namespace App\Http\Controllers\API\Util;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

    use Auth;

    use App\Models\Empresa;
    use App\Models\Processo;
    use App\Models\TipoProcesso;
    use App\Models\Questao;
    use App\Models\UsuarioConfig;
    use App\Models\Perfil;

    class FilterTasks extends Controller
    {
        public function filter(Request $request) {
            try {
                $filtro     =   is_null($request->filter) ? false : $request->filter;

                $userConfig =   UsuarioConfig::where('id_usuario', Auth::user()->id)->select('id_perfil');
                $perfil     =   Perfil::whereIn('id_perfil',$userConfig)->select('id_empresa');
                $empresa    =   Empresa::whereIn('id_empresa',$perfil)
                                ->where('situacao',true)
                                ->orderBy('descricao','asc')
                                ->get();

                foreach ($empresa as $keyEmpresa => $valueEmpresa) {
                    $empresa[$keyEmpresa]->processos =   Processo::where('id_empresa',$valueEmpresa->id_empresa)
                                            ->get();

                    foreach($empresa[$keyEmpresa]->processos as $keyProc => $valueProc) {
                        $empresa[$keyEmpresa]->processos[$keyProc]->tipoAutomatico  =   TipoProcesso::where('id_processo',$valueProc->id_processo)
                                                                                        ->where('situacao',true)
                                                                                        ->where('automatico',true)
                                                                                        ->get();

                        $empresa[$keyEmpresa]->processos[$keyProc]->tipoManual      =   TipoProcesso::where('id_processo',$valueProc->id_processo)
                                                                                        ->where('situacao',true)
                                                                                        ->where('automatico',false)
                                                                                        ->get();


                        foreach ($empresa[$keyEmpresa]->processos[$keyProc]->tipoAutomatico as $keyTipo => $valueTipo) {
                            $empresa[$keyEmpresa]->processos[$keyProc]->tipoAutomatico[$keyTipo]->questoes  =   Questao::where('id_tipo_processo',$valueTipo->id_tipo_processo)
                                                                                                                ->where('situacao',true)
                                                                                                                ->get();
                        } // foreach ($empresa[$keyEmpresa]->processos[$keyProc]->tipoAutomatico as $keyTipo => $valueTipo) { ... }

                        foreach ($empresa[$keyEmpresa]->processos[$keyProc]->tipoManual as $keyTipo => $valueTipo) {
                            $empresa[$keyEmpresa]->processos[$keyProc]->tipoManual[$keyTipo]->questoes      =   Questao::where('id_tipo_processo',$valueTipo->id_tipo_processo)
                                                                                                                ->where('situacao',true)
                                                                                                                ->get();
                        } // foreach ($empresa[$keyEmpresa]->processos[$keyProc]->tipoAutomatico as $keyTipo => $valueTipo) { ... }
                    } // foreach($empresa->processos as $keyProc => $valueProc) { ... }
                } // foreach ($empresa as $key => $value) { ... }


                                
                return response()->json($empresa, 200);
            } // try { ... }
            catch(Exception $error) {

            } // catch(Exception $error) { ... }
        } // public function filter(Request $request) { ... }
    }
