<?php

declare(strict_types=1);

session_start();

use App\Response\ViewResponse;
use App\Response\RedirectResponse;
use App\Router\Router;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Dotenv\Dotenv;
use Twig\Extension\DebugExtension;
use DI\ContainerBuilder;

require_once __DIR__ . '/../vendor/autoload.php';

$loader = new FilesystemLoader(__DIR__ . '/../views');
$twig = new Environment($loader);

if (isset($_SESSION['flush'])) {
    $flashMessages = [];
    foreach ($_SESSION['flush'] as $messageType => $messages) {
        foreach ($messages as $message) {
            $flashMessages[] = [
                'type' => $messageType,
                'message' => $message,
            ];
        }
    }
    $twig->addGlobal('flash_messages', $flashMessages);
    unset($_SESSION['flush']);
}

$twig->addExtension(new DebugExtension());

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$config = require_once __DIR__ . '/../config/config.php';

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions($config);

$container = $containerBuilder->build();

$routeInfo = Router::dispatch();

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        [$class, $method] = $routeInfo[1];
        $vars = $routeInfo[2];

        $controller = $container->get($class);

        $response = $controller->{$method}($vars);

        switch (true) {
            case $response instanceof ViewResponse:
                echo $twig->render($response->getViewName() . '.twig', $response->getData());
                break;
            case $response instanceof RedirectResponse:
                header('Location: ' . $response->getLocation());
                break;
            default:
                break;
        }
}