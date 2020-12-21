<?php


namespace App\Validators;


use App\Repository\UserRepository;
use Assert\Assert;
use Assert\LazyAssertionException;
use Lib\Interfaces\Validators;
use Symfony\Component\HttpFoundation\Request;

class LoginValidator implements Validators
{
    private UserRepository $userManager;

    private array $errors = [];


    /**
     * LoginValidator constructor.
     */
    public function __construct()
    {
        $this->userManager = new UserRepository();
    }


    /**
     * Form validations
     *
     * @param Request $request
     */
    public function validate(Request $request) : void
    {
            try
            {
                Assert::lazy()->tryAll()
                    ->that($request->get('login_email'), 'login_email_error')
                        ->notEmpty('Veuillez remplir votre e-mail')
                        ->email('Vous devez renseigner un e-mail valide')
                    ->that($request->get('login_password'), 'login_password_error')
                        ->notEmpty('Veuillez remplir votre mot de passe')
                    ->verifyNow();
            }
            catch(LazyAssertionException $exceptions)
            {
                foreach($exceptions->getErrorExceptions() as $exception)
                {
                    $this->errors += [$exception->getPropertyPath() => $exception->getMessage()];
                }
            }

            $user = $this->userManager->findOne($request->get('login_email'));

            if($user === null)
            {
                $this->errors += ['login_email_error' => 'Le compte n\'existe pas'];
            }
            else
            {
                if(!password_verify($request->get('login_password'), $user->getPassword()))
                {
                    $this->errors += ['login_password_error' => 'Le mot de passe est incorrect'];
                }
            }

    }


    /**
     * Return form validations errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}