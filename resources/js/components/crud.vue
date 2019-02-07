<script>
export default {
    data: () => ({ }),
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
                return response;
            })
            .catch(function (error)
            { });
        },

        onRead($url) {
            var app = this;
            axios({
                method: 'get',
                url: '/api/' + app.$route.params.taxPayer + '/' + app.$route.params.cycle + '/' + $url ,
                responseType: 'json',
            })
            .then(function (response)
            {
                return response;
            })
            .catch(function (error)
            { });
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
