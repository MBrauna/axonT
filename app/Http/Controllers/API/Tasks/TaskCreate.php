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
        public function createData(Request $request) {
            $typeSS =   $request->input('typeSS','0');

            if($typeSS == 0) {
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

                    // Iniciação de datas
                    $createDate     =   Carbon::now();
                    $dueDate        =   Carbon::now();
                    // Iniciação de datas

                    if(Carbon::now()->isoWeekday() === 6) {
                        $createDate    =   Carbon::now()->addDays(2);
                        $dueDate =   Carbon::now()->addDays(2)->addMinutes($typeData->sla);
                    }
                    elseif(Carbon::now()->isoWeekday() === 7) {
                        $createDate    =   Carbon::now()->addDays(1);
                        $dueDate =   Carbon::now()->addDays(1)->addMinutes($typeData->sla);
                    }
                    else {
                        $createDate    =   Carbon::now();
                        $dueDate =   Carbon::now()->addMinutes($typeData->sla);
                    }
        
                    if($dueDate->isoWeekday() === 6) {
                        $dueDate =   Carbon::now()->addDays(2)->addMinutes($typeData->sla);
                    }
                    elseif($dueDate->isoWeekday() === 7) {
                        $dueDate =   Carbon::now()->addDays(1)->addMinutes($typeData->sla);
                    }

                    
                    // Cria o registro do chamado, aguarda se existirá erro
                    try {
                        $newSS                      =   new Chamado;
                        $newSS->id_empresa          =   $idCompany;
                        $newSS->id_processo         =   $idProccess;
                        $newSS->id_tipo_processo    =   $idType;
                        $newSS->id_situacao         =   $typeData->id_situacao_inicial;
                        $newSS->data_vencimento     =   $dueDate;
                        $newSS->id_solicitante      =   Auth::user()->id;
                        $newSS->url                 =   $_SERVER['HTTP_HOST'];
                        $newSS->titulo              =   $title;
                        $newSS->situacao            =   true;
                        $newSS->data_cria           =   $createDate;
                        $newSS->data_alt            =   $createDate;
                        $newSS->usr_cria            =   Auth::user()->id;
                        $newSS->usr_alt             =   Auth::user()->id;
                        $newSS->save();
                    }
                    catch(Exception $error) {
                        return response()->json([
                            'error' =>  [
                                'code'      =>  'AXONT0006',
                                'message'   =>  'Não foi possível gerar o ID# de solicitação! Verifique.',
                            ]
                        ],202);
                    }

                    // Coleta as questões e prepara para armazenar
                    $questionList   =   Question::where('id_tipo_processo',intval($idType))
                                        ->where('situacao',true)
                                        ->orderBy('ordem','asc')
                                        ->orderBy('titulo','asc')
                                        ->get();

                    $listQuestions  =   [];
                    
                    foreach ($questionList as $keyQuestion => $valueQuestion) {
                        // Check para respostas
                        switch ($variable) {
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
                            // Remove o registro já que não deu certo.
                            Chamado::where('id_chamado',$newSS->id_chamado)->delete();

                            return response()->json([
                                'error' =>  [
                                    'code'      =>  'AXONT0007',
                                    'message'   =>  'Não foi possível gerar o ID# de solicitação! Verifique.',
                                ]
                            ],202);
                        } // if($valueQuestion->obrigatorio && ($tmpQuestionResp == null || trim($tmpQuestionResp) == '')) { ... }

                        $tmpData    =   [
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

                    // Gera o código do item do chamado
                    foreach ($listQuestions as $keyList => $valueList) {
                        try {
                            $newSSItem              =   new ChamadoItem;
                            $newSSItem->id_chamado  =   $newSS->id_chamado;
                            $newSSItem->tipo        =   $valueList->tipo;
                            $newSSItem->id_questao  =   $valueList->id_questao;
                            $newSSItem->obrigatorio =   $valueList->obrigatorio;
                            $newSSItem->ordem       =   $valueList->ordem;
                            $newSSItem->questao     =   $valueList->questao;
                            $newSSItem->resposta    =   $valueList->resposta;
                            $newSSItem->data_cria   =   Carbon::now();
                            $newSSItem->data_alt    =   Carbon::now();
                            $newSSItem->usr_cria    =   Auth::user()->id;
                            $newSSItem->usr_alt     =   Auth::user()->id;
                            $newSSItem->save();
                        }
                        catch(Exception $error) {
                            // Remove o registro já que não deu certo.
                            ChamadoItem::where('id_chamado',$newSS->id_chamado)->delete();
                            Chamado::where('id_chamado',$newSS->id_chamado)->delete();

                            return response()->json([
                                'error' =>  [
                                    'code'      =>  'AXONT0009',
                                    'message'   =>  'Não foi possível armazenar os itens deste chamado! Verifique.',
                                ]
                            ],202);
                        } // catch(Exception $error) { ... }
                    }


                    // Salva os arquivos no sistema de chamados
                    
                } catch (Exception $error) {
                    return response()->json([
                        'error' =>  [
                            'code'      =>  'AXONT0008',
                            'message'   =>  'Não foi possível gerar a solicitação! Verifique.',
                        ]
                    ],202);
                }

                return response()->json([
                    'idChamado' =>  $newSS->id_chamado,
                    'url'       =>  $newSS->url."/Task/ID"."/".$newSS->id_chamado,
                ],200);

            }
            else if($typeSS == 1) {
                dd($request);
            }
        } // public function createData(Request $request) { ... }
    }
