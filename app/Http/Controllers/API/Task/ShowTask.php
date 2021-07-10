<?php

    namespace App\Http\Controllers\API\Task;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

    use Auth;
    use Carbon\Carbon;
    use Storage;

    use App\Models\Arquivo;
    use App\Models\Chamado;
    use App\Models\ChamadoItem;
    use App\Models\Empresa;
    use App\Models\Processo;
    use App\Models\TipoProcesso;
    use App\Models\Situacao;
    use App\Models\User;
    use App\Models\Tarefa;

    class ShowTask extends Controller {
        public function getID(Request $request) {
            try {
                $idTask =   $request->input('idTask');

                if(is_null($idTask) || !is_numeric($idTask)) {
                    return response()->json([],200);
                } // if(is_null($idTask) || !is_numeric($idTask)) { ... }

                // Coleta os dados do chamado
                $task   =   Chamado::where('id_chamado',$idTask)->first();

                if(is_null($task) || !isset($task->id_chamado)) {
                    return response()->json([],200);
                } // if(is_null($task) || !isset($task->id_chamado)) { ... }

                $listPermission =   [];

                foreach (getAccess() as $keyAccess => $valueAccess) {
                    foreach ($valueAccess->proccessData as $keyProccess => $valueProccess) {
                        if(!in_array($valueProccess->id_processo, $listPermission)) {
                            array_push($listPermission,$valueProccess->id_processo);
                        } // if(!in_array($valueProccess, $listPermission)) { ... }
                    } // foreach ($valueAccess->proccessData as $keyProccess => $valueProccess) { ... }
                } // foreach (getAccess() as $keyAccess => $valueAccess) { ... }

                if(is_null($task) || !isset($task->id_chamado) || !in_array($task->id_processo, $listPermission)) {
                    return redirect()->route('task.list');
                } // if(is_null($chamado) || !isset($chamado->id_chamado) || !in_array($chamado->id_processo, $listPermission)) { ... }

                $tmpTipoProcesso    =   TipoProcesso::where('id_tipo_processo',$task->id_tipo_processo)->first();
                $tmpProcesso        =   Processo::where('id_processo',$task->id_processo)->first();

                $tmpProcesso->descriptions  =   (object)[
                    'responsavel'   =>  is_null($tmpProcesso->id_usuario_responsavel) ? null : User::where('id',$tmpProcesso->id_usuario_responsavel)->first(),
                ];

                $task->descriptions =   (object)[
                    'empresa'           =>  is_null($task->id_empresa) ? null : Empresa::where('id_empresa',$task->id_empresa)->first(),
                    'processo'          =>  $tmpProcesso,
                    'tipoProcesso'      =>  $tmpTipoProcesso,
                    'situacao'          =>  is_null($task->id_situacao) ? null : Situacao::where('id_situacao',$task->id_situacao)->first(),
                    'solicitante'       =>  is_null($task->id_solicitante) ? null : User::where('id',$task->id_solicitante)->first(),
                    'responsavel'       =>  is_null($task->id_responsavel) ? null : User::where('id',$task->id_responsavel)->first(),
                    'dataVencimento'    =>  is_null($task->data_vencimento) ? null : Carbon::parse($task->data_vencimento)->format('d/m/Y H:i'),
                    'dataConclusao'     =>  is_null($task->data_conclusao) ? 'Em atendimento' : Carbon::parse($task->data_conclusao)->format('d/m/Y H:i'),
                    'dataCria'          =>  Carbon::parse($task->data_cria)->format('d/m/Y H:i'),
                    'dataAlt'           =>  Carbon::parse($task->data_alt)->format('d/m/Y H:i'),
                    'prazoContratado'   =>  Carbon::parse($task->data_criacao)->diff(Carbon::parse($task->data_criacao)->addMinutes($tmpTipoProcesso->sla))->format('%ya %mm %dd %H:%I:%S'),
                    'prazoAtribuido'    =>  Carbon::parse($task->data_criacao)->diff(Carbon::parse($task->data_vencimento))->format('%ya %mm %dd %H:%I:%S'),
                    'prazo'             =>  (is_null($task->data_conclusao) ? (Carbon::now()->greaterThan(Carbon::parse($task->data_vencimento)) ? Carbon::now()->diff(Carbon::parse($task->data_vencimento))->format('%Ya %mm %dd %H:%I:%S') : Carbon::now()->diff(Carbon::parse($task->data_vencimento))->format('%ya %mm %dd %H:%I:%S')) : Carbon::parse($task->data_criacao)->diff(Carbon::parse($task->data_conclusao))->format('%ya %mm %dd %H:%I:%S')),
                    'prazoColor'        =>  (is_null($task->data_conclusao) ? (Carbon::now()->greaterThan(Carbon::parse($task->data_vencimento)) ? 'text-danger' : 'text-success') : 'text-success'),
                ];


                $task->itemChamado  =   ChamadoItem::where('id_chamado',$task->id_chamado)
                                        ->orderBy('ordem','asc')
                                        ->orderBy('id_chamado_item','asc')
                                        ->get();

                $task->taskEntry    =   Tarefa::where('id_chamado',$task->id_chamado)
                                        ->orderBy('id_tarefa','asc')
                                        ->get();
                $listIDTasksEntry   =   Tarefa::where('id_chamado',$task->id_chamado)->select('id_tarefa');

                $task->archives     =   Arquivo::where('id_chamado',$task->id_chamado)
                                        ->orWhereIn('id_tarefa',$listIDTasksEntry)
                                        ->orderBy('id_arquivo','asc')
                                        ->get();

                foreach ($task->taskEntry as $keyData => $valueData) {
                    $tmpListFiles                           =   Arquivo::where('id_tarefa',$valueData->id_tarefa)
                                                                ->orderBy('id_arquivo','asc')
                                                                ->get();
                    $listFilesTmp                           =   [];
                    foreach ($tmpListFiles as $keyFiles => $valueFiles) {
                        $valueFiles->url    =   Storage::url('tarefa/'.$valueData->nome_servidor);
                        array_push($listFilesTmp, $valueFiles);
                    } // foreach ($tmpListFiles as $keyFiles => $valueFiles) { ... }

                    $task->taskEntry[$keyData]->allFiles    =   $listFilesTmp;

                    $task->taskEntry[$keyData]->descriptions    =   (object)[
                        'situacaoAnt'   =>  is_null($valueData->id_situacao_anterior) ? null : Situacao::where('id_situacao',$valueData->id_situacao_anterior)->first(),
                        'situacaoAtr'   =>  is_null($valueData->id_situacao_atribuida) ? null : Situacao::where('id_situacao',$valueData->id_situacao_atribuida)->first(),
                        'usrAnt'        =>  is_null($valueData->id_usuario_anterior) ? null : User::where('id',$valueData->id_usuario_anterior)->first(),
                        'usrAtr'        =>  is_null($valueData->id_usuario_atribuido) ? null : User::where('id',$valueData->id_usuario_atribuido)->first(),
                        'dtVencAnt'     =>  is_null($valueData->data_venc_anterior) ? null : Carbon::parse($valueData->data_venc_anterior)->format('d/m/Y H:i'),
                        'dtVencAtr'     =>  is_null($valueData->data_venc_atribuida) ? null : Carbon::parse($valueData->data_venc_atribuida)->format('d/m/Y H:i'),
                        'resp'          =>  is_null($valueData->usr_cria) ? null : User::where('id',$valueData->usr_cria)->first(),
                    ];
                } // foreach ($task->taskEntry as $keyData => $valueData) { ... }

                foreach ($task->archives as $keyData => $valueData) {
                    $task->archives[$keyData]->createdBy    =   User::where('id',$valueData->usr_cria)->first();

                    if(is_null($valueData->id_tarefa)) {
                        $task->archives[$keyData]->url      =   Storage::url('chamado/'.$valueData->nome_servidor);
                    } // if(is_null($valueData->id_tarefa)) { ... }
                    else {
                        $task->archives[$keyData]->url      =   Storage::url('tarefa/'.$valueData->nome_servidor);
                    } // else { ... }
                } // foreach ($task->archives as $keyData => $valueData) { ... }


                foreach ($task->itemChamado as $keyData => $valueData) {
                    switch ($valueData->tipo) {
                        case 'user':
                            if(is_null($valueData->resposta)) {
                                $task->itemChamado[$keyData]->respostaFormatada =   'Nenhum usuário selecionado';
                            } // if(is_null($valueData->resposta)) { ... }
                            else {
                                $task->itemChamado[$keyData]->respostaFormatada =   (User::where('id',intval($valueData->resposta))->first())['name'];
                            } // else { ... }
                            break;
                        case 'date':
                            if(is_null($valueData->resposta)) {
                                $task->itemChamado[$keyData]->respostaFormatada =   'Período não informado';
                            } // if(is_null($valueData->resposta)) { ... }
                            else {
                                $task->itemChamado[$keyData]->respostaFormatada =   Carbon::parse($valueData->resposta)->format('d/m/Y H:i');
                            } // else { ... }
                            break;
                        case 'datetime':
                            if(is_null($valueData->resposta)) {
                                $task->itemChamado[$keyData]->respostaFormatada =   'Período não informado';
                            } // if(is_null($valueData->resposta)) { ... }
                            else {
                                $task->itemChamado[$keyData]->respostaFormatada =   Carbon::parse($valueData->resposta)->format('d/m/Y H:i');
                            } // else { ... }
                            break;
                        default:
                            $task->itemChamado[$keyData]->respostaFormatada =   $valueData->resposta;
                            break;
                    } // switch ($valueData->tipo) { ... }
                } // foreach ($task->itemChamado as $keyData => $valueData) { ... }

                return response()->json($task,200);
            } // try { ... }
            catch(Exception $error) {
                return response()->json([],200);
            } // catch(Exception $error) { ... }
        } // public function getID(Request $request) { ... }
    } // class ShowTask extends Controller { ... }
