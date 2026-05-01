<?php

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

if (is_file(dirname(__DIR__) . '/.env')) {
    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
}

$_SERVER['APP_ENV'] ??= $_ENV['APP_ENV'] ?? 'test';
$_SERVER['APP_DEBUG'] ??= $_ENV['APP_DEBUG'] ?? '1';
$_SERVER['APP_SECRET'] = $_SERVER['APP_SECRET'] ?: ($_ENV['APP_SECRET'] ?: 'test-secret');
$_ENV['APP_SECRET'] = $_SERVER['APP_SECRET'];
$_SERVER['KERNEL_CLASS'] = $_ENV['KERNEL_CLASS'] = Kernel::class;