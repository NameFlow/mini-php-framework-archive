<?php

declare(strict_types=1);

// Front Controller

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Helpers/dd.php';

use App\Core\Container;
use App\Routing\Router;

$requestUri = $_SERVER['REQUEST_URI' ?? '/'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

$container = new Container();

$router = new Router($requestUri, $requestMethod, $container);

$response = $router->handleUri();
$responseCode = $response->httpResponseCode;
$responseView = $response->view;

http_response_code($responseCode);
if ($responseView !== null) {
    echo $responseView->getView();
}

?>

