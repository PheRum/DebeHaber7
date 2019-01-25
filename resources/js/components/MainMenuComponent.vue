<template>
    <div>

    </div>
</template>

<script>
export default {
    props: ['taxpayer','cycle'],
    data: () => ({
        isLoggedIn: false,
        search:''

    }),

    computed:
    {
        orgProfiles()
        {
            var app = this;
            return this.profiles.filter(profile => { return profile.following != null });
        },

        allowAccess()
        {
            for (var i = 0; i < this.profiles.length; i++)
            {
                if (this.profiles[i].following != null)
                {
                    if( this.profiles[i].following.slug == this.$route.params.profile)
                    {
                        return true
                    }
                }
                else
                {
                    if (this.profiles[i].slug == this.$route.params.profile)
                    {
                        return true
                    }
                }
            }

            return false
        },

        isOrganization()
        {
            for (var i = 0; i < this.profiles.length; i++)
            {
                if (this.profiles[i].following != null)
                {
                    if( this.profiles[i].following.slug == this.$route.params.profile)
                    {
                        return true
                    }
                }
            }

            return false
        },

        isAdmin()
        {
            for (var i = 0; i < this.profiles.length; i++)
            {
                if (this.profiles[i].following != null)
                {
                    if( this.profiles[i].following.slug == this.$route.params.profile)
                    {
                        if (this.profiles[i].role <= 1) {
                            return true
                        }
                    }
                }
            }

            return false
        },

        isManager()
        {
            for (var i = 0; i < this.profiles.length; i++)
            {
                if (this.profiles[i].following != null)
                {
                    if( this.profiles[i].following.slug == this.$route.params.profile)
                    {
                        if (this.profiles[i].role <= 2) {
                            return true
                        }
                    }
                }
            }

            return false
        },

        isEmployee()
        {
            for (var i = 0; i < this.profiles.length; i++)
            {
                if (this.profiles[i].following != null)
                {
                    if( this.profiles[i].following.slug == this.$route.params.profile)
                    {
                        if (this.profiles[i].role <= 3) {
                            return true
                        }
                    }
                }
            }

            return false
        },

        isMember()
        {
            for (var i = 0; i < this.profiles.length; i++)
            {
                if (this.profiles[i].following != null)
                {
                    if( this.profiles[i].following.slug == this.$route.params.profile)
                    {
                        if (this.profiles[i].role <= 4) {
                            return true
                        }
                    }
                }
            }

            return false
        },
    },

    methods: {
        logout()
        {
            axios.post('/logout').then(() => {
                location.reload(true);
            });
        },

        onSave($data)
        {
            var app = this;
            axios.post('/store-profiles' , $data)
            .then((data) =>
            {
                this.$router.go(this.$router.currentRoute)
            })
            .catch(ex => {
                console.log(ex.response);
                this.$toast.open({
                    duration: 5000,
                    message: 'Error trying to save record',
                    type: 'is-danger'
                })
            });
        }

    },

    mounted() {
        var app = this;

        //hides certain buttons if user is not logged in
        app.isLoggedIn = this.taxpayer != "" ? true : false

    }
}
</script>
