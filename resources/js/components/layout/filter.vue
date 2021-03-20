<template>
    <div>
        <div class="card border border-primary">
            <div class="card-body">
                <div class="row">
                    <div class="col-3 col-xs-3 col-sm-2 col-md-1 text-center">
                        <button class="btn btn-sm btn-block btn-outline-primary" data-toggle="modal" data-target="#filterModal">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                    <div class="col-9 col-xs-9 col-sm-10 col-md-5">
                        <select class="form-control form-control-sm" id="timerFilter" v-model="timerAtt">
                            <option v-bind:value="null" class="text-primary">Atualização desligada</option>
                            <option v-bind:value="10" class="text-primary">Atualizar a cada 10 minutos</option>
                            <option v-bind:value="20" class="text-primary">Atualizar a cada 20 minutos</option>
                            <option v-bind:value="30" class="text-primary">Atualizar a cada 30 minutos</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                        <button class="btn btn-sm btn-primary btn-block" type="button">Atualizar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title text-center text-white" id="filterModalLabel">
                            Filtros da aplicação
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span class="text-white" aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row" v-if="dataCompany == null || dataCompany == []">
                                <div class="col-12 text-center">
                                    <center><logo :size="100"></logo></center>
                                </div>
                            </div>
                            <div class="row" v-else>
                                <h6 class="col-12 text-center text-primary">Informativos</h6>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label for="CompanyFilter">Empresa:</label>
                                        <select class="form-control form-control-sm" id="CompanyFilter" name="CompanyFilter" v-model="idCompany" v-on:change="selectCompany">
                                            <option v-bind:value="null">Todas as empresas</option>
                                            <option v-for="curreg in dataCompany" v-bind:key="curreg.id_empresa" v-bind:value="curreg.id_empresa">{{ curreg.id_empresa }} - {{ curreg.descricao }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label for="ProcessFilter">Processo:</label>
                                        <select class="form-control form-control-sm" id="ProcessFilter" name="ProcessFilter" v-model="idCompany" v-on:change="selectCompany">
                                            <option v-bind:value="null">Todas as empresas</option>
                                            <option v-for="curreg in dataCompany" v-bind:key="curreg.id_empresa" v-bind:value="curreg.id_empresa">{{ curreg.id_empresa }} - {{ curreg.descricao }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label for="SituationFilter">Situação:</label>
                                        <select class="form-control form-control-sm" id="SituationFilter" name="SituationFilter" v-model="idCompany" v-on:change="selectCompany">
                                            <option v-bind:value="null">Todas as empresas</option>
                                            <option v-for="curreg in dataCompany" v-bind:key="curreg.id_empresa" v-bind:value="curreg.id_empresa">{{ curreg.id_empresa }} - {{ curreg.descricao }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label for="UserFilter">Usuário:</label>
                                        <select class="form-control form-control-sm" id="UserFilter" name="UserFilter" v-model="idCompany" v-on:change="selectCompany">
                                            <option v-bind:value="null">Todas as empresas</option>
                                            <option v-for="curreg in dataCompany" v-bind:key="curreg.id_empresa" v-bind:value="curreg.id_empresa">{{ curreg.id_empresa }} - {{ curreg.descricao }}</option>
                                        </select>
                                    </div>
                                </div>

                                <!--
                                    <h6 class="col-12 text-center text-primary">Data de criação</h6>
                                    <date-picker class="col-12 col-sm-6" id="createDateIni" v-model="createDateIni" type="datetime" placeholder="Data de criação inicial"></date-picker>
                                    <date-picker class="col-12 col-sm-6" id="createDateEnd" v-model="createDateEnd" type="datetime" placeholder="Data de criação final"></date-picker>
                                -->
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-primary text-white">
                        <button type="button" class="btn btn-sm btn-outline-light" data-dismiss="modal">
                            Fechar filtros
                        </button>
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
                'userData'      :   null,
                'dataCompany'   :   null,

                'timerAtt'      :   null,
                'idCompany'     :   null,
            }
        },
        methods: {
            selectCompany   :   function(){
                this.PREFERENCES.setCompany(this.idCompany);
            },
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
                        'idCompany'         :   vm.idCompany,
                        'idProcess'         :   null,
                        'idSituation'       :   null,
                    };

                    axios.post('/api/util/company',vm.request,vm.header)
                    .then(function (response) {
                        if(response.status === 200) {
                            vm.dataCompany  =   response.data;
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

            this.userData   =   JSON.parse(this.auth);

            this.idCompany  =   this.PREFERENCES.getCompany();

            this.initFilter();
        },
    }
</script>
