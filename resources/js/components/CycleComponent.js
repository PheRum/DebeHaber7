Vue.component('model',
{
    props: ['taxpayer','cycle'],
    data() {
        return {
            showList : true,
            cycle_id : 0,
            cycles : [],
            showCycle : 0
        }
    },

    methods:
    {
        changeCycle(cycleID)
        {
            var app = this;
            window.location.href = '/' + app.taxpayer + '/' + cycleID + '/stats/';
        },

        init: function (data)
        {
            var app = this;
            axios.get('/api/' + app.taxpayer + '/get_cycle')
            .then(({ data }) =>
            {
                app.cycles = data;
            });
        }
    },

    mounted: function mounted()
    {
        var app = this;
        app.init();
        app.cycle_id = app.cycle;
    }
});
