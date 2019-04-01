<?php

declare(strict_types=1);

$_SERVER['APP_ENV'] = 'test';
$_ENV['APP_ENV'] = 'test';
putenv('APP_ENV=test');

require dirname(__DIR__) . '/config/bootstrap.php';
