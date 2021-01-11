<?php


namespace App\Controller;


use App\Repository\PostRepository;
use Lib\AbstractController;
use Lib\Exceptions\BadRequestException;
use Lib\Exceptions\ForbiddenException;
use Lib\mailer\ContactMail;
use Swift_Message;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class HomeController extends AbstractController
{
    private PostRepository $postManager;


    /**
     * HomeController constructor.
     *
     * @param Request $request
     * @param Session $session
     */
    public function __construct(Request $request, Session $session)
    {
        parent::__construct($request, $session);

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
        else
        {
            $currentPage = 1;
        }

        return $this->render('home/index.html.twig', [
            'posts' => $this->postManager->findAll(($currentPage-1) * $perPage, $perPage),
            'pagination' => $countPages
        ]);
    }


    /**
     * Search post
     *
     * @return Response
     * @throws BadRequestException
     */
    public function search() : Response
    {
        if($this->request->getMethod() != 'POST')
        {
            throw new BadRequestException('Le formulaire n\'a pas été soumis', 400);
        }

        return $this->render('post/sort.html.twig', [
            'posts' => $this->postManager->search($this->request->request->get('search')),
            'type' => 'search'
        ]);
    }


    /**
     * Send contact mail
     *
     * @return Response
     * @throws BadRequestException
     * @throws ForbiddenException
     */
    public function sendMail() : Response
    {
        if($this->request->getMethod() != 'POST')
        {
            throw new BadRequestException('Le formulaire n\'a pas ete soumis', 400);
        }

        if($this->request->request->get('honeypot') != null)
        {
            throw new ForbiddenException('No spam, Thanks!', 403);
        }

        if($this->request->request->get('agree') === null)
        {
            $this->session->getFlashBag()->add('alert', ['danger' => 'Veuillez cocher la case concernant l\'utilisation de vos données.']);
            return $this->redirectToRoute('/');
        }

        $message = (new Swift_Message($this->request->request->get('subject')))
            ->setFrom([$this->request->request->get('from')])
            ->setTo(['hello@simplecoder.fr'])
            ->setContentType("text/html")
            ->setBody('
                <html>
                    <head>
                        <meta charset="UTF-8">
                        <style>
                        * {
                            margin: 0;
                            padding: 0;
                            box-sizing: border-box;
                        }
                        </style>
                    </head>
                    <body style="width: 100vw;" class="d-flex flex-row align-items-center">
                        <div style="background-color: #505c90; color: #ffffff; width: 100vw">
                            <h1 style="text-align: center; padding-top: 20px; padding-bottom: 20px;">Vous avez reçu un message!</h1>
                        </div>
                    
                        <div style="width: 80vw; margin: auto; margin-top: 60px">
                            <h4 style="margin-bottom: 8px;">De: ' . $this->request->request->get('from') . '</h4>
    
                            <h4 style="margin-top: 40px; margin-bottom: 8px;">Message:</h4>
                            <p style="text-align: justify;">' . $this->request->request->get('message') . '</p>
                        </div>
                    
                        <div style="position: fixed; bottom: 0; left: 10vw; width: 80vw; border-top: 1px solid #505c90;">
                            <p style="text-align: center; margin-top: 20px; margin-bottom: 20px;">Copyright &copy; 2021 simplecoder.com</p>
                        </div>
                    </body>
                </html>
            ')
        ;

        if($this->mailer()->send($message))
        {
            $this->session->getFlashBag()->add('alert', ['success' => 'Votre email a bien été envoyé.']);
            return $this->redirectToRoute('/');
        }
        else
        {
            throw new BadRequestException('Votre e-mail n\'a pas été envoyé', 400);
        }
    }
}