<template>
    <div>
        <b-row>
            <b-col>
                <b-card :title="$parent.$route.meta.title" :sub-title="$parent.$route.meta.description">
                    <b-container>
                        <b-row>
                            <b-col>
                                <b-form-group label="Invoice Date">
                                    <b-input type="date" required placeholder="Missing Information" v-model="data.date"/>
                                </b-form-group>
                                <b-form-group label="Customer">
                                    <!-- <b-input type="text" placeholder="Search for Customer" v-model="data.customer.name"/> -->
                                </b-form-group>
                            </b-col>
                            <b-col>
                                <b-form-group label="Document">
                                    <b-form-select placeholder="Documents can simplfy manually loading data" v-model="data.document_id"/>
                                </b-form-group>

                                <b-form-group label="Invoice Number">
                                    <b-input type="text" placeholder="Missing Information" v-model="data.number"/>
                                </b-form-group>

                                <b-form-group label="Invoice Code">
                                    <b-input-group>
                                        <b-input type="text" placeholder="Invoice Code"/>
                                        <b-input-group-append>
                                            <b-input type="date" placeholder="Code Expiry Date"/>
                                        </b-input-group-append>
                                    </b-input-group>
                                </b-form-group>
                            </b-col>
                        </b-row>
                    </b-container>
                </b-card>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <b-card no-body>
                    <b-table hover> </b-table>
                </b-card>
            </b-col>
        </b-row>
    </div>
</template>

<script>
import crud from '../../components/crud.vue';
export default {
    components: { 'crud': crud },
    data() {
        return {
            data: [],
            documents: [],
            accountCharts: [],
            accountVats: [],
            accountItems: [],
        };
    },
    methods: {
        onSave()
        {
            crud.onSave(1);
        }
    },
    mounted() {
        var app = this;
        var baseUrl = '/api/' + app.$route.params.taxPayer + '/' + app.$route.params.cycle + '/'

        crud.methods.onRead(baseUrl + "commercial/sales/" + app.$route.params.id)
        .then(function (response) { app.data = response.data; });

        crud.methods.onRead(baseUrl + "charts/for/money/")
        .then(function (response) { app.accountCharts = response.data; });

        crud.methods.onRead(baseUrl + "charts/for/vats-debit")
        .then(function (response) { app.accountVats = response.data; });

        crud.methods.onRead(baseUrl + "charts/for/income")
        .then(function (response) { app.accountItems = response.data; });

        // Toast.fire({
        //     type: 'success',
        //     title: 'Ready when you are!'
        // })
    }
}
</script>
