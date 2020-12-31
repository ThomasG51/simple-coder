<?php


namespace App\Controller;


use App\Repository\TagsRepository;
use Lib\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
}