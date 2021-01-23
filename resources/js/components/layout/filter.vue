<template>
    <div class="card border border-primary">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                    <div class="form-group">
                        <select class="form-control form-control-sm" id="employeeFilter" name="employeeFilter">
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                    <button class="btn btn-sm btn-primary btn-block" type="button">Atualizar</button>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center text-primary font-weight-bold">
                    Clique aqui para mais filtros
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
        ],
        components: {
        },
        data() {
            return {
            }
        },
        methods: {
        },
        mounted() {
        },
    }
</script>
