<template>
    <div class="text-primary">
        123
    </div>
</template>

<script>
    export default {
        props: [
            'auth', 'token','bearer'
        ],
        components: {
        },
        data() {
            return {
                'userData'  :   null,
            }
        },
        methods: {
            
        },
        mounted() {
            //this.LAYOUT.initData();
            //this.PREFERENCES.initData();

            //this.userData   =   JSON.parse(this.auth);
            //this.initFilter();
        },
    }
</script>
