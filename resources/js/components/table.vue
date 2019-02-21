<template>
    <div>
        <vue-topprogress ref="topProgress"></vue-topprogress>
        <b-card v-if="lists.length > 0 && is_loaded" no-body>
            <b-table hover responsive :items="lists" :fields="columns" :current-page="current_page">
                <template slot="date" slot-scope="data">
                    {{ new Date(data.item.date).toLocaleDateString() }}
                </template>
                <template slot="expiry" slot-scope="data">
                    <div v-if="data.item.expiry >= new Date()">
                        {{ new Date(data.item.expiry).toLocaleDateString() }}
                    </div>
                    <div v-else>
                        <span class="text-danger">{{ new Date(data.item.expiry).toLocaleDateString() }}</span>
                    </div>
                </template>
                <template slot="total" slot-scope="data">
                    <span class="float-right">
                        {{ new Number(sumValue(data.item.details)).toLocaleString() }}
                        <small class="text-success text-uppercase" v-if="data.item.currency != null">{{ data.item.currency.code }}</small>
                    </span>
                </template>
                <template slot="debit" slot-scope="data">
                    <span class="float-right">
                        {{ new Number(sumDebit(data.item.details)).toLocaleString() }}
                        <small class="text-success text-uppercase" v-if="data.item.currency != null">{{ data.item.currency.code }}</small>
                    </span>
                </template>
                <template slot="balance" slot-scope="data">
                    <span class="float-right">
                        {{ new Number(data.item.balance).toLocaleString() }}
                        <small class="text-success text-uppercase" v-if="data.item.currency != null">{{ data.item.currency.code }}</small>
                    </span>
                </template>
                <template slot="buy_rate" slot-scope="data">
                    <span class="float-right">
                        {{ new Number(data.item.buy_rate).toLocaleString() }}
                    </span>
                </template>
                <template slot="sell_rate" slot-scope="data">
                    <span class="float-right">
                        {{ new Number(data.item.sell_rate).toLocaleString() }}
                    </span>
                </template>


                <template slot="row-details" slot-scope="row">
                    <b-card>
                        <b-row v-for="detail in row.item.details" :key="detail.key">
                            <b-col sm="1" class="text-sm-right"><b>chart:</b></b-col>
                            <b-col>{{ detail.chart.name }}</b-col>
                            <b-col sm="3" class="text-sm-right"><b>debit:</b></b-col>
                            <b-col>{{ new Number(detail.debit).toLocaleString() }}</b-col>
                            <b-col sm="3" class="text-sm-right"><b>credit:</b></b-col>
                            <b-col>{{ new Number(detail.credit).toLocaleString() }}</b-col>
                        </b-row>

                        <b-button size="sm" @click="row.toggleDetails">Hide Details</b-button>
                    </b-card>
                </template>

                <template slot="hasDetails" slot-scope="row">
                    <b-button-group size="sm" class="show-when-hovered">
                        <b-button @click="row.toggleDetails"><i class="material-icons md-19">remove_red_eye</i></b-button>
                    </b-button-group>
                </template>

                <template slot="action" slot-scope="data">
                    <b-button-group size="sm" class="show-when-hovered">
                        <b-button :to="{ name: formURL, params: { id: data.item.id }}"><i class="material-icons md-18">edit</i></b-button>
                        <b-button @click="onDelete(data.item)"><i class="material-icons md-19">delete_outline</i></b-button>
                    </b-button-group>
                </template>
            </b-table>
        </b-card>
        <b-card v-else-if="is_loaded == false">
            <h4>Please Wait</h4>
            <p class="lead">We are currently fetching your data.</p>
        </b-card>
        <b-card v-else-if="lists.length == 0">
            <h4>You're running on empty!</h4>
            <p class="lead">How about <router-link :to="{ name: formURL, params: { id: 0}}">creating</router-link> some data</p>
            <b-img thumbnail fluid width="256" src="/img/apps/no-data.svg" alt="Thumbnail" />
        </b-card>
        <b-pagination v-if="lists.length > 0"
            size="md" align="center" :total-rows="meta.total"
            :per-page="meta.per_page"  @change="list()"></b-pagination>
        </div>
    </template>

    <script>
    import crud from './crud.vue';
    export default {
        components: { 'crud': crud },
        props: ['columns'],
        data: () => ({
            skip: 1,
            lists: [],
            meta: [],
            is_loaded: false,
            current_page: 1,
        }),
        computed: {
            // a computed getter
            formURL: function () {
                return this.$route.name.replace('List', 'Form');
            },
            viewURL: function () {
                return this.$route.name.replace('List', 'View');
            },
        },
        methods:
        {
            list() {
                var app = this;
                var page = 1;
                this.$refs.topProgress.start();

                if (app.$children[2] != null) {
                    page = app.$children[2].currentPage;
                }

                axios.get('/api' + app.$route.path + '?page=' + page )
                .then(({ data }) =>
                {
                    app.lists = data.data;
                    app.meta = data.meta;
                    app.skip += app.pageSize;
                    app.is_loaded = true;
                    //finishes the top progress bar
                    this.$refs.topProgress.done()
                }).catch(function (error) {
                    this.$refs.topProgress.fail();
                    app.$snack.danger({ text: error.message });
                });
            },

            onDelete(row) {
                var app = this;

                crud.methods.onDelete('/api' + app.$route.path,row.id).then(function (response) {
                    app.lists.splice(app.lists.indexOf(row), 1);
                    app.$snack.success({text:'Deleted'});
                }).catch(function (error) {
                    console.log(error);
                    app.$snack.danger({ text: error.message });
                });
            },

            sumValue(details) {
                return details.reduce(function(sum, row) {
                    return sum + new Number(row.default_currency);
                }, 0);
            },

            sumDebit(details) {
                return details.reduce(function(sum, row) {
                    return sum + new Number(row.debit);
                }, 0);
            },

            sumCredit(details) {
                return details.reduce(function(sum, row) {
                    return sum + new Number(row.credit);
                }, 0);
            }
        },

        mounted() {
            var app = this;
            this.list();
        }
    }
    </script>
