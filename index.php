<?php

require 'vendor/autoload.php';

use App\Models\Level;
use App\Models\Logger;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

$routes = [];

// Define routes
$routes[] = ['GET', '/favicon', '/Views/favicon.php'];
$routes[] = ['GET', '/', '/Views/Authentication & User Management/index.php'];
$routes[] = ['GET', '/profile', '/Views/Authentication & User Management/profile.php'];
$routes[] = ['GET', '/login', '/Views/Authentication & User Management/login.php'];
$routes[] = ['GET', '/register', '/Views/Authentication & User Management/register.php'];
$routes[] = ['GET', '/projects', '/Views/Project Management/projects.php'];

// Create the dispatcher
$dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) use ($routes) {
    foreach ($routes as $route) {
        list($method, $uri, $file) = $route;
        $r->addRoute($method, $uri, function () use ($file) {
            include __DIR__ . $file;
        });
    }
});

// Dispatch the request
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        // 404 Not Found response
        http_response_code(404);

        include __DIR__ . '/Views/Security & Error Handling/error.php';

        break;

    case Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // 405 Method Not Allowed response
        header('Allow: ' . implode(', ', $allowedMethods));
        http_response_code(405);
        echo 'Method Not Allowed';
        break;

    case Dispatcher::FOUND:

        // Debug
        Logger::log("uri: $uri", __FILE__, Level::DEBUG);

        $routeInfo[1]->__invoke();

        break;
}