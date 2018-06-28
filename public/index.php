<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$config = require __DIR__ . '/../app/config/config.php';

/**
 * Class Autoloader
 */
spl_autoload_register(function ($name) {

    $base_dir = __DIR__ . '/../';
    $filePath = str_replace('\\', '/', $name) . '.php';

    include $base_dir . $filePath;
});


(new framework\core\Application($config))->run();