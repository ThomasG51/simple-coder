<?php


namespace App\Controller;


use App\Repository\TagsRepository;
use Lib\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class TagsController extends AbstractController
{
    /**
     * Create new tag
     *
     * @return Response
     */
    public function create() : Response
    {
        $request = Request::createFromGlobals();

        if($request->request->get('name'))
        {
            $tags = $request->request->get('name');

            $tagsManager = new TagsRepository();

            if($tagsManager->findOne($request->request->get('name')))
            {
                dd('Le tag existe déjà.');
            }

            $tagsManager->create($tags);
        }

        return $this->redirectToRoute('/post/create');
    }
}