<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Kernel;

$kernel = new Kernel(__DIR__ . '/..');
$kernel->handleRequest();