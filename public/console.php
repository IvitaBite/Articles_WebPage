<?php

declare(strict_types=1);

use DI\ContainerBuilder;

require_once __DIR__ . '/../vendor/autoload.php';

$config = require_once __DIR__ . '/../config/config.php';

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions($config);

$container = $containerBuilder->build();
