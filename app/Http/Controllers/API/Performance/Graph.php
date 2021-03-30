<?php

    namespace App\Http\Controllers\API\Performance;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

    use Auth;
    use Carbon\Carbon;
    use DB;

    use App\Models\Chamado;
    use App\Models\Empresa;
    use App\Models\Processo;
    use App\Models\UsuarioConfig;
    use App\Models\Perfil;

    class Graph extends Controller {
        public function getGraphs(Request $request) {
            try {
                $empresa    =   getCompanyPermission();

                // Inicia os dados dos filtros
                if(!is_null($request->idCompany)) {
                    $tmpCompany =   [];
                    foreach ($empresa as $keyEmpresa => $valueEmpresa) {
                        if($valueEmpresa->id_empresa == $request->idCompany) {
                            array_push($tmpCompany, $valueEmpresa);
                        } // if($valueEmpresa->id_empresa == $request->idCompany) { ... }
                    } // foreach ($empresa as $keyEmpresa => $valueEmpresa) { ... }

                    // Se for necessário filtro ... então limpa os dados.
                    $empresa    =   $tmpCompany;
                } // if(isset($request->idCompany) && !is_null($request->idCompany) && $filtro) { ... }

                foreach ($empresa as $key => $value) {
                    $empresa[$key]->graphs  =   [
                        $this->graph1($request, $value->id_empresa),
                        $this->graph2($request, $value->id_empresa),
                        $this->graph3($request, $value->id_empresa),
                        $this->graph4($request, $value->id_empresa),
                        $this->graph5($request, $value->id_empresa),
                    ];
                } // foreach ($empresa as $key => $value) { ... }

                return response()->json($empresa,200,['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],JSON_UNESCAPED_UNICODE);
            }
            catch(Exception $error){
                return response()->json([
                    'error' =>  [
                        "code"      =>  "ERROR0001",
                        "message"   =>  "Não foi possível gerar os gráficos para a empresa [".$request->idEmpresa."]",
                    ]
                ],200,['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],JSON_UNESCAPED_UNICODE);
            }
        } // public function getGraphs(Request $request) { ... }

        private function graph1(Request $request, $empresa) {

            $vReturn    =   (object)[
                "content"   =>  [
                    "id"        =>  1,
                ],
                "title"     =>  (object)[
                    "text"  =>  "Comportamento das solicitações de serviço",
                    "align" =>  "left"
                ],
                "subtitle"     =>  (object)[
                    "text"  =>  "",
                    "align" =>  "left"
                ],
                "colors"    =>  [
                    "#00296b",
                    "#730800",
                    "#004506",
                    "#e36600",
                    "#008dad",
                    "#00e5ff",
                    "#9900ff",
                    "#ee00ff",
                    "#004d16",
                    "#b34205",
                ],
                "fill"  =>  (object)[
                    "type"      =>  "gradient",
                    "gradient"  =>  (object)[
                        "shadeIntensity"    =>  1,
                        "inverseColors"     =>  false,
                        "opacityFrom"       =>  0.45,
                        "opacityTo"         =>  0.05,
                        "stops"             =>  [20, 100, 100, 100]
                    ],
                ],
                "series"    =>  [
                    (object)[
                        "name"  =>  "Criadas",
                        "data"  =>  [],
                    ],
                    (object)[
                        "name"  =>  "Atrasadas",
                        "data"  =>  [],
                    ],
                    (object)[
                        "name"  =>  "Concluídas",
                        "data"  =>  [],
                    ],
                    (object)[
                        "name"  =>  "Saldo",
                        "data"  =>  [],
                    ],
                ],
                "chart"     =>  (object)[
                    "height"    =>  350,
                    "type"      =>  "area",
                ],
                "dataLabels"=>  (object)[
                    "enabled"   =>  false,
                ],
                "stroke"    =>  (object)[
                    "curve"     =>  "smooth",
                ],
                "xaxis"     =>  (object)[
                    "type"      =>  "date",
                    "categories"=>  [],
                ],
                "tooltip"   =>  (object)[
                    "x"         =>  (object)[
                        "format"    =>  "dd/MM/yy",
                    ]
                ],
            ];

            try {
                // Datas para execução dos processos.
                $startDate  =   Carbon::now()->subMonths(1)->startOfDay();
                $endDate    =   Carbon::now();
                $refDate    =   $startDate;
                // Datas para execução dos processos.

                while($refDate->lessThanOrEqualTo($endDate)) {
                    // Marca a data da categoria
                    array_push($vReturn->xaxis->categories, $refDate->copy()->format('d/m/Y'));

                    // Coleta os dados da data de referência
                    $refIniDate =   $refDate->copy()->startOfDay();
                    $refEndDate =   $refDate->copy()->endOfDay();

                    if(Carbon::now()->lessThanOrEqualTo($refEndDate)) {
                        $refEndDate =   Carbon::now();
                    } // if(Carbon::now()->lessThanOrEqualTo($refEndDate)) { ... }

                    // -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- //

                    $criada     =   Chamado::where('id_empresa',$empresa)
                                    ->where('data_cria','<=',$refEndDate)
                                    ->where('data_cria','>=',$refIniDate)
                                    ->count();

                    $atrasada   =   Chamado::where('id_empresa',$empresa)
                                    ->whereNull('data_conclusao')
                                    ->where('data_vencimento','<=',$refEndDate)
                                    ->where('data_cria','<=',$refEndDate)
                                    ->count();

                    $concluida  =   Chamado::where('id_empresa',$empresa)
                                    ->whereNotNull('data_conclusao')
                                    ->where('data_conclusao','>=',$refIniDate)
                                    ->where('data_conclusao','<=',$refEndDate)
                                    ->count();

                    $saldo      =   Chamado::where('id_empresa',$empresa)
                                    ->where(function($query) use ($refIniDate, $refEndDate){
                                        $query->orWhere(function($query1) use($refIniDate, $refEndDate) {
                                            $query1->whereNull('data_conclusao');
                                            $query1->where('data_cria','<=',$refEndDate);
                                        });

                                        $query->orWhere(function($query1) use($refIniDate, $refEndDate) {
                                            $query1->whereNotNull('data_conclusao');
                                            $query1->where('data_conclusao','>=',$refEndDate);
                                            $query1->where('data_cria','<=',$refEndDate);
                                        });
                                    })
                                    ->count();

                    // -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- //

                    array_push($vReturn->series[0]->data, $criada);
                    array_push($vReturn->series[1]->data, $atrasada);
                    array_push($vReturn->series[2]->data, $concluida);
                    array_push($vReturn->series[3]->data, $saldo);

                    // -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- //

                    // Percorre para a próxima data
                    $refDate    =   $refDate->copy()->addDays(1)->startOfDay();
                } // while($refDate->lessThanOrEqualTo($endDate)) { ... }
            }
            catch(Exception $error) {}

            return $vReturn;

        }

        private function graph2(Request $request, $empresa) {

            $vReturn    =   (object)[
                "content"   =>  [
                    "id"        =>  2,
                ],
                "title"     =>  (object)[
                    "text"  =>  "Comportamento das solicitações de serviço",
                    "align" =>  "left"
                ],
                "subtitle"     =>  (object)[
                    "text"  =>  "",
                    "align" =>  "left"
                ],
                "colors"    =>  [
                    "#00296b",
                    "#730800",
                    "#004506",
                    "#e36600",
                    "#008dad",
                    "#00e5ff",
                    "#9900ff",
                    "#ee00ff",
                    "#004d16",
                    "#b34205",
                ],
                "fill"  =>  (object)[
                    "type"      =>  "gradient",
                    "gradient"  =>  (object)[
                        "shadeIntensity"    =>  1,
                        "inverseColors"     =>  false,
                        "opacityFrom"       =>  0.45,
                        "opacityTo"         =>  0.05,
                        "stops"             =>  [20, 100, 100, 100]
                    ],
                ],
                "series"    =>  [
                    (object)[
                        "name"  =>  "Criadas",
                        "data"  =>  [],
                    ],
                    (object)[
                        "name"  =>  "Atrasadas",
                        "data"  =>  [],
                    ],
                    (object)[
                        "name"  =>  "Concluídas",
                        "data"  =>  [],
                    ],
                    (object)[
                        "name"  =>  "Saldo",
                        "data"  =>  [],
                    ],
                ],
                "chart"     =>  (object)[
                    "height"    =>  350,
                    "type"      =>  "area",
                ],
                "dataLabels"=>  (object)[
                    "enabled"   =>  false,
                ],
                "stroke"    =>  (object)[
                    "curve"     =>  "smooth",
                ],
                "xaxis"     =>  (object)[
                    "type"      =>  "date",
                    "categories"=>  [],
                ],
                "tooltip"   =>  (object)[
                    "x"         =>  (object)[
                        "format"    =>  "dd/MM/yy",
                    ]
                ],
            ];

            try {
                // Datas para execução dos processos.
                $startDate  =   Carbon::now()->subMonths(12)->startOfDay();
                $endDate    =   Carbon::now();
                $refDate    =   $startDate->copy();
                // Datas para execução dos processos.

                while($refDate->lessThanOrEqualTo($endDate)) {
                    // Marca a data da categoria
                    array_push($vReturn->xaxis->categories, $refDate->copy()->format('m/Y'));

                    // Coleta os dados da data de referência
                    $refIniDate =   $refDate->copy()->startOfMonth()->startOfDay();
                    $refEndDate =   $refDate->copy()->endOfMonth()->endOfDay();

                    if(Carbon::now()->lessThanOrEqualTo($refEndDate)) {
                        $refEndDate =   Carbon::now();
                    } // if(Carbon::now()->lessThanOrEqualTo($refEndDate)) { ... }

                    // -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- //

                    $criada     =   Chamado::where('id_empresa',$empresa)
                                    ->where('data_cria','<=',$refEndDate)
                                    ->where('data_cria','>=',$refIniDate)
                                    ->count();

                    $atrasada   =   Chamado::where('id_empresa',$empresa)
                                    ->whereNull('data_conclusao')
                                    ->where('data_vencimento','<=',$refEndDate)
                                    ->where('data_cria','<=',$refEndDate)
                                    ->count();

                    $concluida  =   Chamado::where('id_empresa',$empresa)
                                    ->whereNotNull('data_conclusao')
                                    ->where('data_conclusao','>=',$refIniDate)
                                    ->where('data_conclusao','<=',$refEndDate)
                                    ->count();

                    $saldo      =   Chamado::where('id_empresa',$empresa)
                                    ->where(function($query) use ($refIniDate, $refEndDate){
                                        $query->orWhere(function($query1) use($refIniDate, $refEndDate) {
                                            $query1->whereNull('data_conclusao');
                                            $query1->where('data_cria','<=',$refEndDate);
                                        });

                                        $query->orWhere(function($query1) use($refIniDate, $refEndDate) {
                                            $query1->whereNotNull('data_conclusao');
                                            $query1->where('data_conclusao','>=',$refEndDate);
                                            $query1->where('data_cria','<=',$refEndDate);
                                        });
                                    })
                                    ->count();

                    // -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- //

                    array_push($vReturn->series[0]->data, $criada);
                    array_push($vReturn->series[1]->data, $atrasada);
                    array_push($vReturn->series[2]->data, $concluida);
                    array_push($vReturn->series[3]->data, $saldo);

                    // -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- //

                    // Percorre para a próxima data
                    $refDate    =   $refDate->copy()->addMonths(1)->startOfMonth()->startOfDay();
                } // while($refDate->lessThanOrEqualTo($endDate)) { ... }
            }
            catch(Exception $error) {}

            return $vReturn;

        }

        private function graph3(Request $request, $empresa) {

            $vReturn    =   (object)[
                "content"   =>  [
                    "id"        =>  3,
                ],
                "title"     =>  (object)[
                    "text"  =>  "Solicitação de serviço",
                    "align" =>  "left"
                ],
                "subtitle"     =>  (object)[
                    "text"  =>  "Maior número de atendimentos atrasados",
                    "align" =>  "left"
                ],
                "colors"    =>  [
                    "#00296b",
                    "#730800",
                    "#004506",
                    "#e36600",
                    "#008dad",
                    "#00e5ff",
                    "#9900ff",
                    "#ee00ff",
                    "#004d16",
                    "#b34205",
                ],
                /*"fill"  =>  (object)[
                    "type"      =>  "gradient",
                    "gradient"  =>  (object)[
                        "shadeIntensity"    =>  1,
                        "inverseColors"     =>  false,
                        "opacityFrom"       =>  0.45,
                        "opacityTo"         =>  0.05,
                        "stops"             =>  [20, 100, 100, 100]
                    ],
                ],*/
                "series"    =>  [
                    (object)[
                        "name"  =>  "Criadas",
                        "data"  =>  [],
                    ],
                    (object)[
                        "name"  =>  "Atrasadas",
                        "data"  =>  [],
                    ],
                    (object)[
                        "name"  =>  "Concluídas",
                        "data"  =>  [],
                    ],
                    (object)[
                        "name"  =>  "Saldo",
                        "data"  =>  [],
                    ],
                ],
                "chart"     =>  (object)[
                    "height"    =>  350,
                    "type"      =>  "bar",
                ],
                "plotOptions"   =>  (object)[
                    "bar"   =>  (object)[
                        "horizontal"    =>  true,
                    ],
                ],
                "dataLabels"=>  (object)[
                    "enabled"   =>  false,
                ],
                "stroke"    =>  (object)[
                    "curve"     =>  "smooth",
                ],
                "xaxis"     =>  (object)[
                    "type"      =>  "date",
                    "categories"=>  [],
                ],
                "tooltip"   =>  (object)[
                    "x"         =>  (object)[
                        "format"    =>  "dd/MM/yy",
                    ]
                ],
            ];

            $vQtde      =   5;

            try {
                // Datas para execução dos processos.
                $startDate  =   Carbon::now()->subMonths(1)->startOfDay();
                $endDate    =   Carbon::now();
                $refDate    =   $startDate;

                // Datas para execução dos processos.
                $SSAtrasada =   Chamado::where('id_empresa',$empresa)
                                ->whereNull('data_conclusao')
                                ->where('data_vencimento','<=',Carbon::now())
                                ->select(
                                    'id_processo',
                                    DB::raw('count(1) as atrasada')
                                )
                                ->groupBy([
                                    'id_processo',
                                ])
                                ->orderBy('atrasada','desc')
                                ->get();
                
                foreach ($SSAtrasada as $keyGroup => $valueGroup) {
                    if($vQtde <= 0) break;

                    $proccess       =   Processo::where('id_processo',$valueGroup->id_processo)->first();

                    if(!isset($proccess) && is_null($proccess)) continue;

                    array_push($vReturn->xaxis->categories, $proccess->sigla);

                    $criada         =   Chamado::where('id_empresa',$empresa)
                                        ->where('id_processo',$valueGroup->id_processo)
                                        ->where('data_cria','>=',Carbon::now()->subMonths(1)->startOfDay())
                                        ->where('data_cria','<=',Carbon::now())
                                        ->count();
                    $concluida      =   Chamado::where('id_empresa',$empresa)
                                        ->where('id_processo',$valueGroup->id_processo)
                                        ->whereNotNull('data_conclusao')
                                        ->where('data_conclusao','>=',Carbon::now()->subMonths(1)->startOfDay())
                                        ->where('data_conclusao','<=',Carbon::now())
                                        ->count();
                    $saldo          =   Chamado::where('id_empresa',$empresa)
                                        ->where('id_processo',$valueGroup->id_processo)
                                        ->where(function($query){
                                            $query->orWhere(function($query1){
                                                $query1->whereNull('data_conclusao');
                                                $query1->where('data_cria','<=',Carbon::now());
                                            });

                                            $query->orWhere(function($query1){
                                                $query1->whereNotNull('data_conclusao');
                                                $query1->where('data_conclusao','>=',Carbon::now());
                                                $query1->where('data_cria','<=', Carbon::now());
                                            });
                                        })
                                        ->count();

                    // -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- //

                    array_push($vReturn->series[0]->data, $criada);
                    array_push($vReturn->series[1]->data, $valueGroup->atrasada);
                    array_push($vReturn->series[2]->data, $concluida);
                    array_push($vReturn->series[3]->data, $saldo);

                    // -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- //

                    $vQtde  =   $vQtde - 1;
                }
                
            }
            catch(Exception $error) {}

            return $vReturn;

        }

        private function graph4(Request $request, $empresa) {

            $vReturn    =   (object)[
                "content"   =>  [
                    "id"        =>  4,
                ],
                "title"     =>  (object)[
                    "text"  =>  "Solicitação de serviço",
                    "align" =>  "left"
                ],
                "subtitle"     =>  (object)[
                    "text"  =>  "Maior saldo de solicitações aguardando atendimento",
                    "align" =>  "left"
                ],
                "colors"    =>  [
                    "#00296b",
                    "#730800",
                    "#004506",
                    "#e36600",
                    "#008dad",
                    "#00e5ff",
                    "#9900ff",
                    "#ee00ff",
                    "#004d16",
                    "#b34205",
                ],
                /*"fill"  =>  (object)[
                    "type"      =>  "gradient",
                    "gradient"  =>  (object)[
                        "shadeIntensity"    =>  1,
                        "inverseColors"     =>  false,
                        "opacityFrom"       =>  0.45,
                        "opacityTo"         =>  0.05,
                        "stops"             =>  [20, 100, 100, 100]
                    ],
                ],*/
                "series"    =>  [
                    (object)[
                        "name"  =>  "Criadas",
                        "data"  =>  [],
                    ],
                    (object)[
                        "name"  =>  "Atrasadas",
                        "data"  =>  [],
                    ],
                    (object)[
                        "name"  =>  "Concluídas",
                        "data"  =>  [],
                    ],
                    (object)[
                        "name"  =>  "Saldo",
                        "data"  =>  [],
                    ],
                ],
                "chart"     =>  (object)[
                    "height"    =>  350,
                    "type"      =>  "bar",
                ],
                "plotOptions"   =>  (object)[
                    "bar"   =>  (object)[
                        "horizontal"    =>  true,
                    ],
                ],
                "dataLabels"=>  (object)[
                    "enabled"   =>  false,
                ],
                "stroke"    =>  (object)[
                    "curve"     =>  "smooth",
                ],
                "xaxis"     =>  (object)[
                    "type"      =>  "date",
                    "categories"=>  [],
                ],
                "tooltip"   =>  (object)[
                    "x"         =>  (object)[
                        "format"    =>  "dd/MM/yy",
                    ]
                ],
            ];

            $vQtde      =   5;

            try {
                // Datas para execução dos processos.
                $startDate  =   Carbon::now()->subMonths(1)->startOfDay();
                $endDate    =   Carbon::now();
                $refDate    =   $startDate;

                // Datas para execução dos processos.
                $SSSaldo    =   Chamado::where('id_empresa',$empresa)
                                ->where(function($query){
                                    $query->orWhere(function($query1){
                                        $query1->whereNull('data_conclusao');
                                        $query1->where('data_cria','<=',Carbon::now());
                                    });

                                    $query->orWhere(function($query1){
                                        $query1->whereNotNull('data_conclusao');
                                        $query1->where('data_conclusao','>=',Carbon::now());
                                        $query1->where('data_cria','<=', Carbon::now());
                                    });
                                })
                                ->select(
                                    'id_processo',
                                    DB::raw('count(1) as saldo')
                                )
                                ->groupBy([
                                    'id_processo',
                                ])
                                ->orderBy('saldo','desc')
                                ->get();
                
                foreach ($SSSaldo as $keyGroup => $valueGroup) {
                    if($vQtde <= 0) break;

                    $proccess       =   Processo::where('id_processo',$valueGroup->id_processo)->first();

                    if(!isset($proccess) && is_null($proccess)) continue;

                    array_push($vReturn->xaxis->categories, $proccess->sigla);

                    $criada         =   Chamado::where('id_empresa',$empresa)
                                        ->where('id_processo',$valueGroup->id_processo)
                                        ->where('data_cria','>=',Carbon::now()->subMonths(1)->startOfDay())
                                        ->where('data_cria','<=',Carbon::now())
                                        ->count();

                    $atrasada       =   Chamado::where('id_empresa',$empresa)
                                        ->where('id_processo',$valueGroup->id_processo)
                                        ->whereNull('data_conclusao')
                                        ->where('data_vencimento','<=',Carbon::now())
                                        ->where('data_cria','<=',Carbon::now())
                                        ->count();

                    $concluida      =   Chamado::where('id_empresa',$empresa)
                                        ->where('id_processo',$valueGroup->id_processo)
                                        ->whereNotNull('data_conclusao')
                                        ->where('data_conclusao','>=',Carbon::now()->subMonths(1)->startOfDay())
                                        ->where('data_conclusao','<=',Carbon::now())
                                        ->count();

                    /*$saldo          =   Chamado::where('id_empresa',$empresa)
                                        ->where('id_processo',$valueGroup->id_processo)
                                        ->where(function($query){
                                            $query->orWhere(function($query1){
                                                $query1->whereNull('data_conclusao');
                                                $query1->where('data_cria','<=',Carbon::now());
                                            });

                                            $query->orWhere(function($query1){
                                                $query1->whereNotNull('data_conclusao');
                                                $query1->where('data_conclusao','>=',Carbon::now());
                                                $query1->where('data_cria','<=', Carbon::now());
                                            });
                                        })
                                        ->count();*/

                    // -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- //

                    array_push($vReturn->series[0]->data, $criada);
                    array_push($vReturn->series[1]->data, $atrasada);
                    array_push($vReturn->series[2]->data, $concluida);
                    array_push($vReturn->series[3]->data, $valueGroup->saldo);

                    // -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- //

                    $vQtde  =   $vQtde - 1;
                }
                


                
            }
            catch(Exception $error) {}

            return $vReturn;

        }

        private function graph5(Request $request, $empresa) {

            $vReturn    =   (object)[
                "content"   =>  [
                    "id"        =>  5,
                ],
                "title"     =>  (object)[
                    "text"  =>  "Solicitação de serviço",
                    "align" =>  "left"
                ],
                "subtitle"     =>  (object)[
                    "text"  =>  "Processo que mais recebe solicitação",
                    "align" =>  "left"
                ],
                "colors"    =>  [
                    "#00296b",
                    "#730800",
                    "#004506",
                    "#e36600",
                    "#008dad",
                    "#00e5ff",
                    "#9900ff",
                    "#ee00ff",
                    "#004d16",
                    "#b34205",
                ],
                /*"fill"  =>  (object)[
                    "type"      =>  "gradient",
                    "gradient"  =>  (object)[
                        "shadeIntensity"    =>  1,
                        "inverseColors"     =>  false,
                        "opacityFrom"       =>  0.45,
                        "opacityTo"         =>  0.05,
                        "stops"             =>  [20, 100, 100, 100]
                    ],
                ],*/
                "series"    =>  [
                    (object)[
                        "name"  =>  "Criadas",
                        "data"  =>  [],
                    ],
                    (object)[
                        "name"  =>  "Atrasadas",
                        "data"  =>  [],
                    ],
                    (object)[
                        "name"  =>  "Concluídas",
                        "data"  =>  [],
                    ],
                    (object)[
                        "name"  =>  "Saldo",
                        "data"  =>  [],
                    ],
                ],
                "chart"     =>  (object)[
                    "height"    =>  350,
                    "type"      =>  "bar",
                ],
                "plotOptions"   =>  (object)[
                    "bar"   =>  (object)[
                        "horizontal"    =>  true,
                    ],
                    "dataLabels" => (object)[
                        "position"  =>  "top",
                    ],
                ],
                "dataLabels"=>  (object)[
                    "enabled"   =>  true,
                    "offsetX"   =>  -6,
                ],
                "stroke"    =>  (object)[
                    "curve"     =>  "smooth",
                    "show"      =>  true,
                    "width"     =>  1,
                ],
                "xaxis"     =>  (object)[
                    "type"      =>  "date",
                    "categories"=>  [],
                ],
                "tooltip"   =>  (object)[
                    "x"         =>  (object)[
                        "format"    =>  "dd/MM/yy",
                    ]
                ],
            ];

            $vQtde      =   5;

            try {
                // Datas para execução dos processos.
                $startDate  =   Carbon::now()->subMonths(1)->startOfDay();
                $endDate    =   Carbon::now();
                $refDate    =   $startDate;

                // Datas para execução dos processos.
                $SSSaldo    =   Chamado::where('id_empresa',$empresa)
                                ->where(function($query){
                                    $query->orWhere(function($query1){
                                        $query1->whereNull('data_conclusao');
                                        $query1->where('data_cria','<=',Carbon::now());
                                    });

                                    $query->orWhere(function($query1){
                                        $query1->whereNotNull('data_conclusao');
                                        $query1->where('data_conclusao','>=',Carbon::now());
                                        $query1->where('data_cria','<=', Carbon::now());
                                    });
                                })
                                ->select(
                                    'id_processo',
                                    DB::raw('count(1) as saldo')
                                )
                                ->groupBy([
                                    'id_processo',
                                ])
                                ->orderBy('saldo','desc')
                                ->get();
                
                foreach ($SSSaldo as $keyGroup => $valueGroup) {
                    if($vQtde <= 0) break;

                    $proccess       =   Processo::where('id_processo',$valueGroup->id_processo)->first();

                    if(!isset($proccess) && is_null($proccess)) continue;

                    array_push($vReturn->xaxis->categories, $proccess->sigla);

                    $criada         =   Chamado::where('id_empresa',$empresa)
                                        ->where('id_processo',$valueGroup->id_processo)
                                        ->where('data_cria','>=',Carbon::now()->subMonths(1)->startOfDay())
                                        ->where('data_cria','<=',Carbon::now())
                                        ->count();

                    $atrasada       =   Chamado::where('id_empresa',$empresa)
                                        ->where('id_processo',$valueGroup->id_processo)
                                        ->whereNull('data_conclusao')
                                        ->where('data_vencimento','<=',Carbon::now())
                                        ->where('data_cria','<=',Carbon::now())
                                        ->count();

                    $concluida      =   Chamado::where('id_empresa',$empresa)
                                        ->where('id_processo',$valueGroup->id_processo)
                                        ->whereNotNull('data_conclusao')
                                        ->where('data_conclusao','>=',Carbon::now()->subMonths(1)->startOfDay())
                                        ->where('data_conclusao','<=',Carbon::now())
                                        ->count();

                    /*$saldo          =   Chamado::where('id_empresa',$empresa)
                                        ->where('id_processo',$valueGroup->id_processo)
                                        ->where(function($query){
                                            $query->orWhere(function($query1){
                                                $query1->whereNull('data_conclusao');
                                                $query1->where('data_cria','<=',Carbon::now());
                                            });

                                            $query->orWhere(function($query1){
                                                $query1->whereNotNull('data_conclusao');
                                                $query1->where('data_conclusao','>=',Carbon::now());
                                                $query1->where('data_cria','<=', Carbon::now());
                                            });
                                        })
                                        ->count();*/

                    // -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- //

                    array_push($vReturn->series[0]->data, $criada);
                    array_push($vReturn->series[1]->data, $atrasada);
                    array_push($vReturn->series[2]->data, $concluida);
                    array_push($vReturn->series[3]->data, $valueGroup->saldo);

                    // -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- //

                    $vQtde  =   $vQtde - 1;
                }
                


                
            }
            catch(Exception $error) {}

            return $vReturn;
        }
    } // class Graph extends Controller { ... }
