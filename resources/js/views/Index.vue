
<template>
  <div>
    <h1>{{ $t('welcomeMsg') }}</h1>
      <router-view></router-view>
  </div>
</template>

<script>
import i18n from '../plugins/i18n';
export default
{
    data() {
        return {
            profile: [],
        };
    },

    mounted: function mounted()
    {
        var app = this;
         i18n.locale = this.$parent.spark.language;
        //make code to navigate to profile if
        //app.onLoad();
    }
}
</script>
