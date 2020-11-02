<?php


namespace App\Controller;


use Lib\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class PostController extends AbstractController
{
    public function create(): Response
    {
        return $this->render('post/create.html.twig', []);
    }

    public function show(): Response
    {
        return $this->render('post/show.html.twig', []);
    }
}