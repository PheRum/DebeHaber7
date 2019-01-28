<template>
    <b-container>
        <b-row>
            <b-col>
                <b-card :title="$parent.$route.meta.title" :sub-title="$parent.$route.meta.description">
                    <b-container>
                        <b-row>
                            <hr>
                            <b-col>
                                <b-form-group label="Invoice Date">
                                    <b-input type="date" required placeholder="Missing Information"/>
                                </b-form-group>
                                <b-form-group label="Customer">
                                    <b-input type="text" placeholder="Search for Customer"/>
                                </b-form-group>
                            </b-col>
                            <b-col>
                                <b-form-group label="Document">
                                    <b-form-select placeholder="Documents can simplfy manually loading data"/>
                                </b-form-group>
                                <b-form-group label="Invoice Number">
                                    <b-input type="text" placeholder="Missing Information"/>
                                </b-form-group>
                            </div>
                            <b-form-group label="Invoice Code">
                                <b-input-group>
                                    <b-input type="text" placeholder="Invoice Code"/>
                                    <b-input-group-append>
                                        <b-input type="date" placeholder="Code Expiry Date"/>
                                    </b-input-group-append>
                                </b-input-group>
                            </b-form-group>
                        </b-col>
                    </b-row>
                </b-container>
            </b-card>
        </b-col>
    </b-row>
    <b-row>
        <b-col>
            <b-card header="Details" header-tag="header">

            </b-card>
        </b-col>
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
