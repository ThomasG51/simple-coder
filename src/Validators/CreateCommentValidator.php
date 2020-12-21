<?php


namespace App\Validators;


use Assert\Assert;
use Assert\LazyAssertionException;
use Lib\Interfaces\Validators;
use Symfony\Component\HttpFoundation\Request;

class CreateCommentValidator implements Validators
{
    private array $errors = [];


    /**
     * Form validation
     *
     * @param Request $request
     */
    public function validate(Request $request)
    {
        try
        {
            Assert::lazy()->tryAll()
                ->that($request->request->get('text'))
                ->notEmpty('Votre commentaire ne peut pas être vide')
                ->string('Veuillez entrer un commentaire valide')
                ->verifyNow();
        }
        catch(LazyAssertionException $exceptions)
        {
            foreach($exceptions->getErrorExceptions() as $exception)
            {
                $this->errors += $exception->getMessage();
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