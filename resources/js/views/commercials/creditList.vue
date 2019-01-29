<template>
    <b-container>
        <b-row>
            <b-col>
                <b-card-group deck>
                    <b-card>
                        <button type="button" name="button">Click Me</button>
                    </b-card>

                    <b-card>

                    </b-card>
                </b-card-group>
            </b-col>
        </b-row>
        <b-row>
            <b-col v-if="$route.name == 'creditList'">
                <b-card title="Credit List" sub-title="All your credit data will be shown here.">
                    <table>

                    </table>
                </b-card>
            </b-col>

            <router-view v-else></router-view>
        </b-row>

    </b-container>
</template>

<script>
export default {
    name: "",
    data: () => ({

    })
}
</script>
