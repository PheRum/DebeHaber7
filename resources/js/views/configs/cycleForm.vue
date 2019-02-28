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
                                <b-form-group :label="$t('commercial.year')">
                                    <b-input type="text" required placeholder="Missing Information" v-model="data.year"/>
                                </b-form-group>
                                <b-form-group :label="$t('commercial.start')">
                                    <b-input type="date" placeholder="Start Date" v-model="data.start_date"/>
                                </b-form-group>
                            </b-col>
                            <b-col>
                                <b-form-group :label="$t('commercial.end')">
                                    <b-input type="date" placeholder="End Date" v-model="data.end_date"/>
                                </b-form-group>
                                <b-form-group :label="$t('commercial.chartVersion')" v-if="versions.length > 0">
                                    <b-form-select v-model="data.chart_version_id">
                                        <option v-for="doc in versions" :key="doc.key" :value="doc.id">{{ doc.name }}</option>
                                    </b-form-select>
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
                start_date: '',
                year: '',
                end_date: '',
                id: 0,

            },
            pageUrl: '/config/cycles',
            versions: [],


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
            .onUpdate(app.baseUrl + app.pageUrl + "/store", app.data)
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
            .onUpdate(app.baseUrl + app.pageUrl + "/store", app.data)
            .then(function (response) {
                app.$snack.success({ text: this.$i18n.t('commercial.invoiceSaved', app.data.number) });
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
        crud.methods
        .onRead(app.baseUrl + '/config/chart-versions')
        .then(function (response) {
            app.versions = response.data;
        });
        
        if (app.$route.params.id > 0) {
            crud.methods
            .onRead(app.baseUrl + app.pageUrl + '/' + app.$route.params.id)
            .then(function (response) {
                app.data = response.data.data;
            });
        } else {
            app.data.start_date = new Date(Date.now()).toISOString().split("T")[0];
            app.data.end_date = new Date(Date.now()).toISOString().split("T")[0];
        }
    }
}
</script>
