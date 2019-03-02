<template>
  <div>
    <h4>You're running on empty!</h4>
    <div v-show="isCreated!==true">
      <p class="lead">How about <router-link :to="{ name: formURL, params: { id: 0}}">creating</router-link> some data</p>
    </div>
    <b-img thumbnail fluid width="256" src="/img/apps/no-data.svg" alt="Thumbnail" />
  </div>
</template>
<script>
export default {
  props: ['isCreated'],
  data: () => ({

  }),
  computed: {
    formURL: function () {
      return this.$route.name.replace('List', 'Form');
    }
  },
}
</script>
