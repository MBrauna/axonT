<?php

    namespace App\Http\Controllers\Page\Task;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

    use Auth;
    use DB;
    use Storage;
    use Carbon\Carbon;

    use App\Models\Agendamento;
    use App\Models\AgendamentoItem;
    use App\Models\Chamado;
    use App\Models\ChamadoItem;
    use App\Models\Configuracao;
    use App\Models\Empresa;
    use App\Models\Processo;
    use App\Models\Questao;
    use App\Models\TipoProcesso;
    use App\Models\User;

    class Create extends Controller
    {
        public function startPage(Request $request) {
            try {
                $typeData   =   $request->input('id_configuracao');

                if(is_null($typeData)) {
                    // Se não tiver um código de configuração então é o primeiro acesso, envia para a página de cadastro
                    return view('page.solicitation.create');
                } // if(is_null($typeData)) { ... }
                else {
                    $typeData   =   intval($typeData);

                    switch ($typeData) {
                        case 1:
                            // Configuração para ID=1 --> Solicitação de serviço
                            return view('page.solicitation.createService');
                            break;
                        case 2:
                            // Configuração para ID=1 --> Troca de objetos
                            return view('page.solicitation.createObject');
                            break;
                        default:
                            return view('page.solicitation.create');
                            break;
                    } // switch ($typeData) { ... }
                } // else { ... }
            }
            catch(Exception $error) {
                return view('page.errorPage');
            }
        } // public function startPage(Request $request) { ... }

        public function createData(Request $request) {
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

                        return redirect()->route('task.create');
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

                        return redirect()->route('task.create');
                    } // catch(Exception $error) { ... }
                }


                // Salva os arquivos no sistema de chamados
                $files = $request->file('arquivoBPMS');
                if($files){
                    foreach($files as $chave => $file) {
                        if($file->isValid()) {
                            $nomeServidor       =   Carbon::now()->timestamp.'-'.$chave.'.'.$file->getClientOriginalExtension();

                            DB::beginTransaction();
                            DB::table('arquivo')
                            ->insert([
                                'id_chamado'    =>  $newSS->id_chamado,
                                'id_usuario'    =>  Auth::user()->id,
                                'nome_servidor' =>  $nomeServidor,
                                'nome_arquivo'  =>  $file->getClientOriginalName(),
                                'extensao'      =>  $file->getClientOriginalExtension(),
                                'mime'          =>  $file->getMimeType(),
                                'tamanho'       =>  $file->getSize(),
                                'data_cria'     =>  Carbon::now(),
                                'data_alt'      =>  Carbon::now(),
                                'usr_cria'      =>  Auth::user()->id,
                                'usr_alt'       =>  Auth::user()->id,
                            ]);
                            DB::commit();

                            $upload = $file->storeAs('chamado', $nomeServidor);
                        } // if($file->isValid()) { ... }
                    }
                }


            } catch (Exception $error) {
                ChamadoItem::where('id_chamado',$newSS->id_chamado)->delete();
                Chamado::where('id_chamado',$newSS->id_chamado)->delete();
                return redirect()->route('task.create');
            }

            return redirect()->route('task.idTask', ['idTask' => $newSS->id_chamado]);
        } // public function createData(Request $request) { ... }

        public function createObject(Request $request) {
            try {
                $typeProccess       =   intval($request->input('typeProccess',0));

                $originCompany      =   $request->input('originCompany');
                $originProccess     =   $request->input('originProccess');
                $originType         =   $request->input('originType');
                $originResponsible  =   $request->input('originResponsible');

                $destinyCompany     =   $request->input('destinyCompany');
                $destinyProccess    =   $request->input('destinyProccess');
                $destinyType        =   $request->input('destinyType');
                $destinyResponsible =   $request->input('destinyResponsible');

                $entregavel         =   $request->input('entregavel');
                $periodicidade      =   $request->input('periodicidade');
                $qtde_periodicidade =   $request->input('qtde_periodicidade');
                $periodicidade_data =   $request->input('periodicidade_data');
                $periodicidade_hora =   $request->input('periodicidade_hora');

                $fileList           =   $request->file('arquivoBPMS');

                // # --- # --- # --- # --- # --- # --- # --- # --- # --- # --- # --- # --- # --- # --- # --- # --- #
                // Lista de dados obrigatórios do sistema
                // # --- # --- # --- # --- # --- # --- # --- # --- # --- # --- # --- # --- # --- # --- # --- # --- #
                switch ($typeProccess) {
                    case 1:
                        // Para informações de entrada
                        if(is_null($originCompany) || is_null($originProccess) || is_null($originType)) {
                            return back();
                        } // if(is_null($originCompany) || is_null($originProccess) || is_null($originType)) { ... }
                        break;
                    case 2:
                        // Para informações de saída
                        if(is_null($originCompany) || is_null($originProccess) || is_null($originType)) {
                            return back();
                        } // if(is_null($originCompany) || is_null($originProccess) || is_null($originType)) { ... }

                        if(is_null($destinyCompany) || is_null($destinyProccess) || is_null($destinyType)) {
                            return back();
                        } // if(is_null($destinyCompany) || is_null($destinyProccess) || is_null($destinyType)) { ... }
                        break;
                    default:
                        return back();
                        break;
                } // switch ($typeProccess) { ... }

                if(is_null($entregavel) || is_null($qtde_periodicidade) || is_null($periodicidade_data) || is_null($periodicidade_hora)) {
                    return back();
                } // if(is_null($entregavel) || is_null($qtde_periodicidade) || is_null($periodicidade_data) || is_null($periodicidade_hora)) { ... }

                // # --- # --- # --- # --- # --- # --- # --- # --- # --- # --- # --- # --- # --- # --- # --- # --- #
                // Lista de dados obrigatórios do sistema
                // # --- # --- # --- # --- # --- # --- # --- # --- # --- # --- # --- # --- # --- # --- # --- # --- #

                $questionData       =   Questao::where('id_tipo_processo',$originType)
                                        ->where('situacao',true)
                                        ->orderBy('ordem','asc')
                                        ->get();

                foreach ($questionData as $keyQuestion => $valueQuestion) {
                    if($valueQuestion->tipo == 'datetime') {
                        $dataHora       =   $request->input('idQuestion_'.$valueQuestion->id_questao.'_data').' '.$request->input('idQuestion_'.$valueQuestion->id_questao.'_hora');

                        $questionData[$keyQuestion]->valuesRequest  =   (object)[
                            'original'  =>  $dataHora,
                            'parsed'    =>  Carbon::parse($dataHora)
                        ]; // $questionData[$keyQuestion]->valuesRequest  =   (object)[ ... ]
                    }
                    elseif($valueQuestion->tipo == 'date') {
                        $questionData[$keyQuestion]->valuesRequest  =   (object)[
                            'original'  =>  $request->input('idQuestion_'.$valueQuestion->id_questao),
                            'parsed'    =>  Carbon::parse($request->input('idQuestion_'.$valueQuestion->id_questao))->startOfDay(),
                        ]; // $questionData[$keyQuestion]->valuesRequest  =   (object)[ ... ]
                    }
                    elseif($valueQuestion->tipo == 'user') {
                        $userData   =   User::where('id',$request->input('idQuestion_'.$valueQuestion->id_questao))->first();

                        if(is_null($userData)) {
                            $questionData[$keyQuestion]->valuesRequest  =   (object)[
                                'original'  =>  $request->input('idQuestion_'.$valueQuestion->id_questao),
                                'parsed'    =>  $request->input('idQuestion_'.$valueQuestion->id_questao),
                            ]; // $questionData[$keyQuestion]->valuesRequest  =   (object)[ ... ]
                        } // if(is_null($userData)) { ... }
                        else {
                            $questionData[$keyQuestion]->valuesRequest  =   (object)[
                                'original'  =>  $request->input('idQuestion_'.$valueQuestion->id_questao),
                                'parsed'    =>  $userData->name,
                            ]; // $questionData[$keyQuestion]->valuesRequest  =   (object)[ ... ]
                        } // else { ... }
                    }
                    else  {
                        $questionData[$keyQuestion]->valuesRequest  =   (object)[
                            'original'  =>  $request->input('idQuestion_'.$valueQuestion->id_questao),
                            'parsed'    =>  $request->input('idQuestion_'.$valueQuestion->id_questao),
                        ]; // $questionData[$keyQuestion]->valuesRequest  =   (object)[ ... ]
                    }
                } // foreach ($questionData as $keyQuestion => $valueQuestion) { ... }


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
                } // else { ... }

                $schudule                               =   new Agendamento;
                $schudule->tipo                         =   intval($typeProccess);
                $schudule->id_processo_origem           =   is_null($originProccess) ? null : intval($originProccess);
                $schudule->id_processo_destino          =   is_null($destinyProccess) ? null : intval($destinyProccess);
                $schudule->id_tipo_processo_origem      =   is_null($originType) ? null : intval($originType);
                $schudule->id_tipo_processo_destino     =   is_null($destinyType) ? null : intval($destinyType);
                $schudule->data_inicial                 =   $data;
                $schudule->data_final                   =   Carbon::now();
                $schudule->proximo_agendamento          =   $data;
                $schudule->qtde_criado                  =   0;
                $schudule->aprova_origem                =   false;
                $schudule->aprova_destino               =   false;
                $schudule->id_solicitante               =   Auth::user()->id;
                $schudule->id_usuario_origem            =   is_null($originResponsible) ? null : intval($originResponsible);
                $schudule->id_usuario_destino           =   is_null($destinyResponsible) ? null : intval($destinyResponsible);
                $schudule->id_processo_referencia       =   null;//intval($idProcessoReferencia);
                $schudule->url                          =   $_SERVER['HTTP_HOST'];
                $schudule->titulo                       =   $entregavel;
                $schudule->tipo_objeto                  =   null; //intval($tipoObjeto),
                $schudule->meio                         =   null; //intval($meio),
                $schudule->periodicidade                =   intval($periodicidade);
                $schudule->qtde_periodicidade           =   intval($qtde_periodicidade);
                $schudule->situacao                     =   true;
                $schudule->data_cria                    =   Carbon::now();
                $schudule->data_alt                     =   Carbon::now();
                $schudule->usr_cria                     =   Auth::user()->id;
                $schudule->usr_alt                      =   Auth::user()->id;
                $schudule->save();


                foreach ($questionData as $keyData => $valueData) {
                    $schuduleItem                   =   new AgendamentoItem;
                    $schuduleItem->id_agendamento   =   $schudule->id_agendamento;
                    $schuduleItem->tipo             =   $valueData->tipo;
                    $schuduleItem->id_questao       =   $valueData->id_questao;
                    $schuduleItem->obrigatorio      =   $valueData->obrigatorio;
                    $schuduleItem->questao          =   $valueData->titulo;
                    $schuduleItem->resposta         =   $valueData->valuesRequest->parsed;
                    $schuduleItem->original         =   $valueData->valuesRequest->original;
                    $schuduleItem->ordem            =   $valueData->ordem;
                    $schuduleItem->situacao         =   true;
                    $schuduleItem->data_cria        =   Carbon::now();
                    $schuduleItem->data_alt         =   Carbon::now();
                    $schuduleItem->usr_alt          =   Auth::user()->id;
                    $schuduleItem->usr_cria         =   Auth::user()->id;
                    $schuduleItem->save();
                } // foreach ($questionData as $keyData => $valueData) { ... }


                if($fileList){
                    foreach($fileList as $chave => $file) {
                        if($file->isValid()) {
                            $nomeServidor       =   Carbon::now()->timestamp.'-'.$chave.'.'.$file->getClientOriginalExtension();

                            DB::beginTransaction();
                            DB::table('arquivo')
                            ->insert([
                                'id_agendamento'=>  $schuduleItem->id_agendamento,
                                'id_usuario'    =>  Auth::user()->id,
                                'nome_servidor' =>  $nomeServidor,
                                'nome_arquivo'  =>  $file->getClientOriginalName(),
                                'extensao'      =>  $file->getClientOriginalExtension(),
                                'mime'          =>  $file->getMimeType(),
                                'tamanho'       =>  $file->getSize(),
                                'data_cria'     =>  Carbon::now(),
                                'data_alt'      =>  Carbon::now(),
                                'usr_cria'      =>  Auth::user()->id,
                                'usr_alt'       =>  Auth::user()->id,
                            ]);
                            DB::commit();

                            $upload = $file->storeAs('chamado', $nomeServidor);
                        } // if($file->isValid()) { ... }
                    }
                }

                return redirect()->route('task.listAutomatic');
            } // try { ... }
            catch(Exception $error) {
                return redirect()->route('mainPage');
            } // catch(Exception $error) { ... }
        } // public function createObject(Request $request) { ... }

        public function editObject(Request $request) {
            try {
                if(!isset($request->idTask)) {
                    return back();
                } // if(!isset($request->idTask)) { ... }
    
                if(!isset($request->entregavel) || trim($request->entregavel) == "") {
                    return back();
                } // if(!isset($request->entregavel) || trim($request->entregavel) == "") { ... }
    
                if(!isset($request->periodicidade)) {
                    return back();
                }
    
                if(is_null($request->periodicidade_data) && is_null($request->periodicidade_hora)) {
                    $data   =   Carbon::now();
                } // if(is_null($periodicidade_data) && is_null($periodicidade_hora)) { ... }
                elseif(!is_null($request->periodicidade_data) && is_null($request->periodicidade_hora)) {
                    $data   =   Carbon::parse($request->periodicidade_data.' '.Carbon::now()->hour.':'.Carbon::now()->minute);
                } // elseif if(is_null($periodicidade_data) && is_null($periodicidade_hora)) { ... }
                elseif(is_null($request->periodicidade_data) && !is_null($request->periodicidade_hora)) {
                    $data   =   Carbon::now()->startOfDay();
                    $data   =   $data->addHours(explode(':',$request->periodicidade_hora)[0]);
                    $data   =   $data->addMinutes(explode(':',$request->periodicidade_hora)[1]);
                } // elseif if(is_null($periodicidade_data) && is_null($periodicidade_hora)) { ... }
                else {
                    $data   =   Carbon::parse($request->periodicidade_data.' '.$request->periodicidade_hora);
                } // else { ... }
    
                $schudule                       =   Agendamento::find($request->idTask);
                $schudule->titulo               =   trim($request->entregavel);
                $schudule->periodicidade        =   intval($request->periodicidade);
                $schudule->qtde_periodicidade   =   intval($request->qtde_periodicidade);
                $schudule->data_inicial         =   $data;
                $schudule->save();
    
                $schuduleItem                   =   AgendamentoItem::where('id_agendamento',$request->idTask)->get();
    
                foreach ($schuduleItem as $keyQuestion => $valueQuestion) {
                    $parsed     =   null;
                    $original   =   null;
    
    
                    if($valueQuestion->tipo == 'datetime') {
                        $dataHora       =   $request->input('idAgendamento_'.$valueQuestion->id_questao.'_data').' '.$request->input('idAgendamento_'.$valueQuestion->id_questao.'_hora');
    
                        $parsed         =   Carbon::parse($dataHora);
                        $original       =   $dataHora;
                    }
                    elseif($valueQuestion->tipo == 'date') {
                        $parsed         =   Carbon::parse($request->input('idAgendamento_'.$valueQuestion->id_questao))->startOfDay();
                        $original       =   $request->input('idAgendamento_'.$valueQuestion->id_questao);
                    }
                    elseif($valueQuestion->tipo == 'user') {
                        $userData   =   User::where('id',$request->input('idAgendamento_'.$valueQuestion->id_questao))->first();
    
                        if(is_null($userData)) {
                            $parsed         =   $request->input('idAgendamento_'.$valueQuestion->id_questao);
                            $original       =   $request->input('idAgendamento_'.$valueQuestion->id_questao);
                        } // if(is_null($userData)) { ... }
                        else {
                            $parsed         =   $userData->name;
                            $original       =   $request->input('idAgendamento_'.$valueQuestion->id_questao);
                        } // else { ... }
                    }
                    else  {
                        $parsed         =   $request->input('idAgendamento_'.$valueQuestion->id_questao);
                        $original       =   $request->input('idAgendamento_'.$valueQuestion->id_questao);
                    }
    
    
                    $tmpSchuduleItem            =   AgendamentoItem::find($valueQuestion->id_agendamento_item);
                    $tmpSchuduleItem->original  =   $original;
                    $tmpSchuduleItem->resposta  =   $parsed;
                    $tmpSchuduleItem->save();
                } // foreach ($schuduleItem as $keyData => $valueData) { ... }
    
                return redirect()->route('task.listAutomatic');
            } // try { ... }
            catch(Exception $error) {
                dd($error);
            }
        } // public function editObject(Request $request) { ... }
    } // class Create extends Controller { ... }
