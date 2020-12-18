<?php


namespace App\Controller;


use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Lib\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends AbstractController
{
    private UserRepository $userManager;

    private PostRepository $postManager;

    private CommentRepository $commentManager;


    /**
     * DashboardController constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->userManager = new UserRepository();
        $this->postManager = new PostRepository();
        $this->commentManager = new CommentRepository();
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
        return $this->render('dashboard/post.html.twig', [
            'posts' => $this->postManager->findAll()
        ]);
    }


    public function manageComment() : Response
    {
        return $this->render('/dashboard/comment.html.twig', [
            'comments' => $this->commentManager->findAll()
        ]);
    }
}