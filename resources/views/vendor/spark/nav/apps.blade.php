<b-nav vertical class="mb-25">
    <h3 class="nav-heading sub">
        @{{ $t('general.taxPayer') }}
        <b-link href="/home" v-b-tooltip.hover title="Change Taxpayer" class="float-right">
            <i class="material-icons md-14 float-right"> sync </i>
            <small>@{{ $t('general.change') }}</small>
        </b-link>
    </h3>

    <b-button variant="light" class="mb-10" v-b-toggle.collapse-taxpayer>
        <i class="material-icons float-left">expand_more</i>
        <span class="nav-heading"> @{{ spark.taxPayerData.alias }} </span>
        <b-badge variant="primary">
            {{-- {{ spark.taxPayerData->where('id', request()->route('cycle'))->first()->year }} --}}
        </b-badge>
        {{-- @if ($cycleData->where('id', request()->route('cycle'))->first()->year == \Carbon\Carbon::now()->year)
            <b-badge variant="primary">
                {{ $cycleData->where('id', request()->route('cycle'))->first()->year }}
            </b-badge>
        @else
            <b-badge variant="danger">
                {{ $cycleData->where('id', request()->route('cycle'))->first()->year }}
            </b-badge>
        @endif --}}
    </b-button>

    <b-collapse id="collapse-taxpayer" accordion="sub-menu">
        <b-nav-item class="sub-menu" :to="{ name: 'taxPayer'}">
            <i class="material-icons md-18 ml-10 mr-10">dashboard</i>
            @{{ $t('general.dashBoard') }}
        </b-nav-item>
        <b-nav-item href="/home" class="sub-menu">
            <i class="material-icons md-18 ml-10 mr-10">sync</i>
            @{{ $t('general.changeTaxPayer') }}
        </b-nav-item>
        <h3 class="nav-heading sub">
            @{{ $t('general.configuration') }}
        </h3>
        <b-nav vertical>
            {{-- :href="/taxpayer/' + spark.taxPayerData.id" --}}
            <b-nav-item class="sub-menu" href="/taxpayer/{{ $taxPayerData->id }}">
                <i class="material-icons md-18 ml-10 mr-10">settings</i>
                @{{ $t('general.settingsTaxPayer') }}
            </b-nav-item>
            <b-nav-item class="sub-menu" :to="{ name: 'documentList'}">
                <i class="material-icons md-18 ml-10 mr-10">file_copy</i>
                @{{ $t('commercial.documents') }}
            </b-nav-item>
            <b-nav-item class="sub-menu" :to="{ name: 'rateList'}">
                <i class="material-icons md-18 ml-10 mr-10">public</i>
                @{{ $t('commercial.exchangeRates') }}
            </b-nav-item>
        </b-nav>
        <h3 class="nav-heading sub">
            Change Cycle
        </h3>
        <b-nav vertical>
            @foreach ($cycleData as $cycle)
                <b-nav-item class="sub-menu" href="/{{ $taxPayerData->id }}/{{ $cycle->id }}/">
                    <i class="material-icons md-18 ml-10 mr-10">calendar_today</i>
                    {{ $cycle->year }}
                </b-nav-item>
            @endforeach
            <b-nav-item class="sub-menu" :to="{ name: 'cycleList'}">
                <i class="material-icons md-18 ml-10 mr-10">more_horiz</i>
                @{{ $t('general.showMore') }}
            </b-nav-item>
        </b-nav>
        <h3 class="nav-heading sub">
            {{__('teams.teams')}}: @{{ currentTeam.name }}
        </h3>
        <b-nav vertical>
            <b-nav-item href="/home" class="sub-menu" v-b-tooltip.hover title="Team Dashboard">
                <i class="material-icons md-18 ml-10 mr-10">dashboard</i>
                @{{ $t('general.teamDashBoard') }}
            </b-nav-item>

            <b-nav-item href="/settings/{{ Spark::teamsPrefix() }}/{{ \Auth::user()->currentTeam->id }}" class="sub-menu" v-b-tooltip.hover title="Team Settings">
                <i class="material-icons md-18 ml-10 mr-10">settings</i>
                @{{ $t('general.teamSettings') }}
            </b-nav-item>
        </b-nav>
    </b-collapse>

    <h3 class="nav-heading sub">
        Menu
    </h3>

    <b-button variant="light" class="mb-10" v-b-toggle.collapse-commercial>
        <i class="material-icons float-left">expand_more</i>
        <span class="nav-heading"> @{{ $t('general.transactions') }} </span>
    </b-button>

    <b-collapse id="collapse-commercial" accordion="sub-menu">

        <h3 class="nav-heading sub">
            @{{ $t('commercial.income', 2) }}
        </h3>
        <b-nav vertical>
            <b-nav-item class="sub-menu" :to="{ name: 'salesList'}">
                <i class="material-icons md-18 ml-10 mr-10">send</i>
                @{{ $t('commercial.salesBook') }}
            </b-nav-item>
            <b-nav-item class="sub-menu" :to="{ name: 'creditList'}">
                <i class="material-icons md-18 ml-10 mr-10">redo</i>
                @{{ $t('commercial.creditBook') }}
            </b-nav-item>
            <b-nav-item class="sub-menu" disabled>
                <i class="material-icons md-18 ml-10 mr-10">attach_money</i>
                @{{ $t('commercial.accountReceivables') }}
            </b-nav-item>
        </b-nav>

        <h3 class="nav-heading sub">
            @{{ $t('commercial.expense') }}
        </h3>
        <b-nav vertical>
            <b-nav-item class="sub-menu" :to="{ name: 'purchaseList'}">
                <i class="material-icons md-18 ml-10 mr-10">shopping_cart</i>
                @{{ $t('commercial.purchaseBook') }}
            </b-nav-item>
            <b-nav-item class="sub-menu" :to="{ name: 'debitList'}">
                <i class="material-icons md-18 ml-10 mr-10">undo</i>
                @{{ $t('commercial.debitBook') }}
            </b-nav-item>
            <b-nav-item class="sub-menu" disabled>
                <i class="material-icons md-18 ml-10 mr-10">attach_money</i>
                @{{ $t('commercial.accountPayables') }}
            </b-nav-item>
        </b-nav>

        <h3 class="nav-heading sub">
            Internal
        </h3>
        <b-nav vertical>
            <b-nav-item class="sub-menu" :to="{ name: 'purchaseList'}">
                <i class="material-icons md-18 ml-10 mr-10">vpn_key</i>
                @{{ $t('commercial.fixedAssets') }}
            </b-nav-item>
            <b-nav-item class="sub-menu" :to="{ name: 'debitList'}">
                <i class="material-icons md-18 ml-10 mr-10">unarchive</i>
                @{{ $t('commercial.inventories') }}
            </b-nav-item>
            <b-nav-item class="sub-menu" disabled>
                <i class="material-icons md-18 ml-10 mr-10">settings_applications</i>
                @{{ $t('commercial.productions') }}
            </b-nav-item>
            <b-nav-item class="sub-menu">
                <i class="material-icons md-18 ml-10 mr-10">attach_money</i>
                @{{ $t('commercial.moneyMovements') }}
            </b-nav-item>
        </b-nav>
    </b-collapse>

    <b-button variant="light" class="mb-10" v-b-toggle.collapse-accounting>
        <i class="material-icons float-left">expand_more</i>
        <span class="nav-heading"> @{{ $t('general.accounting') }} </span>
    </b-button>

    <b-collapse id="collapse-accounting" accordion="sub-menu">
        <h3 class="nav-heading sub">
            Daily Accounting
        </h3>
        <b-nav vertical>
            <b-nav-item class="sub-menu" :to="{ name: 'journalList'}">
                <i class="material-icons md-18 ml-10 mr-10">notes</i>
                @{{ $t('accounting.journal') }}
            </b-nav-item>
        </b-nav>
        <h3 class="nav-heading sub">
            @{{ $t('accounting.cycle', 5) }}
        </h3>
        <b-nav vertical>
            <b-nav-item class="sub-menu" :to="{ name: 'cycleList'}">
                <i class="material-icons md-18 ml-10 mr-10">calendar_today</i>
                @{{ $t('accounting.accountingCycle') }}
            </b-nav-item>
            <b-nav-item class="sub-menu" :to="{ name: 'openingBalanceForm'}">
                <i class="material-icons md-18 ml-10 mr-10">play_circle_outline</i>
                @{{ $t('accounting.openingBalance') }}
            </b-nav-item>
            <b-nav-item class="sub-menu" :to="{ name: 'closingBalanceForm'}">
                <i class="material-icons md-18 ml-10 mr-10">pause_circle_outline</i>
                @{{ $t('accounting.closingBalance') }}
            </b-nav-item>
            <b-nav-item class="sub-menu" :to="{ name: 'budgetForm'}">
                <i class="material-icons md-18 ml-10 mr-10">playlist_add_check</i>
                @{{ $t('accounting.cycleBudget') }}
            </b-nav-item>
        </b-nav>
        <h3 class="nav-heading sub">
            @{{ $t('general.configuration') }}
        </h3>
        <b-nav vertical>
            <b-nav-item class="sub-menu" :to="{ name: 'chartList'}">
                <i class="material-icons md-18 ml-10 mr-10">settings</i>
                @{{ $t('accounting.chartOfAccounts') }}
            </b-nav-item>
        </b-nav>
    </b-collapse>

    <b-button variant="light" class="mb-10" v-b-toggle.collapse-accounting @if($teamRole != 'Audit') disabled @endif>
        <i class="material-icons float-left">expand_more</i>
        <span class="nav-heading"> @{{ $t('general.auditing') }} </span>
    </b-button>

    <b-button variant="light" v-b-toggle.collapse-reporting>
        <i class="material-icons float-left">expand_more</i>
        <span class="nav-heading"> @{{ $t('general.report') }} </span>
    </b-button>

    <b-collapse id="collapse-reporting" accordion="sub-menu">
        <h3 class="nav-heading sub">
            General
        </h3>
        <b-nav vertical>
            <b-nav-item class="sub-menu" :to="{ name: 'journalList'}">
                <i class="material-icons md-18 ml-10 mr-10">list</i>
                Commercial
            </b-nav-item>
            <b-nav-item class="sub-menu" :to="{ name: 'journalList'}">
                <i class="material-icons md-18 ml-10 mr-10">list</i>
                Accounting
            </b-nav-item>
            <b-nav-item class="sub-menu" :to="{ name: 'journalList'}">
                <i class="material-icons md-18 ml-10 mr-10">list</i>
                Auditing
            </b-nav-item>
        </b-nav>
    </b-collapse>
</b-nav>
