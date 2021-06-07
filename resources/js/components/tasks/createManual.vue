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
                    <div v-else class="row">
                        123
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
                            vm.loading      =   false;

                            console.log(response.data)
                            
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
            getData : function(){
                try {
                    var vm      =   this;
                    vm.loading  =   false;

                    var header   = {
                        'headers'   :   {
                            'Authorization' :   'Bearer ' + this.bearer,
                        },
                    };
                    var request = {
                        '_token'            :   vm.token,
                    };

                    axios.post('/api/util/getPermission',request,header)
                    .then(function (response) {
                        if(response.status === 200) {
                            vm.loading      =   false;

                            console.log(response.data)
                            
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
                    Vue.$toast.error('Erro ao coletar os dados de solicitação de serviço! Verifique.');
                }
            } // getData : function(){ ... }
        },
        mounted() {
            this.LAYOUT.initData();
            this.PREFERENCES.initData();

            this.loading    =   true;
            this.verifyPermission();
        },
    }
</script>
