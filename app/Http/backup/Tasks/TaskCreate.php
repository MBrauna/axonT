<?php

    namespace App\Http\Controllers\API\Tasks;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

    use Auth;
    use DB;
    use Carbon\Carbon;

    use App\Models\Chamado;
    use App\Models\ChamadoItem;
    use App\Models\Empresa;
    use App\Models\Processo;
    use App\Models\Questao;
    use App\Models\TipoProcesso;
    use App\Models\User;

    class TaskCreate extends Controller
    {
        public function validateDataManual(Request $request) {
            try {
                // Coleta os dados da solicitação de serviço
                $idCompany  =   $request->input('idCompany');
                $idProccess =   $request->input('idProccess');
                $idType     =   $request->input('idType');
                $title      =   $request->input('title');

                // Valida os dados principais de entrada
                if(is_null($idCompany)) return response()->json([
                    'error' =>  [
                        'code'      =>  'AXONT0001',
                        'message'   =>  'Empresa não foi informada! Verifique.',
                    ],
                ],202);

                if(is_null($idProccess)) return response()->json([
                    'error' =>  [
                        'code'      =>  'AXONT0002',
                        'message'   =>  'Processo não foi informado! Verifique.',
                    ],
                ],202);

                if(is_null($idType)) return response()->json([
                    'error' =>  [
                        'code'      =>  'AXONT0003',
                        'message'   =>  'Tipo do processo não foi informado! Verifique.',
                    ],
                ],202);

                if(is_null($title)) return response()->json([
                    'error' =>  [
                        'code'      =>  'AXONT0004',
                        'message'   =>  'Título não foi informado! Verifique.',
                    ],
                ],202);


                // Consulta se os dados informados são verídicos.
                $validate   =   DB::table('empresa')
                                ->join('processo','processo.id_empresa','empresa.id_empresa')
                                ->join('tipo_processo','tipo_processo.id_processo','processo.id_processo')
                                ->where('empresa.situacao',true)
                                ->where('processo.situacao',true)
                                ->where('tipo_processo.situacao',true)
                                ->where('empresa.id_empresa',$idCompany)
                                ->where('processo.id_processo',$idProccess)
                                ->where('tipo_processo.id_tipo_processo',$idType)
                                ->count();

                if($validate <= 0 || $validate > 1) return response()->json([
                    'error' =>  [
                        'code'      =>  'AXONT0005',
                        'message'   =>  'Não foi possível gerar o chamado! Dados principais são inválidos.',
                    ],
                ],202);

                // Coleta o dado do tipo para criação do registro de chamado.
                $typeData   =   TipoProcesso::where('id_tipo_processo',$idCompany)->first();

                // Coleta as questões e prepara para armazenar
                $questionList   =   Questao::where('id_tipo_processo',intval($idType))
                                    ->where('situacao',true)
                                    ->orderBy('ordem','asc')
                                    ->orderBy('titulo','asc')
                                    ->get();

                $listQuestions  =   [];
                
                foreach ($questionList as $keyQuestion => $valueQuestion) {
                    // Check para respostas
                    switch ($valueQuestion->tipo) {
                        case 'datetime':
                            $tmpQuestionResp    =   Carbon::parse($request->input('idQuestion_'.$valueQuestion->id_questao.'_date','').' '.$request->input('idQuestion_'.$valueQuestion->id_questao.'_time',''));
                            break;
                        case 'date':
                            $tmpQuestionResp    =   Carbon::parse($request->input('idQuestion_'.$valueQuestion->id_questao,''))->startOfDay();
                            break;
                        case 'user':
                            $tmpQuestionResp    =   ($request->input('idQuestion_'.$valueQuestion->id_questao,'') == null || $request->input('idQuestion_'.$valueQuestion->id_questao,'') == '') ? 'Nenhum usuário selecionado' : User::find(intval($request->input('idQuestion_'.$valueQuestion->id_questao,'')));
                        default:
                            $tmpQuestionResp    =   $request->input('idQuestion_'.$valueQuestion->id_questao,'');
                            break;
                    }

                    if($valueQuestion->obrigatorio && ($tmpQuestionResp == null || trim($tmpQuestionResp) == '')) {
                        return response()->json([
                            'error' =>  [
                                'code'      =>  'AXONT0007',
                                'message'   =>  'Não foi possível gerar o ID# de solicitação! Verifique.',
                            ]
                        ],202);
                    } // if($valueQuestion->obrigatorio && ($tmpQuestionResp == null || trim($tmpQuestionResp) == '')) { ... }

                    $tmpData    =   (object)[
                        'tipo'          =>  $valueQuestion->tipo,
                        'id_questao'    =>  $valueQuestion->id_questao,
                        'obrigatorio'   =>  $valueQuestion->obrigatorio,
                        'ordem'         =>  $keyQuestion,
                        'questao'       =>  $valueQuestion->titulo,
                        'resposta'      =>  $tmpQuestionResp,
                        'realData'      =>  ($valueQuestion->tipo == 'user') ? $request->input('idQuestion_'.$valueQuestion->id_questao,'') : $tmpQuestionResp,
                        'alt_data_ref'  =>  $valueQuestion->alt_data_vencimento,
                    ];

                    array_push($listQuestions, $tmpData);
                } // foreach ($questionList as $keyQuestion => $valueQuestion) { ... }
                
                if(count($questionList) <> count($listQuestions)) {
                    return response()->json([
                        'error' =>  [
                            'code'      =>  'AXONT0009',
                            'message'   =>  'Não foi possível gerar o ID# de solicitação! Verifique.',
                        ]
                    ],202);
                } // if(count($questionList) <> count($listQuestions)) { ... }
            } catch (Exception $error) {
                return response()->json([
                    'error' =>  [
                        'code'      =>  'AXONT0009',
                        'message'   =>  'Não foi possível gerar a solicitação! Verifique.',
                    ]
                ],202);
            }

            return response()->json([
                'sucesso'   =>  true,
            ],200);
        } // public function validateData(Request $request) { ... }

    }
