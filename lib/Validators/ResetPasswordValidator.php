<?php


namespace Lib\Validators;


use App\Repository\UserRepository;
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
        if(empty($request->get('old_password')))
        {
            $this->errors += ['old_password_error' => 'Veuillez remplir votre ancien mot de passe !'];
        }

        if(empty($request->get('new_password')))
        {
            $this->errors += ['new_password_error' => 'Veuillez remplir votre nouveau mot de passe !'];
        }

        if(empty($request->get('confirm_password')))
        {
            $this->errors += ['confirm_password_error' => 'Veuillez confirmez votre nouveau mot de passe !'];
        }

        if(password_verify($request->get('old_password'), $this->session->get('user')->getPassword()) == false)
        {
            $this->errors += ['old_password_error' => 'Votre ancien mot de passe n\'est pas valide'];
        }

        if(password_verify($request->get('old_password'), $this->session->get('user')->getPassword()) && $request->get('old_password') == $request->get('new_password'))
        {
            $this->errors += ['new_password_error' => 'Votre mot de passe ne peut pas être identique à l\'ancien !'];
        }

        if(!empty($request->get('new_password')) && !empty($request->get('confirm_password')) && $request->get('new_password') != $request->get('confirm_password'))
        {
            $this->errors += ['confirm_password_error' => 'Les deux nouveaux mots de passe doivent être identiques !'];
        }

        if(strlen($request->get('new_password')) < 8)
        {
            $this->errors += ['new_password_error' => 'Le mot de passe doit contenir 8 caractères minimum'];
        }

        if(!preg_match('([0-9]+)', $request->get('new_password')))
        {
            $this->errors += ['new_password_error' => 'Le mot de passe doit contenir 1 chiffre minimum'];
        }

        if(!preg_match('([a-z]+)', $request->get('new_password')))
        {
            $this->errors += ['new_password_error' => 'Le mot de passe doit contenir 1 minuscule minimum'];
        }

        if(!preg_match('([A-Z]+)', $request->get('new_password')))
        {
            $this->errors += ['new_password_error' => 'Le mot de passe doit contenir 1 majuscule minimum'];
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