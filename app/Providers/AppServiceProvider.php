<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Log;

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
        if (file_exists(base_path('composer.json'))) {
            $composer = json_decode(file_get_contents(base_path('composer.json')), true);

            if (!empty($composer)) {
                Log::info("Injecting additional config from " . base_path('composer.json'));

                foreach (['name', 'description', 'version', 'type', 'keywords'] as $field) {
                    if (empty(config("app.$field")) && array_key_exists($field, $composer)) {
                        config(["app.$field" => $composer[$field]]);
                    }
                }
            }
        }

        if (file_exists(public_path('mix-manifest.json'))) {
            $mix = json_decode(file_get_contents(public_path('mix-manifest.json')), true);

            if (!empty($mix)) {
                Log::info("Injecting additional config from " . public_path('mix-manifest.json'));

                config(["app.mix-manifest" => $mix]);
            }
        }
    }
}
