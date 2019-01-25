
Vue.component('list',
{
    props: ['taxpayer', 'cycle', 'base-url'],
    data() {
        return {
            list : [],
            selectedList : [],
            total : 0,
            skip : 0,
            pageSize : 100,
            search : '',
        }
    },

    methods:
    {
        list()
        {
            var app = this;

            axios.get('/api/' + this.taxpayer + '/' + this.cycle + '/' + this.baseurl + '/' + app.skip + '',
            {
                params:
                {
                    page: app.list.length / 100 + 1,
                },
            })
            .then(({ data }) =>
            {
                if (data.length > 0)
                {
                    for (let i = 0; i < data.length; i++)
                    {
                        app.list.push(data[i]);
                    }

                    app.skip += app.pageSize;
                    $state.loaded();
                }
            });
        },

        onEdit: function(data)
        {
            var app = this;

            $.ajax({
                url: '/api/' + app.taxpayer + '/' + app.cycle + '/' +  app.baseurl + '/by-id/' + data,
                headers: {'X-CSRF-TOKEN': CSRF_TOKEN},
                type: 'get',
                dataType: 'json',
                async: true,
                success: function(data)
                {
                    console.log('/api/' + app.taxpayer + '/' + app.cycle + '/' +  app.baseurl + '/by-id/' + data);
                    app.$children[0].onEdit(data[0]);

                },
                error: function(xhr, status, error)
                {
                    console.log(status);
                }
            });
        },

        onDelete: function(data)
        {
            //SweetAlert message and confirmation.

            var app = this;
            $.ajax({
                url: '/taxpayer/' + this.taxpayer + '/' + this.cycle + '/' + this.baseurl + '/' + data.ID,
                headers: {'X-CSRF-TOKEN': CSRF_TOKEN},
                type: 'delete',
                dataType: 'json',
                async: true,
                success: function(responsedata)
                {
                    for (var i = 0; i < app.list.length; i++) {
                        if (data.ID == app.list[i].ID)
                        {
                            app.list.splice(i,1);
                        }
                    }
                    //console.log(data);
                },
                error: function(xhr, status, error)
                {
                    console.log(xhr.responseText);
                }
            });
        },

        onAnull: function(data)
        {
            //SweetAlert message and confirmation.
            var app = this;
            $.ajax({
                url: '/taxpayer/' + this.taxpayer + '/' + this.cycle + '/' +  this.baseurl + '/anull/' + data.ID,
                headers: {'X-CSRF-TOKEN': CSRF_TOKEN},
                type: 'get',
                dataType: 'json',
                async: true,
                success: function(responsedata)
                {
                    data.Value=0;
                },
                error: function(xhr, status, error)
                {
                    console.log(xhr.responseText);
                }
            });
        },

        //onApprove??
    },
    mounted: function mounted()
    {

    }
});
