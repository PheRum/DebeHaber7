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
                <b-card no-body>
                    <b-table hover :items="data" :fields="columns">
                        <template slot="code" slot-scope="data">
                            {{data.item.code}}
                        </template>
                        <template slot="name" slot-scope="data">
                            {{data.item.name}}
                        </template>
                        <template slot="debit" slot-scope="data" v-if="data.item.is_accountable">
                            <!-- mask?? -->
                            <b-input type="text" v-model="data.item.debit"  placeholder="Debit"/>

                        </template>
                        <template slot="credit" slot-scope="data" v-if="data.item.is_accountable">
                            <!-- mask?? -->
                            <b-input type="text" v-model="data.item.credit"  placeholder="credit"/>

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
            pageUrl: '/accounting/closing-balance',

            data: [],

            lastDeletedRow: [],
        };
    },
    computed: {
        columns()
        {
            return  [ {
                key: 'code',
                label: this.$i18n.t('commercial.code'),
                sortable: true
            },
            {
                key: 'name',
                label: this.$i18n.t('commercial.name'),
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
                app.data.customer_id = 0;
                app.data.customer = [];

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
    },

    mounted() {
        var app = this;

        crud.methods
        .onRead(app.baseUrl + app.pageUrl)
        .then(function (response) {
            app.data = response.data;
        });

    }
}
</script>
