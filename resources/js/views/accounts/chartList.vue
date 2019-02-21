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
          <table-template :columns="columns"></table-template>
        </div>
        <router-view v-else></router-view>
      </b-col>
    </b-row>
  </div>
</template>

<script>
import crud from '../../components/crud.vue'
export default {
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
        label: this.$i18n.t('commercial.name'),
        sortable: true
      },
      {
        key: 'action',
        label: '',
        sortable: false
      }];
    }
  }
}
</script>
