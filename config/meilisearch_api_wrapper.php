<?php

return [
    'api_key' => env('MEILISEARCH_API_KEY', 'masterKey'),
    'host' => env('MEILISEARCH_HOST', 'http://localhost:7700'),
    'index' => env('MEILISEARCH_INDEX', 'movies'),
];
