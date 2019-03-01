<template>
    <div>
        <b-row class="mb-5">
            <b-col >
                <b-btn class="d-none d-md-block float-left mr-10" v-shortkey="['esc']" @shortkey="onCancel()" @click="onCancel()">
                    <i class="material-icons">keyboard_backspace</i>
                    {{ $t('general.return') }}
                </b-btn>
                <h3 class="upper-case">
                    <img :src="$route.meta.img" alt="" class="mr-10" width="32">
                    {{ $route.meta.title }}
                </h3>
            </b-col>
            <b-col>
                <b-button-toolbar class="float-right d-none d-md-block">
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
                        <b-form-group :label="$t('commercial.code')">
                            <b-input required placeholder="Missing Information" v-model.trim="data.code"/>
                        </b-form-group>
                        <b-form-group :label="$t('commercial.name')">
                            <b-input required placeholder="Missing Information" v-model.trim="data.name"/>
                        </b-form-group>

                        <b-alert show variant="info">
                            <h5 class="alert-heading">
                                <i class="material-icons md-18">school</i>
                                Chart Configuration
                            </h5>
                            <p>
                                Charts are the life blood of any accounting system. All journal entries have multiple charts, and configuring them correctly can speed up the accounting process.
                                The first step is to
                            </p>
                            <p>
                                <small>
                                    <b>Is Accountable: </b>
                                </small>
                            </p>
                            <small>
                                <a href="#">More Info</a>
                            </small>
                        </b-alert>

                        <b-form-group label="Chart Type">
                            <b-form-radio-group buttons v-model.number="data.type" :options="spark.enumChartType" name="enumChartType"/>
                        </b-form-group>

                        <b-form-group label="Is Accountable">
                            <b-form-checkbox switch v-model="data.is_accountable" size="lg" name="check-button">
                                {{ $t('accounting.isAccountable') }}
                            </b-form-checkbox>
                        </b-form-group>

                        <b-form-group label="Asset Types" v-if="data.type == 1" description="Only accountable charts can be used in journals or transactions. If marked as false, it can only be used to summarise child accounts.">
                            <b-form-radio-group v-model.number="data.sub_type" :options="spark.enumAsset"/>
                        </b-form-group>
                        <b-form-group label="Liability Types" v-if="data.type == 2" description="Only accountable charts can be used in journals or transactions. If marked as false, it can only be used to summarise child accounts.">
                            <b-form-radio-group v-model.number="data.sub_type" :options="spark.enumLiability"/>
                        </b-form-group>
                        <b-form-group label="Equity Types" v-if="data.type == 3" description="Only accountable charts can be used in journals or transactions. If marked as false, it can only be used to summarise child accounts.">
                            <b-form-radio-group v-model.number="data.sub_type" :options="spark.enumEquity"/>
                        </b-form-group>
                        <b-form-group label="Revenue Types" v-if="data.type == 4" description="Only accountable charts can be used in journals or transactions. If marked as false, it can only be used to summarise child accounts.">
                            <b-form-radio-group v-model.number="data.sub_type" :options="spark.enumRevenue"/>
                        </b-form-group>
                        <b-form-group label="Expense Types" v-if="data.type == 5" description="Only accountable charts can be used in journals or transactions. If marked as false, it can only be used to summarise child accounts.">
                            <b-form-radio-group v-model.number="data.sub_type" :options="spark.enumExpense"/>
                        </b-form-group>
                    </b-container>
                </b-card>
            </b-col>
        </b-row>

        <b-row>
            <b-col>
                <b-card title="Partner" sub-title="Sync with Customer or Supplier">

                </b-card>
            </b-col>
        </b-row>

        <b-row>
            <b-col>
                <b-card title="Fixed Asset Group" sub-title="State the life cycle the fixed assets related to this chart will have">

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
                parent_id: [],
                chart_version_id: 0,
                taxpayer_id: null,
                country: null,
                is_accountable: false,
                code: '',
                name: '',
                level: 1,
                type: 1,
                sub_type: 1,

                partner_taxid: null,
                partner_name: null,

                coefficient: null,
                asset_years: null,
                created_at: '',
                updated_at: ''
            },
            pageUrl: '/accounting/charts',
            parentCharts: [],
        };
    },
    computed: {
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
                app.$snack.success({ text: this.$i18n.t('general.saved', app.data.number) });
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
                app.$snack.success({ text: this.$i18n.t('general.saved', app.data.number) });
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
            app.data.code = '',
            app.data.type = 1,
            app.data.is_accountable = false
        }
    }
}
</script>
