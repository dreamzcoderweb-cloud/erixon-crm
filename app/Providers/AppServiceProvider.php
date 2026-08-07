<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Crypt;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (file_exists(app_path('helpers.php'))) {
            require_once app_path('helpers.php');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (Schema::hasTable('general_settings')) {
            View::share('generalSetting', GeneralSetting::getSettings());
        }
    }
}
