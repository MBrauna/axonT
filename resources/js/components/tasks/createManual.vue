<template>
    <div>
        <!-- Em carregamento -->
        <div class="row" v-if="loading">
            <div class="col-12">
                <center><logo :size="100"></logo></center>
            </div>
        </div>

        <!-- Etapa inicial - Escolha de parametros -->
        <div class="row" v-if="!loading && step == 0">
            <div class="col-12 col-sm-4 col-md-4">
                <label for="companyTask">Empresa:</label>
                <select class="form-control form-control-sm" id="companyTask" v-model="choiceCompany" @change="selectChoiceCompany">
                    <option v-bind:value="null">Nenhuma opção selecionada</option>
                    <option v-for="curreg in data" v-bind:key="curreg.id_empresa" v-bind:value="curreg.id_empresa">{{ curreg.descricao }}</option>
                </select>
            </div>

            <div class="col-12 col-sm-4 col-md-4" v-if="choiceCompany != null">
                <label for="proccessTask">Processo:</label>
                <select class="form-control form-control-sm" id="proccessTask" v-model="choiceProccess" @change="selectChoiceProccess">
                    <option v-bind:value="null">Nenhuma opção selecionada</option>
                    <option v-for="curreg in dataProccessList" v-bind:key="curreg.id_processo" v-bind:value="curreg.id_processo">{{ curreg.descricao }}</option>
                </select>
            </div>

            <div class="col-12 col-sm-4 col-md-4" v-if="choiceProccess != null && choiceCompany != null">
                <label for="companyTask">Tipo:</label>
                <select class="form-control form-control-sm" id="proccessTask" v-model="choiceType" @change="selectQuestion">
                    <option v-bind:value="null">Nenhuma opção selecionada</option>
                    <option v-for="curreg in dataTypeList" v-bind:key="curreg.id_tipo_processo" v-bind:value="curreg.id_tipo_processo">{{ curreg.titulo }}</option>
                </select>
            </div>

            <div class="col-12" v-if="choiceCompany != null && choiceCompany != '' && choiceProccess != null && choiceProccess != '' && choiceType != null && choiceType != ''">
                <button class="btn btn-sm btn-block btn-primary" type="button" @click="confirmChoices()">
                    Confirmar seleção
                </button>
            </div>
        </div>

        <!-- Etapa secundária - Preenchimento de informações -->
        <form class="row" v-if="!loading && step == 1 && choiceCompany != null && choiceCompany != '' && choiceProccess != null && choiceProccess != '' && choiceType != null && choiceType != ''">
            <input type="hidden" name="type" value="0" required>
            <input type="hidden" name="idCompany" v-bind:value="saveDataCompany.id" required>
            <input type="hidden" name="idProccess" v-bind:value="saveDataProccess.id" required>
            <input type="hidden" name="idTask" v-bind:value="choiceType" required>

            <div class="col-12">
                <ul class="list-group border border-primary">
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-12 text-center" v-if="saveDataCompany.image != null">
                                <div class="row">
                                    <div class="col-12">
                                        <img v-bind:src="saveDataCompany.image" height="40vw">
                                    </div>
                                    <div class="col-12" v-if="saveDataCompany.resp != null">
                                        <small>
                                            <small class="text-primary font-weight-bold">
                                                Responsável: {{ saveDataCompany.resp.name }}
                                            </small>
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="col-12 text-primary text-center font-weight-bold">
                                <div class="row">
                                    <div class="col-12">
                                        <small>{{ saveDataCompany.sigla }} - {{ saveDataCompany.name }}</small>
                                    </div>
                                    <div class="col-12" v-if="saveDataCompany.resp != null">
                                        <small>
                                            <small class="text-primary font-weight-bold">
                                                Responsável: {{ saveDataCompany.resp.name }}
                                            </small>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item text-center">
                        <div class="row">
                            <div class="col-12 d-flex justify-content-around">
                                <i v-bind:class="saveDataProccess.icon"></i>
                                <small class="text-primary font-weight-bold">
                                    [{{ saveDataProccess.sigla }}] - {{ saveDataProccess.name }}
                                </small>
                                <i v-bind:class="saveDataProccess.icon"></i>
                            </div>
                            <div class="col-12 text-primary text-center font-weight-bold d-flex justify-content-center" v-if="saveDataProccess.resp != null">
                                <small>
                                    <small class="text-primary font-weight-bold">
                                        Responsável: {{ saveDataProccess.resp.name }}
                                    </small>
                                </small>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-12 text-center">
                                <small class="text-primary font-weight-bold">
                                    {{ saveDataType.title }}
                                </small>
                            </div>
                            <div class="col-6 text-center">
                                <small>
                                    <small class="text-primary font-weight-bold">
                                        {{ saveDataType.subtitle }}
                                    </small>
                                </small>
                            </div>
                            <div class="col-6 text-primary font-weight-bold d-flex justify-content-center">
                                <i class="fas fa-business-time"></i>
                                <small>
                                    <small class="text-primary font-weight-bold">
                                        {{ saveDataType.sla }} minuto(s)
                                    </small>
                                </small>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="col-12">
                <ul class="list-group border border-primary">
                    <li class="list-group-item" v-for="curreg in dataQuestionList" v-bind:key="curreg.id_questao">
                        <div class="row">
                            <div class="col-12">
                                <label v-bind:for="'idQuestion_' + curreg.id_questao">{{ curreg.titulo }}</label>
                                <input v-if="curreg.tipo == 'date'" type="date" class="form-control form-control-sm" v-bind:name="'idQuestion_' + curreg.id_questao" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" required>
                                <input v-else-if="curreg.tipo == 'datetime'" type="date" class="form-control form-control-sm" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:name="'idQuestion_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" required>
                                <input v-else-if="curreg.tipo == 'text'" type="text" class="form-control form-control-sm" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:name="'idQuestion_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" required>
                                <input v-else-if="curreg.tipo == 'number'" type="number" class="form-control form-control-sm" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:name="'idQuestion_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" required>
                                <textarea v-else class="form-control form-control-sm" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" v-bind:name="'idQuestion_' + curreg.id_questao" required></textarea>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="col-12">
                <ul class="list-group border border-primary">
                    <li class="list-group-item">
                        <div class="row d-flex justify-content-between">
                            <span class="col-12 col-sm-8 col-md-9 col-lg-10">Adicionar arquivos</span>
                            <div class="col-12 col-sm-4 col-md-3 col-lg-2">
                                <button class="btn btn-sm btn-outline-white btn-block" @click="newRegister">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover table-striped" id="tableFile">
                                <thead>
                                    <tr>
                                        <th scope="col">Arquivo</th>
                                        <th scope="col">Data</th>
                                        <th scope="col">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th scope="col">Arquivo</th>
                                        <th scope="col">Data</th>
                                        <th scope="col">Ações</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </li>
                </ul>
            </div>
        </form>
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
                'loading'   :   true,
                'userData'  :   null,
                'step'      :   0,


                'choiceCompany'     :   null,
                'choiceProccess'    :   null,
                'choiceType'        :   null,

                'data'              :   null,
                'dataProccessList'  :   null,
                'dataTypeList'      :   null,
                'dataQuestionList'  :   null,

                'saveDataCompany'   :   null,
                'saveDataProccess'  :   null,
                'saveDataType'      :   null,
            }
        },
        methods: {
            initFilter  :   function(){
                try {
                    var vm      =   this;
                    vm.header   = {
                        'headers'   :   {
                            'Authorization' :   'Bearer ' + this.bearer,
                        },
                    };
                    vm.request = {
                        '_token'            :   vm.token,
                        'filter'            :   false,
                    };

                    axios.post('/api/util/filterTasks',vm.request,vm.header)
                    .then(function (response) {
                        if(response.status === 200) {
                            vm.loading  =   false;
                            vm.step     =   0;
                            vm.data     =   response.data;
                        } // if(response.status === 200) { ... }
                    })
                    .catch(function(retorno){
                        console.log('Ocorreu um erro desconhecido! Verifique.');
                        console.log(retorno);
                    });
                } // try { ... }
                catch(error) {
                    console.log('Ocorreu um erro desconhecido! Verifique.');
                    console.log(error);
                } // catch(error) { ... }
            }, // initFilter  :   function(){ ... }
            selectChoiceCompany :   function(){
                var vm  =   this;
                
                vm.dataProccessList     =   [];
                vm.dataTypeList         =   [];
                vm.dataQuestionList     =   [];

                vm.choiceProccess       =   null;
                vm.choiceType           =   null;

                vm.data.forEach((element) => {
                    if(element.id_empresa == vm.choiceCompany) {
                        vm.dataProccessList   =   element.processos;
                    }
                });
            },
            selectChoiceProccess    :   function(){
                var vm  =   this;
                
                vm.dataTypeList     =   [];
                vm.dataQuestionList =   [];

                vm.choiceType       =   null;

                vm.dataProccessList.forEach((element) => {
                    if(element.id_processo == vm.choiceProccess) {
                        vm.dataTypeList =   element.tipoManual;
                    }
                });
            },
            selectQuestion :    function(){
                var vm  =   this;

                vm.dataTypeList.forEach((element) => {
                    if(element.id_tipo_processo == vm.choiceType) {
                        vm.dataQuestionList =   element.questoes;
                    } // if(element.id_tipo_processo == vm.choiceType) { ... }
                });
            },
            confirmChoices    :   function(){
                var vm  =   this;

                vm.step =   1;

                vm.data.forEach((element) => {
                    if(element.id_empresa == vm.choiceCompany) {
                        vm.saveDataCompany  =   {
                            'id'    :   element.id_empresa,
                            'name'  :   element.descricao,
                            'sigla' :   element.sigla,
                            'image' :   element.imagem,
                            'resp'  :   element.responsavel
                        };

                        element.processos.forEach((element2) => {
                            if(element2.id_processo == vm.choiceProccess) {
                                vm.saveDataProccess =   {
                                    'id'    :   element2.id_processo,
                                    'name'  :   element2.descricao,
                                    'sigla' :   element2.sigla,
                                    'icon'  :   element2.icone,
                                    'resp'  :   element2.responsavel,
                                };

                                element2.tipoManual.forEach((element3) => {
                                    if(element3.id_tipo_processo == vm.choiceType) {
                                        vm.saveDataType =   {
                                            'id'        :   element3.id_tipo_processo,
                                            'title'     :   element3.titulo,
                                            'subtitle'  :   element3.subtitulo,
                                            'sla'       :   element3.sla,
                                        };
                                    }
                                })
                            } // if(element2.id_processo == vm.choiceProccess) { ... }
                        });
                    }
                });
            },
            newRegister     :   function(){

                var tbodyRef = document.getElementById('tableFile').getElementsByTagName('tbody')[0];
                var newRow = tbodyRef.insertRow();
                var newCell = newRow.insertCell();
                var newCell2 = newRow.insertCell();
                var newText = document.innerHTML("<a hrf='#'>123</a>");

                newCell.appendChild(newText);
                newCell2.appendChild(newText);
            },
            
        },
        mounted() {
            this.LAYOUT.initData();
            this.PREFERENCES.initData();

            this.userData   =   JSON.parse(this.auth);
            this.loading    =   true;
            this.initFilter();
        },
    }
</script>
