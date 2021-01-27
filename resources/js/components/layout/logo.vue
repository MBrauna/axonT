<template>
    <div>
        <semipolar-spinner
            :size="height"
            color="#02126e"
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
                height: 30,
            }
        },
        methods: {
        },
        mounted() {
            this.LAYOUT.initData();
            this.PREFERENCES.initData();

            if(this.size !== null) {
                this.height = this.size;
            }
        },
    }
</script>
