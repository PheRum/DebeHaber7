@extends('spark::layouts.app')

@section('content')
    <b-container>
        <b-row>
            <b-col>
                <b-card header="Taxpayers" header-tag="header">
                    @if(isset($taxPayerIntegrations))
                        <ul>
                            @foreach ($taxPayerIntegrations->sortBy('taxpayer.name') as $integration)
                                <li>
                                    <a href="{{ url('selectTaxPayer', $integration->taxpayer) }}">{{ $integration->taxPayer->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        Nothing Here, but still on Home.Blade
                    @endif
                </b-card>
            </b-col>

            <b-col>
                <b-card header="Members" header-tag="header">
                    <div v-for="user in currentTeam.users" :key="user.key">
                        <img :src="user.photo_url" alt="">
                        <span>@{{ user.name }}</span>
                        <span>@{{ user.email }}</span>
                        <span>@{{ user.pivot.role }}</span>
                    </div>
                </b-card>
            </b-col>
        </b-row>

        <b-row>
            <b-col>
                <b-card header="Activities" header-tag="header">

                </b-card>
            </b-col>
        </b-row>
    </b-container>
@endsection
