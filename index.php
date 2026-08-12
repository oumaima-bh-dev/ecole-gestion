<?php
/**
 * Main Front Controller / Router
 */

// Basic error reporting for development (students can debug easily)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include base classes
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/models/Model.php';
require_once __DIR__ . '/controllers/Controller.php';

// Determine the controller and action
$controllerParam = isset($_GET['controller']) ? strtolower(trim($_GET['controller'])) : 'home';
$actionParam = isset($_GET['action']) ? trim($_GET['action']) : 'index';

// Map controller parameter to class name
$controllerClass = ucfirst($controllerParam) . 'Controller';
$controllerFile = __DIR__ . '/controllers/' . $controllerClass . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    if (class_exists($controllerClass)) {
        $controllerInstance = new $controllerClass();
        if (method_exists($controllerInstance, $actionParam)) {
            // Execute the action
            $controllerInstance->$actionParam();
        } else {
            // Graceful fallback to default dashboard or login
            header("HTTP/1.0 404 Not Found");
            echo "<h1>404 - Action '$actionParam' non trouvée.</h1>";
        }
    } else {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 - Classe '$controllerClass' non trouvée.</h1>";
    }
} else {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 - Contrôleur '$controllerParam' non trouvé.</h1>";
}
