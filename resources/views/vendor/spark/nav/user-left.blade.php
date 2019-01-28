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
            <b-nav-item>
                <router-link :to="{ name: 'commercialMenu'}">
                    <i class="fa fa-briefcase"></i> Commercial
                </router-link>
            </b-nav-item>
            <b-nav-item>
                <router-link :to="{ name: 'accountingMenu'}">
                    <i class="fa fa-book"></i> Accounting
                </router-link>
            </b-nav-item>
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
