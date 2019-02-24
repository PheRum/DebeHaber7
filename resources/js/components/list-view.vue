<template>
    <div>
        <vue-topprogress ref="topProgress"></vue-topprogress>
        <b-card no-body>

            <b-table hover responsive :busy="crud.loading" :items="crud.items" :fields="columns" show-empty>

                <!-- <slot v-bind:row="row.item"></slot> -->

                <template slot="row-details" slot-scope="row">
                    <!-- <slot name="details" v-bind:row="row"></slot> -->
                </template>

                <template slot="has-details" slot-scope="row">
                    <b-button-group size="sm" class="show-when-hovered">
                        <b-button @click="row.toggleDetails"><i class="material-icons md-19">remove_red_eye</i></b-button>
                    </b-button-group>
                </template>

                <template slot="actions" slot-scope="data">
                    <b-button-group size="sm" class="show-when-hovered">
                        <b-button :to="{ name: formURL, params: { id: data.item.id }}"><i class="material-icons md-18">edit</i></b-button>
                        <b-button @click="onDelete(data.item)"><i class="material-icons md-19">delete_outline</i></b-button>
                    </b-button-group>
                </template>
                <div slot="table-busy" class="text-center text-danger my-2">
                    <strong>Loading...</strong>
                </div>
                <template slot="empty" slot-scope="scope">
                    <h4>You're running on empty!</h4>
                    <p class="lead">How about <router-link :to="{ name: formURL, params: { id: 0}}">creating</router-link> some data</p>
                    <b-img thumbnail fluid width="256" src="/img/apps/no-data.svg" alt="Thumbnail" />
                </template>
            </b-table>
        </b-card>
        <b-pagination align="center" :total-rows="meta.total" :per-page="meta.per_page" @change="list()"></b-pagination>
    </div>
</template>

<script>
import crud from './crud.vue';
export default {
    components: { 'crud': crud },
    props: ['columns'],
    data: () => ({

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

            //Loading indicators
            this.$refs.topProgress.start();
            app.loading = true;

            if (app.$children[2] != null) {
                page = app.$children[2].currentPage;
            }

            axios.get('/api' + app.$route.path + '?page=' + page )
            .then(({ data }) => {
                app.lists = data.data;
                app.meta = data.meta;
                app.skip += app.pageSize;
                //finishes the top progress bar
            }).catch(function (error) {
                this.$refs.topProgress.fail();
                app.$snack.danger({ text: error.message });
            });

            app.loading = false;
            this.$refs.topProgress.done()
        },

        undoDeletedRow() {
            if (this.lastDeletedItem.uuid > 0) {
                crud.methods
                .onUpdate(app.baseUrl + app.pageUrl, this.lastDeletedItem)
                .then(function (response) { });
                //axios code to insert detail again??? or let save do it.
            }

            this.lists.push(this.lastDeletedItem);
        },
    },

    mounted() {
        var app = this;
        this.list();
    }
}
</script>
