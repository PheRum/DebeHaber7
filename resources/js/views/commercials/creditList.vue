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
                <div v-if="$route.name == 'creditList'">
                    <table-template :columns="columns"></table-template>
                </div>
                <router-view v-else></router-view>
            </b-col>
        </b-row>
    </b-container>
</template>

<script>
export default {
    name: "",
    data: () => ({
        columns: [
            {
                key: 'number',
                sortable: true
            },
            {
                key: 'action',
                sortable: false
            },
        ],
    })
}
</script>
