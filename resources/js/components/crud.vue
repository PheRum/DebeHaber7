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
            .catch(function (error) {
                console.log(error.response);
                return error.response
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
                console.log(error.response);
                return error.response
            });
        },

        onDelete($url, $dataId) {
            return axios({
                method: 'delete',
                url: $url + '/' + $dataId,
                responseType: 'json',
            })
            .then(function (response) {
                return response;
            })
            .catch(function (error) {
                console.log(error.response);
                return error.response
            });
        }
    }
}
</script>
