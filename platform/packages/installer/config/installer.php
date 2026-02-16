<?php

return [
    'enabled' => env('CMS_ENABLE_INSTALLER', false),
    'requirements' => [
        'php' => [
            'openssl',
            'pdo',
            'mbstring',
            'tokenizer',
            'JSON',
            'cURL',
            'gd',
            'fileinfo',
            'xml',
            'ctype',
        ],
        'apache' => [
            'mod_rewrite',
        ],
        'permissions' => [
            '.env',
            'storage/framework/',
            'storage/logs/',
            'bootstrap/cache/',
        ],
    ],
];
