<template>
    <div>
        <b-button-group size="sm" class="show-when-hovered">
            <b-button :to="{ name: formURL, params: { id: data.item.id }}">
                <i class="material-icons md-18">edit</i>
            </b-button>
            <b-button @click="onDelete(data.item)"><i class="material-icons md-19">delete_outline</i></b-button>
        </b-button-group>
    </div>
</template>
<script>
export default {
    data: () => ({

    })
}
</script>
