<template>
    <div>
        <b-row>
            <b-col>
                <b-card>
                    <b-container>
                        <b-row>
                            <b-col>
                                <b-form-group label="Invoice Date">
                                    <b-input type="date" required placeholder="Missing Information" v-model="data.date"/>
                                </b-form-group>
                                <b-form-group label="Customer">
                                    <b-input-group>
                                        <b-input-group-text slot="prepend">
                                            <i class="material-icons">search</i>
                                        </b-input-group-text>
                                        <b-input type="text" placeholder="Search for Customer" v-model="data.customer.name"/>
                                    </b-input-group>
                                </b-form-group>
                                <b-form-group label="Payment Condition">
                                    <b-input-group>
                                        <b-input type="number" placeholder="Payment" v-model="data.payment_condition"/>
                                        <b-input-group-append v-if="data.payment_condition == 0">
                                            <b-form-select v-model="data.chart_account_id" class="mb-3">
                                                <option v-for="account in accountCharts" :key="account.key" :value="account.id">{{ account.name }}</option>
                                            </b-form-select>
                                        </b-input-group-append>
                                    </b-input-group>
                                </b-form-group>
                                <b-form-group label="Exchange Rates">
                                    <b-input-group>
                                        <b-input-group-prepend>
                                            <b-form-select v-model="data.currency_id">
                                                <option v-for="currency in currencies" :key="currency.key" :value="currency.id">{{ currency.name }}</option>
                                            </b-form-select>
                                        </b-input-group-prepend>
                                        <b-input type="number" placeholder="Payment" v-model="data.rate"/>
                                    </b-input-group>
                                </b-form-group>
                            </b-col>
                            <b-col>
                                <b-form-group label="Document" v-if="documents.length > 0">
                                    <b-form-select v-model="data.document_id" class="mb-3">
                                        <option v-for="doc in documents" :key="doc.key" :value="doc.id">{{ doc.name }}</option>
                                    </b-form-select>
                                </b-form-group>

                                <b-form-group label="Invoice Number">
                                    <b-input type="text" placeholder="Missing Information" v-model="data.number"/>
                                </b-form-group>

                                <b-form-group label="Invoice Code">
                                    <b-input-group>
                                        <b-input type="text" placeholder="Invoice Code" v-model="data.code"/>
                                        <b-input-group-append>
                                            <b-input type="date" placeholder="Code Expiry Date" v-model="data.code_expiry"/>
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
                    <b-table hover :items="data.details" :fields="columns">
                        <template slot="chart_id" slot-scope="data">
                            <b-form-select v-model="data.item.chart_id" class="mb-3">
                                <option v-for="item in itemCharts" :key="item.key" :value="item.id">{{ item.name }}</option>
                            </b-form-select>
                        </template>
                        <template slot="chart_vat_id" slot-scope="data">
                            <b-form-select v-model="data.item.chart_vat_id" class="mb-3">
                                <option v-for="vat in vatCharts" :key="vat.key" :value="vat.id">{{ vat.name }}</option>
                            </b-form-select>
                        </template>
                        <template slot="value" slot-scope="data">
                            <b-form-input v-model="data.item.value" type="number" placeholder="Value"></b-form-input>
                        </template>
                    </b-table>
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
            currencies: [],
            accountCharts: [],
            vatCharts: [],
            itemCharts: [],

            columns: [
                {
                    key: 'chart_id',
                    label: 'Item',
                },
                {
                    key: 'chart_vat_id',
                    label: 'Vat',
                },
                {
                    key: 'value',
                    label: 'Value',
                }
            ]
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
        .then(function (response) {  app.data = response; });

        crud.methods.onRead('/api/' + app.$route.params.taxPayer + '/currencies')
        .then(function (response) { app.currencies = response; });

        crud.methods.onRead(baseUrl + "accounting/charts/for/money/")
        .then(function (response) { app.accountCharts = response; });

        crud.methods.onRead(baseUrl + "accounting/charts/for/vats-debit")
        .then(function (response) { app.vatCharts = response; });

        crud.methods.onRead(baseUrl + "accounting/charts/for/income")
        .then(function (response) { app.itemCharts = response; });
    }
}
</script>
