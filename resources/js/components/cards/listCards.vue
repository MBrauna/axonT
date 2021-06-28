<template>
    <div class="card mt-2">
        <div class="card-header bg-primary text-white text-center font-weight-bold">
            Cartões de tarefas para execução
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12" v-if="loading">
                    <center><logo :size="100"></logo></center>
                </div>
                <div class="col-12" v-else>
                    123
                </div>
            </div>
        </div>
        <div class="card-footer text-center">
            <small> 1nesstech - {{ new Date().getFullYear() }}</small>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'token', 'bearer'
        ],
        data() {
            return {
                loading: true,
                content: []
            }
        },
        methods: {
            getData :   function(){
                try {
                    var vm      = this;
                    vm.loading  = true;

                    var header   = {
                        'headers'   :   {
                            'Authorization' :   'Bearer ' + this.bearer,
                        },
                    };

                    var request = {
                        '_token'        :   vm.token,
                        'idCompany'     :   vm.PREFERENCES.getCompany(),
                        'idProcess'     :   vm.PREFERENCES.getProccess(),
                        'idType'        :   vm.PREFERENCES.getType(),
                    };

                    axios.post('/api/card/list',request, header)
                    .then(function (response) {
                        vm.loading  =   false;
                        if(response.status === 200) {
                            vm.content  =   respose.data;
                        }
                        else {
                            Vue.$toast.error('Não foi possível coletar informações de cartões');
                        }
                    })
                    .catch(function (error) {
                        Vue.$toast.error('Não foi possível coletar informações de cartões');
                        vm.isBusy   =   false;
                    });
                }
                catch(error) {
                    Vue.$toast.error('Não foi possível completar a operação! Verifique.');
                }
            }
        },
        mounted() {
            this.LAYOUT.initData();
            this.PREFERENCES.initData();
            this.getData();
        },
    }
</script>
