<template>
    <section>
        <b-card bg-variant="primary"
                text-variant="white"
                header="Primary"
                class="text-center">

                <a :href="$router.resolve({ name:'index', params:{profile:org.following.slug}}).href">Link Name</a>

                <router-link :to="{ name: 'routeName', params: { property: value}}">
                    Icon
                </router-link>
        </b-card>
    </section>
</template>
<script>
export default {
    name: "",
    data: () => ({

    })
}
</script>
