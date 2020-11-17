<?php


namespace App\Controller;


use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Repository\TagsLineRepository;
use App\Repository\TagsRepository;
use Lib\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends AbstractController
{
    /**
     * @var PostRepository
     */
    private $postManager;

    /**
     * @var CategoryRepository
     */
    private $categoryManager;

    /**
     * @var TagsRepository
     */
    private $tagsManager;

    /**
     * @var TagsLineRepository
     */
    private $tagsLineManager;


    /**
     * PostController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->postManager = new PostRepository();
        $this->categoryManager = new CategoryRepository();
        $this->tagsManager = new TagsRepository();
        $this->tagsLineManager = new TagsLineRepository();
    }


    /**
     * Create new post
     *
     * @return Response
     */
    public function create(): Response
    {
        if(
            $this->request->getMethod() == 'POST' &&
            !empty($this->request->request->get('title')) &&
            !empty($this->request->request->get('content')) &&
            !empty($this->request->files->get('cover')) &&
            !empty($this->request->request->get('category'))
        ) {
            $title = $this->request->request->get('title');
            $content = $this->request->request->get('content');
            $category = $this->request->request->get('category');
            $tags = $this->request->request->get('tags');
            $slug = $this->formatSlug($title);
            $coverName = $this->uploadFile($this->request->files->get('cover'));

            $this->postManager->create($title, $coverName, $content, $slug,1, $category);

            foreach($tags as $tag)
            {
                $this->tagsLineManager->create($tag, $this->postManager->getLastId('post'));
            }

            return $this->redirectToRoute('/');
        }

        return $this->render('post/create.html.twig', [
            'categories' => $this->categoryManager->findAll(),
            'tags' => $this->tagsManager->findAll()
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
        return $this->render('post/show.html.twig', [
            'post' => $this->postManager->findOne($slug),
            'tags' => $this->tagsLineManager->findTagsByPost($slug)
        ]);
    }
}