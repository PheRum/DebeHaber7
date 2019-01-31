<!-- Left Side Of Navbar -->
<li class="nav-item">
        <b-nav fill>
            <b-nav-item href="/home" active>
                <i class="material-icons">home</i> Team
            </b-nav-item>
            <b-nav fill>
                <b-nav-item href="/settings/{{ Spark::teamsPrefix() }}/{{ \Auth::user()->currentTeam->id }}" active>
                    <i class="material-icons">settings</i> Settings
                </b-nav-item>
            </b-nav>
        </b-nav>
    </li>
