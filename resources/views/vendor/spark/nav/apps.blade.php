
<ul class="nav flex-column">
    <li class="nav-item ">
        <a href="#owner" aria-controls="owner" role="tab" data-toggle="tab" class="nav-link active show" aria-selected="true">
            Team Profile
        </a>
    </li>
    <li class="nav-item ">
        <a href="#membership" aria-controls="membership" role="tab" data-toggle="tab" class="nav-link">
            Membership
        </a>
    </li>
    <li class="nav-item ">
        <a href="/settings" class="nav-link">
            Your Settings
        </a>
    </li>
</ul>

<b-nav vertical>
    <b-nav-item>
        <router-link :to="{ name: 'taxPayer'}">
            @{{ spark.taxPayerData.alias }}
        </router-link>
    </b-nav-item>

    <b-nav-item v-b-toggle.collapse-commercial>
        Commercial
    </b-nav-item>

    <b-collapse id="collapse-commercial" accordion="sub-menu">

        <ul class="nav flex-column mb-4 ">
            <li class="nav-item ">
                <a href="#owner" aria-controls="owner" role="tab" data-toggle="tab" class="nav-link active show" aria-selected="true">
                    Team Profile
                </a>
            </li>
            <li class="nav-item ">
                <a href="#membership" aria-controls="membership" role="tab" data-toggle="tab" class="nav-link">
                    Membership
                </a>
            </li>
            <li class="nav-item ">
                <a href="/settings" class="nav-link">
                    Your Settings
                </a>
            </li>
        </ul>


        <b-nav vertical>
            <h3 class="nav-heading ">
                Revenue
            </h3>
            <b-nav-item>
                <router-link to="{ name: 'salesList'}">
                    Sales Book
                </router-link>
            </b-nav-item>
            <b-nav-item>
                <router-link to="{ name: 'creditList'}">
                    Credit Notes
                </router-link>
            </b-nav-item>
            <b-nav-item>
                Accounts Receivables
            </b-nav-item>

            <b-nav-item>
                <router-link :to="{ name: 'purchaseList'}">
                    Purchase Book
                </router-link>
            </b-nav-item>
            <b-nav-item>
                <router-link :to="{ name: 'debitList'}">
                    Debit Notes
                </router-link>
            </b-nav-item>
            <b-nav-item>
                {{-- <b-list-group-item v-b-toggle="'collapse-commercial'" class="d-flex justify-content-between align-items-center"> --}}
                Accounts Payables
                {{-- </b-list-group-item> --}}
            </b-nav-item>
        </b-nav>
    </b-collapse>

    <b-nav-item v-b-toggle.collapse-accounting>
        Accounting
    </b-nav-item>

    <b-collapse id="collapse-accounting" accordion="sub-menu">
        <b-nav-item>
            <router-link to="{ name: 'salesList'}">
                Sales Book
            </router-link>
        </b-nav-item>
        <b-nav-item>
            <router-link to="{ name: 'salesList'}">
                Sales Book
            </router-link>
        </b-nav-item>
        <b-nav-item>
            <router-link to="{ name: 'salesList'}">
                Sales Book
            </router-link>
        </b-nav-item>
        <b-nav-item>
            <router-link to="{ name: 'salesList'}">
                Sales Book
            </router-link>
        </b-nav-item>
    </b-collapse>

    <b-nav-item @if($teamRole != 'Audit') disabled @endif v-b-toggle.collapse-auditing>
        Auditing
    </b-nav-item>

    <b-nav-item v-b-toggle.collapse-reports>
        Reports
    </b-nav-item>
</b-nav>
