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
        $errorPost = [];

        if($this->request->getMethod() == 'POST')
        {
            if(empty($this->request->request->get('title')))
            {
                $errorPost += ['title' => 'Veuillez remplir le titre.'];
            }

            if(empty($this->request->request->get('content')))
            {
                $errorPost += ['content' => 'Veuillez remplir le contenu.'];
            }

            if(empty($this->request->files->get('cover')))
            {
                $errorPost += ['cover' => 'Veuillez selectionner une couverture.'];
            }

            if(empty($this->request->request->get('category')))
            {
                $errorPost += ['category' => 'Veuillez selectionner une catégorie.'];
            }

            if(empty($this->request->request->get('tags')))
            {
                $errorPost += ['tags' => 'Veuillez cocher des tags.'];
            }

            if(empty($errorPost))
            {
                $title = $this->request->request->get('title');
                $content = $this->request->request->get('content');
                $category = $this->request->request->get('category');
                $tags = $this->request->request->get('tags');
                $slug = $this->formatSlug($title);
                $coverName = $this->uploadFile($this->request->files->get('cover'));

                $this->postManager->create($title, $coverName, $content, $slug, 1, $category);

                foreach ($tags as $tag) {
                    $this->tagsLineManager->create($tag, $this->postManager->getLastId('post'));
                }

                return $this->redirectToRoute('/');
            }
        }

        return $this->render('post/create.html.twig', [
            'categories' => $this->categoryManager->findAll(),
            'tags' => $this->tagsManager->findAll(),
            'error_post' => $errorPost
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