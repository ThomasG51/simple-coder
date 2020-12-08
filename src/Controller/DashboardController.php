<?php


namespace App\Controller;


use App\Repository\UserRepository;
use Lib\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends AbstractController
{
    private UserRepository $userManager;

    /**
     * DashboardController constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->userManager = new UserRepository();
    }


    public function manageUser()
    {
        return $this->render('dashboard/user.html.twig', [
            'users' => $this->userManager->findAll()
        ]);
    }
}