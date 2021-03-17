<template>
    <div>
        <div class="row pt-3">
            <div class="col-12">
                <form class="card border-primary was-validated" method="POST" v-bind:action="url" autocomplete="off" enctype="multipart/form-data">
                    <div class="card-header bg-primary text-center text-white">
                        <small>Cadastro de troca de objetos </small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Primeira etapa - Seleção do tipo -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="idProcessoReferencia" class="text-success font-weight-bold">Processo de referência:</label>
                                    <select class="form-control form-control-sm" id="idProcessoReferencia" name="idProcessoReferencia" v-model="processoReferencia" required>
                                        <option value="">Nenhum processo de referência escolhido</option>
                                        <option v-for="conteudo in listaProcessoOrigem" v-bind:key="conteudo.id_processo" v-bind:value="conteudo.id_processo">[{{ conteudo.sigla_empresa }}] - {{ conteudo.descricao }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="tipoObjeto">Tipo:</label>
                                    <select class="form-control form-control-sm" id="idTipo" name="idTipo" v-model="opcao" @change="selectOption()" required>
                                        <option value="">Nenhum tipo escolhido</option>
                                        <option v-for="conteudo in listaOpcoes" v-bind:key="conteudo.id" v-bind:value="conteudo.id">{{ conteudo.description }}</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Primeira etapa - Seleção do tipo -->

                            <!-- Segunda etapa - Processo alvo -->
                            <!-- Entrada -->
                            <div class="col-12 col-sm-6 col-md-4 col-lg-4" v-if="opcaoEntrada">
                                <div class="form-group">
                                    <label for="idProcessoOrigem" class="text-success font-weight-bold">Processo de origem:</label>
                                    <select class="form-control form-control-sm" id="idProcessoOrigem" name="idProcessoOrigem" v-model="processoOrigem" @change="selectProcess(processoOrigem,null,1)" required>
                                        <option value="">Nenhum processo de origem escolhido</option>
                                        <option v-for="conteudo in listaProcessoOrigem" v-bind:key="conteudo.id_processo" v-bind:value="conteudo.id_processo">[{{ conteudo.sigla_empresa }}] - {{ conteudo.descricao }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-4" v-if="opcaoEntrada">
                                <div class="form-group">
                                    <label for="idSubProcessoOrigem" class="text-success font-weight-bold">Tipo de solicitação de serviço:</label>
                                    <select class="form-control form-control-sm" id="idSubProcessoOrigem" name="idSubProcessoOrigem" v-model="tipoOrigem" @change="selectTipo(1); selectResponsavel()" required>
                                        <option value="">Nenhum tipo de processo de origem escolhido</option>
                                        <option v-for="conteudo in listaTipoOrigem" v-bind:key="conteudo.id_tipo_processo" v-bind:value="conteudo.id_tipo_processo">{{ conteudo.descricao }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4" v-if="opcaoEntrada">
                                <div class="form-group">
                                    <label for="responsavelOrigem"  class="text-success font-weight-bold">Responsável pela Origem:</label>
                                    <select class="form-control form-control-sm" id="responsavelOrigem" name="responsavelOrigem" v-model="subordinadoOrigem" @change="selectResponsavel()">
                                        <option value="">Nenhum responsável atribuído</option>
                                        <option v-for="conteudo in listaSubordinadoOrigem" v-bind:key="conteudo.id" v-bind:value="conteudo.id">{{ conteudo.name }}</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Saída -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6" v-if="opcaoSaida">
                                <div class="form-group">
                                    <label for="idProcessoDestino" class="text-danger font-weight-bold">Processo de destino:</label>
                                    <select class="form-control form-control-sm" id="idProcessoDestino" name="idProcessoDestino" v-model="processoDestino" @change="selectProcess(null, processoDestino,2); selectResponsavel();" required> <!--  -->
                                        <option value="">Nenhum processo de destino escolhido</option>
                                        <option v-for="conteudo in listaProcessoDestino" v-bind:key="conteudo.id_processo" v-bind:value="conteudo.id_processo">[{{ conteudo.sigla_empresa }}] - {{ conteudo.descricao }}</option>
                                    </select>
                                </div>
                            </div>
                            <!--<div class="col-12 col-sm-6 col-md-6 col-lg-6" v-if="opcaoSaida">
                                <div class="form-group">
                                    <label for="idSubProcessoDestino" class="text-danger font-weight-bold">Tipo de solicitação de serviço:</label>
                                    <select class="form-control form-control-sm" id="idSubProcessoDestino" name="idSubProcessoDestino" v-model="tipoDestino" @change="selectTipo(2)" required>
                                        <option value="">Nenhum tipo de processo de destino escolhido</option>
                                        <option v-for="conteudo in listaTipoDestino" v-bind:key="conteudo.id_tipo_processo" v-bind:value="conteudo.id_tipo_processo">{{ conteudo.descricao }}</option>
                                    </select>
                                </div>
                            </div>-->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6" v-if="opcaoSaida">
                                <div class="form-group">
                                    <label for="responsavelDestino"  class="text-danger font-weight-bold">Responsável pelo Destino:</label>
                                    <select class="form-control form-control-sm" id="responsavelDestino" name="responsavelDestino" v-model="subordinadoDestino"> <!-- @change="selectResponsavel()" -->
                                        <option value="">Nenhum responsável atribuído</option>
                                        <option v-for="conteudo in listaSubordinadoDestino" v-bind:key="conteudo.id" v-bind:value="conteudo.id">{{ conteudo.name }}</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Segunda etapa - Processo alvo -->


                            <!-- Dados do entregável -->
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12" v-if="opcaoDados">
                                <div class="form-group">
                                    <label for="entregavel">Entregável:</label>
                                    <input class="form-control form-control-sm" type="text" name="entregavel" minlength="10" maxlength="250" id="entregavel" placeholder="Informe o título do entregável" v-model="entregavelData" @change="trimData()" required>
                                </div>
                            </div>
                            <!-- Dados do entregável -->

                            <!-- Dados de periodicidade -->
                            <div class="col-12 col-sm-12 col-md-6" v-if="opcaoDados">
                                <div class="form-group">
                                    <label for="periodicidade">Periodicidade:</label>
                                    <select class="form-control form-control-sm" id="periodicidade" v-model="periodicidade" required>
                                        <option value="">Nenhum período escolhido</option>
                                        <option v-for="conteudo in listaEventoEntrada" v-bind:key="conteudo.id" v-bind:value="conteudo">{{ conteudo.description }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6" v-if="opcaoDados">
                                <div class="form-group">
                                    <label for="qtde_periodicidade">Tempo {{ (periodicidade.id == undefined) ? '' : 'em ' + periodicidade.description }}:</label>
                                    <input type="number" min="1" max="9999" class="form-control form-control-sm" id="qtde_periodicidade" name="qtde_periodicidade" value="" required>
                                </div>
                            </div>
                            <input type="hidden" name="periodicidade" v-bind:value="periodicidade.id">
                            <div class="col-12" v-if="periodicidade.date">
                                <div class="form-group">
                                    <label for="periodicidade_data">Data de início:</label>
                                    <input type="date" v-bind:min="menorHora" class="form-control form-control-sm" id="periodicidade_data" name="periodicidade_data" required>
                                </div>
                            </div>
                            <div class="col-12" v-if="periodicidade.hour">
                                <div class="form-group">
                                    <label for="periodicidade_hora">Horário de início:</label>
                                    <input type="time" class="form-control form-control-sm" id="periodicidade_hora" name="periodicidade_hora" required>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6" v-if="periodicidade.datetime">
                                <div class="form-group">
                                    <label for="periodicidade_data">Data de início:</label>
                                    <input type="date" v-bind:min="menorHora" class="form-control form-control-sm" id="periodicidade_data" name="periodicidade_data" required>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6" v-if="periodicidade.datetime">
                                <div class="form-group">
                                    <label for="periodicidade_hora">Horário de início:</label>
                                    <input type="time" class="form-control form-control-sm" id="periodicidade_hora" name="periodicidade_hora" required>
                                </div>
                            </div>
                            <!-- Dados de periodicidade -->


                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12" v-if="opcaoDados">
                                <div class="form-group" v-for="(curreg, idx) in listaQuestao" v-bind:key="curreg.id_pergunta_tipo">
                                    <label v-bind:for="'idQuestion_' + curreg.id_questao">{{ curreg.titulo }}</label>
                                    <input v-if="curreg.tipo == 'date'" type="date" class="form-control form-control-sm" v-bind:name="'idQuestion_' + curreg.id_questao" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" v-model="dataQuestionList[idx].valueData" @change="trimData" required>
                                    <input v-else-if="curreg.tipo == 'datetime'" type="date" class="form-control form-control-sm" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:name="'idQuestion_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" v-model="dataQuestionList[idx].valueData" @change="trimData" required>
                                    <input v-else-if="curreg.tipo == 'text'" type="text" class="form-control form-control-sm" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:name="'idQuestion_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" v-model="dataQuestionList[idx].valueData" @change="trimData" required>
                                    <input v-else-if="curreg.tipo == 'number'" type="number" class="form-control form-control-sm" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:name="'idQuestion_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" v-model="dataQuestionList[idx].valueData" @change="trimData" required>
                                    <textarea v-else class="form-control form-control-sm" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" v-bind:name="'idQuestion_' + curreg.id_questao" v-model="dataQuestionList[idx].valueData" @change="trimData" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer" v-if="opcaoDados">
                        <button type="submit" class="btn btn-block btn-sm btn-primary">Cadastrar troca de objetos</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'auth', 'token','bearer'
        ],
        components: {
        },
        data() {
            return {
                carregamento: false,
                opcaoEntrada: false,
                opcaoSaida: false,
                opcaoDados: false,
                opcao: "",
                processoReferencia: "",
                processoOrigem: "",
                processoDestino: "",
                tipoOrigem: "",
                tipoDestino: "",
                subordinadoOrigem: "",
                subordinadoDestino: "",
                menorHora: null,
                periodicidade: "",


                entregavelData: "",

                // -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # --
                listaOpcoes: [
                    {'id': 1, 'description': 'Entrada', 'icone': 'fas fa-arrow-alt-circle-down'},
                    {'id': 2, 'description': 'Saída', 'icone': 'fas fa-sign-out-alt'},
                ],
                listaProcessoOrigem: {},
                listaProcessoDestino: {},
                listaTipoOrigem: {},
                listaTipoDestino: {},
                listaSubordinadoOrigem: {},
                listaSubordinadoDestino: {},
                listaQuestao: {},
                // -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # --
                listaTipoObj: [
                    {
                        "id" : 1,
                        "description" : "Documento digitalizado",
                    },
                ],
                listaMeio: [
                    {
                        "id" : 1,
                        "description" : "e-mail",
                    },
                ],
                listaEventoEntrada: [
                    {
                        "id" : 1,
                        "description" : "Dia(s)",
                        "date" : false,
                        "hour" : false,
                        "datetime" : true,
                    },
                    {
                        "id" : 4,
                        "description" : "Mês(es)",
                        "date" : false,
                        "hour" : false,
                        "datetime" : true,
                    },
                    {
                        "id" : 7,
                        "description" : "Ano(s)",
                        "date" : false,
                        "hour" : false,
                        "datetime" : true,
                    },
                ],
            }
        },
        methods: {
            trimData: function(){
                entregavelData  =   entregavelData.trim();
            },
            selectOption: function(){
                var vm = this;
                vm.processoOrigem           =   "";
                vm.processoDestino          =   "";
                vm.tipoOrigem               =   "";
                vm.tipoDestino              =   "";
                vm.subordinadoOrigem        =   "";
                vm.subordinadoDestino       =   "";
                vm.menorHora                =   null;
                vm.periodicidade            =   "";

                vm.listaTipoOrigem          =   {};
                vm.listaTipoDestino         =   {};
                vm.listaSubordinadoOrigem   =   {};
                vm.listaSubordinadoDestino  =   {};
                vm.listaQuestao             =   {};

                if(vm.opcao == 1) {
                    vm.opcaoEntrada = true;
                    vm.opcaoSaida = false;
                }
                else if(vm.opcao == 2){
                    vm.processoOrigem = vm.processoReferencia;
                    vm.selectProcess(vm.processoOrigem,null,1);
                    vm.opcaoEntrada = true;
                    vm.opcaoSaida = true;
                }
                else {
                    vm.opcaoEntrada = false;
                    vm.opcaoSaida = false;
                }
            },
            selectProcess: function(idOrigem, idDestino, id) {
                var vm      =   this;

                if(idOrigem != null) {

                    if(id == vm.opcao) {
                        vm.listaQuestao         =   {};
                    }

                    vm.tipoOrigem               =   "";
                    vm.listaTipoOrigem          =   {};
                    vm.subordinadoOrigem        =   "";
                    vm.listaSubordinadoOrigem   =   {};

                    vm.coletaSubProcesso(idOrigem,0);
                } // if(idOrigem != null) { ... }

                if(idDestino != null) {
                    vm.tipoDestino              =   "";
                    vm.listaTipoDestino         =   {};
                    vm.subordinadoDestino       =  "";
                    vm.listaSubordinadoDestino  =   {};

                    vm.coletaSubProcesso(idDestino,1);
                } // if(idOrigem != null) { ... }

            },
            selectTipo: function(id) {
                var vm  =   this;
                /*if(vm.opcao == id) {
                    vm.coletaQuestao();
                }*/
                vm.coletaQuestao();
            },
            selectResponsavel: function() {
                var vm = this;

                if(vm.opcaoEntrada && !vm.opcaoSaida) {
                    /*if(vm.subordinadoOrigem != "") {
                        vm.opcaoDados = true;
                    } // if(opcaoEntrada && subordinadoOrigem != "") { ... }
                    else {
                        vm.opcaoDados = false;
                    }*/
                    vm.opcaoDados = true;
                }
                else if(!vm.opcaoEntrada && vm.opcaoSaida) {
                    /*if(vm.opcaoSaida && vm.subordinadoDestino != "") {
                        vm.opcaoDados = true;
                    } // if(opcaoSaida && subordinadoOrigem != "") { ... }
                    else {
                        vm.opcaoDados = true;
                    }*/
                    if(vm.opcaoSaida) {
                        vm.opcaoDados = true;
                    } // if(opcaoSaida && subordinadoOrigem != "") { ... }
                    else {
                        vm.opcaoDados = true;
                    }
                }
                else if(vm.opcaoEntrada && vm.opcaoSaida) {
                    /*if(vm.subordinadoDestino != "" && vm.subordinadoOrigem != "") {
                        vm.opcaoDados = true;
                    }
                    else {
                        vm.opcaoDados = true;
                    }*/
                    vm.opcaoDados = true;
                }
                else {
                    vm.opcaoDados = false;
                }
            },
            coletaProcesso : function(){
                var vm = this;

                var header   = {
                    'headers'   :   {
                        'Authorization' :   'Bearer ' + this.bearer,
                    },
                };

                var vRequisicao     =   {
                    'idUsuario' :   vm.id,
                };
                vm.carregamento = true;

                axios.post('/api/util/resp',vRequisicao)
                .then(function (response) {
                    if(response.status === 200) {
                        vm.carregamento         =   false;
                        vm.listaProcessoOrigem  =   response.data.processoOrigem;
                        vm.listaProcessoDestino =   response.data.processoDestino;
                    }
                    else {
                        vm.$bvToast.toast(
                            (response.data.erro.mensagem) ? response.data.erro.mensagem : 'Erro ao obter filtros! Verifique.',
                            {
                                title: 'Mensagem BPMS',
                                autoHideDelay: 5000,
                                appendToast: true,
                                solid: true,
                                variant: 'warning',
                            }
                        );
                    }
                })
                .catch(function(response){
                    vm.$bvToast.toast(
                        (response.data.erro.mensagem) ? response.data.erro.mensagem : 'Erro ao obter filtros! Verifique.',
                        {
                            title: 'Mensagem BPMS',
                            autoHideDelay: 5000,
                            appendToast: true,
                            solid: true,
                            variant: 'warning',
                        }
                    );
                });
            },
            coletaSubProcesso : function(idProcesso, idTipo){
                var vm          = this;
                if(idProcesso != null) {

                    if(idTipo == 0) {
                        vm.listaTipoOrigem          =   {};
                        vm.tipoOrigem               =   "";
                        vm.listaSubordinadoOrigem   =   {};
                        vm.subordinadoOrigem        =   "";
                    } // if(idTipo == 0) { ... }

                    if(idTipo == 1) {
                        vm.listaTipoDestino         =   {};
                        vm.tipoDestino              =   "";
                        vm.listaSubordinadoDestino  =   {};
                        vm.subordinadoDestino       =   "";
                    } // if(idTipo == 0) { ... }

                    vm.carregamento         =   true;

                    try {
                        var header   = {
                            'headers'   :   {
                                'Authorization' :   'Bearer ' + this.bearer,
                            },
                        };

                        var request = {
                            '_token'            :   vm.token,
                            'idUsuarioBPMS'     :   vm.id,
                            'idProcessoBPMS'    :   idProcesso,
                        };

                        axios.post('/api/util/tipoObj',request, header)
                        .then(function (response) {
                            if(response.status === 200) {
                                vm.carregamento =   false;
                                if(idTipo == 0) {
                                    vm.listaTipoOrigem          =   response.data.tipo;
                                    vm.listaSubordinadoOrigem   =   response.data.sub;
                                }

                                if(idTipo == 1) {
                                    vm.listaTipoDestino         =   response.data.tipo;
                                    vm.listaSubordinadoDestino  =   response.data.sub;
                                }
                            }
                            else {
                                vm.$bvToast.toast(
                                    (typeof response.data.erro != "undefined") ? response.data.erro.mensagem : 'Erro ao obter o tipo de processo! Verifique.',
                                    {
                                        title: 'Mensagem BPMS',
                                        autoHideDelay: 5000,
                                        appendToast: true,
                                        solid: true,
                                        variant: 'warning',
                                    }
                                );
                            }
                        })
                        .catch(function(response){
                            vm.$bvToast.toast(
                                (response.data.erro.mensagem) ? response.data.erro.mensagem : 'Erro ao obter filtros! Verifique.',
                                {
                                    title: 'Mensagem BPMS',
                                    autoHideDelay: 5000,
                                    appendToast: true,
                                    solid: true,
                                    variant: 'warning',
                                }
                            );
                        });
                    }
                    catch(erro) {
                        vm.$bvToast.toast(
                            'Não foi possível obter os dados do tipo de processo! Verifique.',
                            {
                                title: 'Contate um administrador',
                                autoHideDelay: 5000,
                                appendToast: true,
                                solid: true,
                                variant: 'warning',
                            }
                        );
                    }
                }
            },
            coletaQuestao : function(){
                var vm      =   this;
                var header   = {
                    'headers'   :   {
                        'Authorization' :   'Bearer ' + this.bearer,
                    },
                };

                vm.listaQuestao         =   [];

                if(vm.opcao == 1 && vm.tipoOrigem == null) return;
                if(vm.opcao == 2 && vm.tipoDestino == null) return;

                vm.carregamento         =   true;

                try {
                    var request = {
                        '_token'            :   vm.token,
                        'idUsuario'         :   document.getElementById("idUsuarioBPMS").value,
                        'idProcesso'        :   (vm.opcao == 1) ? vm.processoOrigem : vm.processoOrigem, //vm.processoDestino,
                        'idTipo'            :   (vm.opcao == 1) ? vm.tipoOrigem : vm.tipoOrigem,//vm.tipoDestino,
                    };

                    axios.post('/api/tasks/questoes',request, header)
                    .then(function (response) {
                        if(response.status === 200) {
                            vm.carregamento =   false;
                            vm.listaQuestao =   response.data.questao;
                            vm.menorHora    =   response.data.menorHora;
                        }
                        else {
                            vm.carregamento =   false;
                            vm.$bvToast.toast(
                                (typeof response.data.erro != "undefined") ? response.data.erro.mensagem : 'Erro ao obter o tipo de processo! Verifique.',
                                {
                                    title: 'Mensagem BPMS',
                                    autoHideDelay: 5000,
                                    appendToast: true,
                                    solid: true,
                                    variant: 'warning',
                                }
                            );
                        }
                    })
                    .catch(function(response){
                        vm.$bvToast.toast(
                            (response.data.erro.mensagem) ? response.data.erro.mensagem : 'Erro ao obter filtros! Verifique.',
                            {
                                title: 'Mensagem BPMS',
                                autoHideDelay: 5000,
                                appendToast: true,
                                solid: true,
                                variant: 'warning',
                            }
                        );
                    });
                }
                catch(erro) {
                    vm.$bvToast.toast(
                        'Não foi possível obter os dados do tipo de processo! Verifique.',
                        {
                            title: 'Contate um administrador',
                            autoHideDelay: 5000,
                            appendToast: true,
                            solid: true,
                            variant: 'warning',
                        }
                    );
                }
            },
        },
        mounted() {
            this.LAYOUT.initData();
            this.PREFERENCES.initData();

            this.loading    =   true;
            this.userData   =   JSON.parse(this.auth);
            this.coletaProcesso();
        }
    }
</script>