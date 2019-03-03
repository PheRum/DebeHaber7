<template>
    <GChart type="PieChart" :data="chartData" :options="chartOptions" />
</template>
<script>
import crud from '../../components/crud.vue';

export default {
    props: ['type', 'startDate', 'endDate'],
    components: { 'crud': crud },
    data () {
        return {
            // Array will be automatically processed with visualization.arrayToDataTable function
            chartData: '',
            chartOptions: {
                chart: {
                    title: 'Breakdown',
                    subtitle: 'Breaks own the details based on usage',
                }
            }
        }
    },

    computed: {
        baseUrl() {
            var app = this;
            return '/api/' + app.$route.params.taxPayer + '/kpi/transactions/' + app.type + '/' + app.startDate + '/' + app.endDate;
        },
    },

    mounted() {
        var app = this;
      //do something after mounting vue instance
      // alert(app.baseUrl + '/transactions/' + this.type + '/' + this.startDate + '/' + this.endDate);
      crud.methods
      .onRead(app.baseUrl)
      .then(function (response) {
          app.chartData = response.data.data;
      });
    }
}
</script>
