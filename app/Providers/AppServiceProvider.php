<?php

namespace App\Providers;

use App\Models\Pengaturan;
use Illuminate\Support\Facades\Schema;
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
        date_default_timezone_set(config('app.timezone', 'Asia/Makassar'));
        \Carbon\Carbon::setLocale(config('app.locale', 'id'));

        // Global App Setting & Theme Sharing
        View::composer('*', function ($view) {
            static $activeSetting = null;

            if ($activeSetting === null) {
                if (Schema::hasTable('pengaturans')) {
                    $activeSetting = Pengaturan::where('is_active', true)->first();
                    if (!$activeSetting) {
                        $activeSetting = Pengaturan::first();
                    }
                }

                if (!$activeSetting) {
                    $activeSetting = new Pengaturan([
                        'app_name' => 'ABSENSI',
                        'institution_name' => 'Universitas Hasanuddin',
                        'faculty_name' => 'Fakultas Kedokteran',
                        'short_name' => 'FK UNHAS',
                        'website_url' => 'www.med.unhas.ac.id',
                        'email_contact' => 'prodis1_fkuh@gmail.com',
                        'theme_color' => 'unhas-red',
                        'primary_color_hex' => '#b91c1c',
                        'secondary_color_hex' => '#0f172a',
                    ]);
                }
            }

            $view->with('appSetting', $activeSetting);
        });
    }
}
