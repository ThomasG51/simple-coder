<?php


namespace App\Controller;


use App\Repository\CategoryRepository;
use Lib\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends AbstractController
{
    /**
     * Create new category
     *
     * @return Response
     */
    public function create() : Response
    {
        $request = Request::createFromGlobals();

        if($request->request->get('name'))
        {
            $category = $request->request->get('name');

            $categoryManager = new CategoryRepository();

            if($categoryManager->findOne($request->request->get('name')))
            {
                dd('La catégorie existe déjà.');
            }

            $categoryManager->create($category);
        }

        return $this->redirectToRoute('/post/create');
    }
}