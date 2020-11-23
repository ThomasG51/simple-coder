<?php


namespace App\Controller;


use App\Repository\CategoryRepository;
use Lib\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends AbstractController
{
    /**
     * @var CategoryRepository
     */
    private $categoryManager;


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
        if($this->request->request->get('name'))
        {
            $category = $this->request->request->get('name');

            if($this->categoryManager->findOne($this->request->request->get('name')))
            {
                dd('La catégorie existe déjà.');
            }

            $this->categoryManager->create($category);

            return new JsonResponse($this->categoryManager->findLast());
        }
    }
}