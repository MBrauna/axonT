<?php

    namespace App\Http\Controllers\API\Task;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

    use Auth;

    use App\Models\Empresa;
    use App\Models\Processo;
    use App\Models\User;
    use App\Models\UsuarioEmpresa;

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
                    // Coleta todos os processos vinculados a esse chamado
                    $companies[$keyCompany]->allProccess    =   Processo::where('id_empresa',$valueCompany->id_empresa)
                                                                ->where('situacao',true)
                                                                ->orderBy('descricao','asc')
                                                                ->orderBy('sigla','asc')
                                                                ->get();
                } // foreach ($companies as $keyCompany => $valueCompany) { ... }


                return response()->json($companies,200);
            } // try { ... }
            catch(Exception $error) {
                return response()->json([],500);
            } // catch(Exception $error) { ... }
        } // public function getData(Request $request) { ... }
    } // class GetDataManual extends Controller { ... }
