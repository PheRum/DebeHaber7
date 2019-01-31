
<b-nav vertical>
    <b-nav-item :to="{ name: 'taxPayer'}" v-b-toggle.collapse-taxpayer class="nav-heading" disabled>
        <i class="material-icons when-closed">expand_more</i>
        @{{ spark.taxPayerData.alias }}
        <b-badge pill variant="info">
            <span>
                {{ $cycleData->where('id', request()->route('cycle'))->first()->year }}
            </span>
        </b-badge>
    </b-nav-item>

    <b-collapse id="collapse-taxpayer" accordion="sub-menu">
        <h3 class="nav-heading sub">
            Configuration
        </h3>
        <b-nav vertical>
            <b-nav-item class="sub-menu" :to="{ name: 'salesList'}">
                <i class="material-icons ml-10 mr-10">file_copy</i>
                Documents
            </b-nav-item>
            <b-nav-item class="sub-menu" :to="{ name: 'creditList'}">
                <i class="material-icons ml-10 mr-10">public</i>
                Exchange Rates
            </b-nav-item>
            <b-nav-item class="sub-menu" :to="{ name: 'creditList'}">
                <i class="material-icons ml-10 mr-10">settings</i>
                Taxpayer Settings
            </b-nav-item>
            <b-nav-item href="/home" class="sub-menu">
                <i class="material-icons ml-10 mr-10">undo</i>
                Change Taxpayer
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
                more
            </b-nav-item>
        </b-nav>
    </b-collapse>

    <b-nav-item :to="{ name: 'commercialMenu'}" v-b-toggle.collapse-commercial class="nav-heading" disabled>
        <i class="material-icons when-closed">expand_more</i>
        Commercial
    </b-nav-item>

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
                <i class="material-icons ml-10 mr-10">cancel_presentation</i>
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
                <i class="material-icons ml-10 mr-10">cancel_presentation</i>
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

    <b-nav-item v-b-toggle.collapse-accounting disabled class="nav-heading">
        <i class="material-icons when-closed">expand_more</i>
        Accounting
    </b-nav-item>

    <b-collapse id="collapse-accounting" accordion="sub-menu">
        <h3 class="nav-heading sub">
            Daily Accounting
        </h3>
        <b-nav vertical>
            <b-nav-item class="sub-menu" :to="{ name: 'journalList'}">
                <i class="material-icons ml-10 mr-10">list</i>
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
            <b-nav-item class="sub-menu" :to="{ name: 'openingForm'}">
                <i class="material-icons ml-10 mr-10">playlist_play</i>
                Open &amp; Close Cycle
            </b-nav-item>
            <b-nav-item class="sub-menu" :to="{ name: 'budgetForm'}">
                <i class="material-icons ml-10 mr-10">playlist_add_check</i>
                Cycle Budgets
            </b-nav-item>
        </b-nav>
        <h3 class="nav-heading sub">
            Settings
        </h3>
        <b-nav vertical>
            <b-nav-item class="sub-menu" :to="{ name: 'purchaseList'}">
                <i class="material-icons ml-10 mr-10">shopping_cart</i>
                Chart of Accounts
            </b-nav-item>
        </b-nav>
    </b-collapse>

    <b-nav-item @if($teamRole != 'Audit') disabled @endif v-b-toggle.collapse-auditing class="nav-heading">
        <i class="material-icons when-closed">expand_more</i>
        Auditing
    </b-nav-item>

    <b-nav-item v-b-toggle.collapse-reports disabled class="nav-heading">
        <i class="material-icons when-closed">expand_more</i>
        Reports
    </b-nav-item>
</b-nav>
