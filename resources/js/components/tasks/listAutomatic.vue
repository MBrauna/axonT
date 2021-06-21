<template>
    <div>
        <div>
            <div class="card border border-primary shadow-sm" v-if="isBusy">
                <div class="card-body">
                    <center><logo :size="100"></logo></center>
                </div>
            </div>
            <div class="card border border-primary shadow-sm" v-else>
                <div class="card-header">
                    <button type="submit" class="btn btn-success btn-sm btn-block" @click="consultaDados">
                        Atualizar lista
                    </button>
                </div>
                <div class="card-body">
                    <b-table
                        id="tabela-solicitacao"
                        responsive
                        fixed
                        :striped="false"
                        :small="true"
                        :busy="isBusy"
                        :hover="true"
                        :items="items"
                        :fields="fields"
                        :sort-by.sync="sortBy"
                        :sort-desc.sync="sortDesc"
                        sticky-header="55vh"
                    >
                        <template v-slot:cell(btnAprovacao)="data">
                            <aceitar-acordo :token="token" :bearer="bearer" :data="data.value"></aceitar-acordo>
                        </template>

                        <template v-slot:cell(acao)="data">
                            <div class="row">
                                <div class="col-12 col-sm-6 col-md-6">
                                    <b-button @click="openModal(data.value.id)" class="btn btn-sm btn-block btn-success">
                                        <i class="fas fa-eye"></i>
                                    </b-button>
                                </div>
                                <div class="col-12 col-sm-6 col-md-6">
                                    <button type="button" class="btn btn-sm btn-block btn-danger" @click="removeItem(data.value.id)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </b-table>
                </div>
                <div class="card-footer bg-primary">
                    <small class="d-block text-white text-center">Ordenado por {{ coletaNome(sortBy) }} - {{ sortDesc ? 'decrescente' : 'crescente'}} </small>
                </div>
            </div>
        </div>

        <b-modal
            v-for="(curinfo, iddx) in items"
            v-bind:key="curinfo.acao.id"
            v-model="curinfo.acao.modal"
            centered
            size="xl"
            class="bg-primary text-center"
            :title="(items[iddx].listaQuestao.length > 0) ? 'Ver/Editar objeto de troca' : null"
            :hide-footer='true'
            @close="curinfo.acao.modal = false"
        >
            <form method="POST" action="/task/editAutomatic" class="row was-validated" v-if="items[iddx].listaQuestao.length > 0">
                <!-- Dados do entregável -->
                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="form-group">
                        <label for="entregavel">Entregável:</label>
                        <input class="form-control form-control-sm" type="text" name="entregavel" minlength="10" maxlength="250" id="entregavel" placeholder="Informe o título do entregável" v-model="curinfo.titulo" @change="trimData()" required>
                    </div>
                </div>
                <!-- Dados do entregável -->

                <!-- Dados de periodicidade -->
                <div class="col-12 col-sm-12 col-md-6">
                    <div class="form-group">
                        <label for="periodicidade">Periodicidade:</label>
                        <select class="form-control form-control-sm" id="periodicidade" v-model="curinfo.periodicidade" required>
                            <option value="">Nenhum período escolhido</option>
                            <option v-for="conteudo in curinfo.allPeriodics" v-bind:key="conteudo.id" v-bind:value="conteudo.id">{{ conteudo.name }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6">
                    <div class="form-group">
                        <label for="qtde_periodicidade">Tempo {{ (curinfo.periodicidade == undefined || curinfo.periodicidade == 'null' || curinfo.periodicidade == null || curinfo.periodicidade == '') ? '' : 'em ' + curinfo.allPeriodics.find(element => curinfo.periodicidade == element.id)['name'] }}:</label>
                        <input type="number" min="1" max="9999" class="form-control form-control-sm" id="qtde_periodicidade" name="qtde_periodicidade" v-bind:value="curinfo.qtde_periodicidade" required>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        <label for="periodicidade_data">Data de início:</label>
                        <input type="date" v-bind:min="curinfo.menorHora" class="form-control form-control-sm" id="periodicidade_data" name="periodicidade_data" v-bind:value="curinfo.data_inicial" required>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        <label for="periodicidade_data">Hora de início:</label>
                        <input type="time" class="form-control form-control-sm" id="periodicidade_data" name="periodicidade_data" v-bind:value="moment(String(curinfo.data_inicial)).format('hh:mm')" required>
                    </div>
                </div>
                <!-- Dados de periodicidade -->
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <div class="form-group" v-for="(curreg, idx) in items[iddx].listaQuestao" v-bind:key="curreg.id_agendamento_item">
                        <label v-bind:for="'idAgendamento_' + curreg.id_agendamento_item">{{ curreg.questao }}</label>

                        <input v-if="curreg.tipo == 'date'" type="date" class="form-control form-control-sm" v-bind:name="'idAgendamento_' + curreg.id_questao" v-bind:id="'idAgendamento_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" @change="trimData" :required="curreg.obrigatorio" v-bind:value="curreg.resposta">
                        <div class="row" v-else-if="curreg.tipo == 'datetime'">
                            <div class="col-12 col-sm-6 col-md-6">
                                <input type="date" class="form-control form-control-sm" v-bind:id="'idAgendamento_' + curreg.id_questao + '_date'" v-bind:name="'idAgendamento_' + curreg.id_questao + '_date'" v-bind:placeholder="curreg.placeholder" :required="curreg.obrigatorio" v-bind:value="moment(String(curreg.resposta)).format('L')">
                            </div>
                            <div class="col-12 col-sm-6 col-md-6">
                                <input type="time" class="form-control form-control-sm" v-bind:id="'idAgendamento_' + curreg.id_questao + '_time'" v-bind:name="'idAgendamento_' + curreg.id_questao + '_time'" v-bind:placeholder="curreg.placeholder" :required="curreg.obrigatorio" v-bind:value="moment(String(items[iddx].listaQuestao[idx].resposta)).format('hh:mm')">
                            </div>
                        </div>
                        <input v-else-if="curreg.tipo == 'text'" type="text" class="form-control form-control-sm" v-bind:id="'idAgendamento_' + curreg.id_questao" v-bind:name="'idAgendamento_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" v-bind:value="items[iddx].listaQuestao[idx].resposta" :required="curreg.obrigatorio">
                        <input v-else-if="curreg.tipo == 'email'" type="email" class="form-control form-control-sm" v-bind:id="'idAgendamento_' + curreg.id_questao" v-bind:name="'idAgendamento_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" v-bind:value="items[iddx].listaQuestao[idx].resposta" :required="curreg.obrigatorio">
                        <input v-else-if="curreg.tipo == 'number'" type="number" class="form-control form-control-sm" v-bind:id="'idAgendamento_' + curreg.id_questao" v-bind:name="'idAgendamento_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" v-bind:value="items[iddx].listaQuestao[idx].resposta" :required="curreg.obrigatorio">
                        <select v-else-if="curreg.tipo === 'user'" class="form-control form-control-sm" v-bind:placeholder="curreg.placeholder" v-bind:id="'idAgendamento_' + curreg.id_questao" v-bind:name="'idAgendamento_' + curreg.id_questao" v-bind:value="items[iddx].listaQuestao[idx].resposta" :required="curreg.obrigatorio">
                            <option>Nenhum usuário selecionado</option>
                            <option v-for="curuser in userList" v-bind:key="curuser.id" v-bind:value="curuser.id">{{ curuser.name }}</option>
                        </select>
                        <textarea rows="5" v-else class="form-control form-control-sm" v-bind:id="'idAgendamento_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" v-bind:name="'idAgendamento_' + curreg.id_questao" v-bind:value="items[iddx].listaQuestao[idx].resposta" :required="curreg.obrigatorio"></textarea>
                    </div>
                </div>
                <div class="col-12">
                    <button type="buttom" class="btn btn-primary btn-sm btn-block">
                        Confirmar alterações
                    </button>
                </div>
            </form>
            <div class="row" v-else>
                <div class="col-12">
                    <h3 class="text-primary text-center font-weight-bold">
                        <i class="fas fa-frown-open"></i> Não há questões cadastradas!
                    </h3>
                </div>
            </div>
        </b-modal>
    </div>
</template>

<script>
    export default {
        props: [
            'token','bearer'
        ],
        data() {
            return {
                exemplo: false,
                filtroConteudo:{},
                filter:{},
                isBusy: true,
                sortBy: 'idDesc',
                sortDesc: false,
                requisicao : {},
                perPage: 7,
                currentPage: 1,
                totalRows: 0,
                fields: [
                    { key: 'idDesc',            label: '#ID',                   sortable: true,   thStyle: { width: '5em !important',  background: '#000A44', color: '#ffffff'},  },
                    { key: 'btnAprovacao',      label: 'Acordo',                sortable: true,   thStyle: { width: '20em !important', background: '#000A44', color: '#ffffff'},    stickyColumn: true, },
                    { key: 'tipoDesc',          label: 'Tipo',                  sortable: true,   thStyle: { width: '14em !important', background: '#000A44', color: '#ffffff'}, },
                    { key: 'procOrigem',        label: 'Processo Origem',       sortable: true,   thStyle: { width: '14em !important', background: '#000A44', color: '#ffffff'}, },
                    { key: 'tipoOrigem',        label: 'Tipo Processo Origem',  sortable: true,   thStyle: { width: '14em !important', background: '#000A44', color: '#ffffff'},    class: 'text-center',},
                    { key: 'usuarioOrigem',     label: 'Responsável Origem',    sortable: true,   thStyle: { width: '14em !important', background: '#000A44', color: '#ffffff'},    class: 'text-center',},
                    { key: 'titulo',            label: 'Entregável',            sortable: true,   thStyle: { width: '30em !important', background: '#000A44', color: '#ffffff'}, },
                    { key: 'procDestino',       label: 'Processo Destino',      sortable: true,   thStyle: { width: '14em !important', background: '#000A44', color: '#ffffff'}, },
                    { key: 'usuarioDestino',    label: 'Responsável Destino',   sortable: true,   thStyle: { width: '14em !important', background: '#000A44', color: '#ffffff'}  },
                    { key: 'periodicDesc',      label: 'Periodicidade',         sortable: true,   thStyle: { width: '10em !important', background: '#000A44', color: '#ffffff'},  },
                    { key: 'dataIniDesc',       label: 'Primeiro Agendamento',  sortable: true,   thStyle: { width: '14em !important', background: '#000A44', color: '#ffffff'},    class: 'text-center',},
                    { key: 'qtde_criado',       label: 'Qtde. Criada',          sortable: true,   thStyle: { width: '10em !important', background: '#000A44', color: '#ffffff'},  },
                    { key: 'proxAgendDesc',     label: 'Próximo agendamento',   sortable: true,   thStyle: { width: '14em !important', background: '#000A44', color: '#ffffff'},  },
                    { key: 'acao',              label: 'Ações',                 sortable: true,   thStyle: { width: '15em !important', background: '#000A44', color: '#ffffff'},  },
                ],
                items: [
                ]
            }
        },
        methods: {
            trimData    :   function(){
                var vm  =   this;
                vm.items[iddx].listaQuestao.forEach((element, index) => {
                    if(vm.items[iddx].listaQuestao[index].resposta  !=  undefined) {
                        vm.items[iddx].listaQuestao[index].resposta =   vm.items[iddx].listaQuestao[index].resposta.trim();
                    }
                });
            },
            openModal       :   function(id) {
                try {
                    var vm  =   this;

                    vm.items.forEach((element)  =>  {
                        if(element.acao.id == id) {
                            element.acao.modal  =   true;
                        } // if(element.acao.id == id) { ... }
                    }); // vm.items.forEach((element)  =>  { .... }
                }
                catch(error) {
                    Vue.$toast.success('Ocorreu um erro durante a operação! Informe ao administrador do sistema AxonT');
                }
            },
            removeItem      :   function(idAgendamento) {
                try {
                    var vm      = this;
                    vm.isBusy   = true;

                    var header   = {
                        'headers'   :   {
                            'Authorization' :   'Bearer ' + this.bearer,
                        },
                    };

                    var request = {
                        '_token'        :   vm.token,
                        'idAgendamento' :   idAgendamento,
                    };

                    axios.post('/api/task/removeScheduling',request, header)
                    .then(function (response) {
                        vm.isBusy = false;
                        if(response.status === 200) {
                            // Atualiza os dados
                            vm.consultaDados();
                            Vue.$toast.success('O registro [' + idAgendamento + '] foi inativado com sucesso! Verifique.');
                        }
                        else {
                            Vue.$toast.error('Não foi possível remover o registro ['+ idAgendamento +'].');
                        }
                    })
                    .catch(function (error) {
                        Vue.$toast.error('Não foi possível remover o registro ['+ idAgendamento +'].');
                        vm.isBusy   =   false;
                    });
                }
                catch(error) {
                    Vue.$toast.error('Não foi possível completar a operação! Verifique.');
                }
            },
            coletaNome      :   function(chave){
                var retorno     =   'padrão';
                this.fields.forEach(element => {
                    if(element.key === chave) retorno = element.label;
                });
                return retorno;
            },
            consultaDados   :   function(){
                var vm      = this;
                vm.isBusy   = true;

                var header   = {
                    'headers'   :   {
                        'Authorization' :   'Bearer ' + this.bearer,
                    },
                };

                var request = {
                    '_token'        :   vm.token,
                    'idCompany'     :   vm.PREFERENCES.getCompany(),
                    'idProccess'    :   vm.PREFERENCES.getProccess(),
                    'idType'        :   vm.PREFERENCES.getType(),
                };

                axios.post('/api/task/listAutomatic',request, header)
                .then(function (response) {
                    vm.isBusy = false;
                    if(response.status === 200) {
                        vm.items    =   response.data;
                        vm.totalRows=   vm.items.length;
                    } // if(response.status === 200) { ... }
                    else {
                        Vue.$toast.error('Não foi possível obter a lista de objetos de troca.');
                    } // else { ... }
                })
                .catch(function (error) {
                    Vue.$toast.error('Erro ao consultar os dados informe ao administrador!');
                    vm.isBusy   =   false;
                });
            },
        },
        mounted() {
            this.LAYOUT.initData();
            this.PREFERENCES.initData();
            this.consultaDados();
        },
    }
</script>