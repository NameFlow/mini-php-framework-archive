<?php

declare(strict_types=1);

namespace App\Routing;

use App\Controllers\Controller;
use App\Core\Container;
use App\Views\View;
use Exception;
use InvalidArgumentException;

class Router
{
    private string $requestUri;
    private string $httpMethod;
    private array $routes;
    private int $httpResponseCode;
    private Container $container;

    public function __construct(string $requestUri, string $httpMethod, Container $container)
    {
        $this->requestUri = $requestUri;
        $this->container = $container;

        $this->httpMethod = trim(strtolower($httpMethod));
        $this->routes = Routes::getAllRoutes();

        $this->httpResponseCode = 200;
    }

    /**
     * 
     * Handles HTTP request by routing it to the appropriate controller.
     * 
     * Redirects to controller if request uri matches with route uri, 
     * then decides which final responce need to return.
     * 
     * If not found matches create Response 404
     * 
     * @return Response The Response object representing result of the request
     * 
     * @throws InvalidArgumentException if in Routes config given NOT instance of Controller
     */

    public function handleUri(): Response 
    {
        $requestUri = $this->requestUri;

        $routesByRequestMethod = $this->getRoutesByRequestMethod();

        foreach ($routesByRequestMethod as $route) {
            $routeUri = $route['uri'];
            $match = $this->matchUriAndPullParams($requestUri, $routeUri);

            // If request uri IS matches with route uri
            if ($match['success'] === true) {
                $controllerClassName = $route['controller'];
                $controller = $this->container->get($controllerClassName);
                
                $controllerMethod = $route['controllerMethod'];
                
                if ($controller instanceof Controller) {
                    $args = $match['params'];
                    
                    $controllerResponse = $controller->$controllerMethod(...$args);

                    return $this->handleControllerResponse($controllerResponse);
                } else {
                    throw new InvalidArgumentException('Only instanceof Controller, given: ' . $controllerClassName);
                }
                break;
            } 
        }

        // If request uri NOT matches with route uri
        $response = $this->makeResponse404();

        return $response;
    }

    /**
     * Sets default http response code.
     *
     * @param int $httpResponseCode HTTP code, like 200, 201
     * @return void
     */ 
    public function setHttpResponseCode(int $httpResponseCode): void
    {
        $this->httpResponseCode = $httpResponseCode;
    }

    /**
     * Gives routes from Routes.php config by http method
     *
     * @return array The array of routes with appropriate http method
     */
    private function getRoutesByRequestMethod(): array
    {
        $httpMethod = $this->httpMethod;

        switch ($httpMethod) {
            case 'get':
                $routesByRequestMethod = $this->routes['get'];
                break;
            case 'post':
                $routesByRequestMethod = $this->routes['post'];
                break;
            case 'put':
                $routesByRequestMethod = $this->routes['put'];
                break;
            case 'delete':
                $routesByRequestMethod = $this->routes['delete'];
                break;
            default:
                throw new Exception("Error Processing Request");
        }

        return $routesByRequestMethod;
    }

    private function matchUriAndPullParams(string $requestUri, string $routeUri): array
    {
        $formattedRequestUri = $this->formatUri($requestUri); 
        $formattedRouteUri = $this->formatUri($routeUri);

        $uriParams = [];

        if (count($formattedRequestUri) !== count($formattedRouteUri)) {
            return [
                'success' => false
            ];
        }

        $count = count($formattedRequestUri);

        for ($i = 0; $i != $count; $i++) {
            $requestUriValue = $formattedRequestUri[$i];
            $routeUriValue = $formattedRouteUri[$i];

            $isParam = preg_match('/^{.*}$/', $routeUriValue);
            if ($isParam) {
                $uriParams[] = $requestUriValue;
                continue;
            }

            if ($requestUriValue !== $routeUriValue) {
                return [
                    'success' => false
                ];
            }
        }

        return [
            'success' => true,
            'params' => $uriParams
        ];
    }

    private function formatUri(string $uri)
    {
        return explode('/', trim($uri, "/"));
    }

    private function handleControllerResponse(Response | View | null $controllerResponse) {
        if ($controllerResponse instanceof Response) {
            $responseCode = $controllerResponse->httpResponseCode;

            if ($responseCode === 404) {
                $view = $controllerResponse->view;
                
                return $this->makeResponse404($view);
            } 

            return $controllerResponse;
        } elseif ($controllerResponse instanceof View) {
            $httpResponseCode = $this->httpResponseCode;

            return new Response($httpResponseCode, $controllerResponse);
        } elseif (is_null($controllerResponse)) {
            return new Response(200);
        }
    }

    private function makeResponse404(null | View $view = null) {
        if ($view === null) {
            $view = new View('404');
        }
        return new Response(404, $view);
    }
}
