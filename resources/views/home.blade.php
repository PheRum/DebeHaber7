@extends('spark::layouts.app')

@section('content')

    <b-container class="bv-example-row">
        <b-row>
            <b-col>
                <b-card header="Taxpayers" header-tag="header">
                    @if(isset($taxPayerIntegrations))
                        @foreach ($taxPayerIntegrations->sortBy('taxpayer.name') as $integration)
                            <a href="{{ url('selectTaxPayer', $integration->taxpayer) }}">{{ $integration->taxPayer->name }}</a>
                        @endforeach
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

    <div class="container">

    </div>
@endsection
