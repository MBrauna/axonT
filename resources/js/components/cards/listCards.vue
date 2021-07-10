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
                                    <form method="POST" action="/card/edit" class="row was-validated" autocomplete="off" enctype="multipart/form-data">

                                        <input type="hidden" name="idChamado" v-bind:value="curreg.id_chamado">
                                        <input type="hidden" name="_token" v-bind:value="token">

                                        <div class="col-12 text-center">
                                            <a v-bind:href="curreg.describe.url" target="_blank"><small>{{ curreg.titulo }}</small></a>
                                        </div>
                                        <div class="col-12 text-center">
                                            <small class="font-weight-bold text-center" style="color: #fa9016;">Vencimento em {{ curreg.describe.data_vencimento }}</small>
                                        </div>

                                        <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="id_situacao">
                                                    <small>Etapa designada:</small>
                                                </label>
                                                <select class="form-control form-control-sm" name="id_situacao" required>
                                                    <option v-for="situacao in curreg.describe.fluxo" v-bind:key="situacao.id_situacao" v-bind:value="situacao.id_situacao" :selected="situacao.selectedData">
                                                        {{ (situacao.selectedData) ? 'MANTER: '+ situacao.descricao : situacao.descricao }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                            <div class="form-group" v-show="curreg.describe.statusData.marcar_responsavel">
                                                <label for="id_responsavel">
                                                    <small>Responsável pelo atendimento:</small>
                                                </label>
                                                <select class="form-control form-control-sm" name="id_responsavel" required>
                                                    <option value="" selected>Nenhum usuário selecionado</option>
                                                    <option v-for="usuario in curreg.describe.subordinates" v-bind:key="usuario.id" v-bind:value="usuario.id">{{ usuario.name }}</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                            <div class="form-group" v-show="curreg.describe.statusData.data_vencimento">
                                                <label for="dataLimite">
                                                    <small>Data limite:</small>
                                                </label>
                                                <input type="date" class="form-control form-control-sm" name="data_vencimento" v-bind:min="curreg.describe.lastDate" required>
                                            </div>
                                        </div>

                                        <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                            <div class="form-group" v-show="curreg.describe.statusData.data_vencimento">
                                                <label for="dataLimite">
                                                    <small>Hora limite:</small>
                                                </label>
                                                <input type="time" class="form-control form-control-sm" name="hora_vencimento" required>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="entrada">
                                                    <small>Anotação:</small>
                                                </label>
                                                <textarea class="form-control form-control-sm" name="entrada" rows="3"></textarea>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <b-form-checkbox v-model="curreg.describe.file" switch>
                                                Deseja anexar arquivos?
                                            </b-form-checkbox>
                                        </div>

                                        <div class="col-12" v-show="curreg.describe.file">
                                            <ul class="list-group border border-primary">
                                                <li class="list-group-item">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <button type="button" class="btn btn-sm btn-block btn-primary" @click="newRegister">Adicionar arquivo</button>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="list-group-item">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-hover table-striped" id="tableFile">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col">Ações</th>
                                                                    <th scope="col">Arquivo</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr v-for="(curreg, idx) in fileList" v-bind:key="idx">
                                                                    <td><b-form-file  name="arquivoBPMS[]" size="sm" placeholder="Selecione o(s) arquivo(s) ..." drop-placeholder="Solte seu(s) arquivo(s) aqui ..." required></b-form-file></td>
                                                                    <td><button type="button" class="btn btn-sm btn-block btn-outline-danger" @click="removeRegister(idx)"><i class="fas fa-trash"></i></button></td>
                                                                </tr>
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <th scope="col">Ações</th>
                                                                    <th scope="col">Arquivo</th>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="col-12">
                                            <button type="submit" class="btn btn-block btn-sm btn-primary">Aplicar tarefa #{{ curreg.id_chamado }}</button>
                                        </div>
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
                loading:    true,
                content:    [],
                fileList:   [],
            }
        },
        methods: {
            newRegister :   function(){
                var vm                  =   this;
                /*var hash                =   Math.floor(Math.random() * 999999);

                var tbodyRef            =   document.getElementById('tableFile').getElementsByTagName('tbody')[0];
                var newRow              =   tbodyRef.insertRow();

                var cellFile            =   newRow.insertCell(0);
                var cellAction          =   newRow.insertCell(1);

                cellFile.innerHTML      =   '';
                cellAction.innerHTML    =   '';*/
                this.fileList.push(1);
            },
            removeRegister  :   function(counter){
                var vm = this;
                vm.fileList.splice(counter,1);
            },
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
