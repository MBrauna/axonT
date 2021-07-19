<?php

    namespace App\Http\Controllers\API\Admin;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

    use Auth;
    use Carbon\Carbon;

    use App\Models\Empresa;
    use App\Models\Processo;
    use App\Models\User;
    use App\Models\UsuarioPerfil;

    class Company extends Controller
    {
        public function getCompanies(Request $request) {
            try {
                $returnData =   [];

                if(Auth::user()->admin_global) {
                    // Se é global tem acesso a todas as empresas
                    $returnData =   (object)[
                        'active'    =>  Empresa::where('situacao',true)->orderBy('descricao','asc')->get(),
                        'inactive'  =>  Empresa::where('situacao',false)->orderBy('descricao','asc')->get(),
                    ];
                } // if(Auth::user()->admin_global) { ... }
                else if(Auth::user()->administrador) {
                    $listPerfil =   UsuarioPerfil::where('id_usuario',Auth::user()->id)
                                    ->where('situacao',true)
                                    ->select('id_processo')
                                    ->get();
                    $permCompanies = [];
                    foreach ($listPerfil as $keyProcess => $valueProcess) {
                        $tmpCompanies   = Processo::where('id_processo',$valueProcess->id_processo)->first();

                        if(isset($tmpCompanies->id_empresa) && !in_array($tmpCompanies->id_empresa,$permCompanies)) {
                            array_push($permCompanies,$tmpCompanies->id_empresa);
                        } // if(isset($tmpCompanies->id_empresa) && !in_array($tmpCompanies->id_empresa,$permCompanies)) { ... }
                    } // foreach ($listCompanies as $keyCompanies => $valueCompanies) { ... }

                    $returnData =   (object)[
                        'active'    =>  Empresa::where('situacao',true)->whereIn('id_empresa',$permCompanies)->orderBy('descricao','asc')->get(),
                        'inactive'  =>  Empresa::where('situacao',false)->whereIn('id_empresa',$permCompanies)->orderBy('descricao','asc')->get(),
                    ];

                }
                else {
                    // Limpa qualquer retorno
                    $returnData = [];
                }

                return response()->json($returnData,200);
            } // try { ... }
            catch(Exception $error) {
                return response()->json($error,402);
            } // catch(Exception $error) { ... }
        } // public function getCompanies(Request $request) { ... }
    }
