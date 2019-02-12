<template>
    <div>
        <h2>Transaction Results</h2>
        <b-card no-body header="Transactions">
            <b-table hover></b-table>
        </b-card>
        <h2>Taxpayer Results</h2>
        <b-card no-body>
            <b-table hover></b-table>
        </b-card>
        <h4>
            <small class="text-uppercase">Results for:</small>
            Chart of Account
        </h4>
        <b-card no-body>
            <b-table hover></b-table>
        </b-card>
    </div>
</template>

<script>
export default {
    data: () => ({
        transactionList: [],
        transactionMeta: '',
        transactionSkip: 0,
        transactionCurrentPage: 0,

        taxPayerList: [],
        taxPayerMeta: '',
        taxPayerSkip: 0,
        taxPayerCurrentPage: 0,

        chartList: [],
        chartMeta: '',
        chartSkip: 0,
        chartCurrentPage: 0,
    }),
    methods: {
        search() {
            var app = this;

            axios.get('/api/?page=' + app.transactionCurrentPage)
            .then(({ data }) =>
            {
                app.transactionList = data.data;
                app.transactionMeta = data.meta;
                app.transactionSkip += app.pageSize;
            });

            axios.get('/api ?page=' + app.transactionCurrentPage)
            .then(({ data }) =>
            {
                app.taxPayerList = data.data;
                app.taxPayerMeta = data.meta;
                app.taxPayerSkip += app.pageSize;
            });

            axios.get('/api ?page=' + app.transactionCurrentPage)
            .then(({ data }) =>
            {
                app.chartList = data.data;
                app.chartMeta = data.meta;
                app.chartSkip += app.pageSize;
            });
        },
        transactionsByTaxPayer(taxPayerId) {
            var app = this;
            axios.get('/api ?page=' + app.transactionCurrentPage)
            .then(({ data }) =>
            {
                app.transactionList = data.data;
                app.transactionMeta = data.meta;
                app.transactionSkip += app.pageSize;
            });
        },

        transactionsByChart(chartId) {
            var app = this;
            axios.get('/api ?page=' + app.transactionCurrentPage)
            .then(({ data }) =>
            {
                app.transactionList = data.data;
                app.transactionMeta = data.meta;
                app.transactionSkip += app.pageSize;
            });
        }
    }
}
</script>
