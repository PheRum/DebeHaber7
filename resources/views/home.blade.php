@extends('spark::layouts.app')

@section('content')
    <b-container>
        <b-row>
            <b-col>
                @if(isset($taxPayerIntegrations))
                    <b-card no-body>
                        <em slot="header">
                            Taxpayers for the Team
                            <div class="float-right">
                                <b-form-input v-model="text" type="text" placeholder="Enter your name">
                                </b-form-input>
                            </div>
                        </em>
                        <b-list-group flush>
                            @foreach ($taxPayerIntegrations->sortBy('taxpayer.name') as $integration)
                                <b-list-group-item href="{{ url('selectTaxPayer', $integration->taxpayer) }}">
                                    @if ($integration->taxpayer->setting->is_company == 1)
                                        <i class="material-icons">work_outline</i>
                                    @else
                                        <i class="material-icons">person_outline</i>
                                    @endif
                                    {{ $integration->taxPayer->name }} <span class="text-muted"> | {{ $integration->taxPayer->taxid }}</span>
                                </b-list-group-item>
                            @endforeach
                        </b-list-group>
                        <em slot="footer">{{ $taxPayerIntegrations->links() }}</em>
                    </b-card>
                @else
                    Nothing Here, but still on Home.Blade
                @endif
            </b-col>

            <b-col>
                <b-card header="Members" header-tag="header">
                    <b-row v-for="user in currentTeam.users" :key="user.key" align-v="center">
                        <b-col cols="2">
                            <b-img :src="user.photo_url" fluid rounded="circle" alt="Circle image" />
                        </b-col>
                        <b-col cols="5">
                            <span>@{{ user.name }}</span>
                            <b-badge variant="primary" class="upper-case">
                                @{{ user.pivot.role }}
                            </b-badge>
                        </b-col>
                        <b-col cols="5">
                            <a>@{{ user.email }}</a>
                        </b-col>
                    </b-row>
                </b-card>

                @include('spark::settings.teams.send-invitation')
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
