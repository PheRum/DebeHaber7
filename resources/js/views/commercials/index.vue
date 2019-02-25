<template>
    <b-container v-if="$route.name == 'commercialMenu'">
        <vue-topprogress ref="topProgress"></vue-topprogress>

        <b-row>
            <b-col>
                <b-card no-body header="Expenses" header-tag="header">
                    <b-list-group flush>
                        <b-list-group-item href="#">
                            <b-row>
                                <b-col>
                                    <img src="/img/icons/purchase.svg" width="32" alt="">
                                    Purchase Book
                                </b-col>
                                <b-col>

                                </b-col>
                            </b-row>
                            <div>

                            </div>
                        </b-list-group-item>
                        <b-list-group-item href="#">
                            <div class="">
                                <img src="/img/icons/credit-note.svg" width="32" alt="">
                                Debit Notes
                            </div>
                        </b-list-group-item>
                        <b-list-group-item href="#">
                            <div>
                                <img src="/img/icons/account-payable.svg" width="32" alt="">
                                Accounts Payables
                            </div>
                        </b-list-group-item>
                    </b-list-group>
                </b-card>
            </b-col>
            <b-col>
                <b-card no-body header="Revenue" header-tag="header">
                    <b-list-group flush>
                        <b-list-group-item href="#">
                            <div>
                                <img src="/img/icons/sales.svg" width="32" alt="">
                                Sales Book
                            </div>
                        </b-list-group-item>
                        <b-list-group-item href="#">
                            <router-link :to="{ name: 'creditList'}">
                                <img src="/img/icons/credit-note.svg" width="32" alt="">
                                Credit Notes
                            </router-link>
                        </b-list-group-item>
                        <b-list-group-item href="#">
                            <div>
                                <img src="/img/icons/account-receivable.svg" width="32" alt="">
                                Accounts Receivables
                            </div>
                        </b-list-group-item>
                    </b-list-group>
                </b-card>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <b-card title="Configuration" sub-title="Configuration">
                    <b-list-group>
                        <b-list-group-item href="#">
                            <div>
                                <img src="/img/icons/sales.svg" width="32" alt="">
                                Sales Book
                            </div>
                        </b-list-group-item>
                        <b-list-group-item href="#">
                            <router-link :to="{ name: 'creditList'}">
                                <img src="/img/icons/credit-note.svg" width="32" alt="">
                                Credit Notes
                            </router-link>
                        </b-list-group-item>
                        <b-list-group-item href="#">
                            <div>
                                <img src="/img/icons/account-receivable.svg" width="32" alt="">
                                Accounts Receivables
                            </div>
                        </b-list-group-item>
                    </b-list-group>
                </b-card>
            </b-col>
        </b-row>
    </b-container>

    <router-view v-else></router-view>
</template>
<script>
export default {
    name: "",
    data: () => ({

    })
}
</script>
