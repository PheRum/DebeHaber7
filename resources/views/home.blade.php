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
                    @endif
                </b-card>
            </b-col>

            <b-col>
                <b-card header="Invite" header-tag="header">

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
