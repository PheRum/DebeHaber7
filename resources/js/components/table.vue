<template>
    <b-card no-body>
        <div v-if="lists.length > 0">
            <b-table hover :items="lists" :fields="columns">
                <template slot="action" slot-scope="data">
                    {{ data.id }} years old
                </template>
            </b-table>
            <b-pagination size="md" align="center" :total-rows="100" v-model="currentPage" :per-page="10"></b-pagination>
        </div>
        <div v-else>
            Bottom of the Barrel
        </div>
    </b-card>
</template>

<script>
export default {
    props: ['columns'],
    data: () => ({
        lists: [
            { id: 1, number: 1123 },
            { id: 2, number: 2345 },
            { id: 3, number: 3456 }
        ],
    }),
}
</script>
