<?php


namespace App\Controller;


use App\Repository\TagsRepository;
use Lib\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TagsController extends AbstractController
{
    private TagsRepository $tagsManager;


    /**
     * TagsController constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->tagsManager = new TagsRepository();
    }


    /**
     * Create new tag
     *
     * @return JsonResponse
     */
    public function create() : JsonResponse
    {
        $this->checkIfConnected();
        $this->checkIfAdmin();

        if($this->request->getMethod() == 'POST')
        {
            $tags = $this->request->request->get('name');

            if(empty($tags))
            {
                return new JsonResponse(['error' => 'Veuillez remplir le formulaire']);
            }

            if($this->tagsManager->findOne($tags))
            {
                return new JsonResponse(['error' => 'Le tag existe déjà.']);
            }

            $this->tagsManager->create($tags);

            return new JsonResponse($this->tagsManager->findLast());
        }

        $this->session->getFlashBag()->add('alert', ['danger' => 'Erreur lors de la création du tag, la formulaire n\'a pas été soumis']);

        return $this->redirectToRoute('/');
    }


    /**
     * Delete tags
     *
     * @param string $name
     * @return Response
     */
    public function delete(string $name) : Response
    {
        $this->checkIfConnected();
        $this->checkIfAdmin();

        if($this->request->getMethod() != 'POST')
        {
            $this->session->getFlashBag()->add('alert', ['danger' => 'Suppression du tag impossible, le formulaire n\'a pas été soumis']);
            // TODO Throw error 400 bad request

            return $this->redirectToRoute('/');
        }

        $tags = $this->tagsManager->findOne($name);

        if($tags === null)
        {
            $this->session->getFlashBag()->add('alert', ['danger' => 'Le tag est introuvable, suppression impossible']);

            return $this->redirectToRoute('/');
        }

        if($this->request->request->get('csrf_token') === $this->session->get('csrf_token'))
        {
            $this->tagsManager->delete($tags);

            $this->session->getFlashBag()->add('alert', ['success' => 'Suppression du tag éffectué']);

            return $this->redirectToRoute('/dashboard/tags');
        }

        $this->session->getFlashBag()->add('alert', ['danger' => 'Token expiré']);

        return $this->redirectToRoute('/');
    }
}