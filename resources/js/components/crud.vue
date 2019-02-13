<script>
export default {
  data() {
    return {
      data:[]
    };
  },
  methods:
  {
    onCreate() {
      //unused
    },

    onUpdate($url, $data) {
      var app = this;
      axios({
        method: 'post',
        url: '/api/' + app.$route.params.taxPayer + '/' + app.$route.params.cycle + '/' + $url ,
        responseType: 'json',
        data: $data
      })
      .then(function (response)
      {
        app.data= response.data;

      })
      .catch(function (error)
      { console.log(app.data); });
    },

    onRead: async function($url) {
      var app = this;
  var resp;
    await  axios({
        method: 'get',
        url:  $url ,
        responseType: 'json',
      })
      .then(function (response)
      {
        resp = response.data;
      })
      .catch(function (error)
      { alert('No Data'); });

      return resp;
    },

    onDelete($url, $dataId) {
      var app = this;
      axios({
        method: 'delete',
        url: '/api/' + app.$route.params.taxPayer + '/' + app.$route.params.cycle + '/' + $url + '/' + $dataId,
        responseType: 'json',
      })
      .then(function (response)
      {
        return response;
      })
      .catch(function (error)
      { });
    }
  }
}
</script>
