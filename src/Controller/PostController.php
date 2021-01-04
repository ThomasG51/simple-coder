<?php


namespace App\Controller;


use App\Entity\Post;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\LikesRepository;
use App\Repository\PinRepository;
use App\Repository\PostRepository;
use App\Repository\TagsLineRepository;
use App\Repository\TagsRepository;
use App\Validators\CreatePostValidator;
use Lib\AbstractController;
use Lib\Exceptions\BadRequestException;
use Lib\Exceptions\NotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    private LikesRepository $likesManager;


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
        $this->likesManager = new LikesRepository();
    }


    /**
     * Create new post
     *
     * @return Response
     */
    public function create(): Response
    {
        $this->checkIfConnected();
        $this->checkIfAdmin();

        $postValidator = new CreatePostValidator();

        if($this->request->getMethod() == 'POST')
        {
            $postValidator->validate($this->request);

            if(empty($postValidator->getErrors()))
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
            'error_post' => $postValidator->getErrors()
        ]);
    }


    /**
     * Show one post
     *
     * @param string $slug
     * @return Response
     * @throws NotFoundException
     */
    public function show(string $slug): Response
    {
        if($this->postManager->findOne($slug) === null)
        {
            throw new NotFoundException('Article introuvable', 404);
        }

        if($this->session->get('user') != null)
        {
            $pin = $this->pinManager->findOne($this->session->get('user'), $this->postManager->findOne($slug));
            $like = $this->likesManager->findOne($this->session->get('user'), $this->postManager->findOne($slug));
        }
        else
        {
            $pin = null;
            $like = null;
        }

        return $this->render('post/show.html.twig', [
            'post' => $this->postManager->findOne($slug),
            'comments' => $this->commentManager->findByPost($this->postManager->findOne($slug)),
            'tags' => $this->tagsLineManager->findTagsByPost($slug),
            'pin' => $pin,
            'like' => $like,
            'count_like' => $this->likesManager->countByPost($this->postManager->findOne($slug))
        ]);

        // TODO show next post
        // TODO show previous post
    }


    /**
     * Update Post
     *
     * @param string $slug
     * @return Response
     * @throws NotFoundException
     */
    public function update(string $slug) : Response
    {
        $this->checkIfConnected();
        $this->checkIfAdmin();

        $post = $this->postManager->findOne($slug);

        if($post === null)
        {
            throw new NotFoundException('Article introuvable', 404);
        }

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


    /**
     * Delete post
     *
     * @param string $slug
     * @return Response
     */
    public function delete(string $slug) : Response
    {
        $this->checkIfConnected();
        $this->checkIfAdmin();

        $post = $this->postManager->findOne($slug);

        if($post === null)
        {
            throw new NotFoundException('Article introuvable', 404);
        }

        $this->checkTokenCsrf();

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
        $this->checkIfConnected();
        $this->checkIfAdmin();

        $post = $this->postManager->findOne($slug);

        if($post === null)
        {
            throw new NotFoundException('Article introuvable', 404);
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
        $this->checkIfConnected();

        $post = $this->postManager->findOne($slug);
        $user = $this->session->get('user');

        if($post === null)
        {
            throw new NotFoundException('Article introuvable', 404);
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


    /**
     * Like post
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function liked(string $slug)
    {
        $this->checkIfConnected();

        $user = $this->session->get('user');
        $post = $this->postManager->findOne($slug);

        if($post === null)
        {
            throw new NotFoundException('Article introuvable', 404);
        }

        if($this->likesManager->findOne($user, $post) != null)
        {
            $this->likesManager->delete($user, $post);

            return new JsonResponse('unlike');
        }

        $this->likesManager->create($user, $post);

        return new JsonResponse('like');
    }


    /**
     * Show post by category
     *
     * @param string $slug
     * @return Response
     */
    public function showByCategory(string $slug) : Response
    {
        return $this->render('/post/sort.html.twig', [
            'posts' => $this->postManager->findByCategory($this->categoryManager->findOne($slug)),
            'category' => $this->categoryManager->findOne($slug),
            'type' => 'category'
        ]);
    }


    /**
     * Show post pinned
     *
     * @return Response
     * @throws \Exception
     */
    public function showByPin() : Response
    {
        $this->checkIfConnected();

        return $this->render('post/sort.html.twig', [
            'posts' => $this->postManager->findPinByUser($this->session->get('user')),
            'type' => 'pin'
        ]);
    }
}