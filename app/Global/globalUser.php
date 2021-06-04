<?php
    if(!function_exists('getAccess')) {
        function getAccess() {
            try {
                $allCompanies   =   App\Models\UsuarioEmpresa::where('id_usuario',Auth::user()->id)
                                    ->select('id_empresa')
                                    ->get();
    
                $empresa    =   App\Models\Empresa::whereIn('id_empresa',$allCompanies)
                                ->where('situacao',true)
                                ->orderBy('descricao','asc')
                                ->get();
                
    
                foreach ($empresa as $keyEmpresa => $valueEmpresa) {
                    //  Após coletar todos os dados de empresa coleta-se separadamente os dados de processos disponíveis
                    $empresa[$keyEmpresa]->proccessData =   App\Models\Processo::where('id_empresa',$valueEmpresa->id_empresa)
                                                            ->where('situacao',true)
                                                            ->orderBy('descricao','asc')
                                                            ->get();
    
                    foreach ($empresa[$keyEmpresa]->proccessData as $keyProccess => $valueProccess) {
                        $empresa[$keyEmpresa]->proccessData[$keyProccess]->automaticType    =   App\Models\TipoProcesso::where('id_processo',$valueProccess->id_processo)
                                                                                                ->where('situacao',true)
                                                                                                ->where('automatico',true)
                                                                                                ->orderBy('titulo','asc')
                                                                                                ->get();

                        foreach ($empresa[$keyEmpresa]->proccessData[$keyProccess]->automaticType as $keyType => $valueType) {
                            $empresa[$keyEmpresa]->proccessData[$keyProccess]->automaticType[$keyType]->questions   =   App\Models\Questao::where('id_tipo_processo',$valueType->id_tipo_processo)
                                                                                                                        ->where('situacao',true)
                                                                                                                        ->orderBy('ordem','asc')
                                                                                                                        ->orderBy('obrigatorio','asc')
                                                                                                                        ->orderBy('titulo','asc')
                                                                                                                        ->get();
                        } // foreach ($empresa[$keyEmpresa]->proccessData[$keyProccess]->automaticType as $keyType => $valueType) { ... }
    
                        
                        $empresa[$keyEmpresa]->proccessData[$keyProccess]->manualType       =   App\Models\TipoProcesso::where('id_processo',$valueProccess->id_processo)
                                                                                                ->where('situacao',true)
                                                                                                ->where('automatico',false)
                                                                                                ->orderBy('titulo','asc')
                                                                                                ->get();
    
                        foreach ($empresa[$keyEmpresa]->proccessData[$keyProccess]->manualType as $keyType => $valueType) {
                            $empresa[$keyEmpresa]->proccessData[$keyProccess]->manualType[$keyType]->questions   =   App\Models\Questao::where('id_tipo_processo',$valueType->id_tipo_processo)
                                                                                                                    ->where('situacao',true)
                                                                                                                    ->orderBy('ordem','asc')
                                                                                                                    ->orderBy('obrigatorio','asc')
                                                                                                                    ->orderBy('titulo','asc')
                                                                                                                    ->get();
                        } // foreach ($empresa[$keyEmpresa]->proccessData[$keyProccess]->manualType as $keyType => $valueType) { ... }
                    } // foreach ($$empresa[$keyEmpresa]->proccessData as $keyProccess => $valueProccess) { ... }
                } // foreach ($empresa as $keyEmpresa => $valueEmpresa) { ... }
    
                return $empresa;
            } // try { ... }
            catch(Exception $error) {
                return [];
            } // catch(Exception $error) { ... }
        }
    } // if(!function_exists('getCompany')) { ... }