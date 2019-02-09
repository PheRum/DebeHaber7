<template>
    <div>
        <b-card v-if="lists.length > 0" no-body>
            <b-table hover :items="lists" :fields="columns" :current-page="current_page" @row-hovered="rowHovered">
                <template slot="date" slot-scope="data">
                    {{ new Number(data.item.date).toLocaleString() }}
                </template>
                <template slot="action" slot-scope="data">
                    <div v-show="isHovered(data.item)">
                        <b-button-group size="sm">
                            <b-button :to="{ name: formURL, params: { id: data.item.id }}"><i class="material-icons md-18">edit</i></b-button>
                            <b-button><i class="material-icons md-18">delete_outline</i></b-button>
                        </b-button-group>
                    </div>
                </template>
            </b-table>
        </b-card>

        <b-card v-else>
            <h4>You're running on empty!</h4>
            <p class="lead">How about <router-link :to="{ name: formURL, params: { id: 0}}">creating</router-link> some data</p>
            <b-img thumbnail fluid width="256" src="/img/apps/no-data.svg" alt="Thumbnail" />
        </b-card>

        <b-pagination v-if="lists.length > 0" size="md" align="center" :total-rows="meta.total"
            :per-page="meta.per_page" v-model="current_page" @change="list()"></b-pagination>
        </div>
    </template>

    <script>
    export default {
        props: ['columns'],
        data: () => ({
            skip: 1,
            lists: [],
            meta: [],
            current_page: 1,
            hoveredRow: null
        }),
        computed: {
            // a computed getter
            formURL: function () {
                return this.$route.name.replace('List', 'Form');
            },
            viewURL: function () {
                return this.$route.name.replace('List', 'View');
            }
        },
        methods:
        {
            list()
            {
                var app = this;

                axios.get('/api' + app.$route.path + '?page=' + app.current_page)
                .then(({ data }) =>
                {
                    if (data.data.length > 0)
                    {
                        app.lists = data.data;
                        app.meta = data.meta;
                        app.skip += app.pageSize;
                        $state.loaded();
                    }
                });
            },
            rowHovered(item){
                this.hoveredRow = item;
                this.$refs.table.refresh();
            },
            isHovered(item){
                return item == this.hoveredRow;
            }
            //onApprove??
        },
        mounted() {
            //do something after mounting vue instance
            var app = this;
            this.list();
        }
    }
    </script>
