<?php


namespace App\Controller;


use Lib\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class ErrorsController extends AbstractController
{
    public function __construct(Request $request, Session $session)
    {
        parent::__construct($request, $session);
    }

    public function renderException(int $code, string $message)
    {
        return $this->render('errors.html.twig', [
            'code' => $code,
            'message' => $message
        ]);
    }
}