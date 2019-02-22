<template>
    <vue-csv-import url="/url/to/post" :map-fields="fields"></vue-csv-import>
</template>

<script>
import VueCsvImport from 'vue-csv-import';
export default {
    name: 'App',
    components: {
        VueCsvImport,
    },
    data() {
        return {
            fields: {
                customer_name: 'Customer Name',
                customer_taxid: 'Customer TaxID',
                number: 'Invoice Number'
            }
            // columns: [
            //     { name: 'Student login', value: 'login' },
            //     { name: 'Student firstname', value: 'firstname' },
            //     { name: 'Student lastname', value: 'lastname' },
            //     { name: 'Other', value: 'other', isOptional: true },
            // ],
            // results: null,
            // help: 'Necessary columns are: login, firstname and lastname',
        };
    },
    methods: {
        onValidate(results) {
            this.results = results;
        },
    },
};
</script>
