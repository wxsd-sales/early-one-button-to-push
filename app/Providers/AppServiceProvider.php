<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

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
        $inject_config_from_composer = function ($composer_path) {
            if (file_exists($composer_path)) {
                $composer = json_decode(file_get_contents($composer_path), true);
                $fields = ['name', 'description', 'version', 'type', 'keywords'];

                if (empty($composer)) {
                    return;
                }

                Log::info("Injecting additional config from " . $composer_path);
                foreach ($fields as $field) {
                    if (empty(config("app.$field")) && array_key_exists($field, $composer)) {
                        config(["app.$field" => $composer[$field]]);
                    }
                }
            }
        };

        $inject_config_from_mix_manifest = function ($mix_manifest_path) {
            if (file_exists($mix_manifest_path)) {
                $mix_manifest = json_decode(file_get_contents($mix_manifest_path), true);

                if (empty($mix_manifest)) {
                    return;
                }

                Log::info("Injecting additional config from " . $mix_manifest_path);
                config(["app.mix-manifest" => $mix_manifest]);
            }
        };

        $inject_config_from_composer(base_path('composer.json'));
        $inject_config_from_mix_manifest(public_path('mix-manifest.json'));
    }
}
