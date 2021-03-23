<template>
    <div>
    </div>
</template>

<script>
    export default {
        props: [
            'data','type'
        ],
        components: {
        },
        data() {
            return {
            }
        },
        methods: {
            execAlert   :   function(){
                var vm  =   this;

                switch (vm.type) {
                    case 'success':
                        Vue.$toast.success(vm.data);
                        break;
                    case 'error':
                        Vue.$toast.error(vm.data);
                        break;
                    case 'warning':
                        Vue.$toast.warning(vm.data);
                        break;
                    case 'info':
                        Vue.$toast.info(vm.data);
                        break;
                    case 'default':
                        Vue.$toast.default(vm.data);
                        break;
                    default:
                        Vue.$toast.default(vm.data);
                        break;
                }
            }
        },
        mounted() {
            this.LAYOUT.initData();
            this.PREFERENCES.initData();
            this.execAlert();
        },
    }
</script>
