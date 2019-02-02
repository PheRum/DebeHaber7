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
</head>
<body>
    <div class="full-height flex-column">
        <nav class="links">
            @auth
                <a href="/home" style="margin-right: 15px;">
                    <button>
                        {{__('Enter')}}
                    </button>
                </a>
            @else
                <a href="/login" style="margin-right: 15px;">
                    <button>
                        {{__('Login')}}
                    </button>
                </a>
            @endauth

            <a href="/register">
                <button>
                    {{__('Register')}}
                </button>
            </a>
        </nav>

        <div class="flex-fill flex-center">
            <h1 class="text-center">
                <img src="/img/color-logo.png" alt="{{__('Logo')}}" />
            </h1>
        </div>
    </div>
</body>
</html>
