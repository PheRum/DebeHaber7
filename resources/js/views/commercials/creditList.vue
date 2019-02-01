<template>
    <b-container>
        <b-row>
            <b-col>
                <b-card-group deck>
                    <b-card bg-variant="light">
                        <h4 class="upper-case">
                            <img :src="$route.meta.img" alt="" class="ml-5 mr-5" width="26">
                            {{ $route.meta.title }}
                        </h4>
                        <p class="lead">
                            {{ $route.meta.description }}, <router-link to="{ name: 'creditForm', param: { id: 0}}">Create</router-link>
                        </p>
                    </b-card>

                    <invoices-this-month-kpi></invoices-this-month-kpi>

                    <b-card no-body>
                        <b-list-group flush>
                            <b-list-group-item href="#">
                                <i class="material-icons md-light">insert_chart</i>
                                Credit Book
                            </b-list-group-item>
                            <b-list-group-item href="#">
                                <i class="material-icons">insert_chart</i>
                                Credit Notes by Customer
                            </b-list-group-item>
                            <b-list-group-item href="#">
                                <i class="material-icons">insert_chart</i>
                                Credit Notes by Vat
                            </b-list-group-item>
                        </b-list-group>
                    </b-card>
                </b-card-group>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <b-card title="Credit List" sub-title="All your credit data will be shown here." v-if="$route.name == 'creditList'">
                    <b-table hover :items="lists" :fields="fields">
                        <template slot="HEAD_name" slot-scope="data">
                            <!-- A custom formatted header cell for field 'name' -->
                            <em>{{data.label}}</em>
                            ABHISDFHa
                        </template>
                        <template slot="actions" slot-scope="row">
                            <!-- We use @click.stop here to prevent a 'row-clicked' event from also happening -->
                            <b-button size="sm" @click.stop="info(row.item, row.index, $event.target)" class="mr-1">
                                Info modal
                            </b-button>
                            <b-button size="sm" @click.stop="row.toggleDetails">
                                {{ row.detailsShowing ? 'Hide' : 'Show' }} Details
                            </b-button>
                        </template>
                    </b-table>
                </b-card>
                <router-view v-else></router-view>
            </b-col>
        </b-row>
    </b-container>
</template>

<script>
export default {
    name: "",
    data: () => ({
        lists: [
            { customer: { name: 'John' }, date: '11/11/2000', number: 42, value: 10000 },
            { customer: { name: 'John' }, date: '11/11/2001', number: 36, value: 12000 },
            { customer: { name: 'John' }, date: '11/11/2002', number: 73, value: 13000 },
            { customer: { name: 'John' }, date: '11/11/2003', number: 62, value: 14000 }
        ],
        fields: {
            date: {
                label: 'Date',
                sortable: true
            },
            'customer.name': {
                label: 'Customer',
                sortable: true
            },
            number: {
                label: 'Person age',
                sortable: true
            },
            value: {
                label: 'Value',
                sortable: true
            },
        },
    })
}
</script>
