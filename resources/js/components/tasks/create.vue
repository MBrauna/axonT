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
                            <button type="button" class="btn btn-primary btn-lg btn-block" @click="selectTypeTask(curreg)">{{ curreg.title }}</button>
                        </div>
                    </div>


                    <div v-if="choiceTypeTask == 1">
                        <div v-if="company == null">
                            <center><logo :size="100"></logo></center>
                        </div>
                        <div v-else>
                            <div class="row">
                                <div class="col-12">
                                    <label for="companyTask">Empresa:</label>
                                    <select class="form-control form-control-sm" id="companyTask" v-model="choiceCompany" @change="selectChoiceCompany">
                                        <option v-bind:value="null">Nenhuma opção selecionada</option>
                                        <option v-for="curreg in company" v-bind:key="curreg.id_empresa" v-bind:value="curreg.id_empresa">{{ curreg.descricao }}</option>
                                    </select>
                                </div>

                                <div class="col-12" v-if="choiceCompany != null">
                                    <label for="proccessTask">Processo:</label>
                                    <select class="form-control form-control-sm" id="proccessTask" v-model="choiceProccess" @change="selectChoiceProccess">
                                        <option v-bind:value="null">Nenhuma opção selecionada</option>
                                        <option v-for="curreg in choiceProccessList" v-bind:key="curreg.id_processo" v-bind:value="curreg.id_processo">{{ curreg.descricao }}</option>
                                    </select>
                                </div>

                                {{ choiceTypeList }}

                                <div class="col-12" v-if="choiceProccess != null && choiceCompany != null">
                                    <label for="companyTask">Tipo:</label>
                                    <select class="form-control form-control-sm" id="proccessTask" v-model="choiceType">
                                        <option v-bind:value="null">Nenhuma opção selecionada</option>
                                        <option v-for="curreg in choiceTypeList" v-bind:key="curreg.id_tipo_processo" v-bind:value="curreg.id_tipo_processo">{{ curreg.descricao }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="choiceTypeTask == 2">
                        Automatico
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
            selectTypeTask  :   function(data) {
                var vm              =   this;
                vm.choiceTypeTask   =   data.value;
                vm.step             =   0;
                
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
            manualData  :   function(){
                
            }, // manualData  :   function(){ ... }
            automaticData   :   function(){

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
                    console.log(element);
                    if(element.id_proccess == vm.choiceProccess) {
                        vm.choiceTypeList   =   element.tipoManual;
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
