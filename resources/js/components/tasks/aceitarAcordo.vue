<template>
    <div class="row">
        <!-- Tipo ENTRADA -->
        <div class="col-12" v-if="data.tipo == 1 && data.permission.origem && data.trust.origem">
            <button class="btn btn-sm btn-block btn-danger">
                Cancelar cliente
            </button>
        </div>

        <div class="col-12" v-if="data.tipo == 1 && data.permission.origem && !data.trust.origem">
            <button class="btn btn-sm btn-block btn-success">
                Firmar cliente
            </button>
        </div>

        <div class="col-12" v-if="data.tipo == 1 && !data.permission.origem && data.trust.origem">
            <button class="btn btn-sm btn-block btn-success" disabled>
                Firmado cliente
            </button>
        </div>

        <div class="col-12" v-if="data.tipo == 1 && !data.permission.origem && !data.trust.origem">
            <button class="btn btn-sm btn-block btn-danger" disabled>
                Cancelado cliente
            </button>
        </div>

        <!-- Tipo SAÍDA -->
        <div class="col-12 col-sm-6 col-md-6" v-if="data.tipo == 2 && data.permission.origem && data.trust.origem">
            <button class="btn btn-sm btn-block btn-danger">
                Cancelar fornec.
            </button>
        </div>

        <div class="col-12 col-sm-6 col-md-6" v-if="data.tipo == 2 && data.permission.origem && !data.trust.origem">
            <button class="btn btn-sm btn-block btn-success">
                Firmar fornec.
            </button>
        </div>

        <div class="col-12 col-sm-6 col-md-6" v-if="data.tipo == 2 && !data.permission.origem && data.trust.origem">
            <button class="btn btn-sm btn-block btn-success" disabled>
                Firmado fornec.
            </button>
        </div>

        <div class="col-12 col-sm-6 col-md-6" v-if="data.tipo == 2 && !data.permission.origem && !data.trust.origem">
            <button class="btn btn-sm btn-block btn-danger" disabled>
                Cancelado fornec.
            </button>
        </div>
        
        






        <!-- Tipo SAÍDA -->
        <div class="col-12 col-sm-6 col-md-6" v-if="data.tipo == 2 && data.permission.destino && data.trust.destino">
            <button class="btn btn-sm btn-block btn-danger">
                Cancelar cliente
            </button>
        </div>

        <div class="col-12 col-sm-6 col-md-6" v-if="data.tipo == 2 && data.permission.destino && !data.trust.destino">
            <button class="btn btn-sm btn-block btn-success">
                Firmar cliente
            </button>
        </div>

        <div class="col-12 col-sm-6 col-md-6" v-if="data.tipo == 2 && !data.permission.destino && data.trust.destino">
            <button class="btn btn-sm btn-block btn-success" disabled>
                Firmado cliente
            </button>
        </div>

        <div class="col-12 col-sm-6 col-md-6" v-if="data.tipo == 2 && !data.permission.destino && !data.trust.destino">
            <button class="btn btn-sm btn-block btn-danger" disabled>
                Cancelado cliente
            </button>
        </div>

    </div>
</template>

<script>
    export default {
        props: [
            'token','bearer','data'
        ],
        components: {
        },
        data() {
            return {
                
            }
        },
        methods: {
            alterData       :   function(){
                try {
                    var vm      =   this;
                    var header   = {
                        'headers'   :   {
                            'Authorization' :   'Bearer ' + this.bearer,
                        },
                    };
                    var request = {
                        '_token'            :   vm.token,
                    };

                    axios.post('/api/tasks/changeAutomaticStatus',request,header)
                    .then(function (response) {
                        vm.typeTasks.forEach(function(element, index){
                            if(element.value == 1) {
                                vm.typeTasks[index].disabled    =   response.verificador;
                                console.log(response.verificador);
                            }
                        }); // vm.typeTasks.forEach(function(element){ ... }

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
            }
        },
        mounted() {
            this.LAYOUT.initData();
            this.PREFERENCES.initData();

            //this.initFilter();
        },
    }
</script>
