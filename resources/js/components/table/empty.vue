<template>
  <div>
      <h4>You're running on empty!</h4>
      <p class="lead">How about <router-link :to="{ name: formURL, params: { id: 0}}">creating</router-link> some data</p>
      <b-img thumbnail fluid width="256" src="/img/apps/no-data.svg" alt="Thumbnail" />
  </div>
</template>
<script>
export default {
  data: () => ({

  })
}
</script>
