<template>
    <div>
        <b-row v-if="$route.name.includes('List')">
            <b-col>
                <b-card-group deck>
                    <b-card bg-variant="dark" text-variant="white">
                        <h4 class="upper-case">
                            <img :src="$route.meta.img" alt="" class="ml-5 mr-5" width="26">
                            {{ $t($route.meta.title) }}
                        </h4>
                        <p class="lead" v-if="$route.name.includes('List')">
                            {{ $t($route.meta.description) }}, <router-link to="{ name: $route.name, params: { id: 0}}">Create</router-link>
                        </p>
                    </b-card>

                    <invoices-this-month-kpi class="d-none d-xl-block"></invoices-this-month-kpi>
                    <invoices-this-month-kpi class="d-none d-xl-block"></invoices-this-month-kpi>

                    <b-card no-body>
                        <b-list-group flush>
                            <b-list-group-item href="#">
                                <i class="material-icons">insert_chart</i>
                                {{ $t('general.report', 2) }} {{ $route.meta.title }}
                            </b-list-group-item>
                            <b-list-group-item href="#" disabled>
                                <i class="material-icons">cloud_upload</i>
                                {{ $t('general.upload') }} {{ $route.meta.title }}
                            </b-list-group-item>
                            <b-list-group-item href="0">
                                <i class="material-icons md-light">add_box</i>
                                {{ $t('general.create') }} {{ $route.meta.title }}
                            </b-list-group-item>
                        </b-list-group>
                    </b-card>
                </b-card-group>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <div v-if="$route.name.includes('List')">
                    <crud :columns="columns" inline-template>
                        <b-card no-body>
                            <b-table hover responsive :items="items" :fields="columns" :current-page="current_page">

                                <template slot="code" slot-scope="data">
                                    <span v-if="data.item.is_accountable">{{ data.item.code }}</span>
                                    <b v-else>{{ data.item.code }}</b>
                                </template>

                                <template slot="name" slot-scope="data">
                                    <span v-if="data.item.is_accountable">{{ data.item.name }}</span>
                                    <b v-else>{{ data.item.name }}</b>
                                </template>

                                <template slot="actions" slot-scope="data">
                                    <table-actions :row="data.item" v-if="data.item.is_accountable && data.item.taxpayer_id != null"></table-actions>
                                </template>

                                <div slot="table-busy">
                                    <table-loading></table-loading>
                                </div>

                                <template slot="empty" slot-scope="scope">
                                    <table-empty></table-empty>
                                </template>
                            </b-table>
                            <!-- <b-pagination align="center" :total-rows="meta.total" :per-page="meta.per_page"  @change="onList()"></b-pagination> -->
                        </b-card>
                    </crud>
                </div>
                <router-view v-else></router-view>
            </b-col>
        </b-row>
    </div>
</template>

<script>
import crud from '../../components/crud.vue'
export default {
    components: { crud },
    data: () => ({

    }),
    computed: {
        columns()
        {
            return  [ {
                key: 'code',
                label: this.$i18n.t('commercial.code'),
                sortable: true
            },
            {
                key: 'name',
                label: this.$i18n.t('commercial.account'),
                sortable: true
            },
            {
                key: 'actions',
                label: '',
                sortable: false
            }];
        }
    }
}
</script>
