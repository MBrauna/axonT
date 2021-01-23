<template>
    <div>
        <div v-bind:id="'chart-' + idempresa + '-' + contentchart.content.id"></div>
    </div>
</template>

<script>
    import ApexCharts from 'apexcharts';
    
    export default {
        props: ['contentchart','idempresa','descempresa','idxreg'],
        components: {
            apexchart: ApexCharts,
        },
        data() {
            return {
                loading: false,
                content: {},
                idxdata: null,
            }
        },
        methods: {
            startChart      :   function(){
                var vm      =   this;
                vm.idxdata  =   vm.idxreg;

                var chart   =   new ApexCharts(document.querySelector('#chart-' + vm.idempresa + '-' + vm.contentchart.content.id), vm.contentchart);

                chart.render();
            },

        },
        mounted() {
            this.startChart();
        },
    }
</script>
