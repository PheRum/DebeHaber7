<b-nav vertical class="mb-25">
    <h3 class="nav-heading sub">
        Taxpayer
        <b-link href="/home" v-b-tooltip.hover title="Change Taxpayer" class="float-right">
            <i class="material-icons md-14 float-right"> sync </i>
            <small>Change</small>
        </b-link>
    </h3>

    <b-button variant="light" class="mb-10" v-b-toggle.collapse-taxpayer>
        <i class="material-icons float-left">expand_more</i>
        <span class="nav-heading"> @{{ spark.taxPayerData.alias }} </span>

        <b-badge variant="primary">
            {{ $cycleData->where('id', request()->route('cycle'))->first()->year }}
        </b-badge>
    </b-button>

    <b-collapse id="collapse-taxpayer" accordion="sub-menu">
        <b-nav-item class="sub-menu" :to="{ name: 'taxPayer'}">
            <i class="material-icons ml-10 mr-10">dashboard</i>
            Dashboard
        </b-nav-item>
        <b-nav-item href="/home" class="sub-menu">
            <i class="material-icons ml-10 mr-10">sync</i>
            Change Taxpayer
        </b-nav-item>
        <h3 class="nav-heading sub">
            Configuration
        </h3>
        <b-nav vertical>
            <b-nav-item class="sub-menu" :to="{ name: 'creditList'}">
                <i class="material-icons ml-10 mr-10">settings</i>
                Configuration
            </b-nav-item>
            <b-nav-item class="sub-menu" :to="{ name: 'salesList'}">
                <i class="material-icons ml-10 mr-10">file_copy</i>
                Documents
            </b-nav-item>
            <b-nav-item class="sub-menu" :to="{ name: 'creditList'}">
                <i class="material-icons ml-10 mr-10">public</i>
                Exchange Rates
            </b-nav-item>
        </b-nav>
        <h3 class="nav-heading sub">
            Change Cycle
        </h3>
        <b-nav vertical>
            @foreach ($cycleData as $cycle)
                <b-nav-item class="sub-menu">
                    <i class="material-icons ml-10 mr-10">calendar_today</i>
                    {{ $cycle->year }}
                </b-nav-item>
            @endforeach
            <b-nav-item class="sub-menu">
                <i class="material-icons ml-10 mr-10">more_horiz</i>
                show more
            </b-nav-item>
        </b-nav>
        <h3 class="nav-heading sub">
            @{{ currentTeam.name }}
        </h3>
        <b-nav vertical>
            <b-nav-item href="/home" class="sub-menu" v-b-tooltip.hover title="Team Dashboard">
                <i class="material-icons ml-10 mr-10">dashboard</i>
                Team Dashboard
            </b-nav-item>

            <b-nav-item href="/settings/{{ Spark::teamsPrefix() }}/{{ \Auth::user()->currentTeam->id }}" class="sub-menu" v-b-tooltip.hover title="Team Settings">
                <i class="material-icons ml-10 mr-10">settings</i>
                Team Settings
            </b-nav-item>
        </b-nav>
    </b-collapse>

    <h3 class="nav-heading sub">
        Menu
    </h3>

    <b-button variant="light" class="mb-10" v-b-toggle.collapse-commercial>
        <i class="material-icons float-left">expand_more</i>
        <span class="nav-heading"> Transactions </span>
    </b-button>

    <b-collapse id="collapse-commercial" accordion="sub-menu">

        <h3 class="nav-heading sub">
            Revenue
        </h3>
        <b-nav vertical>
            <b-nav-item class="sub-menu" :to="{ name: 'salesList'}">
                <i class="material-icons ml-10 mr-10">send</i>
                Sales Book
            </b-nav-item>
            <b-nav-item class="sub-menu" :to="{ name: 'creditList'}">
                <i class="material-icons ml-10 mr-10">redo</i>
                Credit Notes
            </b-nav-item>
            <b-nav-item class="sub-menu" disabled>
                <i class="material-icons ml-10 mr-10">attach_money</i>
                Accounts Receivables
            </b-nav-item>
        </b-nav>

        <h3 class="nav-heading sub">
            Expenses
        </h3>
        <b-nav vertical>
            <b-nav-item class="sub-menu" :to="{ name: 'purchaseList'}">
                <i class="material-icons ml-10 mr-10">shopping_cart</i>
                Purchase Book
            </b-nav-item>
            <b-nav-item class="sub-menu" :to="{ name: 'debitList'}">
                <i class="material-icons ml-10 mr-10">undo</i>
                Debit Notes
            </b-nav-item>
            <b-nav-item class="sub-menu" disabled>
                <i class="material-icons ml-10 mr-10">attach_money</i>
                Accounts Payables
            </b-nav-item>
        </b-nav>

        <h3 class="nav-heading sub">
            Internal
        </h3>
        <b-nav vertical>
            <b-nav-item class="sub-menu" :to="{ name: 'purchaseList'}">
                <i class="material-icons ml-10 mr-10">vpn_key</i>
                Fixed Assets
            </b-nav-item>
            <b-nav-item class="sub-menu" :to="{ name: 'debitList'}">
                <i class="material-icons ml-10 mr-10">unarchive</i>
                Inventories
            </b-nav-item>
            <b-nav-item class="sub-menu" disabled>
                <i class="material-icons ml-10 mr-10">settings_applications</i>
                Production
            </b-nav-item>
            <b-nav-item class="sub-menu">
                <i class="material-icons ml-10 mr-10">attach_money</i>
                Money Movements
            </b-nav-item>
        </b-nav>
    </b-collapse>

    <b-button variant="light" class="mb-10" v-b-toggle.collapse-accounting>
        <i class="material-icons float-left">expand_more</i>
        <span class="nav-heading"> Accounting </span>
    </b-button>

    <b-collapse id="collapse-accounting" accordion="sub-menu">
        <h3 class="nav-heading sub">
            Daily Accounting
        </h3>
        <b-nav vertical>
            <b-nav-item class="sub-menu" :to="{ name: 'journalList'}">
                <i class="material-icons ml-10 mr-10">notes</i>
                Journals
            </b-nav-item>
        </b-nav>
        <h3 class="nav-heading sub">
            Cycles
        </h3>
        <b-nav vertical>
            <b-nav-item class="sub-menu" :to="{ name: 'cycleList'}">
                <i class="material-icons ml-10 mr-10">calendar_today</i>
                Accounting Cycles
            </b-nav-item>
            <b-nav-item class="sub-menu" :to="{ name: 'openingBalanceForm'}">
                <i class="material-icons ml-10 mr-10">play_circle_outline</i>
                Opening Balance
            </b-nav-item>
            <b-nav-item class="sub-menu" :to="{ name: 'closingBalanceForm'}">
                <i class="material-icons ml-10 mr-10">pause_circle_outline</i>
                Closing Balance
            </b-nav-item>
            <b-nav-item class="sub-menu" :to="{ name: 'budgetForm'}">
                <i class="material-icons ml-10 mr-10">playlist_add_check</i>
                Cycle Budgets
            </b-nav-item>
        </b-nav>
        <h3 class="nav-heading sub">
            Configuration
        </h3>
        <b-nav vertical>
            <b-nav-item class="sub-menu" :to="{ name: 'purchaseList'}">
                <i class="material-icons ml-10 mr-10">settings</i>
                Chart of Accounts
            </b-nav-item>
        </b-nav>
    </b-collapse>

    <b-button variant="light" class="mb-10" v-b-toggle.collapse-accounting @if($teamRole != 'Audit') disabled @endif>
        <i class="material-icons float-left">expand_more</i>
        <span class="nav-heading"> Auditing </span>
    </b-button>

    <b-button variant="light" v-b-toggle.collapse-reporting>
        <i class="material-icons float-left">expand_more</i>
        <span class="nav-heading"> Reports </span>
    </b-button>

    <b-collapse id="collapse-reporting" accordion="sub-menu">
        <h3 class="nav-heading sub">
            General
        </h3>
        <b-nav vertical>
            <b-nav-item class="sub-menu" :to="{ name: 'journalList'}">
                <i class="material-icons ml-10 mr-10">list</i>
                Commercial
            </b-nav-item>
            <b-nav-item class="sub-menu" :to="{ name: 'journalList'}">
                <i class="material-icons ml-10 mr-10">list</i>
                Accounting
            </b-nav-item>
            <b-nav-item class="sub-menu" :to="{ name: 'journalList'}">
                <i class="material-icons ml-10 mr-10">list</i>
                Auditing
            </b-nav-item>
        </b-nav>
    </b-collapse>
</b-nav>
