<?php


namespace App\Validators;


use Assert\Assert;
use Assert\LazyAssertionException;
use Lib\Interfaces\Validators;
use Symfony\Component\HttpFoundation\Request;

class CreatePostValidator implements Validators
{
    private array $errors = [];

    public function validate(Request $request)
    {
        try
        {
            Assert::lazy()->tryAll()
                ->that($request->request->get('title'), 'title')
                    ->notEmpty('Veuillez remplir le titre.')
                ->that($request->request->get('content'), 'content')
                    ->notEmpty('Veuillez remplir le contenu.')
                ->that($request->files->get('cover'), 'cover')
                    ->notEmpty('Veuillez selectionner une couverture.')
                ->that($request->request->get('category'), 'category')
                    ->notEmpty('Veuillez selectionner une catégorie.')
                ->that($request->request->get('tags'), 'tags')
                    ->notEmpty('Veuillez cocher des tags.')
                ->verifyNow();
        }
        catch(LazyAssertionException $exceptions)
        {
            foreach($exceptions->getErrorExceptions() as $exception)
            {
                $this->errors += [$exception->getPropertyPath() => $exception->getMessage()];
            }
        }
    }


    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}