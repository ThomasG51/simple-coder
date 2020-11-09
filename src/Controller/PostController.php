<?php


namespace App\Controller;


use App\Repository\PostRepository;
use Lib\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class PostController extends AbstractController
{
    /**
     * Create post
     *
     * @return Response
     */
    public function create(): Response
    {
        return $this->render('post/create.html.twig', []);
    }


    /**
     * Show one post
     *
     * @param $slug
     * @return Response
     */
    public function show($slug): Response
    {
        $postManager = new PostRepository();
        $post = $postManager->findOne($slug);

        return $this->render('post/show.html.twig', [
            'post' => $post
        ]);
    }
}