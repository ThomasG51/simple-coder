<?php


namespace App\Controller;


use App\Repository\PostRepository;
use Lib\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    private PostRepository $postManager;


    /**
     * HomeController constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->postManager = new PostRepository();
    }


    /**
     * Home page
     *
     * @return Response
     * @throws \Exception
     */
    public function index(): Response
    {
        $countPosts = $this->postManager->countItems('post');
        $perPage = 2;
        $countPages = ceil($countPosts / $perPage);

        if($this->request->query->get('page') && $this->request->query->get('page') > 0 && $this->request->query->get('page') <= $countPages)
        {
            $currentPage = $this->request->query->get('page');
        }
        else if($this->request->query->get('page') && $this->request->query->get('page') > $countPages)
        {
            throw new \Exception('La page demandée n\'existe pas', 404);
        }
        else
        {
            $currentPage = 1;
        }

        return $this->render('home/index.html.twig', [
            'posts' => $this->postManager->findAll(($currentPage-1) * $perPage, $perPage),
            'pagination' => $countPages
        ]);
    }

    // TODO Search post
    public function search() : Response
    {
        return $this->render('post/sort.html.twig', [
            'posts' => $this->postManager->search($this->request->request->get('search')),
            'type' => 'search'
        ]);
    }
}