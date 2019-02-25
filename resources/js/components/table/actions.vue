<template>
    <div>
        <b-button-group size="sm" class="show-when-hovered">
            <b-button :to="{ name: formURL, params: { id: row[property_id] }}">
                <i class="material-icons md-18">edit</i>
            </b-button>
            <b-button @click="onDelete(row)">
                <i class="material-icons md-19">delete_outline</i>
            </b-button>
        </b-button-group>
    </div>
</template>
<script>
export default {
  props: ['row', 'property_id'],
  computed: {
        formURL: function () {
            return this.$route.name.replace('List', 'Form');
        }
    },
    data: () => ({

    })
}
</script>
