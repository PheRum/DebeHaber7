<template>
    <div>
        <b-row class="mb-5">
            <b-col >
                <b-btn class="d-none d-md-block float-left" v-shortkey="['esc']" @shortkey="onCancel()" @click="onCancel()">
                    <i class="material-icons">keyboard_backspace</i>
                    {{ $t('general.return') }}
                    <!-- {{ $t('welcomeMsg') }} -->
                </b-btn>
                <h3 class="upper-case">
                    <img :src="$route.meta.img" alt="" class="mr-10" width="32">
                    {{ $route.meta.title }}
                </h3>
            </b-col>
            <b-col>
                <b-button-toolbar class="float-right d-none d-md-block">
                    <b-btn class="ml-15" v-shortkey="['ctrl', 'd']" @shortkey="addDetailRow()" @click="addDetailRow()">
                        <i class="material-icons">playlist_add</i>
                        {{ $t('general.addRowDetail') }}
                    </b-btn>
                    <b-button-group class="ml-15">
                        <b-btn variant="primary" v-shortkey="['ctrl', 'n']" @shortkey="onSaveNew()" @click="onSaveNew()">
                            <i class="material-icons">save</i>
                            {{ $t('general.save') }}
                        </b-btn>
                        <b-btn variant="danger" v-shortkey="['esc']" @shortkey="onCancel()" @click="onCancel()">
                            <i class="material-icons">cancel</i>
                            {{ $t('general.cancel') }}
                        </b-btn>
                    </b-button-group>
                </b-button-toolbar>
                <b-button-toolbar class="float-right d-md-none">
                    <b-btn class="ml-15" v-shortkey="['ctrl', 'd']" @shortkey="addDetailRow()" @click="addDetailRow()">
                        <i class="material-icons">playlist_add</i>
                    </b-btn>
                    <b-button-group class="ml-15">
                        <b-btn variant="primary" v-shortkey="['ctrl', 'n']" @shortkey="onSaveNew()" @click="onSaveNew()">
                            <i class="material-icons">save</i>
                        </b-btn>
                        <b-btn variant="danger" v-shortkey="['esc']" @shortkey="onCancel()" @click="onCancel()">
                            <i class="material-icons">cancel</i>
                        </b-btn>
                    </b-button-group>
                </b-button-toolbar>
            </b-col>
        </b-row>

        <b-row>
            <b-col>
                <b-card>
                    <b-container>
                        <b-row>
                            <b-col>
                                <b-form-group :label="$t('commercial.date')">
                                    <b-input type="date" required placeholder="Missing Information" v-model="data.date"/>
                                </b-form-group>
                                <b-form-group :label="$t('commercial.customer')">
                                    <search-taxpayer></search-taxpayer>
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
                                <b-form-group :label="$t('commercial.document')" v-if="documents.length > 0">
                                    <b-form-select v-model="data.document_id">
                                        <option v-for="doc in documents" :key="doc.key" :value="doc.id">{{ doc.name }}</option>
                                    </b-form-select>
                                </b-form-group>

                                <b-form-group :label="spark.taxPayerConfig.document_code" v-if="spark.taxPayerConfig.document_code != ''">
                                    <b-input-group>
                                        <b-input type="text" placeholder="$t('commercial.code')" v-model="data.code"/>
                                        <b-input-group-append>
                                            <b-input type="date" placeholder="$t('commercial.expiryDate')" v-model="data.code_expiry"/>
                                        </b-input-group-append>
                                    </b-input-group>
                                </b-form-group>

                                <b-form-group :label="$t('commercial.number')">
                                    <b-input type="text" placeholder="Invoice Number" v-mask="spark.taxPayerConfig.document_mask" v-model="data.number"/>
                                </b-form-group>

                                <b-form-group :label="$t('commercial.paymentCondition')">
                                    <b-input-group>
                                        <b-input type="number" placeholder="$t('commercial.paymentCondition')" v-model="data.payment_condition"/>
                                        <b-input-group-append v-if="data.payment_condition == 0">
                                            <b-form-select v-model="data.chart_account_id">
                                                <option v-for="account in accountCharts" :key="account.key" :value="account.id">{{ account.name }}</option>
                                            </b-form-select>
                                        </b-input-group-append>
                                    </b-input-group>
                                    <b-form-text>Specify days between invoice and payment dates. Ex: use 0 for cash, and 30 for thrity days payment terms.</b-form-text>
                                </b-form-group>
                                <b-form-group :label="$t('commercial.exchangeRate')">
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
                            <b-form-input :value="data.item.value" type="number" placeholder="Value"></b-form-input>
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
                customer: [],
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
        };
    },
    methods: {
        onSave() {
            var app = this;
            var baseUrl = '/api/' + app.$route.params.taxPayer + '/' + app.$route.params.cycle + '/commercial/sales'

            crud.methods
            .onUpdate(baseUrl, app.data)
            .then(function (response) {
                app.$snack.success({ text: 'Invoice Nr. ' + app.data.number + ', Saved!' });
                app.$router.go(-1);
            }).catch(function (error) {
                app.$snack.danger({ text: 'Error OMG!' });
            });
        },

        onSaveNew() {
            var app = this;
            var baseUrl = '/api/' + app.$route.params.taxPayer + '/' + app.$route.params.cycle + '/commercial/sales'

            crud.methods
            .onUpdate(baseUrl, app.data)
            .then(function (response) {
                app.$snack.success({ text: 'Invoice Nr. ' + app.data.number + ', Saved!' });
                app.$router.push({ name: 'salesForm', params: { id: '0' }})
                app.data.customer_id = 0;
                app.data.customer = [];

            }).catch(function (error) {
                app.$snack.danger({
                    text: 'Error OMG!',
                });
            });
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
                value: '0',
            })
        },

        deleteRow(item) {

            if (item.id > 0) {
                var app = this;
                var baseUrl = '/api/' + app.$route.params.taxPayer + '/' + app.$route.params.cycle + '/commercial/sales'

                crud.methods
                .onDelete(baseUrl, item.id)
                .then(function (response) { });
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
            if (this.lastDeletedRow.id > 0) {
                //axios code to delete the transaction detail.
            }

            this.data.details.push(this.lastDeletedRow);
        },
    },

    mounted() {
        var app = this;
        var baseUrl = '/api/' + app.$route.params.taxPayer + '/' + app.$route.params.cycle

        crud.methods
        .onRead('/api/' + app.$route.params.taxPayer + '/currencies')
        .then(function (response) {
            app.currencies = response.data.data;
        });

        if (app.$route.params.id > 0) {
            crud.methods
            .onRead(baseUrl + "/commercial/sales/" + app.$route.params.id)
            .then(function (response) {
                app.data = response.data.data;
            });
        } else {
            app.data.date = new Date(Date.now()).toISOString().split("T")[0];
            app.data.chart_account_id = app.accountCharts[0] != null ? app.accountCharts[0].id : null;
            app.data.payment_condition = 0;
            app.data.currency_id = 1;
            app.data.rate = 1;
        }

        crud.methods
        .onRead(baseUrl + "/accounting/charts/for/money/")
        .then(function (response) {
            app.accountCharts = response.data.data;
        });

        crud.methods
        .onRead(baseUrl + "/accounting/charts/for/vats-debit")
        .then(function (response) {
            app.vatCharts = response.data.data;
        });

        crud.methods
        .onRead(baseUrl + "/accounting/charts/for/income")
        .then(function (response) {
            app.itemCharts = response.data.data;
        });
    }
}
</script>
