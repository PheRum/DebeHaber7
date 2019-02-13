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

        onRead: async function($url) {
            var app = this;
            var resp;
            await  axios({
                method: 'get',
                url:  $url ,
                responseType: 'json',
            })
            .then(function (response) {
                resp = response.data;
                Toast.fire({ type: 'success', title: 'Data Loaded' })
            })
            .catch(function (error) {
                Toast.fire({ type: 'error', title: 'Unable to Access Data' })
            });

            return resp;
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
                app.data = response.data;
            })
            .catch(function (error) {
                Toast.fire({ type: 'error', title: 'Unable to Save Data' })
                console.log(app.data);
            });
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
