<template>
    <div class="row d-flex justify-content-center">
        <div v-for="curreg in graphs" v-bind:key="curreg.content.id" class="col-12 col-sm-12 col-md-6" v-bind:id="'chart-' + idCompany + '-' + curreg.content.id">
        </div>
    </div>
</template>

<script>
    import ApexCharts from 'apexcharts';
    
    export default {
        props: ['token','bearer','auth','graphs','idCompany'],
        components: {
            apexchart: ApexCharts,
        },
        data() {
            return {
                loading: false,
            }
        },
        methods: {
            startChart      :   function(curreg){
                var vm      =   this;
                var chart   =   new ApexCharts(document.querySelector('#chart-' + idCompany + '-' + curreg.content.id));

                chart.render();
            }, // startChart      :   function(contentchart){ ... }
            chartAtt        :   function(){
                var vm      =   this;

                vm.graphs.forEach(element => {
                    vm.startChart(element);
                }); // vm.graphs.forEach(element => { ...});
            }, // chartAtt        :   function(){ ... }
            
            
        },
        mounted() {
            this.LAYOUT.initData();
            this.PREFERENCES.initData();
            this.chartAtt();
        },
    }
</script>
