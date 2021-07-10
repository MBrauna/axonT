<?php

    namespace App\Http\Controllers\Page\Cards;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Auth;
    use DB;
    use Carbon\Carbon;

    use App\Models\Chamado;
    use App\Models\Situacao;
    use App\Models\Tarefa;

    class CardsData extends Controller
    {
        public function listCard(Request $request) {
            try {
                return view('page.card.list');
            } // try { ... }
            catch(Exception $error) {
                return redirect()->route('mainPage');
            } // catch(Exception $error) { ... }
        } // public function listTask(Request $request) { ... }

        public function saveCard(Request $request) {
            try {
                $idChamado      =   $request->input('idChamado');
                $idSituacao     =   $request->input('id_situacao');
                $idResponsavel  =   $request->input('id_responsavel');
                $dataVencimento =   $request->input('data_vencimento');
                $horaVencimento =   $request->input('hora_vencimento');
                $entrada        =   $request->input('entrada');
                $arquivos       =   $request->file('arquivoBPMS');

                if(is_null($idChamado) || !is_numeric($idChamado)) return redirect()->route('card.list');
                if(is_null($idSituacao) || !is_numeric($idSituacao)) return redirect()->route('card.list');

                $conteudoChamado=   Chamado::where('id_chamado',$idChamado)->first();
                if(is_null($conteudoChamado)) return redirect()->route('card.list');
                $situacaoNext   =   Situacao::where('id_situacao',$idSituacao)->first();
                $situacaoAtual  =   Situacao::where('id_situacao',$conteudoChamado->id_situacao)->first();
                $dataChamado    =   Chamado::find($idChamado);

                // Sempre vai limpar se não for manter
                if($situacaoNext->limpar_responsavel) {
                    $dataChamado->id_responsavel    =   null;
                    $dataChamado->data_alt          =   Carbon::now();
                    $dataChamado->usr_alt           =   Auth::user()->id;
                } // if($situacaoNext->limpar_responsavel) { ... }

                if(!is_null($idResponsavel) && is_numeric($idResponsavel)) {
                    $dataChamado->id_responsavel    =   intval($idResponsavel);
                    $dataChamado->data_alt          =   Carbon::now();
                    $dataChamado->usr_alt           =   Auth::user()->id;
                } // if(!is_null($idResponsavel) && is_numeric($idResponsavel)) { ... }

                $dataVenc = null;
                if($situacaoNext->data_vencimento) {
                    if(is_null($dataVencimento) && is_null($horaVencimento)) {
                        $dataVenc   =   Carbon::parse($conteudoChamado->data_vencimento);
                    } // if(is_null($dataVencimento) && is_null($horaVencimento)) { ... }
                    elseif(is_null($dataVencimento) && !is_null($horaVencimento)) {
                        $dataVenc   =   Carbon::parse($dataVencimento)->endOfDay();
                    } // elseif(is_null($dataVencimento) && !is_null($horaVencimento)) { ... }
                    elseif(!is_null($dataVencimento) && is_null($horaVencimento)) {
                        $dataVenc   =   Carbon::parse($conteudoChamado->data_vencimento);
                    } // elseif(!is_null($dataVencimento) && is_null($horaVencimento)) { ... }
                    else {
                        $tmpHora    =   explode(':',$horaVencimento);
                        $dataVenc   =   Carbon::parse($dataVencimento)->startOfDay()->hour($tmpHora[0])->minute($tmpHora[1]);
                    }

                    $dataChamado->data_vencimento   =   $dataVenc;
                    $dataChamado->data_alt          =   Carbon::now();
                    $dataChamado->usr_alt           =   Auth::user()->id;
                } // if($situacaoNext->data_vencimento) { ... }

                if($situacaoNext->conclusiva) {
                    $dataChamado->situacao          =   false;
                    $dataChamado->data_conclusao    =   Carbon::now();
                    $dataChamado->data_alt          =   Carbon::now();
                    $dataChamado->usr_alt           =   Auth::user()->id;
                }

                // Dados da tarefa salvos
                $newTask                        =   new Tarefa;
                $newTask->id_chamado            =   intval($idChamado);
                $newTask->conteudo              =   trim($entrada);
                $newTask->id_situacao_anterior  =   $conteudoChamado->id_situacao;
                $newTask->id_situacao_atribuida =   $idSituacao;
                $newTask->id_usuario_anterior   =   $conteudoChamado->id_responsavel;
                $newTask->id_usuario_atribuido  =   $dataChamado->id_responsavel;
                $newTask->data_venc_anterior    =   Carbon::parse($conteudoChamado->data_vencimento);
                $newTask->data_venc_atribuida   =   $dataChamado->data_vencimento;
                $newTask->data_cria             =   Carbon::now();
                $newTask->data_alt              =   Carbon::now();
                $newTask->usr_cria              =   Auth::user()->id;
                $newTask->usr_alt               =   Auth::user()->id;
                $newTask->save();


                // Atualiza os dados do chamado
                $dataChamado->id_situacao       =   $situacaoNext->id_situacao;
                $dataChamado->data_alt          =   Carbon::now();
                $dataChamado->usr_alt           =   Auth::user()->id;
                $dataChamado->save();

                foreach($arquivos as $chave => $file) {
                    if($file->isValid()) {
                        $nomeServidor       =   Carbon::now()->timestamp.'-'.$chave.'.'.$file->getClientOriginalExtension();

                        DB::beginTransaction();
                        DB::table('arquivo')
                        ->insert([
                            'id_tarefa'     =>  $newTask->id_tarefa,
                            'id_chamado'    =>  $newTask->id_chamado,
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

                        $upload = $file->storeAs('tarefa', $nomeServidor);
                    } // if($file->isValid()) { ... }
                } // foreach($arquivos as $chave => $file) { ... }

                return redirect()->route('card.list');
            } // try { ... }
            catch(Exception $error) {
                return redirect()->route('card.list');
            } // catch(Exception $error) { ... }
        } // public function saveCard(Request $request) { ... }
    }
