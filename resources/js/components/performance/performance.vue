<template>
    <div>
        <button class="btn btn-sm btn-block btn-success" type="button" @click="initQuery" :disabled="loading">
            Atualizar informações
        </button>
        <div v-if="loading" class="card shadow mt-3 mb-3 border-primary bg-white">
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

        <div v-else v-for="curreg in content" v-bind:key="curreg.id_empresa" class="card shadow mt-3 mb-3 border-primary bg-white">
            <div class="card-header text-center bg-primary font-weight-bold text-light">
                {{ curreg.descricao }}
            </div>
            <div class="card-body">
                <div class="row d-flex justify-content-center">
                    <div v-for="datacurreg in curreg.graphs" class="col-12 col-sm-12 col-md-6" v-bind:key="datacurreg.content.id" >
                        <apexchart v-bind:height="datacurreg.chart.height" v-bind:type="datacurreg.chart.type" :options="datacurreg" :series="datacurreg.series"></apexchart>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import VueApexCharts from 'vue-apexcharts';

    export default {
        props: ['token','bearer'],
        components: {
            apexchart: VueApexCharts,
        },
        data() {
            return {
                loading: false,
                content: null,
                userData: null,
            }
        },
        methods: {
            verifyToken     :   function(){
                var vm          =   this;

                if(vm.axiontoken == undefined || vm.axiontoken.trim() == '' || vm.axiontoken == null) {
                    vm.loading      =   true;
                }
            },
            initQuery   :   function(){
                try {
                    var vm      =   this;
                    vm.loading  =   true;
                    vm.header   = {
                        'headers'   :   {
                            'Authorization' :   'Bearer ' + this.bearer,
                        },
                    };

                    vm.request = {
                        '_token'            :   vm.token,
                        'filter'            :   true,
                        'idCompany'         :   vm.PREFERENCES.getCompany(),
                        'idProcess'         :   vm.PREFERENCES.getProccess(),
                        'idType'            :   vm.PREFERENCES.getType(),
                    };

                    axios.post('/api/performance/graph',vm.request,vm.header)
                    .then(function (response) {
                        if(response.status === 200) {
                            vm.content  =   response.data;
                        }
                        vm.loading  =   false;
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
            },
        },
        mounted() {
            this.verifyToken();
            this.LAYOUT.initData();
            this.PREFERENCES.initData();

            this.initQuery();
        },
    }
</script>
