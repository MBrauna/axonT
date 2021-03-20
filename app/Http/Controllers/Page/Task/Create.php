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

    class Create extends Controller
    {
        public function startPage(Request $request) {
            try {

                return view('page.solicitation.create',[
                    
                ]);
            }
            catch(Exception $error) {
                return view('page.errorPage');
            }
        }

        public function createData(Request $request) {
            $typeSS =   $request->input('typeSS','0');

            if($typeSS == 0) {
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
                $questionList   =   Question::where('id_tipo_processo',intval($idType))
                                    ->where('situacao',true)
                                    ->orderBy('ordem','asc')
                                    ->orderBy('titulo','asc')
                                    ->get();
                
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
                    $newSS->url                 =   '1nt.1nesstech.com.br';
                    $newSS->titulo              =   $title;
                    $newSS->situacao            =   true;
                    $newSS->data_cria           =   $createDate;
                    $newSS->data_alt            =   $createDate;
                    $newSS->usr_cria            =   Auth::user()->id;
                    $newSS->usr_alt             =   Auth::user()->id;
                }
                catch(Exception $error) {
                    return response()->json([
                        'error' =>  [
                            'code'      =>  'AXONT0006',
                            'message'   =>  'Não foi possível gerar o ID# de solicitação! Verifique.',
                        ]
                    ],200);
                }

                $listQuestions  =   [];
                
                foreach ($questionList as $keyQuestion => $valueQuestion) {
                    $tmpData    =   [
                        'tipo'          =>  1,
                        'id_questao'    =>  1,
                        'obrigatorio'   =>  1,
                        'ordem'         =>  1,
                        'questao'       =>  1,
                        'resposta'      =>  1,
                    ];
                } // foreach ($questionList as $keyQuestion => $valueQuestion) { ... }

            }
            else if($typeSS == 1) {
                dd($request);
            }
        } // public function createData(Request $request) { ... }
    }
