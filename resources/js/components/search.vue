<template>
    <b-form-input v-model="query" @change="search" type="text" class="mb-10" placeholder="Search"></b-form-input>
</template>

<script>
export default {
    data: () => ({
        query: ''
    }),
    
    methods: {
        search() {
            this.$router.push({ name: 'searchResult', query: { q: this.query }})
        }
    }
}
</script>
