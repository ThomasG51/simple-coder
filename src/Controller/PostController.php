<?php


namespace App\Controller;


use App\Repository\PostRepository;
use Lib\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
        $request = Request::createFromGlobals();

        if($request->request->get('title'))
        {
            $title = $request->request->get('title');
            $content = $request->request->get('content');
            $slug = $this->formatSlug($title);
            $coverName = uniqid('upload-', TRUE).'.'.pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);

            $this->uploadFile($_FILES['cover'], $coverName);

            $postManager = new PostRepository();
            $postManager->create($title, $coverName, $content, $slug,1, 1);

            return $this->redirectToRoute('/');
        }

        return $this->render('post/create.html.twig', []);
    }


    /**
     * Show one post
     *
     * @param string $slug
     * @return Response
     */
    public function show(string $slug): Response
    {
        $postManager = new PostRepository();
        $post = $postManager->findOne($slug);

        return $this->render('post/show.html.twig', [
            'post' => $post
        ]);
    }
}