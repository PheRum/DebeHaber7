<!-- Teams -->
<h3 class="nav-heading sub"> {{__('teams.teams')}} : @{{ currentTeam.name }} </h3>

<!-- Create Team -->
{{-- @if (Spark::createsAdditionalTeams())
<a class="dropdown-item" href="/settings#/{{Spark::teamsPrefix()}}">
<i class="fa fa-fw text-left fa-btn fa-plus-circle"></i> {{__('teams.create_team')}}
</a>
@endif --}}

<!-- Switch Current Team -->
{{-- @if (Spark::showsTeamSwitcher())
<a class="dropdown-item" v-for="team in teams" :href="'/settings/{{ Spark::teamsPrefix() }}/'+ team.id +'/switch'">
<span v-if="user.current_team_id == team.id">
<i class="material-icons">supervised_user_circle</i> @{{ team.name }}
</span>

<span v-else>
<img :src="team.photo_url" class="spark-profile-photo-xs" alt="{{__('Team Photo')}}" /><i class="fa fa-btn"></i> @{{ team.name }}
</span>
</a>
@endif --}}

<a class="dropdown-item" :href="'/settings/{{Spark::teamsPrefix()}}/' + user.current_team_id">
    <i class="material-icons">supervised_user_circle</i> {{__('teams.team_profile')}}
</a>

<a class="dropdown-item" :href="'/settings/{{Spark::teamsPrefix()}}/' + user.current_team_id + '/membership'">
    <i class="material-icons">card_membership</i> {{__('Membership')}}
</a>



<div class="dropdown-divider"></div>
