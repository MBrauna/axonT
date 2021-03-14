<template>
    <div>
        
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
                'data'      :   null,
                'userData'  :   null,
            }
        },
        methods: {
            initFilter  :   function(){
                try {
                    var vm      =   this;
                    vm.header   = {
                        'headers'   :   {
                            'Authorization' :   'Bearer ' + this.bearer,
                        },
                    };
                    vm.request = {
                        '_token'            :   vm.token,
                        'filter'            :   false,
                    };

                    axios.post('/api/util/filterTasks',vm.request,vm.header)
                    .then(function (response) {
                        if(response.status === 200) {
                            vm.data =   response.data;
                        } // if(response.status === 200) { ... }
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

            this.userData   =   JSON.parse(this.auth);
            this.initFilter();
        },
    }
</script>
