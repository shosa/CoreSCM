<?php
require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
require __DIR__ . '/app/Core/helpers.php';

define('BASE_PATH', '/scm');

echo "url('/login') = " . url('/login') . "<br>";
echo "url('/dashboard') = " . url('/dashboard') . "<br>";
echo "url('') = " . url('') . "<br>";
