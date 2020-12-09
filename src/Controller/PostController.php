<?php


namespace App\Controller;


use App\Entity\Post;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Repository\TagsLineRepository;
use App\Repository\TagsRepository;
use Lib\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends AbstractController
{
    private PostRepository $postManager;

    private CategoryRepository $categoryManager;

    private TagsRepository $tagsManager;

    private TagsLineRepository $tagsLineManager;


    /**
     * PostController constructor.
     *
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
                $post = new Post();
                $post->setTitle($this->request->request->get('title'));
                $post->setCover($this->uploadFile($this->request->files->get('cover')));
                $post->setText($this->request->request->get('content'));
                $post->setSlug($this->formatSlug($this->request->request->get('title')));
                $post->setStatus('availaible');
                $post->setUser($this->session->get('user'));
                $post->setCategory($this->categoryManager->findOne($this->request->request->get('category')));

                $this->postManager->create($post);

                // TODO utiliser les entity tags dans cette boucle ? interet ?
                foreach ($this->request->request->get('tags') as $tag) {
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

    // TODO Make Update Method
    // TODO Make Delete Method
}