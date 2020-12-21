<?php


namespace App\Controller;


use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Lib\AbstractController;
use App\Validators\CreateCommentValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends AbstractController
{
    private CommentRepository $commentManager;

    private PostRepository $postManager;


    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->commentManager = new CommentRepository();
        $this->postManager = new PostRepository();
    }


    /**
     * Create new comment
     *
     * @return Response
     */
    public function create() : Response
    {
        if($this->request->getMethod() === 'POST')
        {
            $this->checkRole('USER');

            $commentValidator = new CreateCommentValidator();
            $commentValidator->validate($this->request);
            // TODO limit max lenght

            if(empty($commentValidator->getErrors()))
            {
                $comment = new Comment();
                $comment->setText($this->request->request->get('text'));
                $comment->setStatus('available');
                $comment->setUser($this->session->get('user'));
                $comment->setPost($this->postManager->findOne($this->request->request->get('post')));

                $this->commentManager->create($comment);

                // TODO pass errors through redirect method
                return $this->redirectToRoute('/post/' . $this->request->request->get('post'));
            }
        }

        $this->session->getFlashBag()->add('alert', ['danger' => 'Impossible de créer un commentaire, le formulaire n\'a pas été soumis']);

        return $this->redirectToRoute('/');
    }


    /**
     * Delete comment
     *
     * @param int $id
     * @return Response
     */
    public function delete(int $id) : Response
    {
        // TODO delete only own comments

        if($this->request->getMethod() != 'POST')
        {
            $this->session->getFlashBag()->add('alert', ['danger' => 'Suppression du commentaire impossible, le formulaire n\'a pas été soumis']);

            return $this->redirectToRoute('/');
        }

        $comment = $this->commentManager->findOne($id);

        if($comment === null)
        {
            $this->session->getFlashBag()->add('alert', ['danger' => 'Le commentaire est introuvable, suppression impossible']);

            return $this->redirectToRoute('/');
        }

        if($this->request->request->get('csrf_token') === $this->session->get('csrf_token'))
        {
            $this->commentManager->delete($comment);

            $this->session->getFlashBag()->add('alert', ['success' => 'Suppression du commentaire éffectué']);

            return $this->redirectToRoute('/dashboard/comment');
        }

        $this->session->getFlashBag()->add('alert', ['danger' => 'Token expiré']);

        return $this->redirectToRoute('/');
    }


    /**
     * Report comment
     *
     * @param int $id
     * @return Response
     */
    public function report(int $id) : Response
    {
        $comment = $this->commentManager->findOne($id);

        if ($comment === null)
        {
            $this->session->getFlashBag()->add('alert', ['danger' => 'Commentaire introuvable, signalemenet impossible']);

            return $this->redirectToRoute('/');
        }

        if($comment->getStatus() === 'available')
        {
            $comment->setStatus('reported');
            $this->session->getFlashBag()->add('alert', ['success' => 'Commentaire signalé']);
        }
        else
        {
            $this->checkRole('ADMIN');

            $comment->setStatus('available');
            $this->session->getFlashBag()->add('alert', ['success' => 'Commentaire remis en ligne']);
        }

        $this->commentManager->report($comment);

        return $this->redirectToRoute('/post/' . $comment->getPost()->getSlug());
    }
}