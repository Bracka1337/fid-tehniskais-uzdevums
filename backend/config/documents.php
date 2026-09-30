<?php

return [
    'remote_url' => env('DOCUMENTS_REMOTE_URL', 'http://nginx/remote/documents.xml'),
    'validate_schema' => filter_var(env('DOCUMENTS_VALIDATE_SCHEMA', true), FILTER_VALIDATE_BOOLEAN),
    'local_path' => storage_path('app/remote/documents.xml'),
];
