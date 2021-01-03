<?php


namespace App\Controller;


use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\TagsRepository;
use App\Repository\UserRepository;
use Lib\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends AbstractController
{
    private UserRepository $userManager;

    private PostRepository $postManager;

    private CommentRepository $commentManager;

    private TagsRepository $tagsManager;

    private CategoryRepository $categoryManager;


    /**
     * DashboardController constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->setTokenCsrf();

        $this->checkIfConnected();
        $this->checkIfAdmin();

        $this->userManager = new UserRepository();
        $this->postManager = new PostRepository();
        $this->commentManager = new CommentRepository();
        $this->tagsManager = new TagsRepository();
        $this->categoryManager = new CategoryRepository();
    }


    /**
     * User Management
     *
     * @return Response
     */
    public function manageUser() : Response
    {
        return $this->render('dashboard/user.html.twig', [
            'users' => $this->userManager->findAll()
        ]);
    }


    /**
     * Post Management
     *
     * @return Response
     */
    public function managePost() : Response
    {
        $posts = $this->postManager->countItems('post');
        $perPage = 10;
        $pages = ceil($posts / $perPage);
        $currentPage = 1;

        return $this->render('dashboard/post.html.twig', [
            'posts' => $this->postManager->findAll((($currentPage - 1) * $perPage),  $perPage),
            'pagination' => $pages
        ]);
    }


    /**
     * Comment Management
     *
     * @return Response
     */
    public function manageComment() : Response
    {
        return $this->render('/dashboard/comment.html.twig', [
            'comments' => $this->commentManager->findAll()
        ]);
    }


    /**
     * Tags Management
     *
     * @return Response
     */
    public function manageTags() : Response
    {
        return $this->render('dashboard/tags.html.twig', [
            'tags' => $this->tagsManager->findAll()
        ]);
    }


    /**
     * Category Management
     *
     * @return Response
     */
    public function manageCategory() : Response
    {
        return $this->render('dashboard/category.html.twig', [
            'categories' => $this->categoryManager->findAll()
        ]);
    }
}