@extends('spark::layouts.app')

@section('content')
    <b-container>
        <b-row>
            <b-col cols="4">
                <b-card no-body img-src="https://placekitten.com/380/200" img-alt="Image" img-top >

                    <h4 slot="header">{{ $taxPayer->alias }}</h4>

                    <b-card-body>
                        <b-card-title>{{ $taxPayer->name }}</b-card-title>
                        <b-card-sub-title class="mb-2">{{ $taxPayer->taxid }}</b-card-sub-title>
                    </b-card-body>

                    <b-list-group flush>
                        <b-list-group-item>
                            [Blank]
                            <b-badge pill variant="success">Personal</b-badge>
                        </b-list-group-item>
                        <b-list-group-item>
                            [Blank]
                            <b-badge pill variant="danger">Accountant</b-badge>
                        </b-list-group-item>
                        <b-list-group-item>
                            [Blank]
                            <b-badge pill variant="info">Auditor</b-badge>
                        </b-list-group-item>
                    </b-list-group>
                </b-card>
            </b-col>
            <b-col cols="8">
                <b-card no-body>
                    <b-tabs pills card v-model="tabIndex">
                        <b-tab active>
                            <template slot="title">
                                <i class="material-icons">info</i>
                                Information
                            </template>

                            <search-taxpayer></search-taxpayer>

                            <b-form-group label="Tax Identification">
                                <b-form-input type="text" />
                            </b-form-group>

                            <b-form-group label="Taxpayer's Name">
                                <b-form-input type="text" />
                            </b-form-group>

                            <b-form-group label="Alias">
                                <b-form-input type="text" />
                            </b-form-group>

                            <hr>

                            <b-form-group label="Telephone">
                                <b-form-input type="text" />
                            </b-form-group>

                            <b-form-group label="Address">
                                <b-form-input type="text" />
                            </b-form-group>

                            <b-form-group label="Email">
                                <b-form-input type="text" />
                            </b-form-group>

                        </b-tab>
                        <b-tab>
                            <template slot="title">
                                <i class="material-icons">settings</i>
                                Settings
                            </template>
                            Tab Contents 2
                        </b-tab>
                        <b-tab>
                            <template slot="title">
                                <i class="material-icons">supervised_user_circle</i>
                                Integrations
                            </template>
                            Tab Contents 2
                        </b-tab>
                    </b-tabs>
                </b-card>
            </b-col>
        </b-row>

    </b-container>
@endsection
