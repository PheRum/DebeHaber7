<template>
    <GChart type="PieChart" :data="chartData" :options="chartOptions" />
</template>
<script>
export default {
    props: ['url']
    data () {
        return {
            // Array will be automatically processed with visualization.arrayToDataTable function
            chartData: [ ],
            chartOptions: {
                chart: {
                    title: 'Breakdown',
                    subtitle: 'Breaks own the details based on usage',
                }
            }
        }
    },
    mounted() {
      //do something after mounting vue instance

    }
}
</script>
