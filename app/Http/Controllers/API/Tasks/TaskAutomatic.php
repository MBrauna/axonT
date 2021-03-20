<?php

    namespace App\Http\Controllers\API\Tasks;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

    use Auth;
    use DB;
    use Carbon\Carbon;

    use App\Models\Empresa;
    use App\Models\Processo;
    use App\Models\TipoProcesso;
    use App\Models\Questao;

    class TaskAutomatic extends Controller
    {
        public function verifyTaskAutomatic(Request $request) {
            $counter    =   0;

            // Verifica se é administrador
            if(Auth::user()->administrador) {
                $counter += 1;
            } // if(Auth::user()->administrador) { ... }

            $counter    +=  Empresa::where('situacao',true)
                            ->where('id_usuario_responsavel',Auth::user()->id)
                            ->count();

            $counter    +=  Processo::where('situacao',true)
                            ->where('id_usuario_responsavel',Auth::user()->id)
                            ->count();
            
            return response()->json([
                'verificador'   =>  (($counter <= 0) ? false : true)
            ],200);

        } // public function verifyTaskAutomatic(Request $request) { ... }

        public function dataOrignDestiny(Request $request) {
            try {
                $listCompany=   [];
                $tmpData    =   getCompanyPermission();

                foreach ($tmpData as $keyEmpresa => $valueEmpresa) {
                    // Se o usuário tem acesso a uma empresa, então poderá abrir a todos os processos existentes.
                    if(!in_array($valueEmpresa->id_empresa,$listCompany)) {
                        array_push($listCompany, $valueEmpresa->id_empresa);
                    } // if(!in_array($valueEmpresa->id_empresa,$listCompany)) { ... }
                } // foreach ($tmpData as $keyEmpresa => $valueEmpresa) { ... }

                // Consulta os dados de destino
                $destiny    =   DB::table('empresa')
                                ->join('processo','processo.id_empresa','empresa.id_empresa')
                                ->where('empresa.situacao',true)
                                ->where('processo.situacao',true)
                                ->whereIn('empresa.id_empresa',$listCompany)
                                ->select(
                                    'empresa.descricao as desc_empresa',
                                    'empresa.sigla as sigla_empresa',
                                    'processo.*'
                                )
                                ->orderBy('empresa.descricao','asc')
                                ->orderBy('processo.descricao','asc')
                                ->distinct()
                                ->get();


                $origin     =   DB::table('empresa')
                                ->join('processo','processo.id_empresa','empresa.id_empresa')
                                ->join('tipo_processo','tipo_processo.id_processo','processo.id_processo')
                                ->whereIn('empresa.id_empresa',$listCompany)
                                ->where(function($query){
                                    $query->orWhere('empresa.id_usuario_responsavel',Auth::user()->id);
                                    $query->orWhere('processo.id_usuario_responsavel',Auth::user()->id);
                                    $query->orWhereRaw('? = ?',[Auth::user()->administrador,true]);
                                })
                                ->where('empresa.situacao',true)
                                ->where('processo.situacao',true)
                                ->where('tipo_processo.automatico',true)
                                ->select(
                                    'processo.*',
                                    'empresa.descricao as desc_empresa',
                                    'empresa.sigla as sigla_empresa'
                                )
                                ->orderBy('empresa.descricao','asc')
                                ->orderBy('processo.descricao','asc')
                                ->distinct()
                                ->get();

                return response()->json([
                    'processoOrigem'    =>  $origin,
                    'processoDestino'   =>  $destiny,
                ],200);

            } // try { ... }
            catch(Exception $error) {
                return response()->json([],200);
            } // catch(Exception $error) { ... }
        } // public function dataOrignDestiny(Request $request) { ... }

        public function filterObjectType(Request $request) {
            try {
                $idProccess =   $request->input('idProccess');
                if(is_null($idProccess)) return response()->json(['erro' => 'O código do processo não foi informado! Verifique.'],202);
                $validator  =   DB::table('processo')->where('id_processo',intval($idProccess))->count();
                if($validator <= 0) return response()->json(['erro' => 'O código do processo informado não possui parametrização válida! Verifique.'],202);
                $validator  =   DB::table('tipo_processo')->where('id_processo',intval($idProccess))->count();
                if($validator <= 0) return response()->json(['erro' => 'O código do processo informado não possui parametrização para tipo válida! Verifique.'],202);

                $typeList   =   DB::table('empresa')
                                ->join('processo','processo.id_empresa','empresa.id_empresa')
                                ->join('tipo_processo','tipo_processo.id_processo','processo.id_processo')
                                ->where('processo.id_processo',intval($idProccess))
                                ->where('empresa.situacao',true)
                                ->where('processo.situacao',true)
                                ->where('tipo_processo.situacao',true)
                                ->where('tipo_processo.automatico',true)
                                ->select(
                                    'tipo_processo.*'
                                )
                                ->distinct()
                                ->orderBy('tipo_processo.titulo','asc')
                                ->orderBy('tipo_processo.subtitulo','asc')
                                ->get();

                // Coleta os usuários que estao abaixo do usuário
                $subordinate    =   [];

                $vRetorno   =   [
                    'tipo'  =>  $typeList,
                    'sub'   =>  $subordinate,
                ];
                return response()->json($vRetorno,200);
            } // try { ... }
            catch(Exception $error) {
                return response()->json([],200);
            } // catch(Exception $error) { ... }
        } // public function filterObjectType(Request $request) { ... }

        public function filterQuestion(Request $request) {
            try {

                $idProccess =   $request->input('idProccess');
                if(is_null($idProccess)) return response()->json(['erro' => 'O código do processo não foi informado! Verifique.'],202);
                $validador  =   Processo::where('id_processo',intval($idProccess))->count();
                if($validador <= 0) return response()->json(['erro' => 'O código do processo informado não possui parametrização válida! Verifique.'],202);


                $idType     =   $request->input('idType');
                if(is_null($idType)) return response()->json(['erro' => 'O código do tipo não foi informado! Verifique.'],202);
                $validador  =   TipoProcesso::where('id_tipo_processo',intval($idType))->where('id_processo',intval($idProccess))->count();
                if($validador <= 0) return response()->json(['erro' => 'O código do tipo informado não possui parametrização válida! Verifique.'],202);
                //$validador  =   Questao::where('id_tipo_processo',intval($idType))->count();
                //if($validador <= 0) return response()->json(['erro' => 'O código do tipo informado não possui parametrização para questões válida! Verifique.'],202);

                $questao        =   DB::table('empresa')
                                    ->join('processo','processo.id_empresa','empresa.id_empresa')
                                    ->join('tipo_processo','tipo_processo.id_processo','processo.id_processo')
                                    ->join('questao','questao.id_tipo_processo','tipo_processo.id_tipo_processo')
                                    ->where('processo.id_processo',intval($idProccess))
                                    ->where('tipo_processo.id_tipo_processo',intval($idType))
                                    ->where('empresa.situacao',true)
                                    ->where('processo.situacao',true)
                                    ->where('tipo_processo.situacao',true)
                                    //->where('tipo_processo.automatico',true)
                                    ->where('questao.situacao',true)
                                    ->select(
                                        'questao.*'
                                    )
                                    ->distinct()
                                    ->orderBy('questao.ordem','asc')
                                    ->orderBy('questao.titulo','asc')
                                    ->get();

                $vRetorno   =   [
                    'questao'   =>  $questao,
                    'menorHora' =>  Carbon::now()->toDateString(),
                ];

                return response()->json($vRetorno,200);

            } // try { ... }
            catch(Exception $error) {
                return response()->json([],200);
            } // catch(Exception $error) { ... }
        }
    }
