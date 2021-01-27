<template>
    <div class="row d-flex justify-content-center">
        <div v-for="curreg in content" v-bind:key="'chart-' + idCompany + '-' + curreg.content.id" class="col-12 col-sm-12 col-md-6" v-bind:id="'chart-' + idCompany + '-' + curreg.content.id">
        </div>
    </div>
</template>

<script>
    import ApexCharts from 'apexcharts';
    
    export default {
        props: ['token','bearer','auth','idCompany'],
        components: {
            apexchart: ApexCharts,
        },
        data() {
            return {
                loading: false,
                content: [],
            }
        },
        methods: {
            startChart      :   function(contentchart){
                var vm      =   this;
                var chart   =   new ApexCharts(document.querySelector('#chart-' + vm.idCompany + '-' + contentchart.content.id));

                chart.render();
            },
            chartAtt        :   function(){
                var vm      =   this;

                console.log('executei');
                console.log(vm.content);
                vm.content.forEach(function(contentchart){
                    vm.startChart(contentchart);
                }); // vm.content.forEach(function(contentchart){ ... }
            },
            
            
        },
        mounted() {
            this.LAYOUT.initData();
            this.PREFERENCES.initData();
            this.initQuery();
        },
    }
</script>
