<script>
export default {
    props: ['columns'],
    data: () => ({
        skip: 1,
        items: [],
        meta: [],
        loading: false,
        lastDeletedItem: [],
    }),
    computed: {
        formURL: function () {
            return this.$route.name.replace('List', 'Form');
        },
        viewURL: function () {
            return this.$route.name.replace('List', 'View');
        },
    },
    methods:
    {
        onList() {
            var app = this;
            var page = 1;

            //Loading indicators
            // this.$refs.topProgress.start();
            app.loading = true;

            axios.get('/api' + this.$route.path + '?page=' + page )
            .then(({ data }) => {

                app.items = data.data;
                app.meta = data.meta;
                app.skip += app.pageSize;
                //finishes the top progress bar
            }).catch(function (error) {
                // this.$refs.topProgress.fail();
                app.$snack.danger({ text: error.message });
            });

            app.loading = false;
            // this.$refs.topProgress.done()
        },

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
        },

        onDestroy(item) {
            var app = this;
            app.onDelete('/api' + app.$route.path, item.uuid)
            .then(function (response) {
                app.$snack.success({
                    text: app.$i18n.t('general.rowDeleted'),
                    button: app.$i18n.t('general.undo'),
                    action: app.undoDeletedRow
                });
                app.lastDeletedItem = item;
                app.items.splice(app.items.indexOf(item), 1);
            }).catch(function (error) {
                app.$snack.danger({ text: error.message });
            });
        },

        sum(details, id_prop) {
            return details.reduce(function(sum, row) {
                return sum + new Number(row[id_prop]);
            }, 0);
        },
    },
    mounted() {
        var app = this;
        app.onList(app.$route.path);
    }
}
</script>
