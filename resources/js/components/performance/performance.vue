<template>
    <div>
        <div v-for="curreg in dataCompany" v-bind:key="curreg.id_empresa" class="card shadow mt-3 mb-3 border-primary bg-white">
            <div class="card-header text-center bg-primary font-weight-bold text-light">
                {{ curreg.descricao }}
            </div>
            <div class="card-body">
                <chart-axont 
                    :token="token"
                    :bearer="bearer"
                    :auth="userData"
                    :idCompany="curreg.id_empresa">
                </chart-axont>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        props: ['token','bearer','auth'],
        components: {
        },
        data() {
            return {
                loading: false,
                content: {},
                userData: null,
                dataCompany: {},
            }
        },
        methods: {
            verifyToken     :   function(){
                var vm          =   this;

                if(vm.axiontoken == undefined || vm.axiontoken.trim() == '' || vm.axiontoken == null) {
                    vm.loading      =   true;
                }
            },
            initCompanies   :   function(){
                try {
                    var vm      =   this;
                    vm.header   = {
                        'headers'   :   {
                            'Authorization' :   'Bearer ' + this.bearer,
                        },
                    };
                    vm.request = {
                        '_token'            :   vm.token,
                        'filter'            :   true,
                        'idUser'            :   vm.userData.id,
                        'idCompany'         :   vm.PREFERENCES.getCompany(),
                        'idProcess'         :   null,
                        'idSituation'       :   null,
                        'createIni'         :   vm.createDateIni == null ? null : vm.createDateIni.toLocaleDateString(),
                        'createEnd'         :   vm.createDateIni == null ? null : vm.createDateEnd.toLocaleDateString(),
                    };

                    axios.post('/api/util/company',vm.request,vm.header)
                    .then(function (response) {
                        if(response.status === 200) {
                            vm.dataCompany  =   response.data;

                            vm.dataCompany
                        }
                        else {
                            console.log('outro');
                        }
                    })
                    .catch(function(retorno){
                        console.log('teste');
                        console.log(retorno);
                    });
                } // try { ... }
                catch(error) {

                } // catch(error) { ... },
            },
            initQuery   :   function(){
                try {
                    var vm      =   this;
                    vm.header   = {
                        'headers'   :   {
                            'Authorization' :   'Bearer ' + this.bearer,
                        },
                    };

                    vm.request = {
                        '_token'            :   vm.token,
                        'filter'            :   true,
                        'idUser'            :   vm.auth.id,
                        'idCompany'         :   vm.PREFERENCES.getCompany(),
                        'idProcess'         :   null,
                        'idSituation'       :   null,
                    };

                    axios.post('/api/performance/graph',vm.request,vm.header)
                    .then(function (response) {
                        if(response.status === 200) {
                            vm.content  =   response.data;
                        }
                    })
                    .catch(function(retorno){
                        console.log(retorno);
                    });
                } // try { ... }
                catch(error) {
                    console.log(error);
                } // catch(error) { ... }
            },
        },
        mounted() {
            this.verifyToken();
            this.LAYOUT.initData();
            this.PREFERENCES.initData();

            this.userData   =   JSON.parse(this.auth);
            this.initCompanies();
        },
    }
</script>
