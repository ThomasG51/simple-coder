<?php


namespace App\Controller;


use App\Repository\PostRepository;
use Lib\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    public function index(): Response
    {
        $postManager = new PostRepository();

        $countPosts = $postManager->countPost();
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
            'posts' => $postManager->findAll(($currentPage-1) * $perPage, $perPage),
            'pagination' => $countPages
        ]);
    }

    // TODO Search post
}