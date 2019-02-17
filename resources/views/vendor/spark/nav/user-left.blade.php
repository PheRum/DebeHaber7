@if (Spark::developer(Auth::user()->email))
    <div v-if="spark.env != 'production'" class="d-none d-lg-block" disabled>
        <b-badge variant="success" class="success">
            <i class="material-icons mr-5">check_circle_outline</i>
            <span style="vertical-align: middle">@{{spark.env}} Enviornment</span>
        </b-badge>
    </div>
    <div v-else class="d-none d-lg-block" disabled>
        <b-badge variant="danger">
            <i class="material-icons mr-5">warning</i>
            <span style="vertical-align: middle">@{{spark.env}} Enviornment</span>
        </b-badge>
    </div>
@endif
