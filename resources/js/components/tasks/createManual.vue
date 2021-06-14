<template>
    <div class="card border border-primary shadow mt-2">
        <div class="card-header bg-primary text-white text-center">
            Cadastro de solicitação de serviço
        </div>
        <div class="card-body">
            <div class="row">
                <div v-if="loading" class="col-12">
                    <center><logo :size="100"></logo></center>
                </div>
                <div v-else div class="col-12">
                    <div v-if="!permission" class="row">
                        <div class="col-12 text-center d-flex justify-content-center text-primary font-weight-bold">
                            <h3>Você não tem permissão para acessar esta página</h3>
                        </div>
                    </div>
                    <div v-else>
                        <div v-if="step==0" class="row">
                            <!-- Step 0 -->
                            <!-- Seleção de configurações -->
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                <label for="companyTask">Empresa:</label>
                                <select class="form-control form-control-sm" v-model="companyChoice" @change="proccessChoice = null; typeChoice = null;">
                                    <option v-bind:value="null">Nenhuma opção selecionada</option>
                                    <option v-for="curreg in content" v-bind:key="curreg.id_empresa" v-bind:value="curreg.id_empresa">{{ curreg.descricao }}</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                <label for="companyTask">Processo:</label>
                                <select class="form-control form-control-sm" v-model="proccessChoice" :disabled="companyChoice == null" @change="typeChoice = null;">
                                    <option v-bind:value="null">Nenhuma opção selecionada</option>
                                    <option v-for="curreg in content.filter(function(item){ return (item.id_empresa === companyChoice)}).length === 0 ? [] : content.filter(function(item){ return (item.id_empresa === companyChoice)})[0].allProccess" v-bind:key="curreg.id_processo" v-bind:value="curreg.id_processo">{{ curreg.descricao }}</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                <label for="companyTask">Tipo:</label>
                                <select class="form-control form-control-sm" v-model="typeChoice" :disabled="((companyChoice == null) || (proccessChoice == null))">
                                    <option v-bind:value="null">Nenhuma opção selecionada</option>
                                    <option v-for="curreg in content.filter(function(item){ return (item.id_empresa === companyChoice)}).length === 0 ? [] : (content.filter(function(item){ return (item.id_empresa === companyChoice)})[0].allProccess.filter(function(item) { return item.id_processo === proccessChoice }).length === 0 ? [] : content.filter(function(item){ return (item.id_empresa === companyChoice)})[0].allProccess.filter(function(item) { return item.id_processo === proccessChoice })[0].manualType)" v-bind:key="curreg.id_tipo_processo" v-bind:value="curreg.id_tipo_processo">{{ curreg.titulo }}</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                <button class="btn btn-primary btn-sm btn-block" @click="this.setData" :disabled="((companyChoice == null) || (proccessChoice == null) || (typeChoice == null))">Abrir solicitação de serviço</button>
                            </div>
                            <!-- Step 0 -->
                            <!-- Seleção de configurações -->
                        </div>
                        <form method="POST" action="/task/createSS" v-if="step===1" class="row was-validated" autocomplete="off">
                            <!-- Step 1 -->
                            <!-- Abertura do chamado -->
                            <input type="hidden" name="_token" v-bind:value="token">
                            <input type="hidden" name="idCompany" v-bind:value="companyChoice" required>
                            <input type="hidden" name="idProccess" v-bind:value="proccessChoice" required>
                            <input type="hidden" name="idType" v-bind:value="typeChoice" required>


                            <div class="col-12">
                                <ul class="list-group border border-primary">
                                    <li class="list-group-item">
                                        <div class="row">
                                            <div class="col-12 text-center" v-if="listCompany.imagem != null">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <img v-bind:src="listCompany.imagem" height="40vw">
                                                    </div>
                                                    <div class="col-12" v-if="listCompany.respData != null">
                                                        <small>
                                                            <small class="text-primary font-weight-bold">
                                                                Responsável: {{ listCompany.respData.name }}
                                                            </small>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-else class="col-12 text-primary text-center font-weight-bold">
                                                <div class="row">
                                                    <div class="col-12">
                                                        {{ listCompany.sigla }} - {{ listCompany.descricao }}
                                                    </div>
                                                    <div class="col-12" v-if="listCompany.respData != null">
                                                        <small>
                                                            <small class="text-primary font-weight-bold">
                                                                Responsável: {{ listCompany.respData.name }}
                                                            </small>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>

                                    <li class="list-group-item text-center">
                                        <div class="row">
                                            <div class="col-12 d-flex justify-content-around">
                                                <i v-bind:class="listProccess.icone"></i>
                                                <small class="text-primary font-weight-bold">
                                                    [{{ listProccess.sigla }}] - {{ listProccess.descricao }}
                                                </small>
                                                <i v-bind:class="listProccess.icone"></i>
                                            </div>
                                            <div class="col-12 text-primary text-center font-weight-bold d-flex justify-content-center" v-if="listProccess.respData != null">
                                                <small>
                                                    <small class="text-primary font-weight-bold">
                                                        Responsável: {{ listProccess.respData.name }}
                                                    </small>
                                                </small>
                                            </div>
                                        </div>
                                    </li>

                                    <li class="list-group-item">
                                        <div class="row">
                                            <div class="col-12 text-center">
                                                <small class="text-primary font-weight-bold">
                                                    {{ listType.titulo }}
                                                </small>
                                            </div>
                                            <div class="col-6 text-center">
                                                <small>
                                                    <small class="text-primary font-weight-bold">
                                                        {{ listType.subtitulo }}
                                                    </small>
                                                </small>
                                            </div>
                                            <div class="col-6 text-primary font-weight-bold d-flex justify-content-center">
                                                <i class="fas fa-business-time"></i>
                                                <small>
                                                    <small class="text-primary font-weight-bold">
                                                        {{ listType.sla }} minuto(s)
                                                    </small>
                                                </small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <div class="col-12">
                                <ul class="list-group border border-primary">
                                    <li class="list-group-item">
                                        <div class="form-group">
                                            <label for="title">Título</label>
                                            <input type="text" minlength="25" maxlength="320" class="form-control form-control-sm" id="title" name="title" placeholder="Informe o título da solicitação de serviço de forma direta" v-model="title" @change="trimTitle" required>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <div class="col-12">
                                <ul class="list-group border border-primary">
                                    <li class="list-group-item" v-for="(curreg, idx) in listQuestions" v-bind:key="curreg.id_questao">
                                        <div class="row">
                                            <div class="col-12">
                                                <label v-bind:for="'idQuestion_' + curreg.id_questao">{{ curreg.titulo }}</label>
                                                <input v-if="curreg.tipo == 'date'" type="date" class="form-control form-control-sm" v-bind:name="'idQuestion_' + curreg.id_questao" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" v-model="listQuestions[idx].valueData" @change="trimData" :required="curreg.obrigatorio">
                                                <div class="row" v-else-if="curreg.tipo == 'datetime'">
                                                    <div class="col-12 col-sm-6 col-md-6">
                                                        <input type="date" class="form-control form-control-sm" v-bind:id="'idQuestion_' + curreg.id_questao + '_date'" v-bind:name="'idQuestion_' + curreg.id_questao + '_date'" v-bind:placeholder="curreg.placeholder" @change="trimData" :required="curreg.obrigatorio">
                                                    </div>
                                                    <div class="col-12 col-sm-6 col-md-6">
                                                        <input type="time" class="form-control form-control-sm" v-bind:id="'idQuestion_' + curreg.id_questao + '_time'" v-bind:name="'idQuestion_' + curreg.id_questao + '_time'" v-bind:placeholder="curreg.placeholder" @change="trimData" :required="curreg.obrigatorio">
                                                    </div>
                                                </div>
                                                <input v-else-if="curreg.tipo == 'text'" type="text" class="form-control form-control-sm" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:name="'idQuestion_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" v-model="listQuestions[idx].valueData" @change="trimData" :required="curreg.obrigatorio">
                                                <input v-else-if="curreg.tipo == 'email'" type="email" class="form-control form-control-sm" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:name="'idQuestion_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" v-model="listQuestions[idx].valueData" @change="trimData" :required="curreg.obrigatorio">
                                                <input v-else-if="curreg.tipo == 'number'" type="number" class="form-control form-control-sm" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:name="'idQuestion_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" v-model="listQuestions[idx].valueData" @change="trimData" :required="curreg.obrigatorio">
                                                <select v-else-if="curreg.tipo === 'user'" class="form-control form-control-sm" v-bind:placeholder="curreg.placeholder" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:name="'idQuestion_' + curreg.id_questao" v-model="listQuestions[idx].valueData" :required="curreg.obrigatorio">
                                                    <option selected>Nenhum usuário selecionado</option>
                                                    <option v-for="curuser in listProccess.responsible" v-bind:key="curuser.id" v-bind:value="curuser.id">{{ curuser.name }}</option>
                                                </select>
                                                <textarea rows="5" v-else class="form-control form-control-sm" v-bind:id="'idQuestion_' + curreg.id_questao" v-bind:placeholder="curreg.placeholder" v-bind:name="'idQuestion_' + curreg.id_questao" v-model="listQuestions[idx].valueData" @change="trimData" :required="curreg.obrigatorio"></textarea>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            
                            <div class="col-12">
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
                                <button type="submit" class="btn btn-sm btn-block btn-primary">
                                    Criar solicitação de serviço
                                </button>
                            </div>


                            <!-- Abertura do chamado -->
                            <!-- Step 1 -->
                        </form>
                    </div>
                </div>
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
            'token','bearer'
        ],
        components: {
        },
        data() {
            return {
                'loading'       :   true,
                'yearCopright'  :   new Date().getFullYear(),
                'permission'    :   false,
                'step'          :   0,

                'content'       :   null,

                'listCompany'   :   {},
                'listProccess'  :   {},
                'listType'      :   {},
                'listQuestions' :   [],

                'companyChoice' :   null,
                'proccessChoice':   null,
                'typeChoice'    :   null,

                'title'         :   null,
                'fileList'      :   [],
            }
        },
        methods: {
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
                        'id_configuracao'   :   1,
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
                        Vue.$toast.error('Ocorreu um erro desconhecido! Verifique.');
                    });
                } // try { ... }
                catch(error) {
                    Vue.$toast.error('Ocorreu um erro desconhecido! Verifique.');
                } // catch(error) { ... }
            }, // verifyPermission : function(){ ... }

            newRegister :   function(){
                var vm                  =   this;
                var hash                =   Math.floor(Math.random() * 999999);

                var tbodyRef            =   document.getElementById('tableFile').getElementsByTagName('tbody')[0];
                var newRow              =   tbodyRef.insertRow();

                var cellFile            =   newRow.insertCell(0);
                var cellAction          =   newRow.insertCell(1);

                cellFile.innerHTML      =   '<div class="custom-file b-form-file b-custom-control-sm is-invalid" id="__BVID__' + hash + '__BV_file_outer_"><input type="file" name="arquivoBPMS[]" multiple="multiple" class="custom-file-input is-invalid" style="z-index: -5;" id="__BVID__' + hash + '"><label data-browse="Browse" class="custom-file-label" for="__BVID__' + hash + '"><span class="d-block form-file-text" style="pointer-events: none;">Selecione o(s) arquivo(s) ...</span></label></div>';
                cellAction.innerHTML    =   '<button type="button" class="btn btn-sm btn-block btn-outline-danger" onclick="this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode);"><i class="fas fa-trash"></i></button>';
            },

            trimData    :   function(){
                var vm  =   this;
                vm.listQuestions.forEach((element, index) => {
                    if(vm.listQuestions[index].valueData != undefined && vm.listQuestions[index].tipo != 'datetime') {
                        vm.listQuestions[index].valueData    =   vm.listQuestions[index].valueData.trim();
                    }
                });
            },

            trimTitle   :   function(){
                var vm      =   this;

                if(vm.title != null) {
                    vm.title    =   vm.title.trim();
                }
            }, // trimTitle   :   function(){ ... }
            
            setData  :   function(){
                try {
                    var vm = this;
                    vm.step = 1;

                    vm.listCompany  =   vm.companyChoice === null ? {} : vm.content.filter(function(item){ return item.id_empresa === vm.companyChoice})[0];
                    vm.listProccess =   ((vm.companyChoice === null) || (vm.proccessChoice === null)) ? {} : vm.content.filter(function(item){ return (item.id_empresa === vm.companyChoice)})[0].allProccess.filter(function(item) { return item.id_processo === vm.proccessChoice })[0];
                    vm.listType     =   ((vm.companyChoice === null) || (vm.proccessChoice === null) || (vm.typeChoice === null)) ? {} : vm.content.filter(function(item){ return (item.id_empresa === vm.companyChoice)})[0].allProccess.filter(function(item) { return item.id_processo === vm.proccessChoice })[0].manualType.filter(function(item){ return item.id_tipo_processo === vm.typeChoice })[0];
                    vm.listQuestions=   ((vm.companyChoice === null) || (vm.proccessChoice === null) || (vm.typeChoice === null)) ? {} : vm.content.filter(function(item){ return (item.id_empresa === vm.companyChoice)})[0].allProccess.filter(function(item) { return item.id_processo === vm.proccessChoice })[0].manualType.filter(function(item){ return item.id_tipo_processo === vm.typeChoice })[0].allQuestions;
                } // try { ... }
                catch(error) {
                    Vue.$toast.error('Não foi possível iniciar a abertura de Solicitação de serviços! Verifique.');
                } // catch(error) { ... }
            },

            getData  :   function(){
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

                    axios.post('/api/task/getDataManual',request,header)
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
            }, // getData : function(){ ... }
        },
        mounted() {
            this.LAYOUT.initData();
            this.PREFERENCES.initData();

            this.loading    =   true;
            this.verifyPermission();
        },
    }
</script>
