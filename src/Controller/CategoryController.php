<?php


namespace App\Controller;


use App\Repository\CategoryRepository;
use Lib\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends AbstractController
{
    private CategoryRepository $categoryManager;


    /**
     * CategoryController constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

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

        if($this->request->getMethod() == 'POST')
        {
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

        $this->session->getFlashBag()->add('alert', ['danger' => 'Erreur lors de la création de la catégorie, la formulaire n\'a pas été soumis']);

        return $this->redirectToRoute('/');
    }
}