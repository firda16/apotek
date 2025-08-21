<?php

namespace App\Providers;

use Carbon\Carbon;
use App\Models\User;

use App\Models\Setting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        config(['app.locale' => 'id']);
        Carbon::setLocale('id');
        App::setLocale('id');
        View::share('setting', Setting::first());
        View::share('user', User::first());
    }
}
