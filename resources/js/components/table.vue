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
                <template slot="balance" slot-scope="data">
                    <span class="float-right">
                        {{ new Number(data.item.balance).toLocaleString() }}
                        <small class="text-success text-uppercase" v-if="data.item.currency != null">{{ data.item.currency.code }}</small>
                    </span>
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
                this.$refs.topProgress.start();
                var page = 1;

                if (app.$children[2]!=null) {
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
                });

                //todo add fail function in topProgress
            },

            onDelete(row) {
                var app = this;

                crud.methods.onDelete('/api' + app.$route.path,row.id).then(function (response) {
                    console.log(app);
                    app.lists.splice(app.lists.indexOf(row), 1);
                    app.$snack.success({text:'Deleted'});
                }).catch(function (error) {
                    console.log(error);
                    app.$snack.danger({ text: 'Error OMG!' });
                });;
            },

            sumValue(details) {
                return details.reduce(function(sum, row) {
                    return sum + new Number(row.default_currency);
                }, 0);
            }
        },

        mounted() {
            var app = this;
            this.list();
        }
    }
    </script>
