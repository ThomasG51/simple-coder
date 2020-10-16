<?php

namespace Lib\Router;

use Symfony\Component\Yaml\Yaml;

class Router {

    /**
     * Returns a routes collection from the Yaml File
     *
     * @return array
     */
    public function getRouteCollection() : array
    {
        $routesCollection = [];
        $routes = Yaml::parseFile('../config/routes.yaml');

        foreach($routes as $routesNames => $oneRoute)
        {
            $route = new Route($routesNames, $oneRoute['path'], $oneRoute['controller']);
            $routesCollection[$routesNames] = $route;
        }

        return $routesCollection;
    }

    
    /**
     * Returns the controller called by the request
     *
     * @param Request $request
     * @return string
     */
    public function getRouteFromRequest($request) : string
    {
        $uri = $request->getPathInfo();
        $routeCollection = $this->getRouteCollection();

        foreach($routeCollection as $route)
        {
            if ($uri == $route->getPath()) {
                return $route->getController();
            }
        }
    }

    /**
     * Call of the controller
     *
     * @param Request $request
     * @return string
     */
    public function callController($request) : string
    {
        $pattern = array('(([a-zA-Z]+\W[a-zA-Z]+\W)([a-zA-Z]+)(\W+)([a-zA-Z]+))');
        $controllerName = array('$2');
        $methodName = array('$4');
        $subject = $this->getRouteFromRequest($request);
        
        $controller = preg_replace($pattern, $controllerName, $subject);
        $method = preg_replace($pattern, $methodName, $subject);

        return 'Controller : ' . $controller . ' - Method : ' . $method;
    }
}