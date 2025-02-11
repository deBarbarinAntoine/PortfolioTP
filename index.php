<?php

/**
 * Main entry point for the application.
 * Handles HTTP requests, dispatches them to the appropriate view files via routes,
 * and manages responses for valid, invalid, or unsupported requests.
 */

require 'vendor/autoload.php';

// Import essential classes for logging, routing, and debugging purposes.
use App\Models\Level;
use App\Models\Logger;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

/**
 * Array to define application routes.
 *
 * Format for each route:
 * - Method: The HTTP method (e.g., GET, POST).
 * - URI: The URI to match (e.g., /profile or /project/{id:\d+}).
 * - File: The file to include when this route is matched.
 *
 * Example: ['GET', '/favicon', '/Views/favicon.php']
 */
$routes = [];

// Favicon
$routes[] = ['GET', '/favicon', '/Views/favicon.php'];

// Authentication routes
$routes[] = ['GET', '/', '/Views/user/index.php'];
$routes[] = ['GET', '/login', '/Views/user/login.php'];
$routes[] = ['POST', '/login', '/Views/user/login.php'];
$routes[] = ['GET', '/logout', '/Views/user/logout.php'];
$routes[] = ['GET', '/register', '/Views/user/register.php'];
$routes[] = ['POST', '/register', '/Views/user/register.php'];
$routes[] = ['GET', '/reset', '/Views/user/reset_password.php'];
$routes[] = ['POST', '/reset', '/Views/user/change_password.php'];
$routes[] = ['GET', '/reset/mail', '/Views/user/password_reset_mail.php'];

// User Management routes
$routes[] = ['GET', '/profile', '/Views/user/profile.php'];
$routes[] = ['GET', '/profile/update', '/Views/user/edit_profile.php'];
$routes[] = ['POST', '/profile/update', '/Views/user/edit_profile.php'];
$routes[] = ['GET', '/profile/skills', '/Views/user/edit_skills.php'];
$routes[] = ['POST', '/profile/skills', '/Views/user/edit_skills.php'];

// Project Management routes
$routes[] = ['GET', '/projects', '/Views/project/my_projects.php'];
$routes[] = ['GET', '/project/new', '/Views/project/add_project.php'];
$routes[] = ['POST', '/project/new', '/Views/project/add_project.php'];
$routes[] = ['GET', '/project/{id:\d+}', '/Views/project/project.php'];
$routes[] = ['GET', '/project/{id:\d+}/update', '/Views/project/edit_project.php'];
$routes[] = ['POST', '/project/{id:\d+}/update', '/Views/project/edit_project.php'];
$routes[] = ['POST', '/deleteImg/{id:\d+}', '/Views/project/delete_project_image.php'];
$routes[] = ['POST', '/project/{id:\d+}/delete', '/Views/project/delete_project.php'];
$routes[] = ['POST', '/project/{id:\d+}/add', '/Views/project/add_user_to_project.php'];
$routes[] = ['POST', '/deleteUserProject/{id:\d+}', '/Views/project/delete_user_from_project.php'];

// Admin routes
$routes[] = ['GET', '/admin', '/Views/admin/admin_dashboard.php'];

$routes[] = ['GET', '/admin/users', '/Views/admin/admin_users.php'];
$routes[] = ['POST', '/admin/users/changeRole', '/Views/admin/change_role.php'];
$routes[] = ['POST', '/admin/user/{id:\d+}/delete', '/Views/admin/delete_user.php'];

$routes[] = ['GET', '/admin/skills', '/Views/admin/admin_skills.php'];
$routes[] = ['POST', '/admin/skills', '/Views/admin/admin_skills.php'];
$routes[] = ['GET', '/admin/skill/{id:\d+}/update', '/Views/admin/edit_skill.php'];
$routes[] = ['POST', '/admin/skill/{id:\d+}/update', '/Views/admin/edit_skill.php'];
$routes[] = ['POST', '/admin/skill/{id:\d+}/delete', '/Views/admin/delete_skill.php'];

$routes[] = ['POST', '/admin/role/{id:\d+}/update', '/Views/admin/update_role.php'];


/**
 * Creates a dispatcher for routing using FastRoute.
 * The dispatcher maps incoming requests to their corresponding routes and executes their logic.
 */
$dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) use ($routes) {

    // Iterate over each defined route and add it to the RouteCollector.
    foreach ($routes as $route) {
        list($method, $uri, $file) = $route;

        // Add a route to the dispatcher. The route's URI is mapped to an anonymous 
        // function that includes the target view file when executed.
        $r->addRoute($method, $uri, function () use ($file) {

            // Include the route's corresponding view file.
            include __DIR__ . $file;
        });
    }
});

// Retrieve the HTTP method (e.g., GET, POST) and requested URI from the incoming request.
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Dispatch the request
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

// Determine the status of the route matching and act accordingly.
switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        /**
         * Case 1: No matching route found.
         * Respond to the client with a 404 Not Found status and display an error view.
         */
        http_response_code(404);
        include __DIR__ . '/Views/error/error.php';
        break;

    case Dispatcher::METHOD_NOT_ALLOWED:
        /**
         * Case 2: The HTTP method used is not allowed for the matched route.
         * Respond with a 405 Method Not Allowed status and include allowed methods in the header.
         */

        // Retrieve the allowed methods for the requested URI.
        $allowedMethods = $routeInfo[1];

        // Specify the valid methods in the 'Allow' header.
        header('Allow: ' . implode(', ', $allowedMethods));

        http_response_code(405);
        echo 'Method Not Allowed';
        break;

    case Dispatcher::FOUND:
        /**
         * Case 3: Matching route found.
         * Process the route and execute the corresponding action or view file.
         */

        // Retrieve any variables extracted from the URI (e.g., {id:\d+}).
        $vars = $routeInfo[2];

        if (isset($vars['id'])) {

            // Assign the extracted 'id' parameter if available.
            $paramID['id'] = $vars['id'];
        }

        // Log the matching route and associated parameters for debugging purposes.
        Logger::log("uri: $uri", __FILE__, Level::DEBUG);
        if (isset($paramID)) {
            Logger::log("id: " . json_encode($paramID), __FILE__, Level::DEBUG);
        }

        // Invoke the callback function associated with the route.
        $routeInfo[1]->__invoke();

        break;
}