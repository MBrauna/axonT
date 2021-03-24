<?php

    namespace App\Http\Controllers\Page\Task;

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
    use App\Models\Agendamento;
    use App\Models\AgendamentoItem;

    class Create extends Controller
    {
        public function startPage(Request $request) {
            try {
                $success    =   $request->input('success');
                $data       =   $request->input('data');

                if(!is_null($success) && !is_null($data)){
                    return view('page.solicitation.create',[
                        'success'   =>  $success,
                        'data'      =>  (object)$data,
                    ]);
                } // if(!is_null($success) && !is_null($data)){ ... }
                else {
                    return view('page.solicitation.create');
                } // else { ... }
            }
            catch(Exception $error) {
                return view('page.errorPage');
            }
        } // public function startPage(Request $request) { ... }

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
                    if(is_null($idCompany)) {
                        return redirect()->route('task.create',[
                            'success'   =>  false,
                            'data' =>  (object)[
                                'code'      =>  'AXONT0001',
                                'message'   =>  'Empresa não foi informada! Verifique.',
                            ],
                        ]);
                    }

                    if(is_null($idProccess)) return redirect()->route('task.create',[
                        'success'   =>  false,
                        'data' =>  (object)[
                            'code'      =>  'AXONT0002',
                            'message'   =>  'Processo não foi informado! Verifique.',
                        ],
                    ]);


                    if(is_null($idType)) return redirect()->route('task.create',[
                        'success'   =>  false,
                        'data' =>  (object)[
                            'code'      =>  'AXONT0003',
                            'message'   =>  'Tipo do processo não foi informado! Verifique.',
                        ],
                    ]);

                    if(is_null($title)) return redirect()->route('task.create',[
                        'success'   =>  false,
                        'data' =>  (object)[
                            'code'      =>  'AXONT0004',
                            'message'   =>  'Título não foi informado! Verifique.',
                        ],
                    ]);


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

                    if($validate <= 0 || $validate > 1) return redirect()->route('task.create',[
                        'success'   =>  false,
                        'data' =>  (object)[
                            'code'      =>  'AXONT0005',
                            'message'   =>  'Não foi possível gerar o chamado! Dados principais são inválidos.',
                        ],
                    ]);

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
                        return redirect()->route('task.create',[
                            'success'   =>  false,
                            'data' =>  (object)[
                                'code'      =>  'AXONT0006',
                                'message'   =>  'Não foi possível gerar o ID# de solicitação! Verifique.',
                            ]
                        ]);
                    }

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
                            // Remove o registro já que não deu certo.
                            Chamado::where('id_chamado',$newSS->id_chamado)->delete();

                            return redirect()->route('task.create',[
                                'success'   =>  false,
                                'data' =>  (object)[
                                    'code'      =>  'AXONT0007',
                                    'message'   =>  'Não foi possível gerar o ID# de solicitação! Verifique.',
                                ]
                            ]);
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

                            return redirect()->route('task.create',[
                                'success'   =>  false,
                                'data' =>  (object)[
                                    'code'      =>  'AXONT0009',
                                    'message'   =>  'Não foi possível armazenar os itens deste chamado! Verifique.',
                                ]
                            ]);
                        } // catch(Exception $error) { ... }
                    }


                    // Salva os arquivos no sistema de chamados
                    
                } catch (Exception $error) {
                    return redirect()->route('task.create',[
                        'success'   =>  false,
                        'data' =>  (object)[
                            'code'      =>  'AXONT0008',
                            'message'   =>  'Não foi possível gerar a solicitação! Verifique.',
                        ]
                    ]);
                }

                return redirect()->route('task.create',[
                    'success'   =>  true,
                    'data'      =>  (object)[
                        'idChamado' =>  $newSS->id_chamado,
                        'url'       =>  $newSS->url."/Task/ID"."/".$newSS->id_chamado,
                    ],
                ]);

            }
            else if($typeSS == 1) {

                $idProcessoReferencia   =   $request->input('idProcessoReferencia');
                $idProcessoOrigem       =   $request->input('idProcessoOrigem');
                $idProcessoDestino      =   $request->input('idProcessoDestino');
                $idSubProcessoOrigem    =   $request->input('idSubProcessoOrigem');
                $idSubProcessoDestino   =   $request->input('idSubProcessoDestino');
                $responsavelOrigem      =   $request->input('responsavelOrigem');
                $responsavelDestino     =   $request->input('responsavelDestino');
                $entregavel             =   $request->input('entregavel');
                $periodicidade          =   $request->input('periodicidade');
                $qtde_periodicidade     =   $request->input('qtde_periodicidade');
                $periodicidade_data     =   $request->input('periodicidade_data');
                $periodicidade_hora     =   $request->input('periodicidade_hora');
                $tipo                   =   $request->input('idTipo');

                if(is_null($idProcessoReferencia) || is_null($tipo) || is_null($entregavel) || is_null($periodicidade) || is_null($qtde_periodicidade)) return redirect()->route('task.create',[
                    'success'   =>  false,
                    'data' =>  (object)[
                        'code'      =>  'AXONT0010',
                        'message'   =>  'Dados principais não foram preenchidos! Verifique.',
                    ]
                ]);

                $data = null;

                if(is_null($periodicidade_data) && is_null($periodicidade_hora)) {
                    $data   =   Carbon::now();
                } // if(is_null($periodicidade_data) && is_null($periodicidade_hora)) { ... }
                elseif(!is_null($periodicidade_data) && is_null($periodicidade_hora)) {
                    $data   =   Carbon::parse($periodicidade_data.' '.Carbon::now()->hour.':'.Carbon::now()->minute);
                } // elseif if(is_null($periodicidade_data) && is_null($periodicidade_hora)) { ... }
                elseif(is_null($periodicidade_data) && !is_null($periodicidade_hora)) {
                    $data   =   Carbon::now()->startOfDay();
                    $data   =   $data->addHours(explode(':',$periodicidade_hora)[0]);
                    $data   =   $data->addMinutes(explode(':',$periodicidade_hora)[1]);
                } // elseif if(is_null($periodicidade_data) && is_null($periodicidade_hora)) { ... }
                else {
                    $data   =   Carbon::parse($periodicidade_data.' '.$periodicidade_hora);
                }

                $dataCriacao    =   Carbon::now();

                $agendamentoSS                              =   new Agendamento;
                $agendamentoSS->tipo                        =   intval($tipo);
                $agendamentoSS->id_processo_origem          =   is_null($idProcessoOrigem) ? null : intval($idProcessoOrigem);
                $agendamentoSS->id_processo_destino         =   is_null($idProcessoDestino) ? null : intval($idProcessoDestino);
                $agendamentoSS->id_tipo_processo_origem     =   is_null($idSubProcessoOrigem) ? null : intval($idSubProcessoOrigem);
                $agendamentoSS->id_tipo_processo_destino    =   is_null($idSubProcessoDestino) ? null : intval($idSubProcessoDestino);
                $agendamentoSS->data_inicial                =   $data;
                $agendamentoSS->proximo_agendamento         =   $data;
                $agendamentoSS->id_solicitante              =   Auth::user()->id;
                $agendamentoSS->id_usuario_origem           =   is_null($responsavelOrigem) ? null : intval($responsavelOrigem);
                $agendamentoSS->id_usuario_destino          =   is_null($responsavelDestino) ? null : intval($responsavelDestino);
                $agendamentoSS->id_processo_referencia      =   intval($idProcessoReferencia);
                $agendamentoSS->url                         =   $_SERVER['HTTP_HOST'];
                $agendamentoSS->titulo                      =   $entregavel;
                $agendamentoSS->tipo_objeto                 =   null; //intval($tipoObjeto);
                $agendamentoSS->meio                        =   null; //intval($meio);
                $agendamentoSS->periodicidade               =   intval($periodicidade);
                $agendamentoSS->qtde_periodicidade          =   intval($qtde_periodicidade);
                $agendamentoSS->situacao                    =   true;
                $agendamentoSS->data_cria                   =   $dataCriacao;
                $agendamentoSS->data_alt                    =   $dataCriacao;
                $agendamentoSS->usr_cria                    =   Auth::user()->id;
                $agendamentoSS->usr_alt                     =   Auth::user()->id;
                $agendamentoSS->save();


                $questionList   =   Questao::where('id_tipo_processo',intval($idSubProcessoOrigem))
                                    ->where('situacao',true)
                                    ->orderBy('ordem','asc')
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
                        // Remove o registro já que não deu certo.
                        return redirect()->route('task.create',[
                            'success'   =>  false,
                            'data' =>  (object)[
                                'code'      =>  'AXONT0007',
                                'message'   =>  'Não foi possível gerar o ID# de solicitação! Verifique.',
                            ]
                        ]);
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

                // Gera o código do item do chamado
                foreach ($listQuestions as $keyList => $valueList) {
                    try {
                        $newSSItem                  =   new AgendamentoItem;
                        $newSSItem->id_agendamento  =   $agendamentoSS->id_agendamento;
                        $newSSItem->tipo            =   $valueList->tipo;
                        $newSSItem->id_questao      =   $valueList->id_questao;
                        $newSSItem->obrigatorio     =   $valueList->obrigatorio;
                        $newSSItem->ordem           =   $valueList->ordem;
                        $newSSItem->questao         =   $valueList->questao;
                        $newSSItem->resposta        =   $valueList->realData;
                        $newSSItem->data_cria       =   Carbon::now();
                        $newSSItem->data_alt        =   Carbon::now();
                        $newSSItem->usr_cria        =   Auth::user()->id;
                        $newSSItem->usr_alt         =   Auth::user()->id;
                        $newSSItem->save();
                    }
                    catch(Exception $error) {
                        // Remove o registro já que não deu certo.
                        AgendamentoItem::where('id_agendamento',$agendamentoSS->id_agendamento)->delete();
                        Agendamento::where('id_chamado',$agendamentoSS->id_agendamento)->delete();

                        return redirect()->route('task.create',[
                            'success'   =>  false,
                            'data' =>  (object)[
                                'code'      =>  'AXONT0009',
                                'message'   =>  'Não foi possível armazenar os itens deste agendamento! Verifique.',
                            ]
                        ]);
                    } // catch(Exception $error) { ... }
                }
                
                return redirect()->route('task.create',[
                    'success'   =>  true,
                    'data'      =>  (object)[
                        'idChamado' =>  $agendamentoSS->id_agendamento,
                        'url'       =>  $agendamentoSS->url."/Task/Automatic"."/".$agendamentoSS->id_agendamento,
                    ],
                ]);
            }
        } // public function createData(Request $request) { ... }
    }
