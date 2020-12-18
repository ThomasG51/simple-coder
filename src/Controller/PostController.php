<?php


namespace App\Controller;


use App\Entity\Post;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\PinRepository;
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

    private PinRepository $pinManager;

    private CommentRepository $commentManager;


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
        $this->pinManager = new PinRepository();
        $this->commentManager = new CommentRepository();
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
            // TODO refactor with validator

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
                $post->setStatus('available');
                $post->setUser($this->session->get('user'));
                $post->setCategory($this->categoryManager->findOne($this->request->request->get('category')));
                $post->setTags($this->request->request->get('tags'));

                $this->postManager->create($post);

                foreach ($post->getTags() as $tag) {
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
        if($this->postManager->findOne($slug) === null)
        {
            $this->session->getFlashBag()->add('alert', ['danger' => 'Post introuvable']);

            return $this->redirectToRoute('/');
        }

        return $this->render('post/show.html.twig', [
            'post' => $this->postManager->findOne($slug),
            'tags' => $this->tagsLineManager->findTagsByPost($slug),
            'pin' => $this->pinManager->findOne($this->session->get('user'), $this->postManager->findOne($slug)),
            'comments' => $this->commentManager->findByPost($this->postManager->findOne($slug))
        ]);
    }


    /**
     * Update Post
     *
     * @param string $slug
     * @return Response
     */
    public function update(string $slug) : Response
    {
        $post = $this->postManager->findOne($slug);

        if($post)
        {
            if($this->request->getMethod() == 'POST')
            {
                $post->setTitle($this->request->request->get('title'));
                $post->setText($this->request->request->get('content'));
                $post->setCategory($this->categoryManager->findOne($this->request->request->get('category')));
                $post->setTags($this->tagsLineManager->findTagsByPost($post->getSlug()));

                if($this->request->files->get('cover') != null)
                {
                    unlink('upload/' . $post->getCover());
                    $post->setCover($this->uploadFile($this->request->files->get('cover')));
                }
                else
                {
                    $post->setCover($post->getCover());
                }

                $this->postManager->update($post);

                // Remove tags_line
                foreach($post->getTags() as $tag)
                {
                    $this->tagsLineManager->delete($tag->getId());
                }

                // Create new tags_line
                foreach($this->request->request->get('tags') as $tag)
                {
                    $this->tagsLineManager->create($tag, $post->getId());
                }

                return $this->redirectToRoute('/post/'. $post->getSlug());
            }

            return $this->render('post/update.html.twig', [
                'post' => $post,
                'categories' => $this->categoryManager->findAll(),
                'tags' => $this->tagsManager->findAll()
            ]);
        }

        $this->session->getFlashBag()->add('alert', ['danger' => 'Post introuvable, modification impossible']);

        return $this->redirectToRoute('/dashboard/post');
    }


    /**
     * Delete post
     *
     * @param string $slug
     * @return Response
     */
    public function delete(string $slug) : Response
    {
        $post = $this->postManager->findOne($slug);

        if($post === null)
        {
            $this->session->getFlashBag()->add('alert', ['danger' => 'Post introuvable, suppression impossible !']);

            return $this->redirectToRoute('/dashboard/post');
        }

        unlink('upload/' . $post->getCover());
        $this->postManager->delete($post->getSlug());

        $this->session->getFlashBag()->add('alert', ['success' => 'Post supprimé !']);

        return $this->redirectToRoute('/dashboard/post');
    }


    /**
     * Post archiving
     *
     * @param string $slug
     * @return Response
     */
    public function archiving(string $slug) : Response
    {
        $post = $this->postManager->findOne($slug);

        if($post === null)
        {
            $this->session->getFlashBag()->add('alert', ['danger' => 'Post introuvable, archivage impossible !']);

            return $this->redirectToRoute('/dashboard/post');
        }

        if($post->getStatus() == 'archived')
        {
            $post->setStatus('available');

            $this->session->getFlashBag()->add('alert', ['success' => 'Remise en ligne éffectuée.']);
        }
        else
        {
            $post->setStatus('archived');

            $this->session->getFlashBag()->add('alert', ['success' => 'Archivage éffectué.']);
        }

        $this->postManager->archiving($post);

        return $this->redirectToRoute('/dashboard/post');
    }


    /**
     * Pin post
     *
     * @param string $slug
     * @return Response
     */
    public function pin(string $slug) : Response
    {
        $post = $this->postManager->findOne($slug);
        $user = $this->session->get('user');

        if($post === null)
        {
            $this->session->getFlashBag()->add('alert', ['danger' => 'Post introuvable, impossible d\'épingler le post']);

            return $this->redirectToRoute('/');
        }

        if(!empty($this->pinManager->findOne($user, $post)))
        {
            $this->pinManager->delete($user, $post);
            $this->session->getFlashBag()->add('alert', ['success' => 'L\'épingle a bien été supprimée']);

            return $this->redirectToRoute('/post/' . $slug);
        }

        $this->pinManager->create($user, $post);
        $this->session->getFlashBag()->add('alert', ['success' => 'Le billet a bien été épingler']);

        return $this->redirectToRoute('/post/' . $slug);
    }


    // TODO show post by category
}