<template>
    <div class="card border border-primary shadow mt-2">
        <div class="card-header bg-primary text-white text-center">
            Troca de objetos
        </div>
        <div class="card-body">
            <div class="row" v-if="loading">
                <div class="col-12">
                    <center><logo :size="100"></logo></center>
                </div>
            </div>
            <div class="row" v-else-if="!permission && !loading">
                <div class="col-12 text-center d-flex justify-content-center text-primary font-weight-bold">
                    <h3>Você não tem permissão para acessar esta página</h3>
                </div>
            </div>
            <form class="row was-validated" v-else method="POST" action="/task/createObject" autocomplete="off" enctype="multipart/form-data">
                <input type="hidden" name="_token" v-bind:value="token">
                <div class="col-12" v-if="content != null && content != {}">
                    <label for="companyTask">Tipo:</label>
                    <select class="form-control form-control-sm" name="typeProccess" v-model="typeProccess" @change="originCompany = null; originProccess = null; originType = null; originResponsible = null; destinyCompany = null; destinyProccess = null; destinyType = null; destinyResponsible = null;" required>
                        <option v-bind:value="null" selected>Nenhuma opção selecionada</option>
                        <option v-bind:value="1">Entrada</option>
                        <option v-bind:value="2">Saída</option>
                    </select>
                </div>


                <!-- Origem -->
                <div class="col-12" v-if="typeProccess != null">
                    <label for="companyTask" class="font-weight-bold text-success">Empresa de origem:</label>
                    <select class="form-control form-control-sm" v-model="originCompany" @change="originProccess = null; originType = null; originResponsible = null;" name="originCompany" required>
                        <option v-bind:value="null" selected>Nenhuma empresa selecionada</option>
                        <option v-for="curreg in content" v-bind:key="curreg.id_empresa" v-bind:value="curreg.id_empresa">{{ curreg.descricao }}</option>
                    </select>
                </div>

                <div class="col-12 col-sm-12 col-md-4 col-lg-4" v-if="typeProccess != null">
                    <label for="companyTask" class="font-weight-bold text-success">Processo de origem:</label>
                    <select class="form-control form-control-sm" v-model="originProccess" :disabled="originCompany == null" @change="originType = null; originResponsible = null;" name="originProccess" required>
                        <option v-bind:value="null" selected>Nenhum processo selecionado</option>
                        <option v-for="curreg in content.filter(function(item){ return (item.id_empresa === originCompany)}).length === 0 ? [] : content.filter(function(item){ return (item.id_empresa === originCompany)})[0].allProccess" v-bind:key="curreg.id_processo" v-bind:value="curreg.id_processo">{{ curreg.descricao }}</option>
                    </select>
                </div>

                <div class="col-12 col-sm-12 col-md-4 col-lg-4" v-if="typeProccess != null">
                    <label for="companyTask" class="font-weight-bold text-success">Tipo de solicitação de serviço:</label>
                    <select class="form-control form-control-sm" v-model="originType" :disabled="originProccess == null" @change="originResponsible = null;" name="originType" required>
                        <option v-bind:value="null" selected>Nenhum tipo selecionado</option>
                        <option v-for="curreg in content.filter(function(item){ return (item.id_empresa === originCompany)}).length === 0 ? [] : (content.filter(function(item){ return (item.id_empresa === originCompany)})[0].allProccess.filter(function(item) { return item.id_processo === originProccess }).length === 0 ? [] : content.filter(function(item){ return (item.id_empresa === originCompany)})[0].allProccess.filter(function(item) { return item.id_processo === originProccess })[0].automaticType)" v-bind:key="curreg.id_tipo_processo" v-bind:value="curreg.id_tipo_processo">{{ curreg.titulo }}</option>
                    </select>
                </div>

                <div class="col-12 col-sm-12 col-md-4 col-lg-4" v-if="typeProccess != null">
                    <label for="companyTask" class="font-weight-bold text-success">Responsável de origem:</label>
                    <select class="form-control form-control-sm" v-model="originResponsible" :disabled="originProccess == null  || originType == null"  name="originResponsible">
                        <option v-bind:value="null" selected>Nenhum responsável selecionado</option>
                        <option v-for="curreg in content.filter(function(item){ return (item.id_empresa === originCompany)}).length === 0 ? [] : (content.filter(function(item){ return (item.id_empresa === originCompany)})[0].allProccess.filter(function(item) { return item.id_processo === originProccess }).length === 0 ? [] : content.filter(function(item){ return (item.id_empresa === originCompany)})[0].allProccess.filter(function(item) { return item.id_processo === originProccess })[0].responsible)" v-bind:key="curreg.id" v-bind:value="curreg.id">{{ curreg.name }}</option>
                    </select>
                </div>





                <!-- Destino -->
                <div class="col-12" v-if="typeProccess == 2">
                    <label for="companyTask" class="font-weight-bold text-danger">Empresa de destino:</label>
                    <select class="form-control form-control-sm" v-model="destinyCompany" @change="destinyProccess = null; destinyType = null; destinyResponsible = null;" name="destinyCompany" required>
                        <option v-bind:value="null" selected>Nenhuma empresa selecionada</option>
                        <option v-for="curreg in content" v-bind:key="curreg.id_empresa" v-bind:value="curreg.id_empresa">{{ curreg.descricao }}</option>
                    </select>
                </div>

                <div class="col-12 col-sm-12 col-md-4 col-lg-4" v-if="typeProccess == 2">
                    <label for="companyTask" class="font-weight-bold text-danger">Processo de destino:</label>
                    <select class="form-control form-control-sm" v-model="destinyProccess" :disabled="destinyCompany == null" @change="destinyType = null; destinyResponsible = null;"  name="destinyProccess" required>
                        <option v-bind:value="null" selected>Nenhum processo selecionado</option>
                        <option v-for="curreg in content.filter(function(item){ return (item.id_empresa === destinyCompany)}).length === 0 ? [] : content.filter(function(item){ return (item.id_empresa === destinyCompany)})[0].allProccess" v-bind:key="curreg.id_processo" v-bind:value="curreg.id_processo">{{ curreg.descricao }}</option>
                    </select>
                </div>

                <div class="col-12 col-sm-12 col-md-4 col-lg-4" v-if="typeProccess == 2">
                    <label for="companyTask" class="font-weight-bold text-danger">Tipo de solicitação de serviço:</label>
                    <select class="form-control form-control-sm" v-model="destinyType" :disabled="destinyProccess == null" @change="destinyResponsible = null;"  name="destinyType" required>
                        <option v-bind:value="null" selected>Nenhum tipo selecionado</option>
                        <option v-for="curreg in content.filter(function(item){ return (item.id_empresa === destinyCompany)}).length === 0 ? [] : (content.filter(function(item){ return (item.id_empresa === destinyCompany)})[0].allProccess.filter(function(item) { return item.id_processo === destinyProccess }).length === 0 ? [] : content.filter(function(item){ return (item.id_empresa === destinyCompany)})[0].allProccess.filter(function(item) { return item.id_processo === destinyProccess })[0].manualType)" v-bind:key="curreg.id_tipo_processo" v-bind:value="curreg.id_tipo_processo">{{ curreg.titulo }}</option>
                    </select>
                </div>

                <div class="col-12 col-sm-12 col-md-4 col-lg-4" v-if="typeProccess == 2">
                    <label for="companyTask" class="font-weight-bold text-danger">Responsável de destino:</label>
                    <select class="form-control form-control-sm" v-model="destinyResponsible" :disabled="destinyProccess == null  || destinyType == null" name="destinyResponsible">
                        <option v-bind:value="null" selected>Nenhum responsável selecionado</option>
                        <option v-for="curreg in content.filter(function(item){ return (item.id_empresa === destinyCompany)}).length === 0 ? [] : (content.filter(function(item){ return (item.id_empresa === destinyCompany)})[0].allProccess.filter(function(item) { return item.id_processo === destinyProccess }).length === 0 ? [] : content.filter(function(item){ return (item.id_empresa === destinyCompany)})[0].allProccess.filter(function(item) { return item.id_processo === destinyProccess })[0].responsible)" v-bind:key="curreg.id" v-bind:value="curreg.id">{{ curreg.name }}</option>
                    </select>
                </div>



                <!-- Preenchimento do cabeçalho -->
                <!-- Dados do entregável -->
                <div class="col-12 col-sm-12 col-md-12 col-lg-12" v-if="((typeProccess == 1 && originCompany !== null && originProccess !== null && originType !== null) || (typeProccess == 2 && originCompany !== null && originProccess !== null && originType !== null && destinyCompany !== null && destinyProccess !== null && destinyType !== null))">
                    <div class="form-group">
                        <label for="entregavel">Entregável:</label>
                        <input class="form-control form-control-sm" type="text" name="entregavel" minlength="10" maxlength="250" id="entregavel" placeholder="Informe o título do entregável" v-model="title" @change="trimTitle()" required>
                    </div>
                </div>
                <!-- Dados do entregável -->

                <!-- Dados de periodicidade -->
                <div class="col-12 col-sm-12 col-md-6" v-if="((typeProccess == 1 && originCompany !== null && originProccess !== null && originType !== null) || (typeProccess == 2 && originCompany !== null && originProccess !== null && originType !== null && destinyCompany !== null && destinyProccess !== null && destinyType !== null))">
                    <div class="form-group">
                        <label for="periodicidade">Periodicidade:</label>
                        <select class="form-control form-control-sm" id="periodicidade" name="periodicidade" v-model="periodicity" required>
                            <option v-for="conteudo in periodicityList" v-bind:key="conteudo.id" v-bind:value="conteudo">{{ conteudo.description }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6" v-if="((typeProccess == 1 && originCompany !== null && originProccess !== null && originType !== null) || (typeProccess == 2 && originCompany !== null && originProccess !== null && originType !== null && destinyCompany !== null && destinyProccess !== null && destinyType !== null))">
                    <div class="form-group">
                        <label for="qtde_periodicidade">Tempo {{ (periodicity.id == undefined) ? '' : 'em ' + periodicity.description }}:</label>
                        <input type="number" min="1" max="9999" class="form-control form-control-sm" id="qtde_periodicidade" name="qtde_periodicidade" value="" required>
                    </div>
                </div>

                <div class="col-12" v-if="periodicity.date">
                    <div class="form-group">
                        <label for="periodicidade_data">Data de início:</label>
                        <input type="date" v-bind:min="menorHora" class="form-control form-control-sm" id="periodicidade_data" name="periodicidade_data" required>
                    </div>
                </div>
                <div class="col-12" v-if="periodicity.hour">
                    <div class="form-group">
                        <label for="periodicidade_hora">Horário de início:</label>
                        <input type="time" class="form-control form-control-sm" id="periodicidade_hora" name="periodicidade_hora" required>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6 col-lg-6" v-if="periodicity.datetime">
                    <div class="form-group">
                        <label for="periodicidade_data">Data de início:</label>
                        <input type="date" v-bind:min="greatehour" class="form-control form-control-sm" id="periodicidade_data" name="periodicidade_data" required>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6 col-lg-6" v-if="periodicity.datetime">
                    <div class="form-group">
                        <label for="periodicidade_hora">Horário de início:</label>
                        <input type="time" class="form-control form-control-sm" id="periodicidade_hora" name="periodicidade_hora" required>
                    </div>
                </div>
                <!-- Dados de periodicidade -->

                <!-- Dados do questionário -->
                <div class="col-12" v-if="((typeProccess == 1 && originCompany !== null && originProccess !== null && originType !== null) || (typeProccess == 2 && originCompany !== null && originProccess !== null && originType !== null && destinyCompany !== null && destinyProccess !== null && destinyType !== null))">
                    <div class="form-group" v-for="(curreg, idx) in content.filter(function(item){ return (item.id_empresa === originCompany)})[0].allProccess.filter(function(item) { return item.id_processo === originProccess })[0].automaticType[0].allQuestions" v-bind:key="curreg.id_questao">
                        <label v-bind:for="'idQuestion_' + curreg.id_questao">{{ curreg.titulo }}</label>
                        <input v-if="curreg.tipo == 'date'" type="date" class="form-control form-control-sm" v-bind:name="'idQuestion_' + curreg.id_questao" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" v-model="content.filter(function(item){ return (item.id_empresa === originCompany)})[0].allProccess.filter(function(item) { return item.id_processo === originProccess })[0].automaticType[0].allQuestions[idx].valueData" @change="trimData" :required="curreg.obrigatorio">
                        <div class="row" v-else-if="curreg.tipo == 'datetime'">
                            <div class="col-12 col-sm-6 col-md-6">
                                <input type="date" class="form-control form-control-sm" v-bind:id="'idQuestion_' + curreg.id_questao + '_date'" v-bind:name="'idQuestion_' + curreg.id_questao + '_date'" v-bind:placeholder="curreg.placeholder" @change="trimData" :required="curreg.obrigatorio">
                            </div>
                            <div class="col-12 col-sm-6 col-md-6">
                                <input type="time" class="form-control form-control-sm" v-bind:id="'idQuestion_' + curreg.id_questao + '_time'" v-bind:name="'idQuestion_' + curreg.id_questao + '_time'" v-bind:placeholder="curreg.placeholder" @change="trimData" :required="curreg.obrigatorio">
                            </div>
                        </div>
                        <input v-else-if="curreg.tipo == 'text'" type="text" class="form-control form-control-sm" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:name="'idQuestion_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" v-model="content.filter(function(item){ return (item.id_empresa === originCompany)})[0].allProccess.filter(function(item) { return item.id_processo === originProccess })[0].automaticType[0].allQuestions[idx].valueData" @change="trimData" :required="curreg.obrigatorio">
                        <input v-else-if="curreg.tipo == 'email'" type="email" class="form-control form-control-sm" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:name="'idQuestion_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" v-model="content.filter(function(item){ return (item.id_empresa === originCompany)})[0].allProccess.filter(function(item) { return item.id_processo === originProccess })[0].automaticType[0].allQuestions[idx].valueData" @change="trimData" :required="curreg.obrigatorio">
                        <input v-else-if="curreg.tipo == 'number'" type="number" class="form-control form-control-sm" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:name="'idQuestion_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" v-model="content.filter(function(item){ return (item.id_empresa === originCompany)})[0].allProccess.filter(function(item) { return item.id_processo === originProccess })[0].automaticType[0].allQuestions[idx].valueData" @change="trimData" :required="curreg.obrigatorio">
                        <select v-else-if="curreg.tipo === 'user'" class="form-control form-control-sm" v-bind:placeholder="curreg.placeholder" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:name="'idQuestion_' + curreg.id_questao" v-model="content.filter(function(item){ return (item.id_empresa === originCompany)})[0].allProccess.filter(function(item) { return item.id_processo === originProccess })[0].automaticType[0].allQuestions[idx].valueData" :required="curreg.obrigatorio">
                            <option>Nenhum usuário selecionado</option>
                            <option v-for="curuser in userList" v-bind:key="curuser.id" v-bind:value="curuser.id">{{ curuser.name }}</option>
                        </select>
                        <textarea rows="5" v-else class="form-control form-control-sm" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" v-bind:name="'idQuestion_' + curreg.id_questao" v-model="content.filter(function(item){ return (item.id_empresa === originCompany)})[0].allProccess.filter(function(item) { return item.id_processo === originProccess })[0].automaticType[0].allQuestions[idx].valueData" @change="trimData" :required="curreg.obrigatorio"></textarea>
                    </div>
                </div>


                <!-- Arquivos -->
                <div class="col-12" v-if="((typeProccess == 1 && originCompany !== null && originProccess !== null && originType !== null) || (typeProccess == 2 && originCompany !== null && originProccess !== null && originType !== null && destinyCompany !== null && destinyProccess !== null && destinyType !== null))">
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
                    <button type="submit" class="btn btn-primary btn-block btn-sm">Cadastrar troca de objeto</button>
                </div>
            </form>
        </div>
    </div>
</template>
 
<script>
    export default {
        props: [
            'token','bearer','greatehour'
        ],
        components: {
        },
        data() {
            return {
                'loading'           :   false,
                'permission'        :   false,

                'content'           :   {},

                'typeProccess'      :   null,

                'originCompany'     :   null,
                'originProccess'    :   null,
                'originType'        :   null,
                'originResponsible' :   null,

                'destinyCompany'    :   null,
                'destinyProccess'   :   null,
                'destinyType'       :   null,
                'destinyResponsible':   null,

                'title'             :   '',
                'fileList'          :   [],
                'periodicityList'    :   [
                    {
                        "id" : null,
                        "description" : "Nenhum período selecionado",
                        "date" : false,
                        "hour" : false,
                        "datetime" : false,
                    },
                    {
                        "id" : 1,
                        "description" : "Dia(s)",
                        "date" : false,
                        "hour" : false,
                        "datetime" : true,
                    },
                    {
                        "id" : 4,
                        "description" : "Mês(es)",
                        "date" : false,
                        "hour" : false,
                        "datetime" : true,
                    },
                    {
                        "id" : 7,
                        "description" : "Ano(s)",
                        "date" : false,
                        "hour" : false,
                        "datetime" : true,
                    },
                ],
                'periodicity'   :   {},
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
            trimTitle: function(){
                try {
                    var vm = this;
                    vm.title  =   vm.title.trim();

                    return true;
                }
                catch(error){
                    return false;
                }
            },
            trimData: function(){

            },
            verifyPermission  :   function(){
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
                        'id_configuracao'   :   2,
                    };

                    axios.post('/api/util/getPermission',request,header)
                    .then(function (response) {
                        if(response.status === 200) {
                            if(!response.data.permission) {
                                vm.loading      = false;
                                vm.permission   = false;
                            }
                            else {
                                vm.permission   = true;
                                vm.getData();
                            }
                        } // if(response.status === 200) { ... }
                        else {
                            Vue.$toast.error('Não foi possível validar as permissões! Verifique com o administrador.');
                        }
                    })
                    .catch(function(retorno){
                        console.log(retorno);
                        Vue.$toast.error('Ocorreu um erro desconhecido! Verifique.');
                    });
                } // try { ... }
                catch(error) {
                    console.log(error);
                    Vue.$toast.error('Ocorreu um erro desconhecido! Verifique.');
                } // catch(error) { ... }
            }, // verifyPermission : function(){ ... }
            getData : function(){
                try {
                    var vm      =   this;
                    vm.loading  =   true;

                    var header   = {
                        'headers'   :   {
                            'Authorization' :   'Bearer ' + vm.bearer,
                        },
                    };
                    var request = {
                        '_token'            :   vm.token,
                    };

                    axios.post('/api/task/getDataAutomatic',request,header)
                    .then(function (response) {
                        if(response.status === 200) {
                            vm.loading      =   false;
                            vm.content      =   response.data;
                        } // if(response.status === 200) { ... }
                        else {
                            Vue.$toast.error('Não foi possível validar as permissões! Verifique com o administrador.');
                        }
                    })
                    .catch(function(retorno){
                        Vue.$toast.error('Ocorreu um erro desconhecido! Verifique.');
                    });
                } // try { ... }
                catch(error) {
                    Vue.$toast.error('Ocorreu um erro desconhecido! Verifique.');
                } // catch(error) { ... }
            } // getData : function(){ ... }
        },
        mounted() {
            this.LAYOUT.initData();
            this.PREFERENCES.initData();

            this.loading    =   true;
            this.periodicity=   this.periodicityList[0];
            this.verifyPermission();
        }
    }
</script>