<template>
    <div>
        <semipolar-spinner
            :size="30"
            color="#bfc7ff"
        />
    </div>
</template>

<script>
    import { SemipolarSpinner  } from 'epic-spinners';

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
