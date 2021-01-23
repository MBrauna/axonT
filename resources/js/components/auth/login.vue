<template>
    <form ref="myForm" method="POST" @submit.prevent="loginValidate">
        <input type="hidden" name="_token" v-bind:value="axiontoken">
        <input type="hidden" name="email" v-bind:value="emailLOGIN">
        <input type="hidden" name="password" v-bind:value="passwordLOGIN">


        <div v-if="loading" class="card shadow" style="background: #ffffff; border-left: 0.5em solid #000a44; border-right: 0.5em solid #000a44;">
            <div class="card-header bg-white">
                <center>
                    <img src="/img/axionT.png" width="90vw">
                </center>
            </div>
            <div class="card-body">
                <center>
                    <logo size="65"></logo>
                    <span class="font-weight-bold text-center">{{ textAlert }}</span>
                </center>
            </div>
        </div>
        <div v-if="!loading" class="card shadow-sm" style="background: #ffffff; border-left: 0.5em solid #000a44; border-right: 0.5em solid #000a44;">
            <div class="card-header bg-white border-light">
                <h3 class="text-light text-center">
                    <img src="/img/axionT.png" width="90vw">
                </h3>
            </div>
            <div class="card-body">
                <div class="input-group mb-1 shadow-sm">
                    <span class="input-group-text bg-primary text-white">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="email" class="form-control" placeholder="E-mail" aria-label="E-mail" aria-describedby="Informe o e-mail do usuário" v-model="emailLOGIN" v-on:change="executaTrim()">
                </div>

                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-primary text-white">
                        <i class="fas fa-key"></i>
                    </span>
                    <input type="password" class="form-control" placeholder="Senha" aria-label="Senha" aria-describedby="Informe sua senha" v-model="passwordLOGIN" v-on:change="executaTrim()">
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-primary btn-block font-weight-bold shadow-sm" type="submit">Acessar</button>
                <h6 class="text-center text-primary mt-1">
                    <small>Versão {{ version }}</small>
                </h6>
            </div>
        </div>
    </form>
</template>

<script>
    export default {
        props: ['axiontoken'],
        components: {
        },
        data() {
            return {
                version: "Não definida",
                emailLOGIN: "",
                passwordLOGIN: "",
                textAlert: "Carregando sistema! Aguarde.",
                loading: false,
            }
        },
        methods: {
            getVersion      :   function(){
                var vm      =   this;
                var version =   vm.LAYOUT.retrieveData();
                vm.version  =   version['LAYOUT_version'];
            },
            executaTrim     :   function(){
                var vm          =   this;
                vm.emailLOGIN   =   vm.emailLOGIN.trim();
                vm.passwordLOGIN=   vm.passwordLOGIN.trim();
            },
            verifyToken     :   function(){
                var vm          =   this;

                if(vm.axiontoken == undefined || vm.axiontoken.trim() == '' || vm.axiontoken == null) {
                    vm.loading      =   true;
                    vm.textAlert    =   "Erro no sistema! Comunique ao administrador.";
                }
            },
            loginValidate   :   function(e){
                var vm      =   this;

                vm.loading      =   true;
                vm.textAlert    =   "Carregando sistema! Aguarde.";

                if(vm.emailLOGIN == null || vm.emailLOGIN == "") {
                    vm.loading  =   false;
                    Vue.$toast.open({
                        message: 'Dados de login não preenchidos! Verifique.',
                        type: 'error',
                        pauseOnHover: true,
                        duration: 10000,
                    });
                    return false;
                }

                if(vm.passwordLOGIN == "" || vm.passwordLOGIN == null) {
                    vm.loading  =   false;
                    Vue.$toast.open({
                        message: 'Senha não preenchida! Verifique.',
                        type: 'error',
                        pauseOnHover: true,
                        duration: 10000,
                    });
                    return false;
                }

                e.preventDefault();
                vm.$refs.myForm.submit();
            }
        },
        mounted() {
            this.getVersion();
            this.verifyToken();
        },
    }
</script>
