<template>
  <div>
    <b-row v-if="$route.name.includes('List')">
      <b-col>
        <b-card-group deck>
          <b-card bg-variant="dark" text-variant="white">
            <h4 class="upper-case">
              <img :src="$route.meta.img" alt class="ml-5 mr-5" width="26">
              {{ $t($route.meta.title) }}
            </h4>
            <p class="lead" v-if="$route.name.includes('List')">
              {{ $t($route.meta.description) }},
              <router-link to="{ name: $route.name, params: { id: 0}}">Create</router-link>
            </p>
          </b-card>

          <invoices-this-month-kpi class="d-none d-xl-block"></invoices-this-month-kpi>
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
              <b-table
                hover
                responsive
                :items="items"
                :fields="columns"
                :current-page="current_page"
              >
                <template slot="type" slot-scope="data">
                  <b-badge
                    v-if="data.item.type == 1"
                    variant="primary"
                  >{{ spark.enumChartType[data.item.type] }}</b-badge>
                  <b-badge
                    v-else-if="data.item.type == 2"
                    variant="info"
                  >{{ spark.enumChartType[data.item.type] }}</b-badge>
                  <b-badge
                    v-else-if="data.item.type == 3"
                    variant="warning"
                  >{{ spark.enumChartType[data.item.type] }}</b-badge>
                  <b-badge
                    v-else-if="data.item.type == 4"
                    variant="success"
                  >{{ spark.enumChartType[data.item.type] }}</b-badge>
                  <b-badge
                    v-else-if="data.item.type == 5"
                    variant="danger"
                  >{{ spark.enumChartType[data.item.type] }}</b-badge>

                  <b-badge
                    v-if="data.item.type == 1 && data.item.sub_type != null"
                    pill
                  >{{ spark.enumAsset[data.item.sub_type] }}</b-badge>
                  <b-badge
                    v-else-if="data.item.type == 2 && data.item.sub_type != null"
                    pill
                  >{{ spark.enumLiability[data.item.sub_type] }}</b-badge>
                  <b-badge
                    v-else-if="data.item.type == 3 && data.item.sub_type != null"
                    pill
                  >{{ spark.enumEquity[data.item.sub_type] }}</b-badge>
                  <b-badge
                    v-else-if="data.item.type == 4 && data.item.sub_type != null"
                    pill
                  >{{ spark.enumRevenue[data.item.sub_type] }}</b-badge>
                  <b-badge
                    v-else-if="data.item.type == 5 && data.item.sub_type != null"
                    pill
                  >{{ spark.enumExpense[data.item.sub_type] }}</b-badge>
                </template>

                <template slot="code" slot-scope="data">
                  <span v-if="data.item.is_accountable">{{ data.item.code }}</span>
                  <b v-else>{{ data.item.code }}</b>
                </template>

                <template slot="name" slot-scope="data">
                  <span v-if="data.item.is_accountable">{{ data.item.name }}</span>
                  <b v-else>{{ data.item.name }}</b>
                </template>

                <template slot="actions" slot-scope="data">
                  <table-actions :row="data.item" v-if="data.item.taxpayer_id != null"></table-actions>
                </template>

                <div slot="table-busy">
                  <table-loading></table-loading>
                </div>

                <template slot="empty">
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
import crud from "../../components/crud.vue";
export default {
  components: { crud },
  data: () => ({}),
  computed: {
    columns() {
      return [
        {
          key: "code",
          label: this.$i18n.t("commercial.code"),
          sortable: true
        },
        {
          key: "name",
          label: this.$i18n.t("commercial.account"),
          sortable: true
        },
        {
          key: "type",
          label: ""
        },
        {
          key: "actions",
          label: ""
        }
      ];
    }
  },
  methods: {
    typeVariant(chartType) {
      if (chartType == 1) {
        return "light";
      } else if (chartType == 2) {
        return "dark";
      } else if (chartType == 3) {
        return "warning";
      } else if (chartType == 4) {
        return "success";
      } else if (chartType == 5) {
        return "danger";
      }
    }
  }
};
</script>
