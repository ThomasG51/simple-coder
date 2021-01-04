<?php


namespace App\Controller;


use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Lib\AbstractController;
use App\Validators\CreateCommentValidator;
use Lib\Exceptions\BadRequestException;
use Lib\Exceptions\NotFoundException;
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
        $this->checkIfConnected();

        if($this->request->getMethod() === 'POST')
        {
            $commentValidator = new CreateCommentValidator();
            $commentValidator->validate($this->request);

            if(empty($commentValidator->getErrors()))
            {
                $comment = new Comment();
                $comment->setText($this->request->request->get('text'));
                $comment->setStatus('available');
                $comment->setUser($this->session->get('user'));
                $comment->setPost($this->postManager->findOne($this->request->request->get('post')));

                $this->commentManager->create($comment);

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
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function delete(int $id) : Response
    {
        $this->checkIfConnected();
        $this->checkIfAdmin();

        if($this->request->getMethod() != 'POST')
        {
            throw new BadRequestException('Le formulaire n\'a pas été soumis', 400);
        }

        $comment = $this->commentManager->findOne($id);

        if($comment === null)
        {
            throw new NotFoundException('Commentaire introuvable', 404);
        }

        $this->checkTokenCsrf();

        $this->commentManager->delete($comment);

        $this->session->getFlashBag()->add('alert', ['success' => 'Suppression du commentaire éffectué']);

        return $this->redirectToRoute('/dashboard/comment');
    }


    /**
     * Report comment
     *
     * @param int $id
     * @return Response
     * @throws NotFoundException
     */
    public function report(int $id) : Response
    {
        $comment = $this->commentManager->findOne($id);

        if ($comment === null)
        {
            throw new NotFoundException('Commentaire introuvable', 404);
        }

        if($comment->getStatus() === 'available')
        {
            $comment->setStatus('reported');
            $this->session->getFlashBag()->add('alert', ['success' => 'Commentaire signalé']);
        }
        else
        {
            $this->checkIfAdmin();

            $comment->setStatus('available');
            $this->session->getFlashBag()->add('alert', ['success' => 'Commentaire remis en ligne']);
        }

        $this->commentManager->report($comment);

        return $this->redirectToRoute('/post/' . $comment->getPost()->getSlug());
    }
}