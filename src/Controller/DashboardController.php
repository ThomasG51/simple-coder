<?php


namespace App\Controller;


use Lib\AbstractController;

class DashboardController extends AbstractController
{
    public function manageUser()
    {
        return $this->render('dashboard/user.html.twig', []);
    }
}