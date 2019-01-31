<template>
    <b-button-group size="sm">
        <b-button variant="link" v-if="list != ''">
            <router-link :to="{ name: list}">
                <i class="material-icons">remove_red_eye</i>
            </router-link>
        </b-button>
        
        <b-button variant="link" v-if="form != ''">
            <router-link :to="{ name: form, param: { id: 0}}">
                <i class="material-icons">library_add</i>
            </router-link>
        </b-button>

        <b-button variant="link" v-if="report != ''" :href="report">
            <i class="material-icons">insert_chart</i>
        </b-button>
    </b-button-group>
</template>

<script>
export default {
    props: ['list', 'form', 'report'],
    data: () => ({

    })
}
</script>
