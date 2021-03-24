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
                        <template v-slot:table-busy>
                        <div class="text-center text-primary my-2">
                            <b-spinner class="align-middle"></b-spinner>
                            <strong>Carregando ...</strong>
                        </div>
                        </template>

                        <template v-slot:cell(id)="data">
                            <span v-html="data.value"></span>
                        </template>

                        <template v-slot:cell(titulo)="data">
                            <span v-html="data.value"></span>
                        </template>

                        <template v-slot:cell(responsavel)="data">
                            <span v-html="data.value"></span>
                        </template>

                        <template v-slot:cell(prazo)="data">
                            <span v-html="data.value"></span>
                        </template>

                        <template v-slot:cell(dataSolicitacao)="data">
                            <span v-html="moment(data.value).format('DD/MM/YYYY HH:mm:ss')"></span>
                        </template>

                        <template v-slot:cell(dataVencimento)="data">
                            <span v-html="moment(data.value).format('DD/MM/YYYY HH:mm:ss')"></span>
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
                sortBy: 'dataVencimento',
                sortDesc: false,
                requisicao : {},
                perPage: 7,
                currentPage: 1,
                totalRows: 0,
                fields: [
                    { key: 'id',                label: '#ID',                   sortable: true,   thStyle: { width: '5em !important', background: '#000A44', color: '#ffffff'},  },
                    { key: 'titulo',            label: 'O que foi solicitado',  sortable: true,   thStyle: { width: '20em !important', background: '#000A44', color: '#ffffff'},    stickyColumn: true, },
                    { key: 'solicitante',       label: 'Quem solicitou',        sortable: true,   thStyle: { width: '14em !important', background: '#000A44', color: '#ffffff'}, },
                    { key: 'responsavel',       label: 'Quem atende',           sortable: true,   thStyle: { width: '14em !important', background: '#000A44', color: '#ffffff'}, },
                    { key: 'dataSolicitacao',   label: 'Data solicitação',      sortable: true,   thStyle: { width: '12em !important', background: '#000A44', color: '#ffffff'},    class: 'text-center',},
                    { key: 'dataVencimento',    label: 'Data vencimento',       sortable: true,   thStyle: { width: '12em !important', background: '#000A44', color: '#ffffff'},    class: 'text-center'},
                    { key: 'prazo',             label: 'Prazo',                 sortable: true,   thStyle: { width: '12em !important', background: '#000A44', color: '#ffffff'},    class: 'text-center' },
                    { key: 'situacao',          label: 'Situação atual',        sortable: true,   thStyle: { width: '30em !important', background: '#000A44', color: '#ffffff'}, },
                    { key: 'processo',          label: 'Processo',              sortable: true,   thStyle: { width: '20em !important', background: '#000A44', color: '#ffffff'}  },
                    { key: 'empresa',           label: 'Empresa',               sortable: true,   thStyle: { width: '6em !important', background: '#000A44', color: '#ffffff'},  },
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

                axios.post('/api/tasks/list',request, header)
                .then(function (response) {
                    vm.isBusy = false;
                    if(response.status === 200) {
                        vm.items    =   response.data;
                        vm.totalRows=   vm.items.length;
                    }
                    else {
                        Vue.$toast.error('Não foi possível obter a lista de solicitações de serviço.');
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