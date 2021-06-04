<template>
    <div class="pb-3">
        <div class="accordion" id="filterMain">
            <div class="card border border-primary">
                <div class="card-header bg-primary" id="filterCompany">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-sm btn-block text-center font-weight-bold text-white" type="button" data-toggle="collapse" data-target="#collapseFilterOne" aria-expanded="true" aria-controls="collapseFilterOne">
                            Filtro geral
                        </button>
                    </h2>
                </div>

                <div id="collapseFilterOne" class="collapse" aria-labelledby="filterCompany" data-parent="#filterMain">
                    <div class="card-body">
                        <div class="row" v-if="loading">
                            <div class="col-12">
                                <center><logo :size="70"></logo></center>
                            </div>
                        </div>
                        <div class="row" v-else>
                            <div class="col-12 col-sm-6 col-md-6">
                                <div class="form-group">
                                    <label for="timerFilter">Tempo de atualização:</label>
                                    <select class="form-control form-control-sm" id="timerFilter" v-model="timerAtt">
                                        <option v-bind:value="null" class="text-primary">Atualização desligada</option>
                                        <option v-bind:value="10" class="text-primary">Atualizar a cada 10 minutos</option>
                                        <option v-bind:value="20" class="text-primary">Atualizar a cada 20 minutos</option>
                                        <option v-bind:value="30" class="text-primary">Atualizar a cada 30 minutos</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-6">
                                <div class="form-group">
                                    <label for="timerFilter">Empresa:</label>
                                    <select class="form-control form-control-sm" id="CompanyFilter" name="CompanyFilter" v-model="idCompany" @change="selectCompany">
                                        <option v-bind:value="null">Todas as empresas</option>
                                        <option v-for="curreg in dataCompany" v-bind:key="curreg.id_empresa" v-bind:value="curreg.id_empresa">{{ curreg.id_empresa }} - {{ curreg.descricao }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-6">
                                <div class="form-group">
                                    <label for="timerFilter">Processo:</label>
                                    <select class="form-control form-control-sm" id="ProccessFilter" name="ProccessFilter" v-model="idProccess" @change="selectProccess">
                                        <option v-bind:value="null">Todos os processos</option>
                                        <option v-for="curreg in dataProccess" v-bind:key="curreg.id_proccess" v-bind:value="curreg.id_proccess">{{ curreg.descricao }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-6">
                                <div class="form-group">
                                    <label for="timerFilter">Tipo:</label>
                                    <select class="form-control form-control-sm" id="TypeFilter" name="TypeFilter" v-model="idType" @change="selectType">
                                        <option v-bind:value="null">Todos os tipos</option>
                                        <option v-for="curreg in dataType" v-bind:key="curreg.id_tipo_processo" v-bind:value="curreg.id_tipo_processo">[{{ curreg.sla }}] - {{ curreg.titulo }}</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import DatePicker from 'vue2-datepicker';
    import 'vue2-datepicker/index.css';

    export default {
        props: [
            'auth', 'token','bearer'
        ],
        components: {
            DatePicker,
        },
        data() {
            return {
                'fulldata'      :   [],
                'loading'       :   false,

                'dataCompany'   :   [],
                'dataProccess'  :   [],
                'dataType'      :   [],

                'timerAtt'      :   null,
                'idCompany'     :   null,
                'idProccess'    :   null,
                'idType'        :   null,
            }
        },
        methods: {
            selectCompany   :   function(){
                var vm      =   this;
                this.PREFERENCES.setCompany(this.idCompany);
                this.PREFERENCES.setProccess(null);
                this.PREFERENCES.setType(null);

                vm.idProccess   =   null;
                vm.idType       =   null;

                vm.dataProccess =   [];
                vm.dataType     =   [];

                try {
                    vm.fulldata.forEach((elementCompany) => {
                        if(elementCompany.id_empresa == vm.idCompany || vm.idCompany == null || vm.idCompany == '' || vm.idCompany == 'null') {
                            elementCompany.processos.forEach((elementProccess) => {
                                if(elementCompany.id_processo == vm.idProccess || vm.idProccess == null || vm.idProccess == '' || vm.idProccess == 'null') {
                                    if(!vm.dataProccess.includes(elementProccess)){
                                        vm.dataProccess.push(elementProccess);
                                    } // if(!vm.dataProccess.includes(elementProccess)){ ... }

                                    elementProccess.tipoAutomatico.forEach((elementType) => {
                                        if(!vm.dataType.includes(elementType)) {
                                            vm.dataType.push(elementType);
                                        } // if(!vm.dataType.includes(elementType)) { ... }
                                    }); // elementProccess.tipoManual.forEach((elementType) => { ...});
                                    elementProccess.tipoManual.forEach((elementType) => {
                                        if(!vm.dataType.includes(elementType)) {
                                            vm.dataType.push(elementType);
                                        } // if(!vm.dataType.includes(elementType)) { ... }
                                    }); // elementProccess.tipoManual.forEach((elementType) => { ... });
                                }
                            }); // elementCompany.processos.forEach((elementProccess) => { ... });
                        } // if(elementCompany.id_empresa == vm.idCompany || vm.idCompany == null || vm.idCompany == '' || vm.idCompany == 'null') { ... }
                    }); // vm.fulldata.forEach((elementCompany) => { ... });
                } // try { ... }
                catch(error) {
                    console.log(error);
                } // catch(error) { ... }
            },
            selectProccess : function(){
                this.PREFERENCES.setProccess(this.idProccess);
                /*var vm      =   this;
                vm.dataType     =   [];

                this.PREFERENCES.setProccess(this.idProccess);
                
                this.PREFERENCES.setType(null);
                vm.idType       =   null;

                try {
                    vm.fulldata.forEach((elementCompany) => {
                        if(elementCompany.id_empresa == vm.idCompany || vm.idCompany == null || vm.idCompany == '' || vm.idCompany == 'null') {
                            elementCompany.processos.forEach((elementProccess) => {
                                if(elementCompany.id_processo == vm.idProccess || vm.idProccess == null || vm.idProccess == '' || vm.idProccess == 'null') {
                                    if(!vm.dataProccess.includes(elementProccess)){
                                        vm.dataProccess.push(elementProccess);
                                    } // if(!vm.dataProccess.includes(elementProccess)){ ... }

                                    elementProccess.tipoAutomatico.forEach((elementType) => {
                                        if(!vm.dataType.includes(elementType)) {
                                            vm.dataType.push(elementType);
                                        } // if(!vm.dataType.includes(elementType)) { ... }
                                    }); // elementProccess.tipoManual.forEach((elementType) => { ...});
                                    elementProccess.tipoManual.forEach((elementType) => {
                                        if(!vm.dataType.includes(elementType)) {
                                            vm.dataType.push(elementType);
                                        } // if(!vm.dataType.includes(elementType)) { ... }
                                    }); // elementProccess.tipoManual.forEach((elementType) => { ... });
                                }
                            }); // elementCompany.processos.forEach((elementProccess) => { ... });
                        } // if(elementCompany.id_empresa == vm.idCompany || vm.idCompany == null || vm.idCompany == '' || vm.idCompany == 'null') { ... }
                    }); // vm.fulldata.forEach((elementCompany) => { ... });
                } // try { ... }
                catch(error) {
                    console.log(error);
                } // catch(error) { ... }
                */
            },
            selectType : function(){
                var vm = this;
                this.PREFERENCES.setType(vm.idType);
            },
            initFilter  :   function(){
                try {
                    var vm      =   this;

                    vm.loading  =   true;

                    vm.header   = {
                        'headers'   :   {
                            'Authorization' :   'Bearer ' + this.bearer,
                        },
                    };
                    vm.request = {
                        '_token'    :   vm.token,
                    };

                    axios.post('/api/util/company',vm.request,vm.header)
                    .then(function (response) {
                        if(response.status === 200) {
                            vm.fulldata     =   response.data;
                            vm.dataCompany  =   response.data;

                            vm.dataProccess =   [];
                            vm.dataType     =   [];

                            try {
                                vm.fulldata.forEach((elementCompany) => {
                                    if(elementCompany.id_empresa == vm.idCompany || vm.idCompany == null || vm.idCompany == '' || vm.idCompany == 'null') {
                                        elementCompany.processos.forEach((elementProccess) => {
                                            if(elementCompany.id_processo == vm.idProccess || vm.idProccess == null || vm.idProccess == '' || vm.idProccess == 'null') {
                                                if(!vm.dataProccess.includes(elementProccess)){
                                                    vm.dataProccess.push(elementProccess);
                                                } // if(!vm.dataProccess.includes(elementProccess)){ ... }

                                                elementProccess.tipoAutomatico.forEach((elementType) => {
                                                    if(!vm.dataType.includes(elementType)) {
                                                        vm.dataType.push(elementType);
                                                    } // if(!vm.dataType.includes(elementType)) { ... }
                                                }); // elementProccess.tipoManual.forEach((elementType) => { ...});
                                                elementProccess.tipoManual.forEach((elementType) => {
                                                    if(!vm.dataType.includes(elementType)) {
                                                        vm.dataType.push(elementType);
                                                    } // if(!vm.dataType.includes(elementType)) { ... }
                                                }); // elementProccess.tipoManual.forEach((elementType) => { ... });
                                            }
                                        }); // elementCompany.processos.forEach((elementProccess) => { ... });
                                    } // if(elementCompany.id_empresa == vm.idCompany || vm.idCompany == null || vm.idCompany == '' || vm.idCompany == 'null') { ... }
                                }); // vm.fulldata.forEach((elementCompany) => { ... });
                            } // try { ... }
                            catch(error) {
                                console.log(error);
                            } // catch(error) { ... }

                            vm.loading  = false;
                        }
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
            } // initFilter  :   function(){ ... }
        },
        mounted() {
            this.LAYOUT.initData();
            this.PREFERENCES.initData();

            this.idCompany  =   this.PREFERENCES.getCompany();
            this.idProccess =   this.PREFERENCES.getProccess();
            this.idType     =   this.PREFERENCES.getType();

            this.initFilter();
        },
    }
</script>
