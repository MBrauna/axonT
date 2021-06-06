<template>
    <div class="card border border-primary shadow">
        <div class="card-header bg-primary text-white text-center">
            Escolha uma opção abaixo:
        </div>
        <div class="card-body">
            <div v-if="loading" class="row">
                <div class="col-12">
                    <center><logo :size="100"></logo></center>
                </div>
            </div>
            <div v-else class="row">
                <div v-if="content.length <= 0" class="col-12 d-flex justify-content-center text-center">
                    <div class="row">
                        <div class="col-12">
                            <h3 class="text-center text-primary font-weight-bold">
                                Usuário não possui permissão!
                            </h3>
                            <br>
                        </div>
                        <div class="col-12">
                            <span>Verifique com o administrador do sistema</span>
                        </div>
                    </div>
                </div>
                <div v-else class="col-12 col-sm-6 col-md-6 col-lg-6" v-for="curreg in content" v-bind:key="curreg.id_usuario_config">
                    <form method="POST" action="">
                        <input type="hidden" name="_token" v-bind:value="token">
                        <input v-if="curreg.permission"  type="hidden" name="id_configuracao" v-bind:value="curreg.id_configuracao">
                        <button v-bind:type="curreg.permission ? 'submit' : 'button'" class="btn btn-outline-primary btn-block btn-sm" :disabled="!curreg.permission">
                            {{ curreg.configData.titulo }}
                        </button>
                    </form>
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
                'yearCopright'  :   new Date().getFullYear(),
                'typeChoice'    :   null,
                'choice'        :   -1,
                'step'          :   0,
                'content'       :   [],
                'loading'       :   true,
            }
        },
        methods: {
            selectChoice    :   function(data){
                var vm          =   this;

                vm.choice       =   data.value;
                vm.typeChoice   =   data;
                vm.step         =   1;
            },
            reselectChoice  :   function(){
                var vm          =   this;
                vm.typeChoice   =   null;
                vm.step         =   0;
            },
            initFilter       :   function(){
                try {
                    var vm      =   this;

                    vm.loading  =   true;
                    vm.content  =   [];

                    var header   = {
                        'headers'   :   {
                            'Authorization' :   'Bearer ' + this.bearer,
                        },
                    };
                    var request = {
                        '_token'            :   vm.token,
                    };

                    axios.post('/api/util/getUserOptions',request,header)
                    .then(function (response) {
                        vm.loading  =   false;
                        vm.content  =   response.data;

                    })
                    .catch(function(retorno){
                        vm.loading  =   false;
                    });
                } // try { ... }
                catch(error) {
                    vm.loading  =   false;
                } // catch(error) { ... }
            }
        },
        mounted() {
            this.LAYOUT.initData();
            this.PREFERENCES.initData();

            this.initFilter();
        },
    }
</script>
