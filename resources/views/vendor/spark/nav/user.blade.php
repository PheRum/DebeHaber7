<spark-navbar :user="user" :teams="teams" :current-team="currentTeam" :unread-announcements-count="unreadAnnouncementsCount" :unread-notifications-count="unreadNotificationsCount" inline-template>
    <nav class="navbar navbar-light navbar-expand-md navbar-spark mb-25">
        <ul class="nav navbar-nav flex-fill w-100 flex-nowrap">
            <li class="nav-item active">
                <a class="nav-link" href="#">Menu</a>
            </li>
            <li class="nav-item d-none d-md-block">
                @includeIf('spark::nav.user-left')
            </li>
        </ul>

        <ul class="nav navbar-nav flex-fill justify-content-center d-none d-md-block">
            @include('spark::nav.brand')
        </ul>

        <ul class="nav navbar-nav flex-fill w-100 justify-content-end">
            <b-nav-item href="/docs" v-b-tooltip.hover :title="$t('general.documentation')">
                <i class="material-icons">import_contacts</i>
            </b-nav-item>

            <b-nav-item href="/tickets" v-b-tooltip.hover :title="$t('general.askForHelp')">
                <i class="material-icons">contact_support</i>
            </b-nav-item>

            <b-nav-item @click="showNotifications" v-b-tooltip.hover :title="$t('general.notifications')">
                <i v-if="notificationsCount > 0" red400 class="material-icons error">notifications_active</i>
                <i v-else class="material-icons">notifications</i>
                <b-badge v-if="notificationsCount > 0" variant="primary">@{{notificationsCount}}</b-badge>
            </b-nav-item>

            <ul class="navbar-nav d-none d-md-block">
                <li class="nav-item dropdown">
                    <a href="#" class="d-block d-md-flex text-center nav-link dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img :src="user.photo_url" class="dropdown-toggle-image" alt="Img"/>
                        <span class="d-none d-md-block">@{{ user.name }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                        <!-- Impersonation -->
                        @if (session('spark:impersonator'))
                            <h3 class="nav-heading sub"> {{__('Impersonation')}} </h3>

                            <!-- Stop Impersonating -->
                            <a class="dropdown-item" href="/spark/kiosk/users/stop-impersonating">
                                <i class="material-icons">security</i> {{__('Back To My Account')}}
                            </a>

                            <div class="dropdown-divider"></div>
                        @endif

                        <!-- Developer -->
                        @if (Spark::developer(Auth::user()->email))
                            @include('spark::nav.developer')
                        @endif

                        <!-- Subscription Reminders -->
                        @include('spark::nav.subscriptions')

                        <!-- Settings -->
                        <h3 class="nav-heading sub"> {{__('Settings')}} </h3>
                        <!-- Your Settings -->
                        <a class="dropdown-item" href="/settings">
                            <i class="material-icons">account_circle</i> {{__('Profile')}}
                        </a>

                        <a class="dropdown-item" v-for="team in teams" :href="'/settings/{{ Spark::teamsPrefix() }}/'+ team.id +'/switch'">
                            <img :src="team.photo_url" class="spark-profile-photo-xs" alt="I" /> {{__('teams.teams')}}
                        </a>

                        <a class="dropdown-item" href="/settings#/api">
                            <i class="material-icons">extension</i> @{{ $t('general.integrations') }}
                            {{-- {{__('API')}} --}}
                        </a>

                        <div class="dropdown-divider"></div>

                        @include('spark::nav.teams')

                        <!-- Logout -->
                        <a class="dropdown-item" href="/logout">
                            <span>
                                <i class="material-icons text-left fa-fw">power_settings_new</i> {{__('Logout')}}
                            </span>
                        </a>
                    </div>
                </li>
            </ul>
        </ul>
    </nav>
</spark-navbar>
