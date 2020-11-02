<?php

namespace Lib;


use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;


abstract class AbstractController
{
    public function render(string $view, array $params): Response
    {
        $loader = new FilesystemLoader(__DIR__ . '/../templates');
        $twig = new Environment($loader, [
            'cache' => false
        ]);

        $twig->addFunction(new TwigFunction('asset', function ($asset) {
            return sprintf('../assets/%s', ltrim($asset, '/'));
        }));

        $render = $twig->render($view, $params);

        return new Response($render, 200, []);
    }
}