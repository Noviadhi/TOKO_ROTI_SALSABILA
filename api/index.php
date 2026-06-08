<?php

declare(strict_types=1);

$temporaryPath = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR);
$storagePath = getenv('LARAVEL_STORAGE_PATH') ?: $temporaryPath.DIRECTORY_SEPARATOR.'laravel-storage';
$viewPath = getenv('VIEW_COMPILED_PATH') ?: $storagePath.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'views';

foreach ([
    $storagePath,
    $storagePath.DIRECTORY_SEPARATOR.'app',
    $storagePath.DIRECTORY_SEPARATOR.'framework',
    $storagePath.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'cache',
    $storagePath.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'data',
    $storagePath.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'sessions',
    $viewPath,
    $storagePath.DIRECTORY_SEPARATOR.'logs',
] as $directory) {
    if (! is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
}

foreach ([
    'LARAVEL_STORAGE_PATH' => $storagePath,
    'VIEW_COMPILED_PATH' => $viewPath,
] as $key => $value) {
    putenv("{$key}={$value}");
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

require __DIR__.'/../public/index.php';
