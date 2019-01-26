<!-- Left Side Of Navbar -->


@if (request()->route('taxPayer') != null)
    <li class="nav-item">
        <b-nav fill>
            <b-nav-item>
                <router-link :to="{ name: 'commercialMenu'}">
                    Commercial
                </router-link>
            </b-nav-item>
            <b-nav-item> Accounting</b-nav-item>
            {{-- <b-nav-item @if($teamRole != 'Audit') disabled @endif> Audits</b-nav-item> --}}
            <b-nav-item> Reports</b-nav-item>
        </b-nav>
    </li>
@else
    <li class="nav-item">
        <b-nav fill>
            <b-nav-item href="/home" active> <i class="fa fa-fw fa-chart-line"></i> Dashboard</b-nav-item>
        </b-nav>
    </li>
@endif
