<!-- Left Side Of Navbar -->


@if (request()->route('taxPayer') != null)
    <li class="nav-item">
        <b-nav fill>
            <b-nav-item href="/home" active>
                <i class="fa fa-home"></i>
            </b-nav-item>
        </b-nav>
    </li>
    <li class="nav-item">
        <b-nav fill>
            <b-nav-item>
                <router-link :to="{ name: 'taxPayer'}">
                    <i class="fa fa-user"></i>
                </router-link>
            </b-nav-item>
            <b-dropdown text="Commercial" variant="link">
                <b-dropdown-header>
                    Expenses
                </b-dropdown-header>
                <b-dropdown-item>
                    <router-link :to="{ name: 'purchaseList'}">
                        Purchase Book
                    </router-link>
                </b-dropdown-item>
                <b-dropdown-item>
                    <router-link :to="{ name: 'accountPayables'}">
                        Accounts Payables
                    </router-link>
                </b-dropdown-item>
                <b-dropdown-item>
                    <router-link :to="{ name: 'debitList'}">
                        Debit Notes
                    </router-link>
                </b-dropdown-item>
                <b-dropdown-header>
                    Incomes
                </b-dropdown-header>
                <b-dropdown-item>
                    <router-link :to="{ name: 'salesList'}">
                        Sales Book
                    </router-link>
                </b-dropdown-item>
                <b-dropdown-item>
                    <router-link :to="{ name: 'accountPayables'}">
                        Accounts Receivables
                    </router-link>
                </b-dropdown-item>
                <b-dropdown-item>
                    <router-link :to="{ name: 'creditList'}">
                        Credit Notes
                    </router-link>
                </b-dropdown-item>
            </b-dropdown>

            <b-dropdown text="Accounting" variant="link">

                <b-dropdown-header>Daily</b-dropdown-header>
                <b-dropdown-item>
                    <router-link :to="{ name: 'journalList'}">
                        <b>Journals</b>
                    </router-link>
                </b-dropdown-item>
                <b-dropdown-header>Configuration</b-dropdown-header>
                <b-dropdown-item>
                    <router-link :to="{ name: 'chartList'}">
                        Chart of Accounts
                    </router-link>
                </b-dropdown-item>
                <b-dropdown-item>
                    <router-link :to="{ name: 'cycleList'}">
                        Accounting Cycle
                    </router-link>
                </b-dropdown-item>
                <b-dropdown-item>
                    <router-link :to="{ name: 'budgetForm'}">
                        Cycle Budget
                    </router-link>
                </b-dropdown-item>
            </b-dropdown>

            {{-- <b-nav-item @if($teamRole != 'Audit') disabled @endif> Audits</b-nav-item> --}}
            <b-nav-item>
                <router-link :to="{ name: 'reportingMenu'}">
                    <i class="fa fa-chart-pie"></i> Reports
                </router-link>
            </b-nav-item>
        </b-nav>
    </li>
@else
    <li class="nav-item">
        <b-nav fill>
            <b-nav-item href="/home" active> <i class="fa fa-fw fa-chart-line"></i> Dashboard</b-nav-item>
        </b-nav>
    </li>
@endif
