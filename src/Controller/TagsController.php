<?php


namespace App\Controller;


use App\Repository\TagsRepository;
use Lib\AbstractController;
use Lib\Exceptions\BadRequestException;
use Lib\Exceptions\NotFoundException;
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
     * @throws BadRequestException
     */
    public function create() : JsonResponse
    {
        $this->checkIfConnected();
        $this->checkIfAdmin();

        if($this->request->getMethod() != 'POST')
        {
            throw new BadRequestException('Le formulaire n\'a pas été soumis', 400);
        }

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


    /**
     * Delete tags
     *
     * @param string $name
     * @return Response
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function delete(string $name) : Response
    {
        $this->checkIfConnected();
        $this->checkIfAdmin();

        if($this->request->getMethod() != 'POST')
        {
            throw new BadRequestException('Le formulaire n\'a pas été soumis', 400);
        }

        $tags = $this->tagsManager->findOne($name);

        if($tags === null)
        {
            throw new NotFoundException('Tag introuvable', 404);
        }

        $this->checkTokenCsrf();

        $this->tagsManager->delete($tags);

        $this->session->getFlashBag()->add('alert', ['success' => 'Suppression du tag éffectué']);

        return $this->redirectToRoute('/dashboard/tags');
    }
}