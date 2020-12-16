<?php


namespace Lib\Validators;


use App\Repository\UserRepository;
use Assert\Assert;
use Assert\LazyAssertionException;
use Lib\Interfaces\Validators;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class ResetPasswordValidator implements Validators
{
    private UserRepository $userManager;

    private Session $session;

    private array $errors = [];


    /**
     * ResetPasswordValidator constructor.
     */
    public function __construct()
    {
        $this->userManager = new UserRepository();
        $this->session = new Session();
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
            Assert::lazy()
                ->that($request->get('old_password'), 'old_password_error')
                    ->notEmpty('Veuillez remplir votre ancien mot de passe !')
                ->that($request->get('new_password'), 'new_password_error')
                    ->notEmpty('Veuillez remplir votre nouveau mot de passe !')
                    ->minLength(8, 'Le mot de passe doit contenir 8 caractères minimum')
                    ->regex('([0-9]+)', 'Le mot de passe doit contenir au moins 1 chiffre')
                    ->regex('([a-z]+)', 'Le mot de passe doit contenir au moins 1 minuscule')
                    ->regex('([A-Z]+)', 'Le mot de passe doit contenir au moins 1 majuscule')
                ->that($request->get('confirm_password'), 'confirm_password_error')
                    ->notEmpty('Veuillez confirmez votre nouveau mot de passe !')
                    ->same($request->get('new_password'),'Les deux nouveaux mots de passe doivent être identiques !')
                ->verifyNow();
        }
        catch(LazyAssertionException $exceptions)
        {
            foreach($exceptions->getErrorExceptions() as $exception)
            {
                $this->errors += [$exception->getPropertyPath() => $exception->getMessage()];
            }
        }

        if(password_verify($request->get('old_password'), $this->session->get('user')->getPassword()) == false)
        {
            $this->errors += ['old_password_error' => 'Votre ancien mot de passe n\'est pas valide'];
        }

        if(password_verify($request->get('old_password'), $this->session->get('user')->getPassword()) && $request->get('old_password') == $request->get('new_password'))
        {
            $this->errors += ['new_password_error' => 'Votre mot de passe ne peut pas être identique à l\'ancien !'];
        }
    }


    /**
     * Return form validation errors
     *
     * @return array
     */
    public function getErrors() : array
    {
        return $this->errors;
    }
}