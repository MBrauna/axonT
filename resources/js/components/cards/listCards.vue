<template>
    <div class="row">
        <div class="col-12">
            <button class="btn btn-sm btn-block btn-success" @click="getData()">Atualizar dados</button>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white text-center font-weight-bold">
                    Cartões de tarefas para execução
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12" v-if="loading">
                            <center><logo :size="100"></logo></center>
                        </div>
                        <div class="col-12 text-center text-primary font-weight-bold" v-else-if="!loading && content == []">
                            Não há cartões disponíveis para seu usuário
                        </div>
                        <div class="col-12" v-else>
                            <ul class="list-group">
                                <li v-bind:class="'list-group-item ' + curreg.describe.status" v-for="curreg in content" v-bind:key="curreg.id_chamado">
                                    <form method="POST" v-bind:action="url" class="row" autocomplete="off" enctype="multipart/form-data">
                                        <div class="col-12 text-center">
                                            <a v-bind:href="curreg.describe.url" target="_blank">
                                                <small>{{ curreg.titulo }}</small>
                                            </a>
                                        </div>
                                        <div class="col-12 text-center">
                                            <small class="font-weight-bold text-center" style="color: #fa9016;">Vencimento em {{ curreg.describe.data_vencimento }}</small>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-group col-12">
                                                <label for="id_situacao">
                                                    <small>Etapa designada:</small>
                                                </label>
                                                <select class="form-control form-control-sm" id="id_situacao" name="id_situacao" required>
                                                    <option v-for="situacao in curreg.describe.fluxo" v-bind:key="situacao" v-bind:value="situacao">
                                                        {{ situacao }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            123
                                        </div>
                                        {{ curreg }}
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <small> 1nesstech - {{ new Date().getFullYear() }}</small>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'token', 'bearer'
        ],
        data() {
            return {
                loading: true,
                content: []
            }
        },
        methods: {
            getData :   function(){
                try {
                    var vm      = this;
                    vm.loading  = true;

                    var header   = {
                        'headers'   :   {
                            'Authorization' :   'Bearer ' + this.bearer,
                        },
                    };

                    var request = {
                        '_token'        :   vm.token,
                        'idCompany'     :   vm.PREFERENCES.getCompany(),
                        'idProcess'     :   vm.PREFERENCES.getProccess(),
                        'idType'        :   vm.PREFERENCES.getType(),
                    };

                    axios.post('/api/card/list',request, header)
                    .then(function (response) {
                        if(response.status === 200) {
                            vm.loading  =   false;
                            vm.content  =   response.data;
                        }
                        else {
                            Vue.$toast.error('Não foi possível coletar informações de cartões');
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                        Vue.$toast.error('Não foi possível coletar informações de cartões');
                        vm.loading   =   false;
                    });
                }
                catch(error) {
                    Vue.$toast.error('Não foi possível completar a operação! Verifique.');
                }
            }
        },
        mounted() {
            this.LAYOUT.initData();
            this.PREFERENCES.initData();
            this.getData();
        },
    }
</script>
