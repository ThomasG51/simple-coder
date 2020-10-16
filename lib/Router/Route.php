<?php

namespace Lib\Router;

class Route {

    /**
     * Route name
     *
     * @var string
     */
    private $name;

    /**
     * Route path
     *
     * @var string
     */
    private $path;

    /**
     * Controller called
     *
     * @var [type]
     */
    private $controller;

    public function __construct($name, $path, $controller)
    {
        $this->name = $name;
        $this->path = $path;
        $this->controller = $controller;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getController()
    {
        return $this->controller;
    }
}