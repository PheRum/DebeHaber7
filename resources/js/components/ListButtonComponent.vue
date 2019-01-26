<template>

</template>

<script>
export default {
    props: ['url', 'hasFocus'],
    data: () => ({

    }),

    methods: {
        onSave($data)
        {
            var app = this;
            axios.post('/store-profiles' , $data)
            .then((data) =>
            {
                this.$router.go(this.$router.currentRoute)
            })
            .catch(ex => {
                console.log(ex.response);
                this.$toast.open({
                    duration: 5000,
                    message: 'Error trying to save record',
                    type: 'is-danger'
                })
            });
        }
    },

    mounted() {
        var app = this;
    }
}
</script>
