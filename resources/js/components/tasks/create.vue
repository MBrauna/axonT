<template>
    <div>
        
    </div>
</template>

<script>
    

    export default {
        props: [
            'size',
        ],
        components: {
            SemipolarSpinner,
        },
        data() {
            return {
            }
        },
        methods: {
        },
        mounted() {
            this.LAYOUT.initData();
            this.PREFERENCES.initData();
        },
    }
</script>
