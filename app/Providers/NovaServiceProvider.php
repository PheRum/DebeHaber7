<?php

namespace App\Providers;

use Laravel\Nova\Nova;
use Laravel\Nova\Cards\Help;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\NovaApplicationServiceProvider;
use Laravel\Spark\Spark;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
    * Bootstrap any application services.
    *
    * @return void
    */
    public function boot()
    {
        parent::boot();
    }

    /**
    * Register the Nova routes.
    *
    * @return void
    */
    protected function routes()
    {
        Nova::routes()
        ->withAuthenticationRoutes()
        ->withPasswordResetRoutes()
        ->register();
    }

    /**
    * Register the Nova gate.
    *
    * This gate determines who can access Nova in non-local environments.
    *
    * @return void
    */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return in_array($user->email, Spark::$developers);
        });
    }

    /**
    * Get the cards that should be displayed on the Nova dashboard.
    *
    * @return array
    */
    protected function cards()
    {
        return [
            new Help,
            new \Napp\NovaBugsnag\BugsnagErrorRate(),
            new \Napp\NovaBugsnag\BugsnagCriticalErrors()
        ];
    }

    /**
    * Get the tools that should be listed in the Nova sidebar.
    *
    * @return array
    */
    public function tools()
    {
        return [
            new \PhpJunior\NovaLogViewer\Tool(),
        ];
    }

    /**
    * Register any application services.
    *
    * @return void
    */
    public function register()
    {
        // Nova::resourcesIn(app_path('Nova'));
    }
}
