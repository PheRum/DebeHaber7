<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Meta Information -->
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>@yield('title', config('app.name'))</title>

  <!-- Fonts -->
  <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600' rel='stylesheet' type='text/css'>
  <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' rel='stylesheet' type='text/css'>

  <!-- CSS -->
  <link href="{{ mix(Spark::usesRightToLeftTheme() ? 'css/app-rtl.css' : 'css/app.css') }}" rel="stylesheet">

  <!-- Scripts -->
  @stack('scripts')

  <!-- Global Spark Object -->
  <script>
  window.Spark = @json(array_merge(Spark::scriptVariables(), []));

  @php
  $type = '';

  if (Auth::check()) {
    $currentTeam = Auth::user()->currentTeam;
    $integrationType = App\TaxpayerIntegration::where('team_id', $currentTeam->id)
    ->where('taxpayer_id', 621278)
    ->whereIn('status', [1,2])
    ->select('type')
    ->first();

    if ($integrationType->type == '1') {
      $type = 'Accounting';
    }
    else if ($integrationType->type == 2) {
      $type = 'Personal';
    }
    else {
      $type = 'Audit';
    }
  }

  @endphp

  window.Spark = <?php echo json_encode(array_merge(
    Spark::scriptVariables(), [
      'teamType' => $type
    ]
  )); ?>
  </script>
</head>
<body>
  <div id="spark-app" v-cloak>
    <!-- Navigation -->
    @if (Auth::check())
      @include('spark::nav.user')
    @else
      @include('spark::nav.guest')
    @endif

    <!-- Main Content -->
    <main class="py-4">
      @yield('content')
    </main>

    <!-- Application Level Modals -->
    @if (Auth::check())
      @include('spark::modals.notifications')
      @include('spark::modals.support')
      @include('spark::modals.session-expired')
    @endif
  </div>

  <!-- JavaScript -->
  <script src="{{ mix('js/app.js') }}"></script>
  <script src="/js/sweetalert.min.js"></script>
</body>
</html>
