<template>
    <div class="row">
        <div class="col-12" v-if="loading">
            <center><logo :size="100"></logo></center>
        </div>

        <div v-else class="col-12">
            <div class="row" v-if="content == []">
                <h6 class="text-primary text-center font-weight-bold">Dados não localizados</h6>
            </div>
            <div class="row" v-else>
                <div class="col-12">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-center">
                            <span class="text-white font-weight-bold text-center">
                                {{ content.titulo }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                    <label class="font-weight-bold" for="empresaBPMS">Empresa</label>
                                    <input type="text" class="form-control form-control-sm" v-bind:value="(content.descriptions.empresa == [] ? 'Nenhuma empresa' : content.descriptions.empresa.descricao)" readonly>
                                </div>

                                <input type="hidden">
                                <div class="form-group col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                    <label class="font-weight-bold" for="processoBPMS">Processo</label>
                                    <input type="text" class="form-control form-control-sm" v-bind:value="(content.descriptions.processo == [] ? 'Nenhum processo' : content.descriptions.processo.descricao)" readonly>
                                </div>

                                <div class="form-group col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                    <label class="font-weight-bold" for="tipoBPMS">Fluxo</label>
                                    <input type="text" class="form-control form-control-sm" name="tipoBPMS" v-bind:value="(content.descriptions.tipoProcesso == [] ? 'Nenhum fluxo' : content.descriptions.tipoProcesso.titulo)" readonly>
                                </div>

                                <div class="form-group col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                    <label class="font-weight-bold" for="tipoBPMS">Situação atual</label>
                                    <input type="text" class="form-control form-control-sm" name="tipoBPMS" v-bind:value="(content.descriptions.situacao == [] ? 'Nenhuma situação' : content.descriptions.situacao.descricao)" readonly>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-center">
                            <span class="text-white font-weight-bold text-center">
                                Responsável
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-12">
                                    <label class="font-weight-bold" for="empresaBPMS">Solicitante</label>
                                    <input type="text" class="form-control form-control-sm" v-bind:value="(content.descriptions.solicitante == [] ? 'Nenhum solicitante' : content.descriptions.solicitante.name)" readonly>
                                </div>

                                <div class="form-group col-12">
                                    <label class="font-weight-bold" for="processoBPMS">Responsável pelo processo</label>
                                    <input type="text" class="form-control form-control-sm" v-bind:value="content.descriptions.processo.descriptions.responsavel.name" readonly>
                                </div>

                                <div class="form-group col-12">
                                    <label class="font-weight-bold" for="tipoBPMS">Responsável pelo atendimento</label>
                                    <input type="text" class="form-control form-control-sm" name="tipoBPMS" v-bind:value="(content.descriptions.responsavel == null ? 'Espera entre tarefas' : content.descriptions.responsavel.name)" readonly>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-center">
                            <span class="text-white font-weight-bold text-center">
                                Prazos
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-12 col-sm-12">
                                    <label class="font-weight-bold" for="tipoBPMS">Data abertura</label>
                                    <input type="text" class="form-control form-control-sm" name="tipoBPMS" v-bind:value="content.descriptions.dataCria" readonly>
                                </div>

                                <div class="form-group col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6">
                                    <label class="font-weight-bold" for="tipoBPMS">Data conclusão</label>
                                    <input type="text" class="form-control form-control-sm" name="tipoBPMS" v-bind:value="content.descriptions.dataConclusao" readonly>
                                </div>

                                <div class="form-group col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6">
                                    <label class="font-weight-bold" for="tipoBPMS">Data limite</label>
                                    <input type="text" class="form-control form-control-sm" name="tipoBPMS" v-bind:value="content.descriptions.dataVencimento" readonly>
                                </div>

                                <div class="form-group col-12">
                                    <label class="font-weight-bold" for="tipoBPMS">Prazo</label>
                                    <input type="text" v-bind:class="'form-control form-control-sm font-weight-bold ' + content.descriptions.prazoColor"  name="tipoBPMS" v-bind:value="content.descriptions.prazo" readonly>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>



                <div class="col-12 col-sm-12 col-md-12">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-center">
                            <span class="text-white font-weight-bold text-center">
                                Necessidades
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <ul class="list-group">
                                        <li class="list-group-item" v-for="(curreg) in content.itemChamado" v-bind:key="curreg.id_questao">
                                            <div class="row">
                                                <div class="col-12">
                                                    <label v-bind:for="'idQuestion_' + curreg.id_questao">{{ curreg.questao }}</label>
                                                    <input v-if="curreg.tipo == 'date'" type="date" class="form-control form-control-sm" v-bind:name="'idQuestion_' + curreg.id_questao" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:value="curreg.respostaFormatada" readonly>
                                                    <input v-if="curreg.tipo == 'datetime'" type="date" class="form-control form-control-sm" v-bind:name="'idQuestion_' + curreg.id_questao" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:value="curreg.respostaFormatada" readonly>
                                                    <input v-else-if="curreg.tipo == 'text'" type="text" class="form-control form-control-sm" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:name="'idQuestion_' + curreg.id_questao" v-bind:value="curreg.respostaFormatada" readonly>
                                                    <input v-else-if="curreg.tipo == 'email'" type="email" class="form-control form-control-sm" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:name="'idQuestion_' + curreg.id_questao" v-bind:value="curreg.respostaFormatada" readonly>
                                                    <input v-else-if="curreg.tipo == 'number'" type="number" class="form-control form-control-sm" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:name="'idQuestion_' + curreg.id_questao" v-bind:value="curreg.respostaFormatada" readonly>
                                                    <input v-else-if="curreg.tipo == 'user'" type="text" class="form-control form-control-sm" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:name="'idQuestion_' + curreg.id_questao" v-bind:value="curreg.respostaFormatada" readonly>
                                                    <textarea rows="5" v-else class="form-control form-control-sm" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" v-bind:name="'idQuestion_' + curreg.id_questao" v-bind:value="curreg.respostaFormatada" readonly></textarea>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-12 col-sm-12 col-md-12">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-center">
                            <span class="text-white font-weight-bold text-center">
                                Adicionais
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active" id="arquivo-tab" data-toggle="pill" href="#arquivo" role="tab" aria-controls="arquivo" aria-selected="true">
                                                Arquivos enviados
                                            </a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="tarefa-tab" data-toggle="pill" href="#tarefa" role="tab" aria-controls="tarefa" aria-selected="true">
                                                Histórico
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="pills-tabContent">
                                        <div class="tab-pane fade show active" id="arquivo" role="tabpanel" aria-labelledby="arquivo-tab">
                                            <ul class="list-group">
                                                <li v-if="content.archives.length <= 0" class="list-group-item">Nenhum arquivo anexado a esta solicitação de serviço</li>
                                                <li v-else class="list-group-item">
                                                    <ul class="list-group">
                                                        <!-- Arquivos anexados na abertura do chamado -->
                                                        <li class="list-group-item d-flex justify-content-between text-primary font-weigth-bold" v-for="curreg in content.archives" v-bind:key="curreg.id_arquivo">
                                                            <i class="fas fa-file-alt"></i>
                                                            <a v-bind:href="curreg.url" target="_blank">
                                                                <span>{{ curreg.nome_arquivo }} anexado por {{ curreg.createdBy.name }}</span>
                                                            </a>
                                                            <i class="fas fa-file-alt"></i>
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="tab-pane fade" id="tarefa" role="tabpanel" aria-labelledby="tarefa-tab">
                                            <ul class="list-group">
                                                <li v-if="content.archives.length <= 0" class="list-group-item">Nenhum arquivo anexado a esta solicitação de serviço</li>
                                                <li v-else class="list-group-item">
                                                    <ul class="list-group">
                                                        <!-- Arquivos anexados na abertura do chamado -->
                                                        <li class="list-group-item d-flex justify-content-between border-primary" v-for="curreg in content.taskEntry" v-bind:key="curreg.id_tarefa">
                                                            <div class="row">
                                                                <div class="form-group col-12">
                                                                    <label class="font-weight-bold" for="Entrada">Entrada #{{ curreg.id_tarefa }}</label>
                                                                    <textarea rows="5" class="form-control form-control-sm" v-bind:value="curreg.conteudo" readonly></textarea>
                                                                </div>
                                                                <div class="form-group col-12 col-sm-6 col-md-6 col-lg-6">
                                                                    <label class="font-weight-bold" for="Entrada">Usuario Anterior</label>
                                                                    <input type="text" class="form-control form-control-sm" v-bind:value="curreg.descriptions.usrAnt == null ? '' : curreg.descriptions.usrAnt.name" readonly>
                                                                </div>
                                                                <div class="form-group col-12 col-sm-6 col-md-6 col-lg-6">
                                                                    <label class="font-weight-bold" for="Entrada">Usuário Atribuído</label>
                                                                    <input type="text" class="form-control form-control-sm" v-bind:value="curreg.descriptions.usrAtr == null ? '' : curreg.descriptions.usrAtr.name" readonly>
                                                                </div>

                                                                <div class="form-group col-12 col-sm-6 col-md-6 col-lg-6">
                                                                    <label class="font-weight-bold" for="Entrada">Data venc. Anterior</label>
                                                                    <input type="text" class="form-control form-control-sm" v-bind:value="curreg.descriptions.dtVencAnt" readonly>
                                                                </div>
                                                                <div class="form-group col-12 col-sm-6 col-md-6 col-lg-6">
                                                                    <label class="font-weight-bold" for="Entrada">Data venc. Atribuída</label>
                                                                    <input type="text" class="form-control form-control-sm" v-bind:value="curreg.descriptions.dtVencAtr" readonly>
                                                                </div>

                                                                <div class="form-group col-12 col-sm-6 col-md-6 col-lg-6">
                                                                    <label class="font-weight-bold" for="Entrada">Situação Anterior</label>
                                                                    <input type="text" class="form-control form-control-sm" v-bind:value="curreg.descriptions.situacaoAnt.descricao" readonly>
                                                                </div>
                                                                <div class="form-group col-12 col-sm-6 col-md-6 col-lg-6">
                                                                    <label class="font-weight-bold" for="Entrada">Situação Atribuída</label>
                                                                    <input type="text" class="form-control form-control-sm" v-bind:value="curreg.descriptions.situacaoAtr.descricao" readonly>
                                                                </div>

                                                                <div class="col-12">
                                                                    <ul class="list-group">
                                                                        <li class="list-group-item d-flex justify-content-between border-primary" v-for="curfile in curreg.allFiles" v-bind:key="curfile.id_arquivo">
                                                                            <i class="fas fa-file-alt"></i>
                                                                            <a v-bind:href="curfile.url" target="_blank">
                                                                                <span>{{ curfile.nome_arquivo }}</span>
                                                                            </a>
                                                                            <i class="fas fa-file-alt"></i>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['token','bearer','id'],
        components: {
        },
        data() {
            return {
                loading: true,
                content: [],
            }
        },
        methods: {
            chartAtt        :   function(){
                try {
                    var vm      =   this;
                    vm.loading  =   true;
                    vm.header   = {
                        'headers'   :   {
                            'Authorization' :   'Bearer ' + this.bearer,
                        },
                    };

                    vm.request = {
                        '_token'    :   vm.token,
                        'idTask'    :   parseInt(vm.id),
                    };

                    axios.post('/api/task/id',vm.request,vm.header)
                    .then(function (response) {
                        if(response.status === 200) {
                            vm.content  =   response.data;
                            vm.loading  =   false;
                        }
                        else {
                            vm.loading  =   true;
                            Vue.$toast.error('Requisição retornou um erro! Contate o administrador.');
                        }
                    })
                    .catch(function(retorno){
                        Vue.$toast.error('Não foi possível obter os dados da requisição! Verifique permissões.');
                    });
                } // try { ... }
                catch(error) {
                    Vue.$toast.error('Erro desconhecido durante a execução! Verifique com o administrador do sistema.');
                } // catch(error) { ... }
            }, // chartAtt        :   function(){ ... }
            
            
        },
        mounted() {
            this.LAYOUT.initData();
            this.PREFERENCES.initData();
            this.chartAtt();
        },
    }
</script>
