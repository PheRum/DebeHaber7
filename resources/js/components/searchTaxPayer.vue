<template>
    <div>
        <b-input-group>
            <b-input-group-text slot="prepend">
                <span v-if="value != null">
                    <b>{{ value.name }}</b> | {{ value.taxid }}
                    <i class="material-icons md-18 ml-20">search</i>
                </span>
                <i v-else class="material-icons md-18">search</i>
            </b-input-group-text>
            <b-input type="text" v-model="search" placeholder="Search for Taxpayer" @change="searchData()"/>
        </b-input-group>
        <b-list-group>
            <b-list-group-item  v-for="taxPayer in taxPayers" @click="select(taxPayer)" :key="taxPayer.id">
                <b>{{ taxPayer.name }}</b> | {{ taxPayer.taxid }}
            </b-list-group-item>
        </b-list-group>
    </div>
</template>

<script>
export default {
    props: ['value'],
    data: () => ({
        search: '',
        taxPayers: []
    }),
    methods: {
        updateValue: function (value) {
            this.$emit('input', value);
        },
        select(taxPayer)
        {
            var app = this;
            app.updateValue(taxPayer);
            app.taxPayers = [];
            app.search = '';
        },
        searchData()
        {

            var app = this;
            if (app.search.length < 3) {
                app.taxPayers = [];
            } else {
                axios({
                    method: 'get',
                    url: '/api/PYG/get_taxpayers/' + app.search,
                    responseType: 'json',
                })
                .then(function (response) {
                    app.taxPayers = response.data;
                })
                .catch(function (error) {
                    console.log(error.response);
                    return error.response
                });
            }
        }
    },
    mounted() {
        //do something after mounting vue instance
        var app = this;
    }
}
</script>
