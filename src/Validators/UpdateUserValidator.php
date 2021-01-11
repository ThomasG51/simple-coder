<?php


namespace App\Validators;


use Assert\Assert;
use Assert\LazyAssertionException;
use Lib\Interfaces\Validators;
use Symfony\Component\HttpFoundation\Request;

class UpdateUserValidator implements Validators
{
    private array $errors = [];

    public function validate(Request $request)
    {
        try
        {
            Assert::lazy()->tryAll()
                ->that($request->request->get('firstname'), 'firstname')
                    ->notEmpty('Veuillez remplir votre prénom')
                    ->string('Vous devez renseigner un prénom valide')
                ->that($request->request->get('lastname'), 'lastname')
                    ->notEmpty('Veuillez remplir votre nom')
                    ->string('Vous devez renseigner un nom valide')
                ->that($request->request->get('email'), 'email')
                    ->notEmpty('Veuillez remplir votre e-mail')
                    ->email('Vous devez renseigner un e-mail valide')
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