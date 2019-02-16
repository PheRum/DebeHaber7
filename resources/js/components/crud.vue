<script>
export default {
    data() {
        return {

        };
    },
    methods:
    {
        onCreate() {
            //unused
        },

        onRead: async function($url) {
            return axios({
                method: 'get',
                url: $url,
                responseType: 'json',
            })
            .then(function (response) {
                return response.data.data;
            });
        },

        onUpdate($url, $data) {
            return axios({
                method: 'post',
                url: $url,
                responseType: 'json',
                data: $data
            })
            .then(function (response) {
                return response
            })
            .catch(function (error) {
                return error
            });
        },

        onDelete($url, $dataId) {
            var app = this;
            axios({
                method: 'delete',
                url: $url + '/' + $dataId,
                responseType: 'json',
            })
            .then(function (response) {
                return response;
            })
            .catch(function (error) {

            });
        }
    }
}
</script>
