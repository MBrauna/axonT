<template>
  <header class="navbar navbar-expand navbar-light flex-column flex-md-row bd-navbar fixed-top shadow border-primary border-bottom bg-light" id="header">
      <a class="navbar-brand d-none d-md-block" href="/">
          <span style="font-size: 1em; font-weight: bold;" class="text-primary px-3">AxonT</span>
      </a>

      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Menu de navegação">
          <span class="fas fa-bars"></span>
      </button>

      <div class="navbar-nav-scroll">
          <ul class="navbar-nav bd-navbar-nav flex-row">
              <li class="nav-item dropdown">
                  <a class="nav-link text-primary dropdown-toggle" href="#"  role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="menuGraph">
                      Desempenho
                  </a>
                  <div class="dropdown-menu border border-primary" aria-labelledby="menuGraph">
                      <a href="/performance/graph" class="dropdown-item text-primary d-flex justify-content-between">
                          <i class="fas fa-chart-pie mx-2"></i>
                          <small class="font-weight-bolder mx-2">Gráfico</small>
                      </a>
                      <!--<a href="/performance/report" class="dropdown-item text-primary d-flex justify-content-between">
                          <i class="fas fa-table mx-2"></i>
                          <small class="font-weight-bolder mx-2">Relatório</small>
                      </a>-->
                  </div>
              </li>
      
              <li class="nav-item dropdown">
                  <a class="nav-link text-primary dropdown-toggle" href="#"  role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="menuRequest">
                      Solicitação
                  </a>
                  <div class="dropdown-menu border border-primary" aria-labelledby="menuRequest">
                      <a href="/task/create" class="dropdown-item text-primary d-flex justify-content-between">
                          <i class="fas fa-pencil-alt mx-2"></i>
                          <small class="font-weight-bolder mx-2">Abertura</small>
                      </a>
                      <a href="/task/list" class="dropdown-item text-primary d-flex justify-content-between">
                          <i class="fas fa-th-list mx-2"></i>
                          <small class="font-weight-bolder mx-2">Lista de Solicitação</small>
                      </a>
                      <a href="/task/listAutomatic" class="dropdown-item text-primary d-flex justify-content-between">
                          <i class="fas fa-handshake mx-2"></i>
                          <small class="font-weight-bolder mx-2">Troca de Objetos</small>
                      </a>
                  </div>
              </li>
              <li class="nav-item">
                  <a class="nav-link text-primary" href="/card/list">Tarefa</a>
              </li>

              <li class="nav-item dropdown" v-show="authuser.administrador || authuser.admin_global">
                  <a class="nav-link text-primary dropdown-toggle" href="#"  role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="menuAdmin">
                      Administrador
                  </a>
                  <div class="dropdown-menu border border-primary" aria-labelledby="menuAdmin">
                      <a href="/admin/company" class="dropdown-item text-primary d-flex justify-content-between">
                          <i class="fas fa-briefcase mx-2"></i>
                          <small class="font-weight-bolder mx-2">Empresa</small>
                      </a>
                      <a href="/task/list" class="dropdown-item text-primary d-flex justify-content-between">
                          <i class="fas fa-users mx-2"></i>
                          <small class="font-weight-bolder mx-2">Usuário</small>
                      </a>
                  </div>
              </li>
          </ul>
      </div>

      <ul class="navbar-nav flex-row ml-md-auto d-none d-md-flex">
          <li class="nav-item">
              <a class="nav-link text-primary" href="http://1wiki.1nesstech.com.br/Gestor_de_N%C3%ADvel_de_Servi%C3%A7o_(GNS)" target="_blank">
                  <i class="fas fa-question-circle text-primary mx-1"></i>
                  Ajuda
              </a>
          </li>
          <li class="nav-item dropdown">
              <a class="nav-link text-primary dropdown-toggle" href="#"  role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="menuUser">
                  <i class="fas fa-user-circle mx-1"></i>
                  {{ authuser.name }}
              </a>
              <div class="dropdown-menu border border-primary" aria-labelledby="menuUser">
                  <span class="dropdown-item text-muted d-flex justify-content-between">
                      <i></i>
                      <small class="text-lowercase font-italic">{{ authuser.email }}</small>
                      <i></i>
                  </span>
                  <div class="dropdown-divider"></div>
                  <a href="#" class="dropdown-item text-primary d-flex justify-content-between">
                      <i class="fas fa-unlock mx-2"></i>
                      <small class="font-weight-bolder mx-2">Alterar senha</small>
                  </a>
                  <a class="dropdown-item text-primary d-flex justify-content-between" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                      <i class="fas fa-sign-out-alt mx-2"></i>
                      <small class="font-weight-bolder mx-2">Sair</small>
                  </a>
                  <form id="logout-form" action="/logout" method="POST" style="display: none;">
                    <input type="hidden" v-bind:value="csrf" name="_token">
                  </form>
              </div>
          </li>
      </ul>
  </header>
</template>

<script>
    export default {
        props: [
          'user','csrf'
        ],
        components: {
        },
        data() {
            return {
                authuser: {},
            }
        },
        methods: {
        },
        mounted() {
            this.LAYOUT.initData();
            this.PREFERENCES.initData();
            this.authuser = JSON.parse(this.user);
        },
    }
</script>
