<?php


namespace App\Controller;


use App\Repository\PostRepository;
use Lib\AbstractController;
use Lib\Exceptions\BadRequestException;
use Lib\Exceptions\BadGatewayException;
use Lib\Exceptions\ForbiddenException;
use Lib\mailer\ContactMail;
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
     * @throws BadGatewayException
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

        $from = $this->request->request->get('from');
        $to = 'hello@simplecoder.fr';
        $subject = $this->request->request->get('subject');
        $message = $this->request->request->get('message');

        if(ContactMail::send($from, $to, $subject, $message))
        {
            $this->session->getFlashBag()->add('alert', ['success' => 'Votre email a bien été envoyé.']);
            return $this->redirectToRoute('/');
        }
        else
        {
            throw new BadGatewayException('Votre e-mail n\'a pas été envoyé', 502);
        }
    }
}