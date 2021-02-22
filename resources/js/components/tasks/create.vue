<template>
    <div>
        <div v-if="company === null" class="card shadow mt-3 mb-3 border-primary bg-white">
            <div class="card-header text-center bg-primary font-weight-bold text-light">
                Carregando ...
            </div>
            <div class="card-body">
                <center><logo :size="100"></logo></center>
            </div>
            <div class="card-footer bg-primary font-weight-bold text-light text-center">
                Aguarde.
            </div>
        </div>
        <div v-else class="card shadow mt-3 mb-3 border-primary bg-white">
            <div class="card-header text-center bg-primary font-weight-bold text-light">
                Criação - Solicitação de serviço
            </div>
            <div class="card-body">
                <div v-if="step==0">
                    <div class="row">
                        <div v-bind:class="'col-' + (12/typeTasks.length)" v-for="curreg in typeTasks" v-bind:key="curreg.value" >
                            <button type="button" class="btn btn-sm  btn-primary btn-lg btn-block" @click="selectTypeTask(curreg)">{{ curreg.title }}</button>
                        </div>
                    </div>


                    <div v-if="choiceTypeTask == 1">
                        <div v-if="company == null">
                            <center><logo :size="100"></logo></center>
                        </div>
                        <div v-else>
                            <div class="row">
                                <div class="col-12 col-sm-4 col-md-4">
                                    <label for="companyTask">Empresa:</label>
                                    <select class="form-control form-control-sm" id="companyTask" v-model="choiceCompany" @change="selectChoiceCompany">
                                        <option v-bind:value="null">Nenhuma opção selecionada</option>
                                        <option v-for="curreg in company" v-bind:key="curreg.id_empresa" v-bind:value="curreg.id_empresa">{{ curreg.descricao }}</option>
                                    </select>
                                </div>

                                <div class="col-12 col-sm-4 col-md-4" v-if="choiceCompany != null">
                                    <label for="proccessTask">Processo:</label>
                                    <select class="form-control form-control-sm" id="proccessTask" v-model="choiceProccess" @change="selectChoiceProccess">
                                        <option v-bind:value="null">Nenhuma opção selecionada</option>
                                        <option v-for="curreg in choiceProccessList" v-bind:key="curreg.id_processo" v-bind:value="curreg.id_processo">{{ curreg.descricao }}</option>
                                    </select>
                                </div>

                                <div class="col-12 col-sm-4 col-md-4" v-if="choiceProccess != null && choiceCompany != null">
                                    <label for="companyTask">Tipo:</label>
                                    <select class="form-control form-control-sm" id="proccessTask" v-model="choiceType" @change="selectQuestion">
                                        <option v-bind:value="null">Nenhuma opção selecionada</option>
                                        <option v-for="curreg in choiceTypeList" v-bind:key="curreg.id_tipo_processo" v-bind:value="curreg.id_tipo_processo">{{ curreg.titulo }}</option>
                                    </select>
                                </div>

                                <div class="col-12" v-if="choiceCompany != null && choiceCompany != '' && choiceProccess != null && choiceProccess != '' && choiceType != null && choiceType != ''">
                                    <button class="btn btn-sm btn-block btn-primary" type="button" @click="changeStep(); saveData()">
                                        Confirmar seleção
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="choiceTypeTask == 2">
                        Automatico
                    </div>
                </div>
                <div v-if="step == 1">
                    <div v-if="choiceTypeTask == 1">
                        <form class="row">
                            <div class="col-12 col-sm-4 col-md-4">
                                <div class="card bg-primary shadow">
                                    <div class="card-header text-center text-white">
                                        <small>Empresa</small>
                                    </div>
                                    <div class="card-body text-center">
                                        <span class="text-white font-weight-bold">
                                            [{{ saveDataCompany.sigla}}] - {{saveDataCompany.name}}
                                            <br/>
                                            <small v-if="saveDataCompany.resp != null">
                                                Responsável: {{ saveDataCompany.resp.name }}
                                            </small>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4">
                                <div class="card bg-primary shadow">
                                    <div class="card-header text-center text-white">
                                        <small>Processo</small>
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="row">
                                            <div class="col-12 d-flex justify-content-center text-white">
                                                <span class=" text-white font-weight-bold">
                                                    {{ saveDataProccess.name }}
                                                    <br/>
                                                    <small v-if="saveDataProccess.resp != null">
                                                        Responsável: {{ saveDataProccess.resp.name }}
                                                    </small>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4">
                                <div class="card bg-primary shadow">
                                    <div class="card-header text-center text-white">
                                        <small>Tipo (SLA: {{ saveDataType.sla }})</small>
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="row">
                                            <div class="col-12 d-flex justify-content-center text-white">
                                                <span class=" text-white font-weight-bold">
                                                    {{ saveDataType.title }}
                                                    <br/>
                                                    <small>
                                                        {{ saveDataType.subtitle }}
                                                    </small>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="type" v-bind:value="choiceTypeTask" required>
                            <input type="hidden" name="idCompany" v-bind:value="choiceCompany" required>
                            <input type="hidden" name="idProccess" v-bind:value="choiceProccess" required>
                            <input type="hidden" name="idTask" v-bind:value="choiceTypeTask" required>

                            <div class="col-12" v-for="curreg in choiceQuestions" v-bind:key="curreg.id_questao">
                                <label v-bind:for="'idQuestion_' + curreg.id_questao">{{ curreg.titulo }}</label>
                                <input v-if="curreg.tipo == 'date'" type="date" class="form-control form-control-sm" v-bind:name="'idQuestion_' + curreg.id_questao" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" required>
                                <input v-else-if="curreg.tipo == 'datetime'" type="date" class="form-control form-control-sm" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:name="'idQuestion_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" required>
                                <input v-else-if="curreg.tipo == 'text'" type="text" class="form-control form-control-sm" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:name="'idQuestion_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" required>
                                <input v-else-if="curreg.tipo == 'number'" type="number" class="form-control form-control-sm" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:name="'idQuestion_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" required>
                                <textarea v-else class="form-control form-control-sm" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" v-bind:name="'idQuestion_' + curreg.id_questao" required></textarea>
                            </div>

                            <div class="col-12">
                                <div class="card border-primary shadow-sm">
                                    <div class="card-header bg-primary text-white">
                                        <div class="row d-flex justify-content-between">
                                            <span class="col-12 col-sm-8 col-md-9 col-lg-10">Adicionar arquivos</span>
                                            <div class="col-12 col-sm-4 col-md-3 col-lg-2">
                                                <button class="btn btn-sm btn-outline-white btn-block" @click="newRegister">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
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
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div v-if="choiceTypeTask == 2">
                        12313
                    </div>
                    
                </div>
            </div>
            <div class="card-footer text-center bg-primary font-weight-bold text-light">
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
                'company'           :   null,
                'userData'          :   {},
                'step'              :   0,

                'choiceTypeTask'    :   1,
                'choiceCompany'     :   null,
                'choiceProccessList':   [],
                'choiceProccess'    :   null,
                'choiceTypeList'    :   [],
                'choiceType'        :   null,
                'choiceQuestions'   :   null,


                'saveDataCompany'   :   null,
                'saveDataProccess'  :   null,
                'saveDataType'      :   null,


                'typeTasks'         :   [
                    {
                        'title' :   'Manual',
                        'value' :   1,
                    },
                    {
                        'title' :   'Automático',
                        'value' :   2,
                    }
                ],
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
                            vm.company  =   response.data;
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
            changeStep      :   function(){
                var vm  =   this;
                vm.step += 1;
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
            selectTypeTask  :   function(data) {
                var vm                  =   this;
                vm.choiceTypeTask       =   data.value;
                vm.choiceCompany        =   null;
                vm.choiceProccess       =   null;
                vm.choiceType           =   null;
                vm.choiceProccessList   =   [];
                vm.choiceTypeList       =   [];
                vm.step                 =   0;
                
                if(vm.choiceTypeTask == 1) {
                    vm.manualData();
                }
                else if(vm.choiceTypeTask == 2) {
                    vm.automaticData();
                }
            },
            getTypeTask :   function(){
                var vm = this;
                vm.typeTasks.forEach(function(element){
                    if(element.value == vm.choiceTypeTask) {
                        return element;
                    } // if(element.value == vm.choiceTypeTask) { ... }
                }); // vm.typeTasks.forEach(function(element){ ... }
            },
            selectChoiceCompany :   function(){
                var vm  =   this;
                
                vm.choiceProccessList   =   [];
                vm.choiceProccess       =   null;

                vm.company.forEach((element) => {
                    if(element.id_empresa == vm.choiceCompany) {
                        vm.choiceProccessList   =   element.processos;
                    }
                });
            },
            selectChoiceProccess    :   function(){
                var vm  =   this;
                
                vm.choiceTypeList   =   [];
                vm.choiceType       =   null;

                vm.choiceProccessList.forEach((element) => {
                    if(element.id_processo == vm.choiceProccess) {
                        vm.choiceTypeList   =   element.tipoManual;
                    }
                });
            },
            selectQuestion :    function(){
                var vm  =   this;

                vm.choiceTypeList.forEach((element) => {
                    if(element.id_tipo_processo == vm.choiceType) {
                        vm.choiceQuestions  =   element.questoes;
                    } // if(element.id_tipo_processo == vm.choiceType) { ... }
                });
            },
            saveData    :   function(){
                var vm  =   this;

                vm.company.forEach((element) => {
                    if(element.id_empresa == vm.choiceCompany) {
                        vm.saveDataCompany  =   {
                            'id'    :   element.id_empresa,
                            'name'  :   element.descricao,
                            'sigla' :   element.sigla,
                            'imagem':   element.imagem,
                            'resp'  :   element.responsavel
                        };

                        vm.choiceProccessList.forEach((element2) => {
                            if(element2.id_processo == vm.choiceProccess) {
                                vm.saveDataProccess =   {
                                    'id'    :   element2.id_processo,
                                    'name'  :   element2.descricao,
                                    'sigla' :   element2.sigla,
                                    'icon'  :   element2.icone,
                                    'resp'  :   element2.responsavel,
                                };

                                vm.choiceTypeList.forEach((element3) => {
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
            }
        },
        mounted() {
            this.LAYOUT.initData();
            this.PREFERENCES.initData();

            this.userData   =   JSON.parse(this.auth);
            this.initFilter();
        },
    }
</script>
