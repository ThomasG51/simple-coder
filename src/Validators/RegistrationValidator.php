<?php


namespace App\Validators;


use App\Repository\UserRepository;
use Assert\Assert;
use Assert\LazyAssertionException;
use Lib\Interfaces\Validators;
use Symfony\Component\HttpFoundation\Request;

class RegistrationValidator implements Validators
{
    private UserRepository $userManager;

    private array $errors = [];


    /**
     * RegistrationValidator constructor.
     */
    public function __construct()
    {
        $this->userManager = new UserRepository();
    }


    /**
     * Form Validations
     *
     * @param Request $request
     */
    public function validate(Request $request) : void
    {
        try
        {
            Assert::lazy()->tryAll()
                ->that($request->request->get('sign_up_firstname'), 'registration_firstname_error')
                    ->notEmpty('Veuillez remplir votre prénom')
                ->that($request->request->get('sign_up_lastname'), 'registration_lastname_error')
                    ->notEmpty('Veuillez remplir votre nom')
                ->that($request->request->get('sign_up_email'), 'registration_email_error')
                    ->notEmpty('Veuillez remplir votre e-mail')
                    ->email('Vous devez renseigner un e-mail valide')
                ->that($request->request->get('sign_up_password'), 'registration_password_error')
                    ->notEmpty('Veuillez remplir votre mot de passe')
                    ->minLength(8, 'Le mot de passe doit contenir 8 caractères minimum')
                    ->regex('([0-9]+)', 'Le mot de passe doit contenir au moins 1 chiffre')
                    ->regex('([a-z]+)', 'Le mot de passe doit contenir au moins 1 minuscule')
                    ->regex('([A-Z]+)', 'Le mot de passe doit contenir au moins 1 majuscule')
                ->that($request->request->get('sign_up_confirm_password'), 'registration_confirm_error')
                    ->notEmpty('Veuillez confirmer votre mot de passe')
                    ->same($request->request->get('sign_up_password'), 'Les mots de passes ne sont pas identiques')
                ->verifyNow();
        }
        catch(LazyAssertionException $exceptions)
        {
            foreach($exceptions->getErrorExceptions() as $exception)
            {
                $this->errors += [$exception->getPropertyPath() => $exception->getMessage()];
            }
        }

        if($this->userManager->findOne($request->request->get('sign_up_email')))
        {
            $this->errors += ['registration_exist_error' => 'L\'utilisateur existe déjà'];
        }
    }


    /**
     * Return form validation errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}