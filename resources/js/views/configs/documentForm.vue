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
                            <b-form-group :label="$t('commercial.type')" >
                                <b-form-select v-model="data.type" >
                                    <option value="1">Invoice</option>
                                    <option value="2">DebitNote</option>
                                    <option value="3">CreditNote</option>
                                    <option value="4">CustomsClearence</option>
                                    <option value="5">SelfInvoice</option>
                                    <option value="6">Ticket</option>
                                    <option value="7">AirTicket</option>
                                    <option value="8">InvoiceFromAbroad</option>
                                    <option value="9">AbsorbedRetention</option>
                                    <option value="10">ElectronicAirTicket</option>
                                </b-form-select>
                            </b-form-group>
                        </b-row>
                        <b-row>
                            <b-col>
                                <b-form-group :label="$t('commercial.prefix')">
                                    <b-input type="text" required placeholder="Missing Information" v-model="data.prefix"/>
                                </b-form-group>
                                <b-form-group :label="$t('commercial.current')">
                                    <b-input type="text" required placeholder="Missing Information" v-model="data.current_range"/>
                                </b-form-group>
                                <b-form-group :label="$t('commercial.end')">
                                    <b-input type="text" required placeholder="Missing Information" v-model="data.end_range"/>
                                </b-form-group>

                            </b-col>
                            <b-col>
                                <b-form-group :label="$t('commercial.mask')">
                                    <b-input type="text" required placeholder="Missing Information" v-model="data.mask"/>
                                </b-form-group>

                                <b-form-group :label="$t('commercial.start')">
                                    <b-input type="text" required placeholder="Missing Information" v-model="data.start_range"/>
                                </b-form-group>

                                <b-form-group :label="spark.taxPayerConfig.document_code" v-if="spark.taxPayerConfig.document_code != ''">
                                    <b-input-group>
                                        <b-input type="text" placeholder="$t('commercial.code')" v-model="data.code"/>
                                        <b-input-group-append>
                                            <b-input type="date" placeholder="$t('commercial.expiryDate')" v-model="data.code_expiry"/>
                                        </b-input-group-append>
                                    </b-input-group>
                                </b-form-group>

                            </b-col>
                        </b-row>
                    </b-container>
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
                code: '',
                code_expiry: '',
                prefix: '',
                mask: '',
                start_date: '',
                current_date: '',
                end_date: '',
            },
            pageUrl: '/config/documents',


            lastDeletedRow: [],
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
                app.$snack.success({ text: this.$i18n.t('commercial.docuemntSaved') });
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
            app.data.prefix = 1;

        }


    }
}
</script>
