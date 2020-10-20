<?php

namespace Lib\Router;

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
     * @var string[]
     */
    private $args;


    public function __construct(string $name, string $path, string $controller)
    {
        $this->setName($name);
        $this->setPath($path);
        $this->setController($controller);
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

    public function getArgs() : array
    {
        return $this->args;
    }

    public function setArgs(array $args)
    {
        $this->args = $args;
    }

    /**
     * Transform the route path into regex to check if it match with uri
     *
     * @param string $uri
     * @return boolean
     */
    public function checkMatch(string $uri): bool
    {
        $pattern = str_replace('/', '\/', $this->getPath());
        $pattern = sprintf('/^%s$/', $pattern);
        $pattern = preg_replace("/(\{[a-z0-9\-]+\})/", "([a-z0-9\-]+)", $pattern);
        
        $result = preg_match($pattern, $uri ,$matches);

        // Set route args
        array_shift($matches);
        $this->setArgs($matches);

        return $result;
    }
} 