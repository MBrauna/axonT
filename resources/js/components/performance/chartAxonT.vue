<template>
    <div class="row">
        <div class="col-12" v-if="loading">
            <center><logo :size="100"></logo></center>
        </div>

        <div v-else class="col-12">
            <div class="row d-flex justify-content-center">
                <div class="col-12 col-sm-12 col-md-12 col-lg-6" v-for="curreg in content" v-bind:key="curreg.content.id">
                    <apexchart v-bind:height="curreg.chart.height" v-bind:type="curreg.chart.type" :options="curreg" :series="curreg.series"></apexchart>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import VueApexCharts from 'vue-apexcharts';

    export default {
        props: ['token','bearer','company'],
        components: {
            apexchart: VueApexCharts,
        },
        data() {
            return {
                loading: true,
                content: [],
            }
        },
        methods: {
            chartAtt        :   function(){
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
                        'idCompany'         :   vm.company,
                    };

                    axios.post('/api/performance/graph',vm.request,vm.header)
                    .then(function (response) {
                        if(response.status === 200) {
                            vm.content  =   response.data;
                            vm.loading  =   false;
                        }
                        else {
                            vm.loading  =   true;
                        }
                    })
                    .catch(function(retorno){
                        Vue.$toast.error('Não foi possível obter os dados da requisição! Verifique permissões.');
                    });
                } // try { ... }
                catch(error) {
                    Vue.$toast.error('Erro desconhecido durante a execução! Verifique com o administrador do sistema.');
                } // catch(error) { ... }
            }, // chartAtt        :   function(){ ... }
            
            
        },
        mounted() {
            this.LAYOUT.initData();
            this.PREFERENCES.initData();
            this.chartAtt();
        },
    }
</script>
