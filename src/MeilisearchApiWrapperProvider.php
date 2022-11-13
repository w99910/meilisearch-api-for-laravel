<?php

namespace Zlt\MeilisearchApiWrapper;

use Illuminate\Support\ServiceProvider;

class MeilisearchApiWrapperProvider extends ServiceProvider
{
    public function boot()
    {
        // php artisan vendor:publish --tag=meili-wrapper-config
        $this->publishes([
            __DIR__ . '/../config/meilisearch_api_wrapper.php' => config_path('meilisearch_api_wrapper.php'),
        ], 'meili-wrapper-config');
    }
}
