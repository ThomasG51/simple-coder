<?php


namespace App\Controller;


use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Repository\TagsRepository;
use Lib\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends AbstractController
{
    /**
     * Create new post
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
            $coverName = uniqid('upload-', TRUE).'.'.pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
            $category = $request->request->get('category');
            $slug = $this->formatSlug($title);

            $postManager = new PostRepository();

            if ($postManager->findNextId() != null)
            {
                $nextId = $postManager->findNextId();
            }
            else
            {
                $nextId = 0;
            }

            $postManager->create($nextId, $title, $coverName, $content, $slug,1, $category);

            $this->uploadFile($_FILES['cover'], $coverName);

            // Save TagsLine
            // $tagsLineController = new TagsLineController();
            // $tagsLineController->create($tags_id, $nextId);

            return $this->redirectToRoute('/');
        }

        $categoryManager = new CategoryRepository();
        $tagsManager = new TagsRepository();

        return $this->render('post/create.html.twig', [
            'categories' => $categoryManager->findAll(),
            'tags' => $tagsManager->findAll()
        ]);
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

        return $this->render('post/show.html.twig', [
            'post' => $postManager->findOne($slug)
        ]);
    }
}