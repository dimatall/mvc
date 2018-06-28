<?php

return [

    'defaultController' => 'site',
    'defaultMethod' => 'index',

    'controllersNamespace' => 'app\\Controllers\\',
    'modelsNamespace' => 'app\\Models\\',

    'db' => [
        'name' => 'framework',
        'username' => 'root',
        'password' => 'root',
        'host'=> 'localhost',
        'port' => 3306,
        'driver' => 'mysql'
    ]
];