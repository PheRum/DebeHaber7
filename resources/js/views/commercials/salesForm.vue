<template>
    <div>
        <b-row>
            <b-col>
                <h3 class="upper-case">
                    <img :src="$route.meta.img" alt="" class="mr-10" width="32">
                    {{ $route.meta.title }}
                </h3>
            </b-col>
            <b-col>
                <b-button-toolbar class="float-right">
                    <b-btn v-shortkey="['ctrl', 'd']" @shortkey="addDetailRow()" @click="addDetailRow()" class="ml-15 mb-10">
                        <i class="material-icons">playlist_add</i>
                        Add Detail Row
                    </b-btn>
                    <b-button-group class="ml-15 mb-10">
                        <b-btn variant="primary" v-shortkey="['ctrl', 's']" @shortkey="onSave()" @click="onSave()">
                            <i class="material-icons">save</i>
                            Save
                        </b-btn>
                        <b-btn variant="secondary" v-shortkey="['ctrl', 'n']" @shortkey="onSaveNew()" @click="onSave()">
                            <i class="material-icons">save</i>
                            Save &amp; New
                        </b-btn>
                    </b-button-group>
                    <b-btn v-shortkey="['esc']" @shortkey="onCancel()" @click="onCancel()" variant="danger" class="ml-15 mb-10">
                        <i class="material-icons">cancel</i>
                        Cancel
                    </b-btn>
                </b-button-toolbar>
            </b-col>
        </b-row>
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
                                            <span v-if="data.customer != null">
                                                {{ data.customer.name }} | {{ data.customer.taxid }}
                                                <i class="material-icons md-18 ml-20">search</i>
                                            </span>
                                            <i v-else class="material-icons md-18">search</i>
                                        </b-input-group-text>
                                        <b-input type="text" placeholder="Search for Customer"/>
                                    </b-input-group>
                                </b-form-group>

                                <b-container v-if="data.customer != null">
                                    Based on your past transactions, we can quickly recomend the same items again.
                                    <b-row>
                                        <b-col>
                                            <b-button href="">
                                                Favorite Detail 1
                                            </b-button>
                                            <b-button href="">
                                                Favorite Detail 2
                                            </b-button>
                                        </b-col>
                                    </b-row>
                                </b-container>
                            </b-col>
                            <b-col>
                                <b-form-group label="Document" v-if="documents.length > 0">
                                    <b-form-select v-model="data.document_id">
                                        <option v-for="doc in documents" :key="doc.key" :value="doc.id">{{ doc.name }}</option>
                                    </b-form-select>
                                </b-form-group>

                                <b-form-group :label="spark.taxPayerConfig.document_code" v-if="spark.taxPayerConfig.document_code != ''">
                                    <b-input-group>
                                        <b-input type="text" placeholder="Invoice Code" v-model="data.code"/>
                                        <b-input-group-append>
                                            <b-input type="date" placeholder="Code Expiry Date" v-model="data.code_expiry"/>
                                        </b-input-group-append>
                                    </b-input-group>
                                </b-form-group>

                                <b-form-group label="Invoice Number">
                                    <b-input type="text" placeholder="Invoice Number" v-mask="spark.taxPayerConfig.document_mask" v-model="data.number"/>
                                </b-form-group>

                                <b-form-group label="Condition">
                                    <b-input-group>
                                        <b-input type="number" placeholder="Payment" v-model="data.payment_condition"/>
                                        <b-input-group-append v-if="data.payment_condition == 0">
                                            <b-form-select v-model="data.chart_account_id">
                                                <option v-for="account in accountCharts" :key="account.key" :value="account.id">{{ account.name }}</option>
                                            </b-form-select>
                                        </b-input-group-append>
                                    </b-input-group>
                                </b-form-group>
                                <b-form-group label="Rates">
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
                            <b-form-select v-model="data.item.chart_id">
                                <option v-for="item in itemCharts" :key="item.key" :value="item.id">{{ item.name }}</option>
                            </b-form-select>
                        </template>
                        <template slot="chart_vat_id" slot-scope="data">
                            <b-form-select v-model="data.item.chart_vat_id">
                                <option v-for="vat in vatCharts" :key="vat.key" :value="vat.id">{{ vat.name }}</option>
                            </b-form-select>
                        </template>
                        <template slot="value" slot-scope="data">
                            <b-form-input v-model="data.item.value" type="number" placeholder="Value"></b-form-input>
                        </template>
                        <template slot="actions" slot-scope="data">
                            <b-button variant="link" @click="deleteRow(data.item)">
                                <i class="material-icons text-danger">delete_outline</i>
                            </b-button>
                        </template>
                    </b-table>
                </b-card>
            </b-col>
        </b-row>
        <b-row v-if="data.journal_id != null">
            <b-col>
                <b-card no-body>
                    Journal
                    <b-table>

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
            data: {
                chart_account_id: 0,
                code: 0,
                code_expiry: '',
                comment: '',
                currency_id: 0,
                customer_id: 0,
                date: '',
                details: [],
                document_id: '',
                document_type: 1,
                id: 0,
                is_deductible: 0,
                journal_id: null,
                number: '',
                payment_condition: 0,
                rate: 1,
                type: 4
            },

            documents: [],
            currencies: [],
            accountCharts: [],
            vatCharts: [],
            itemCharts: [],

            lastDeletedRow: [],

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
                },
                {
                    key: 'actions',
                    label: '',
                }
            ],

            journals: [
                { chart_id: '', debit: 0, credit: 0 },
                { chart_id: '', debit: 0, credit: 0 },
                { chart_id: '', debit: 0, credit: 0 },
            ]
        };
    },
    methods: {
        onSave() {
            //save and go back to previous url.
        },

        onSaveNew() {
            this.onSave();
            this.$router.push({ name: 'salesForm', params: { id: '0' } })
        },

        onCancel() {

            this.$swal.fire({
                title: 'Cancel?',
                text: "Canceling will not save changes made to this form.",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, cancel changes!',
                cancelButtonText: 'No, Keep working'
            }).then((result) => {
                if (result.value) {
                    this.$router.go(-1);
                }
            })
        },

        addDetailRow() {
            this.data.details.push({
                // index: this.data.details.length + 1,
                chart_id: this.itemCharts[0].id,
                chart_vat_id: this.vatCharts[0].id,
                value: 0,
            })
        },

        deleteRow(item) {
            if (item.id > 0) {
                //axios code to delete the transaction detail.
            }

            this.lastDeletedRow = item;

            this.$snack.success({
                text: 'Record Deleted',
                button: 'Undo',
                action: this.undoDeletedRow
            });

            this.data.details.splice(this.data.details.indexOf(item), 1);
        },

        undoDeletedRow() {
            this.data.details.push(this.lastDeletedRow);
        },

        calculateJournal() {
            //cash

            //itemCharts

            //vat
        }
    },
    mounted() {
        var app = this;
        var baseUrl = '/api/' + app.$route.params.taxPayer + '/' + app.$route.params.cycle + '/'

        crud.methods.onRead('/api/' + app.$route.params.taxPayer + '/currencies')
        .then(function (response) { app.currencies = response; });

        if (app.$route.params.id > 0) {
            crud.methods.onRead(baseUrl + "commercial/sales/" + app.$route.params.id)
            .then(function (response)
            {
                app.data = response;
                //this is required for eliminating from table.
                // if (app.data.details.length > 0) {
                //     var i = 0;
                //     app.data.details.forEach(function(item) {
                //         item.index = i + 1;
                //     });
                // }
            });
        } else {
            app.data.date = new Date(Date.now()).toISOString().split("T")[0];
            app.data.chart_account_id = app.accountCharts[0] != null ? app.accountCharts[0].id : null;
            app.data.payment_condition = 0;
            app.data.currency_id = 1;
            app.data.rate = 1;
        }

        crud.methods.onRead(baseUrl + "accounting/charts/for/money/")
        .then(function (response) { app.accountCharts = response; });

        crud.methods.onRead(baseUrl + "accounting/charts/for/vats-debit")
        .then(function (response) { app.vatCharts = response; });

        crud.methods.onRead(baseUrl + "accounting/charts/for/income")
        .then(function (response) { app.itemCharts = response; });
    }
}
</script>
