<template>
    <div class="row">
        <div class="col-12">
            <button type="submit" class="btn btn-success btn-sm btn-block" @click="getCompanies">
                Atualizar lista
            </button>
        </div>
        <div v-if="loading" class="col-12">
            <div class="card border-primary">
                <div class="card-body">
                    <center><logo :size="100"></logo></center>
                </div>
            </div>
        </div>
        <div v-else class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white text-center">
                    Empresa(s)
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="active-tab" data-toggle="tab" href="#active" role="tab" aria-controls="active" aria-selected="true">
                                Ativa(s)
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="inactive-tab" data-toggle="tab" href="#inactive" role="tab" aria-controls="inactive" aria-selected="false">
                                Inativa(s)
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="active" role="tabpanel" aria-labelledby="active-tab">
                            <b-table
                                id="tabela-solicitacao"
                                responsive
                                fixed
                                :striped="false"
                                :small="true"
                                :busy="loading"
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
                        <div class="tab-pane fade" id="inactive" role="tabpanel" aria-labelledby="inactive-tab">
                            <b-table
                                id="tabela-solicitacao"
                                responsive
                                fixed
                                :striped="false"
                                :small="true"
                                :busy="loading"
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['token','bearer'],
        components: {
        },
        data() {
            return {
                loading: true,
                content: {},
                itemsActive: [],
                fieldsActive: [
                    { key: 'id_empresa',                label: '#ID',                   sortable: true,   thStyle: { width: '5em !important',  background: '#000A44', color: '#ffffff'}, },
                    { key: 'descricao',                 label: 'Empresa',               sortable: true,   thStyle: { width: '20em !important', background: '#000A44', color: '#ffffff'}, stickyColumn: true, },
                    { key: 'sigla',                     label: 'Sigla',                 sortable: true,   thStyle: { width: '14em !important', background: '#000A44', color: '#ffffff'}, },
                    { key: 'id_usuario_responsavel',    label: 'Responsável',           sortable: true,   thStyle: { width: '14em !important', background: '#000A44', color: '#ffffff'}, },
                    { key: 'data_venc_contrato',        label: 'Data de vencimento',    sortable: true,   thStyle: { width: '14em !important', background: '#000A44', color: '#ffffff'}, class: 'text-center',},
                    { key: 'max_arquivos',              label: 'Armazenamento máximo',  sortable: true,   thStyle: { width: '14em !important', background: '#000A44', color: '#ffffff'}, class: 'text-center',},
                    { key: 'utilizado_arquivos',        label: 'Armazenamento usado',   sortable: true,   thStyle: { width: '30em !important', background: '#000A44', color: '#ffffff'}, class: 'text-center',},
                    { key: 'situacao',                  label: 'Situação',              sortable: true,   thStyle: { width: '14em !important', background: '#000A44', color: '#ffffff'}, class: 'text-center',},
                    { key: 'acao',                      label: 'Ações',                 sortable: true,   thStyle: { width: '15em !important', background: '#000A44', color: '#ffffff'}, },
                ],
                itemsInactive: [],
                fieldsInactive: [
                    { key: 'id_empresa',                label: '#ID',                   sortable: true,   thStyle: { width: '5em !important',  background: '#000A44', color: '#ffffff'}, },
                    { key: 'descricao',                 label: 'Empresa',               sortable: true,   thStyle: { width: '20em !important', background: '#000A44', color: '#ffffff'}, stickyColumn: true, },
                    { key: 'sigla',                     label: 'Sigla',                 sortable: true,   thStyle: { width: '14em !important', background: '#000A44', color: '#ffffff'}, },
                    { key: 'id_usuario_responsavel',    label: 'Responsável',           sortable: true,   thStyle: { width: '14em !important', background: '#000A44', color: '#ffffff'}, },
                    { key: 'data_venc_contrato',        label: 'Data de vencimento',    sortable: true,   thStyle: { width: '14em !important', background: '#000A44', color: '#ffffff'}, class: 'text-center',},
                    { key: 'max_arquivos',              label: 'Armazenamento máximo',  sortable: true,   thStyle: { width: '14em !important', background: '#000A44', color: '#ffffff'}, class: 'text-center',},
                    { key: 'utilizado_arquivos',        label: 'Armazenamento usado',   sortable: true,   thStyle: { width: '30em !important', background: '#000A44', color: '#ffffff'}, class: 'text-center',},
                    { key: 'situacao',                  label: 'Situação',              sortable: true,   thStyle: { width: '14em !important', background: '#000A44', color: '#ffffff'}, class: 'text-center',},
                    { key: 'acao',                      label: 'Ações',                 sortable: true,   thStyle: { width: '15em !important', background: '#000A44', color: '#ffffff'}, },
                ],
                sortBy: null,
                sortDesc: null,
            }
        },
        methods: {
            getCompanies: function(){
                var vm = this;

                try {
                    vm.loading = true;
                    var header   = {
                        'headers'   :   {
                            'Authorization' :   'Bearer ' + this.bearer,
                        },
                    };
                    var request = {
                        '_token'            :   vm.token,
                    };

                    axios.post('/api/admin/company',request,header)
                    .then(function (response) {
                        vm.loading  =   false;
                        vm.content  =   response.data;

                    })
                    .catch(function(retorno){
                        vm.loading  =   false;
                        Vue.$toast.error('Não foi possível coletar as opções de empresas.');
                    });
                } // try { ... }
                catch(error) {

                } // catch(error) { ... }
            },
        },
        mounted() {
            this.LAYOUT.initData();
            this.PREFERENCES.initData();
            this.getCompanies()
        },
    }
</script>
