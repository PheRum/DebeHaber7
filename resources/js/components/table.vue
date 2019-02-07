<template>
    <div>
        <b-card v-if="lists.length > 0" no-body>
            <b-table hover :items="lists" :fields="columns"  :current-page="current_page">
                <template slot="date" slot-scope="data">
                    {{ new Number(data.item.date).toLocaleString() }}
                </template>
                <template slot="action" slot-scope="data">
                    <b-button variant="primary" href="">
                        submit
                    </b-button>
                    {{ data.item.number }} years old
                </template>
            </b-table>
        </b-card>

        <b-card v-else>
            <h4>You're running on empty!</h4>
            <p class="lead">How about <router-link :to="{ name: 'creditForm', params: { id: 0}}">creating</router-link> some data</p>
            <b-img thumbnail fluid width="256" src="/img/apps/no-data.svg" alt="Thumbnail" />
        </b-card>

        <b-pagination v-if="lists.length > 0" size="md" align="center" :total-rows="meta.total"
            :per-page="meta.per_page" v-model="current_page" @change="list()"></b-pagination>
    </div>
</template>

<script>
export default {
    props: ['columns', 'url'],
    data: () => ({
        skip: 1,
        lists: [],
        meta: [],
        current_page:1
    }),
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
                    app.meta=data.meta;
                    app.skip += app.pageSize;
                    $state.loaded();
                }
            });
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
