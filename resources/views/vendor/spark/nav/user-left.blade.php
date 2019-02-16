<!-- Left Side Of Navbar -->
<li class="nav-item">
    @if (Spark::developer(Auth::user()->email))
        <b-nav-item v-if="spark.env != 'production'" class="nav-heading" disabled>
            <b-badge variant="success" class="success">
                <i class="material-icons mr-5">check_circle_outline</i>
                <span style="vertical-align: middle">@{{spark.env}} Enviornment</span>
            </b-badge>
        </b-nav-item>
        <b-nav-item v-else class="nav-heading" disabled>
            <b-badge variant="danger">
                <i class="material-icons mr-5">warning</i>
                <span style="vertical-align: middle">@{{spark.env}} Enviornment</span>
            </b-badge>
        </b-nav-item>
    @endif
</li>
