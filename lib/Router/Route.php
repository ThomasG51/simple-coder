<?php

namespace Lib\Router;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Route
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $controller;

    /**
     * @var string
     */
    private $pattern;


    public function __construct(string $name, string $path, string $controller)
    {
        $this->setName($name);
        $this->setPath($path);
        $this->setController($controller);

        $pattern = str_replace('/', '\/', $this->getPath());
        $pattern = sprintf('/^%s$/', $pattern);

        $this->setPattern(preg_replace("/(\{[a-z0-9\-]+\})/", "([a-z0-9\-]+)", $pattern));
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getPath() : string
    {
        return $this->path;
    }

    public function setPath(string $path)
    {
        $this->path = $path;
    }

    public function getController() : string
    {
        return $this->controller;
    }

    public function setController(string $controller)
    {
        $this->controller = $controller;
    }

    public function getPattern() : string
    {
        return $this->pattern;
    }

    public function setPattern(string $pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function checkMatch(Request $request) : bool
    {
        return preg_match($this->getPattern(), $request->getPathInfo());
    }


    /**
     * @param Request $request
     */
    public function run(Request $request)
    {
        preg_match($this->pattern, $request->getPathInfo(), $matches);

        list($controller, $method) = explode("::", $this->getController());

        array_shift($matches);

        $controller = new $controller();

        return call_user_func_array([$controller, $method], $matches);
    }
} 