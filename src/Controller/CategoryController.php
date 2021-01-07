<?php


namespace App\Controller;


use App\Repository\CategoryRepository;
use Lib\AbstractController;
use Lib\Exceptions\BadRequestException;
use Lib\Exceptions\NotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class CategoryController extends AbstractController
{
    private CategoryRepository $categoryManager;


    /**
     * CategoryController constructor.
     *
     * @param Request $request
     * @param Session $session
     */
    public function __construct(Request $request, Session $session)
    {
        parent::__construct($request, $session);

        $this->categoryManager = new CategoryRepository();
    }


    /**
     * Create new category
     *
     * @return JsonResponse
     */
    public function create() : JsonResponse
    {
        $this->checkIfConnected();
        $this->checkIfAdmin();

        if($this->request->getMethod() != 'POST')
        {
            throw new BadRequestException('Le formulaire n\'a pas été soumis', 400);
        }

        $category = $this->request->request->get('name');

        if(empty($category))
        {
            return new JsonResponse(['error' => 'Veuillez remplir le formulaire']);
        }

        if($this->categoryManager->findOne($category))
        {
            return new JsonResponse(['error' => 'La catégorie existe déjà.']);
        }

        $this->categoryManager->create($category);

        return new JsonResponse($this->categoryManager->findLast());
    }


    /**
     * Delete category
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

        $category = $this->categoryManager->findOne($name);

        if($category === null)
        {
            throw new NotFoundException('Catégorie introuvable', 404);
        }

        $this->checkTokenCsrf();

        $this->categoryManager->delete($category);

        $this->session->getFlashBag()->add('alert', ['success' => 'Suppression de la catégorie éffectué']);

        return $this->redirectToRoute('/dashboard/category');
    }
}