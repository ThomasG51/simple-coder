<?php


namespace App\Controller;


use App\Repository\PostRepository;
use Lib\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    public function index(): Response
    {
        $postManager = new PostRepository();

        $posts = $postManager->findAll();

        return $this->render('home/index.html.twig', [
            'posts' => $posts
        ]);
    }
}