<?php
    if(!function_exists('getCompanyPermission')) {
        function getCompanyPermission() {
            try {
                $return =   [];

                // Verifica se o usuário é administrador do sistema, se for ... libera tudo ...
                if(Auth::user()->administrador) {
                    $return =   App\Models\Empresa::where('situacao',true)
                                ->orderBy('descricao','asc')
                                ->get();

                    # Percorrerá os registros da empresa procurando os processos
                    foreach ($return as $keyCompany => $valueCompany) {
                        # Consulta os dados dos processos vinculados a esta empresa
                        $return[$keyCompany]->processos =   App\Models\Processo::where('id_empresa',$valueCompany->id_empresa)
                                                            ->where('situacao',true)
                                                            ->orderBy('descricao','asc')
                                                            ->get();

                        # Percorrerá os processos da empresa a fim de encontrar os tipos
                        foreach ($return[$keyCompany]->processos as $keyProccess => $valueProccess) {
                            $return[$keyCompany]->processos[$keyProccess]->tipoAutomatico   =   App\Models\TipoProcesso::where('id_processo',$valueProccess->id_processo)
                                                                                                ->where('situacao',true)
                                                                                                ->where('automatico',true)
                                                                                                ->orderBy('titulo','asc')
                                                                                                ->get();

                            $return[$keyCompany]->processos[$keyProccess]->tipoManual       =   App\Models\TipoProcesso::where('id_processo',$valueProccess->id_processo)
                                                                                                ->where('situacao',true)
                                                                                                ->where('automatico',false)
                                                                                                ->orderBy('titulo','asc')
                                                                                                ->get();

                            # Percorre os tipos automáticos buscando as perguntas necessárias a ela
                            foreach ($return[$keyCompany]->processos[$keyProccess]->tipoAutomatico as $keyType => $valueType) {
                                $return[$keyCompany]->processos[$keyProccess]->tipoAutomatico[$keyType]->questoes   =   App\Models\Questao::where('id_tipo_processo',$valueType->id_tipo_processo)
                                                                                                                        ->where('situacao',true)
                                                                                                                        ->orderBy('ordem','asc')
                                                                                                                        ->orderBy('titulo','asc')
                                                                                                                        ->get();
                            } // foreach ($return[$keyCompany]->processos[$keyProccess]->tipoAutomatico as $keyType => $valueType) { ... }

                            # Percorre os tipos manuais buscando as perguntas necessárias a ela
                            foreach ($return[$keyCompany]->processos[$keyProccess]->tipoManual as $keyType => $valueType) {
                                $return[$keyCompany]->processos[$keyProccess]->tipoManual[$keyType]->questoes       =   App\Models\Questao::where('id_tipo_processo',$valueType->id_tipo_processo)
                                                                                                                        ->where('situacao',true)
                                                                                                                        ->orderBy('ordem','asc')
                                                                                                                        ->orderBy('titulo','asc')
                                                                                                                        ->get();
                            } // foreach ($return[$keyCompany]->processos[$keyProccess]->tipoAutomatico as $keyType => $valueType) { ... }

                        } // foreach ($return[$keyCompany]->processos as $keyProccess => $valueProccess) { .. }
                    } // foreach ($return as $keyCompany => $valueCompany) { ... }
                } // if(Auth::user()->administrador)  { ... }
                else {

                    $userConfig =   App\Models\UsuarioConfig::where('id_usuario', Auth::user()->id)->select('id_perfil');
                    $perfil     =   App\Models\Perfil::whereIn('id_perfil',$userConfig)->select('id_empresa');
                    $return     =   App\Models\Empresa::whereIn('id_empresa',$perfil)
                                    ->where('situacao',true)
                                    ->orderBy('descricao','asc')
                                    ->get();

                    foreach ($return as $keyEmpresa => $valueEmpresa) {
                        $return[$keyEmpresa]->processos =   App\Models\Processo::where('id_empresa',$valueEmpresa->id_empresa)
                                                            ->where('situacao',true)
                                                            ->orderBy('descricao','asc')
                                                            ->get();

                        if(is_null($return[$keyEmpresa]->id_usuario_responsavel)) {
                            $return[$keyEmpresa]->responsavel  =   null;
                        }
                        else {
                            $return[$keyEmpresa]->responsavel  =   User::find($return[$keyEmpresa]->id_usuario_responsavel);
                        }

                        foreach($return[$keyEmpresa]->processos as $keyProc => $valueProc) {
                            if(is_null($return[$keyEmpresa]->processos[$keyProc]->id_usuario_responsavel)) {
                                $return[$keyEmpresa]->processos[$keyProc]->responsavel  =   null;
                            }
                            else {
                                $return[$keyEmpresa]->processos[$keyProc]->responsavel  =   App\Models\User::find($return[$keyEmpresa]->processos[$keyProc]->id_usuario_responsavel);
                            }

                            $return[$keyEmpresa]->processos[$keyProc]->tipoAutomatico   =   App\Models\TipoProcesso::where('id_processo',$valueProc->id_processo)
                                                                                            ->where('situacao',true)
                                                                                            ->where('automatico',true)
                                                                                            ->get();

                            $return[$keyEmpresa]->processos[$keyProc]->tipoManual       =   App\Models\TipoProcesso::where('id_processo',$valueProc->id_processo)
                                                                                            ->where('situacao',true)
                                                                                            ->where('automatico',false)
                                                                                            ->get();


                            foreach ($return[$keyEmpresa]->processos[$keyProc]->tipoAutomatico as $keyTipo => $valueTipo) {
                                $return[$keyEmpresa]->processos[$keyProc]->tipoAutomatico[$keyTipo]->questoes   =   App\Models\Questao::where('id_tipo_processo',$valueTipo->id_tipo_processo)
                                                                                                                    ->where('situacao',true)
                                                                                                                    ->orderBy('ordem','asc')
                                                                                                                    ->orderBy('titulo','asc')
                                                                                                                    ->get();
                            } // foreach ($return[$keyEmpresa]->processos[$keyProc]->tipoAutomatico as $keyTipo => $valueTipo) { ... }

                            foreach ($return[$keyEmpresa]->processos[$keyProc]->tipoManual as $keyTipo => $valueTipo) {
                                $return[$keyEmpresa]->processos[$keyProc]->tipoManual[$keyTipo]->questoes       =   App\Models\Questao::where('id_tipo_processo',$valueTipo->id_tipo_processo)
                                                                                                                    ->where('situacao',true)
                                                                                                                    ->orderBy('ordem','asc')
                                                                                                                    ->orderBy('titulo','asc')
                                                                                                                    ->get();
                            } // foreach ($return[$keyEmpresa]->processos[$keyProc]->tipoAutomatico as $keyTipo => $valueTipo) { ... }
                        } // foreach($return->processos as $keyProc => $valueProc) { ... }
                    } // foreach ($return as $key => $value) { ... }


                } // else { ... }

                // Finaliza os dados retornando as informações
                return $return;

            } // try { ... }
            catch(Exception $error) {
                return [];
            } // catch(Exception $error) { ... }
        } // function consulta_empresa($idEmpresa) { ... }
    } // if(!function_exists('consulta_empresa')) { ... }