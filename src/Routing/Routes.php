<?php

declare(strict_types=1);

namespace App\Routing;

final class Routes
{
    private static array $GET = [
    ];

    private static array $POST = [
    ];

    private static array $PUT = [
    ];

    private static array $DELETE = [
    ];

    private static array $keys = [
        'uri', 'controller', 'controllerMethod' 
    ];

    private function __construct()
    {
    }

    public static function getAllRoutes(): array
    {
        $routes = self::convertAllToAssoc(self::$keys, [
            'get' => self::$GET,
            'post' => self::$POST,
            'put' => self::$PUT,
            'delete' => self::$DELETE,
        ]);

        return $routes;
    }
    
    private static function convertAllToAssoc(array $keys, array $allRoutes)
    {
        $result = [];
        
        foreach ($allRoutes as $method => $routesByMethod) {
            foreach($routesByMethod as $route) {
                $result[$method][] = array_combine($keys, $route);
            }
        }
        
        return $result;
    }
}
