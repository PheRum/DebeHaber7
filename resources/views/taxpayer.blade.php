@extends('spark::layouts.app')

@section('content')
    <form  action="{{ route('postTaxPayer') }}" method="POST">
        {{ csrf_field() }}
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

                                <search-taxpayer :value="{{$taxPayer}}"></search-taxpayer>

                                <b-form-group  label="Tax Identification">
                                    <b-form-input name="taxid" type="text" value="{{$taxPayer->taxid}}"/>
                                    </b-form-group>

                                    <b-form-group label="Taxpayer's Name">
                                        <b-form-input name="name" type="text" value="{{$taxPayer->name}}"></b-form-input>
                                    </b-form-group>

                                    <b-form-group label="Alias">
                                        <b-form-input name="alias" type="text" value="{{$taxPayer->alias}}"/>
                                        </b-form-group>

                                        <hr>

                                        <b-form-group label="Telephone">
                                            <b-form-input name="telephone" type="text" value="{{$taxPayer->telephone}}"/>
                                            </b-form-group>

                                            <b-form-group label="Address">
                                                <b-form-input name="address" type="text" value="{{$taxPayer->address}}"/>
                                                </b-form-group>

                                                <b-form-group label="Email">
                                                    <b-form-input name="email" type="text" value="{{$taxPayer->email}}"/>
                                                    </b-form-group>

                                                </b-tab>
                                                <b-tab>
                                                    <template slot="title">
                                                        <i class="material-icons">settings</i>
                                                        Settings
                                                    </template>
                                                    <b-form-group label="company">
                                                        <input type="checkbox" checked="{{$taxPayer->is_company}}">
                                                    </b-form-group>

                                                    <b-form-group label="Inventory">
                                                            <input type="checkbox" checked="{{$taxPayer->show_inventory}}">
                                                    </b-form-group>
                                                    <b-form-group label="Production">
                                                            <input type="checkbox" checked="{{$taxPayer->show_production}}">
                                                    </b-form-group>
                                                    <b-form-group label="Fixed Asset">
                                                        <b-form-checkbox checked="{{$taxPayer->show_fixedasset}}"/>
                                                    </b-form-group>

                                                    </b-form-group>
                                                </b-tab>
                                                <b-tab>
                                                    <template slot="title">
                                                        <i class="material-icons">supervised_user_circle</i>
                                                        Integrations
                                                    </template>
                                            
                                                </b-tab>
                                            </b-tabs>
                                        </b-card>
                                    </b-col>
                                </b-row>
                                <button type="submit">Save</button>
                            </b-container>
                        </form>
                    @endsection
