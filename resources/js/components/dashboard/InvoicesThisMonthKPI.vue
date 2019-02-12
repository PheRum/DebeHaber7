<template>
    <b-card>
        <b-container>
            <b-row class="text-center">
                <b-col>
                    TOTAL
                    <h6 class="text-success">{{ total }}</h6>
                </b-col>
                <b-col >
                    VALUE
                    <h6 class="text-warning">{{ value }}</h6>
                </b-col>
                <b-col>
                    VAT
                    <h6 class="text-danger">{{ vat }}</h6>
                </b-col>
            </b-row>
        </b-container>
    </b-card>
</template>
<script>
export default {
    data: () => ({
        total: 0,
        value: 0,
        vat: 0,
    }),
    methods: {
        get_data() {
            var app = this;

            axios.get('/api' + app.$route.path)
            .then(({ data }) =>
            {
                // app.total = data.data;
                // app.value = data.meta;
                // app.vat = app.pageSize;
            });
        },
    },
    mounted() {
        var app = this;
        this.get_data();
    }
}
</script>
