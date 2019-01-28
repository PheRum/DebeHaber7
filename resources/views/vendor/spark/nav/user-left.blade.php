<!-- Left Side Of Navbar -->


@if (request()->route('taxPayer') != null)
    <li class="nav-item">
        <b-nav fill>
            <b-nav-item href="/home" active> <i class="fa fa-fw fa-chart-line"></i> Dashboard</b-nav-item>
        </b-nav>
    </li>
    <li class="nav-item">
        <b-nav fill>
            <b-nav-item>
                <router-link :to="{ name: 'taxPayer'}">
                    <i class="fa fa-home"></i>
                </router-link>
            </b-nav-item>
            <b-nav-item>
                <router-link :to="{ name: 'commercialMenu'}">
                    <i class="fa fa-briefcase"></i> Commercial
                </router-link>
            </b-nav-item>
            <b-nav-item>
                <i class="fa fa-calculator"></i> Accounting
            </b-nav-item>
            {{-- <b-nav-item @if($teamRole != 'Audit') disabled @endif> Audits</b-nav-item> --}}
            <b-nav-item>
                <i class="fa fa-chart-pie"></i> Reports
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
