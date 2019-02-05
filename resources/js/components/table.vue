<template>
    <div>
        <b-card v-if="lists.length > 0" no-body>
            <b-table hover :items="lists" :fields="columns">
                <template slot="action" slot-scope="data">
                    <b-button v-if="row-hovered" variant="primary" href="">
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

        <b-pagination v-if="lists.length > 0" size="md" align="center" :total-rows="100" v-model="currentPage" :per-page="10"></b-pagination>
    </div>
</template>

<script>
export default {
    props: ['columns'],
    data: () => ({
        lists: [
            { id: 1, number: 1123 },
            { id: 2, number: 2345 },
            { id: 3, number: 3456 }
        ],
    }),
    methods:
    {
        list()
        {
            var app = this;

            axios.get('/api/' + this.taxpayer + '/' + this.cycle + '/' + this.baseurl + '/' + app.skip + '',
            {
                params:
                {
                    page: app.list.length / 100 + 1,
                },
            })
            .then(({ data }) =>
            {
                if (data.length > 0)
                {
                    for (let i = 0; i < data.length; i++)
                    {
                        app.list.push(data[i]);
                    }

                    app.skip += app.pageSize;
                    $state.loaded();
                }
            });
        }





        //onApprove??
    },
}
</script>
