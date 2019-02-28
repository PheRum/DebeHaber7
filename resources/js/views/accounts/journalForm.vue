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
                            </b-col>
                            <b-col>

                                <b-form-group :label="$t('commercial.number')">
                                    <b-input type="text" placeholder="Invoice Number" v-mask="spark.taxPayerConfig.document_mask" v-model="data.number"/>
                                </b-form-group>

                            </b-col>
                        </b-row>
                    </b-container>
                </b-card>
            </b-col>
        </b-row>

        <b-row>
            <b-col>
                <b-card>
                    <b-container>
                        <b-row>
                            <b-col>
                                <b-form-group :label="$t('commercial.comment')">
                                    <b-input type="text" placeholder="Comment" v-model="data.comment"/>
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
                                <option v-for="item in accountCharts" :key="item.key" :value="item.id">{{ item.name }}</option>
                            </b-form-select>
                        </template>
                        <template slot="debit" slot-scope="data">
                            <!-- mask?? -->
                            <b-input type="text" v-model="data.item.debit"  placeholder="Debit"/>

                        </template>
                        <template slot="credit" slot-scope="data">
                            <!-- mask?? -->
                            <b-input type="text" v-model="data.item.credit"  placeholder="credit"/>

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
import VueNumeric from 'vue-numeric'
export default {
    components: { 'crud': crud,VueNumeric },
    data() {
        return {
            data: {
                date: '',
                details: [{id:0}],
                id: 0,
                number: '',
                comment: ''
            },
            pageUrl: '/accounting/journals',

            accountCharts: [],

            lastDeletedRow: [],
        };
    },
    computed: {
        columns()
        {
            return  [ {
                key: 'chart_id',
                label: this.$i18n.t('commercial.account'),
                sortable: true
            },
            {
                key: 'debit',
                label: this.$i18n.t('commercial.debit'),
                sortable: true
            },
            {
                key: 'credit',
                label: this.$i18n.t('commercial.credit'),
                sortable: true
            },
            {
                key: 'actions',
                label: '',
                sortable: false
            }];
        },

        baseUrl() {
            return '/api/' + this.$route.params.taxPayer + '/' + this.$route.params.cycle;
        },
    },
    methods: {

        onSave() {
            var app = this;

            crud.methods
            .onUpdate(app.baseUrl + app.pageUrl, app.data)
            .then(function (response) {
                app.$snack.success({ text: this.$i18n.t('commercial.invoiceSaved', app.data.number) });
                app.$router.go(-1);
            }).catch(function (error) {
                app.$snack.danger({ text: 'Error OMG!' });
            });
        },

        onSaveNew() {
            var app = this;

            crud.methods
            .onUpdate(app.baseUrl + app.pageUrl, app.data)
            .then(function (response) {
                app.$snack.success({ text: this.$i18n.t('commercial.JournalSaved', app.data.number) });
                app.$router.push({ name: app.$route.name, params: { id: '0' }})

            }).catch(function (error) {
                app.$snack.danger({
                    text: this.$i18n.t('general.errorMessage'),
                });
            });
        },

        onCancel() {
            this.$swal.fire({
                title: this.$i18n.t('general.cancel'),
                text: this.$i18n.t('general.cancelVerification'),
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: this.$i18n.t('general.cancelConfirmation'),
                cancelButtonText: this.$i18n.t('general.cancelRejection'),
            }).then((result) => {
                if (result.value) {
                    this.$router.go(-1);
                }
            })
        },

        addDetailRow() {
            this.data.details.push({
                // index: this.data.details.length + 1,
                id:0,
                chart_id: this.accountCharts[0].id,
                debit: '0',
                credit: '0',
            })
        },

        deleteRow(item) {

            if (item.id > 0) {
                var app = this;

                crud.methods
                .onDelete(app.baseUrl + app.pageUrl + '/details', item.id)
                .then(function (response) { });
            }

            this.lastDeletedRow = item;

            this.$snack.success({
                text: this.$i18n.t('general.rowDeleted'),
                button: this.$i18n.t('general.undo'),
                action: this.undoDeletedRow
            });

            this.data.details.splice(this.data.details.indexOf(item), 1);
        },

        undoDeletedRow() {
            if (this.lastDeletedRow.id > 0) {
                crud.methods
                .onUpdate(app.baseUrl + app.pageUrl + '/details', this.lastDeletedRow)
                .then(function (response) { });
                //axios code to insert detail again??? or let save do it.
            }

            this.data.details.push(this.lastDeletedRow);
        },
    },

    mounted() {
        var app = this;



        if (app.$route.params.id > 0) {
            crud.methods
            .onRead(app.baseUrl + app.pageUrl + '/' + app.$route.params.id)
            .then(function (response) {
                app.data = response.data.data;
            });
        } else {
            app.data.date = new Date(Date.now()).toISOString().split("T")[0];
        }

        crud.methods
        .onRead(app.baseUrl + "/accounting/charts/for/accountables/")
        .then(function (response) {
            app.accountCharts = response.data.data;
        });


    }
}
</script>
