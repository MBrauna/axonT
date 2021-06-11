<template>
    <div class="card border border-primary shadow mt-2">
        <div class="card-header bg-primary text-white text-center">
            Cadastro de solicitação de serviço
        </div>
        <div class="card-body">
            <div class="row">
                <div v-if="loading" class="col-12">
                    <center><logo :size="100"></logo></center>
                </div>
                <div v-else div class="col-12">
                    <div v-if="!permission" class="row">
                        <div class="col-12 text-center d-flex justify-content-center text-primary font-weight-bold">
                            <h3>Você não tem permissão para acessar esta página</h3>
                        </div>
                    </div>
                    <div v-else>
                        <div v-if="step==0" class="row">
                            <!-- Step 0 -->
                            <!-- Seleção de configurações -->
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                <label for="companyTask">Empresa: {{ companyChoice }}</label>
                                <select class="form-control form-control-sm" v-model="companyChoice" @change="proccessChoice = null; typeChoice = null;">
                                    <option v-bind:value="null">Nenhuma opção selecionada</option>
                                    <option v-for="curreg in content" v-bind:key="curreg.id_empresa" v-bind:value="curreg.id_empresa">{{ curreg.descricao }}</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                <label for="companyTask">Processo: </label>
                                <select class="form-control form-control-sm" v-model="proccessChoice" :disabled="companyChoice == null" @change="typeChoice = null;">
                                    <option v-bind:value="null">Nenhuma opção selecionada</option>
                                    <option v-for="curreg in content.filter(function(item){ return (item.id_empresa === companyChoice)}).length === 0 ? [] : content.filter(function(item){ return (item.id_empresa === companyChoice)})[0].allProccess" v-bind:key="curreg.id_processo" v-bind:value="curreg.id_processo">{{ curreg.descricao }}</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                <label for="companyTask">Tipo:</label>
                                <select class="form-control form-control-sm" v-model="typeChoice" :disabled="((companyChoice == null) || (proccessChoice == null))">
                                    <option v-bind:value="null">Nenhuma opção selecionada</option>
                                    <option v-for="curreg in content.filter(function(item){ return (item.id_empresa === companyChoice)}).length === 0 ? [] : (content.filter(function(item){ return (item.id_empresa === companyChoice)})[0].allProccess.filter(function(item) { return item.id_processo === proccessChoice }).length === 0 ? [] : content.filter(function(item){ return (item.id_empresa === companyChoice)})[0].allProccess.filter(function(item) { return item.id_processo === proccessChoice })[0].manualType)" v-bind:key="curreg.id_tipo_processo" v-bind:value="curreg.id_tipo_processo">{{ curreg.titulo }}</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                <button class="btn btn-primary btn-sm btn-block" @click="step=1;">Abrir solicitação de serviço</button>
                            </div>
                            <!-- Step 0 -->
                            <!-- Seleção de configurações -->
                        </div>
                        <form method="POST" v-if="step===1" class="row was-validated" autocomplete="off">
                            <!-- Step 1 -->
                            <!-- Abertura do chamado -->
                            <input type="hidden" name="_token" v-bind:value="token">
                            <input type="hidden" name="idCompany" v-bind:value="companyChoice" required>
                            <input type="hidden" name="idProccess" v-bind:value="proccessChoice" required>
                            <input type="hidden" name="idType" v-bind:value="typeChoice" required>


                            123


                            <!-- Abertura do chamado -->
                            <!-- Step 1 -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-center">
            <small class="text-primary">
                1nesstech - {{ yearCopright }}
            </small>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'token','bearer'
        ],
        components: {
        },
        data() {
            return {
                'loading'       :   true,
                'yearCopright'  :   new Date().getFullYear(),
                'permission'    :   false,
                'step'          :   0,

                'content'       :   null,
                'companyChoice' :   null,
                'proccessChoice':   null,
                'typeChoice'    :   null,
            }
        },
        methods: {
            verifyPermission  :   function(){
                try {
                    var vm      =   this;
                    vm.loading  =   true;

                    var header   = {
                        'headers'   :   {
                            'Authorization' :   'Bearer ' + this.bearer,
                        },
                    };
                    var request = {
                        '_token'            :   vm.token,
                        'id_configuracao'   :   1,
                    };

                    axios.post('/api/util/getPermission',request,header)
                    .then(function (response) {
                        if(response.status === 200) {
                            if(!response.data.permission) {
                                vm.loading      = false;
                                vm.permission   = false;
                            }
                            else {
                                vm.permission   = true;
                                vm.getData();
                            }
                        } // if(response.status === 200) { ... }
                        else {
                            Vue.$toast.error('Não foi possível validar as permissões! Verifique com o administrador.');
                        }
                    })
                    .catch(function(retorno){
                        Vue.$toast.error('Ocorreu um erro desconhecido! Verifique.');
                    });
                } // try { ... }
                catch(error) {
                    Vue.$toast.error('Ocorreu um erro desconhecido! Verifique.');
                } // catch(error) { ... }
            }, // verifyPermission : function(){ ... }

            getData  :   function(){
                try {
                    var vm      =   this;
                    vm.loading  =   true;

                    var header   = {
                        'headers'   :   {
                            'Authorization' :   'Bearer ' + vm.bearer,
                        },
                    };
                    var request = {
                        '_token'            :   vm.token,
                    };

                    axios.post('/api/task/getDataManual',request,header)
                    .then(function (response) {
                        if(response.status === 200) {
                            vm.loading      =   false;
                            vm.content      =   response.data;
                        } // if(response.status === 200) { ... }
                        else {
                            Vue.$toast.error('Não foi possível validar as permissões! Verifique com o administrador.');
                        }
                    })
                    .catch(function(retorno){
                        Vue.$toast.error('Ocorreu um erro desconhecido! Verifique.');
                    });
                } // try { ... }
                catch(error) {
                    Vue.$toast.error('Ocorreu um erro desconhecido! Verifique.');
                } // catch(error) { ... }
            }, // getData : function(){ ... }
        },
        mounted() {
            this.LAYOUT.initData();
            this.PREFERENCES.initData();

            this.loading    =   true;
            this.verifyPermission();
        },
    }
</script>
