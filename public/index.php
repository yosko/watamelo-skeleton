<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$app = new \App\Application(devEnv: true, root: '../');
$app->run();
