<?php

    namespace App\Http\Controllers\API\Task;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

    use Auth;
    use DB;
    use Carbon\Carbon;

    use App\Models\Chamado;
    use App\Models\ChamadoItem;
    use App\Models\Configuracao;
    use App\Models\Empresa;
    use App\Models\Processo;
    use App\Models\Questao;
    use App\Models\TipoProcesso;
    use App\Models\User;
    use App\Models\UsuarioEmpresa;
    use App\Models\UsuarioPerfil;
    use App\Models\Agendamento;
    use App\Models\AgendamentoItem;

    class GetDataAutomatic extends Controller
    {
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

                    // ==================> [DADOS ADICIONAIS DA EMPRESA]
                    $companies[$keyCompany]->respData   =   is_null($valueCompany->id_usuario_responsavel) ? null : User::find($valueCompany->id_usuario_responsavel);
                    // ==================> [DADOS ADICIONAIS DA EMPRESA]


                    // Coleta todos os processos vinculados a esse chamado
                    $companies[$keyCompany]->allProccess    =   Processo::where('id_empresa',$valueCompany->id_empresa)
                                                                ->where('situacao',true)
                                                                ->orderBy('descricao','asc')
                                                                ->orderBy('sigla','asc')
                                                                ->get();

                    foreach ($companies[$keyCompany]->allProccess as $keyProccess => $valueProccess) {
                        // Se o usuário não for responsável do processo, ou da empresa ou administrador ... pula.
                        if($valueProccess->id_usuario_responsavel != Auth::user()->id && $valueCompany->id_usuario_responsavel != Auth::user()->id && !Auth::user()->administrador) continue;
                        // ==================> [DADOS ADICIONAIS DA EMPRESA]
                        $companies[$keyCompany]->allProccess[$keyProccess]->respData        =   is_null($valueProccess->id_usuario_responsavel) ? null : User::find($valueProccess->id_usuario_responsavel);

                        $listUserProccess   =   UsuarioPerfil::where('id_processo',$valueProccess->id_processo)
                                                ->where('situacao',true)
                                                ->distinct()
                                                ->select('id_usuario');

                        $companies[$keyCompany]->allProccess[$keyProccess]->responsible     =   User::whereIn('id',$listUserProccess)
                                                                                                ->orderBy('name','asc')
                                                                                                ->get();
                        // ==================> [DADOS ADICIONAIS DA EMPRESA]

                        $companies[$keyCompany]->allProccess[$keyProccess]->manualType      =   TipoProcesso::where('situacao',true)
                                                                                                ->where('automatico',false)
                                                                                                ->where('id_processo',$valueProccess->id_processo)
                                                                                                ->orderBy('titulo','asc')
                                                                                                ->get();

                        foreach ($companies[$keyCompany]->allProccess[$keyProccess]->manualType as $keyType => $valueType) {
                            $companies[$keyCompany]->allProccess[$keyProccess]->manualType[$keyType]->allQuestions    = Questao::where('situacao',true)
                                                                                                                        ->where('id_tipo_processo',$valueType->id_tipo_processo)
                                                                                                                        ->orderBy('ordem','asc')
                                                                                                                        ->orderBy('titulo','asc')
                                                                                                                        ->get();
                        } // foreach ($companies[$keyCompany]->allProccess[$keyProccess]->manualType as $keyType => $valueType) { ... }

                        $companies[$keyCompany]->allProccess[$keyProccess]->automaticType   =   TipoProcesso::where('situacao',true)
                                                                                                ->where('automatico',true)
                                                                                                ->where('id_processo',$valueProccess->id_processo)
                                                                                                ->orderBy('titulo','asc')
                                                                                                ->get();

                        foreach ($companies[$keyCompany]->allProccess[$keyProccess]->automaticType as $keyType => $valueType) {
                            $companies[$keyCompany]->allProccess[$keyProccess]->automaticType[$keyType]->allQuestions   =   Questao::where('situacao',true)
                                                                                                                            ->where('id_tipo_processo',$valueType->id_tipo_processo)
                                                                                                                            ->orderBy('ordem','asc')
                                                                                                                            ->orderBy('titulo','asc')
                                                                                                                            ->get();
                        } // foreach ($companies[$keyCompany]->allProccess[$keyProccess]->automaticType as $keyType => $valueType) { ... }
                    } // foreach ($companies[$keyCompany]->allProccess as $keyProccess => $valueProccess) { ... }
                } // foreach ($companies as $keyCompany => $valueCompany) { ... }


                return response()->json($companies,200);
            } // try { ... }
            catch(Exception $error) {
                return response()->json([],500);
            } // catch(Exception $error) { ... }
        } // public function getData(Request $request) { ... }
    }
