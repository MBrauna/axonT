<template>
    <div>
        {{ loading}}
        <br/>
        <br/>
        <br/>
        <br/>
        {{data}}
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        {{ dataPerm}}
    </div>
</template>

<script>

    export default {
        props: [
            'auth', 'token','bearer'
        ],
        components: {
        },
        data() {
            return {
                'loading'   :   true,
                'data'      :   null,
                'dataPerm'  :   null,
            }
        },
        methods: {
            initFilter  :   function(){
                try {
                    var vm      =   this;
                    vm.loading  =   true;
                    var header   = {
                        'headers'   :   {
                            'Authorization' :   'Bearer ' + this.bearer,
                        },
                    };
                    var request = {
                        '_token'            :   vm.token,
                        'filter'            :   false,
                    };

                    axios.post('/api/util/filterTasks',request,header)
                    .then(function (response) {
                        if(response.status === 200) {
                            vm.data  =   response.data;
                        } // if(response.status === 200) { ... }

                        axios.post('/api/tasks/listPermission',request,header)
                        .then(function (responsePerm) {
                            if(response.status === 200) {
                                vm.dataPerm =   responsePerm;
                                vm.loading  =   false;
                            } // if(response.status === 200) { ... }
                        })
                        .catch(function(retorno){
                            console.log('Ocorreu um erro desconhecido! Verifique.');
                            console.log(retorno);
                        });
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
            }, // initFilter  :   function(){ ... }
            
        },
        mounted() {
            this.LAYOUT.initData();
            this.PREFERENCES.initData();

            this.loading    =   true;
            this.userData   =   JSON.parse(this.auth);
            this.initFilter();
        },
    }
</script>
