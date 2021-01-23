<template>
    <div class="card shadow mt-3 mb-3 border-primary bg-white">
        <div class="card-header text-center bg-primary font-weight-bold text-light">
            {{ content.descricao }}
        </div>
        <div class="card-body">
            <div class="row d-flex justify-content-center">
                <div class="col-6 col-sm-6" v-for="(curreg, index) in content.graph" v-bind:key="curreg.content.id + '-' + index"  v-bind:id="'chart-' + content.id_empresa + '-' + curreg.content.id" >
                    <chart-axont :contentchart="curreg" :idempresa="content.id_empresa" :descempresa="content.descricao" :idxreg="index"></chart-axont>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        props: ['axiontoken','employee'],
        components: {
        },
        data() {
            return {
                loading: false,
                content: {},
            }
        },
        methods: {
            verifyToken     :   function(){
                var vm          =   this;

                if(vm.axiontoken == undefined || vm.axiontoken.trim() == '' || vm.axiontoken == null) {
                    vm.loading      =   true;
                }
            },
        },
        mounted() {
            this.verifyToken();
            this.content    =   JSON.parse(this.jsongraphs);
        },
    }
</script>
