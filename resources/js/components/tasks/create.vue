<template>
    <div class="card border border-primary shadow">
        <div class="card-header bg-primary text-white text-center">
            Abertura de solicitação de serviço
        </div>
        <div class="card-body">
            <div class="row" v-if="step == 0">
                <div v-bind:class="'col-' + (12/typeTasks.length)" v-for="curreg in typeTasks" v-bind:key="curreg.value" >
                    <button v-bind:class="'btn btn-s btn-block ' + (choice == curreg.value ? 'btn-primary' : 'btn-outline-primary')" @click="selectChoice(curreg)">
                        {{ curreg.title }}
                    </button>
                </div>
            </div>

            <div class="row" v-if="step == 1">
                <div class="col-12">
                    <button class="btn btn-sm btn-block btn-primary" @click="reselectChoice()">
                        <i class="fas fa-reply-all"></i>
                        <span>Desfazer seleção</span>
                        <i class="fas fa-reply-all"></i>
                    </button>
                </div>

                <!-- Conteúdo do sistema -->
                <div class="col-12" v-if="choice == 0">
                    <create-task-manual
                        :auth='auth'
                        :token='token'
                        :bearer='bearer'
                    ></create-task-manual>
                </div>
                <div class="col-12" v-if="choice == 1">
                    <create-task-automatic
                        :auth='auth'
                        :token='token'
                        :bearer='bearer'
                    ></create-task-automatic>
                </div>
                <!-- Conteúdo do sistema -->
            </div>
        </div>
        <div class="card-footer text-center">
            <small class="text-primary">
                1nesstech - {{ yearCopright }}
            </small>
        </div>
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
                'userData'      :   null,
                'yearCopright'  :   new Date().getFullYear(),
                'typeTasks'     :   [
                    {
                        'title' :   'Manual',
                        'value' :   0,
                    },
                    {
                        'title' :   'Automático',
                        'value' :   1,
                    }
                ],
                'typeChoice'    :   null,
                'choice'        :   0,
                'step'          :   0,
            }
        },
        methods: {
            selectChoice    :   function(data){
                var vm          =   this;

                vm.choice       =   data.value;
                vm.typeChoice   =   data;
                vm.step         =   1;
            },
            reselectChoice  :   function(){
                var vm          =   this;
                vm.typeChoice   =   null;
                vm.step         =   0;
            },
        },
        mounted() {
            this.LAYOUT.initData();
            this.PREFERENCES.initData();

            this.userData   =   JSON.parse(this.auth);
            //this.initFilter();
        },
    }
</script>
