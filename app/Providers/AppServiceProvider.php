<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('cache', function ($expression) {
            list($key, $duration) = explode(', ', $expression);
            $key = trim($key, "'");
            $duration = trim($duration, "'");
            return "<?php if (! app('cache')->has({$key})) { app('cache')->put({$key}, ob_get_contents(), {$duration}); ob_end_flush(); } else { echo app('cache')->get({$key}); } ?>";
        });

        // Cache the $config using the 'forever' method
        $config = Cache::rememberForever('config', function () {
            return DB::table('setting_webs')->where('id', 1)->first();
        });

        View::share('config', $config);
    }

}
