<template>
    <div>
        <b-row v-if="$route.name.includes('List')">
            <b-col>
                <b-card-group deck>
                    <b-card no-body class="overflow-hidden">
                        <b-row>
                            <b-col cols="4">
                                <b-card-img :src="$route.meta.img" />
                            </b-col>
                            <b-col cols="8">
                                <b-card-body :title="$t($route.meta.title)">
                                    <b-card-text>
                                        <p class="lead" v-if="$route.name.includes('List')">
                                            {{ $t($route.meta.description) }}, <router-link :to="{ name: formURL, params: { id: 0}}">Create</router-link>
                                        </p>
                                    </b-card-text>
                                </b-card-body>
                            </b-col>
                        </b-row>
                    </b-card>

                    <invoices-this-month-kpi class="d-none d-xl-block"></invoices-this-month-kpi>

                    <b-card no-body>
                        <b-list-group flush>
                            <b-list-group-item href="#">
                                <i class="material-icons">help</i>
                                {{ $t('general.manual') }}
                            </b-list-group-item>
                            <b-list-group-item :to="{ name: uploadURL }">
                                <i class="material-icons">cloud_upload</i>
                                {{ $t('general.uploadFromExcel') }}
                            </b-list-group-item>
                            <b-list-group-item :to="{ name: formURL, params: { id: 0}}">
                                <i class="material-icons md-light">add_box</i>
                                {{ $t('general.createNewRecord') }}
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

                                <template slot="date" slot-scope="data">
                                    {{ new Date(data.item.date).toLocaleDateString() }}
                                </template>

                                <template slot="total" slot-scope="data">
                                    <span class="float-right">
                                        {{ new Number(sum(data.item.details, 'value')).toLocaleString() }}
                                        <small class="text-success text-uppercase">{{ data.item.currency }}</small>
                                    </span>
                                </template>

                                <template slot="actions" slot-scope="data">
                                    <table-actions :row="data.item"></table-actions>
                                </template>

                                <div slot="table-busy">
                                    <table-loading></table-loading>
                                </div>

                                <template slot="empty" slot-scope="scope">
                                    <table-empty></table-empty>
                                </template>
                            </b-table>
                              <b-pagination align="center" :total-rows="meta.total" :per-page="meta.per_page"  @change="onList()"></b-pagination>
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
        formURL: function () {
            return this.$route.name.replace('List', 'Form');
        },
        columns()
        {

            return  [ {
                key: 'date',
                sortable: true
            },
            {
                key: 'partner_name',
                label: this.$i18n.t('commercial.supplier'),
                sortable: true
            },
            {
                key: 'number',
                label: this.$i18n.t('commercial.number'),
                sortable: true
            },
            {
                key: 'total',
                label: this.$i18n.t('commercial.total'),
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
