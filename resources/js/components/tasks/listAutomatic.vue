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
                    </b-table>
                </div>
                <div class="card-footer bg-primary">
                    <small class="d-block text-white text-center">Ordenado por {{ coletaNome(sortBy) }} - {{ sortDesc ? 'decrescente' : 'crescente'}} </small>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'token','bearer'
        ],
        data() {
            return {
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
                    { key: 'idDesc',            label: '#ID',                   sortable: true,   thStyle: { width: '5em !important', background: '#000A44', color: '#ffffff'},  },
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
                    { key: 'acao',              label: 'Ações',                 sortable: true,   thStyle: { width: '20em !important', background: '#000A44', color: '#ffffff'},  },
                ],
                items: [
                ]
            }
        },
        methods: {
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

                axios.post('/api/tasks/listAutomatic',request, header)
                .then(function (response) {
                    vm.isBusy = false;
                    if(response.status === 200) {
                        vm.items    =   response.data;
                        vm.totalRows=   vm.items.length;
                    }
                    else {
                        Vue.$toast.error('Não foi possível obter a lista de objetos de troca.');
                    }
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