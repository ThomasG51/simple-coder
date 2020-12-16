<?php


namespace Lib\Validators;


use App\Repository\UserRepository;
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
        $firstname = $request->request->get('sign_up_firstname');
        $lastname = $request->request->get('sign_up_lastname');
        $email = $request->request->get('sign_up_email');
        $password = $request->request->get('sign_up_password');
        $confirm_password = $request->request->get('sign_up_confirm_password');

        if(empty($firstname))
        {
            $this->errors += ['registration_firstname_error' => 'Veuillez remplir votre prénom'];
        }

        if(empty($lastname))
        {
            $this->errors += ['registration_lastname_error' => 'Veuillez remplir votre nom'];
        }

        if(empty($email))
        {
            $this->errors += ['registration_email_error' => 'Veuillez remplir votre e-mail'];
        }

        if(empty($password))
        {
            $this->errors += ['registration_password_error' => 'Veuillez remplir votre mot de passe'];
        }

        if(empty($confirm_password))
        {
            $this->errors += ['registration_confirm_error' => 'Veuillez confirmer votre mot de passe'];
        }

        if($this->userManager->findOne($email))
        {
            $this->errors += ['registration_exist_error' => 'L\'utilisateur existe déjà'];
        }

        if(!empty($password) && !empty($confirm_password) && $password != $confirm_password)
        {
            $this->errors += ['registration_match_error' => 'Les mots de passes ne sont pas identiques'];
        }

        if(strlen($password) < 8)
        {
            $this->errors += ['registration_length_error' => 'Le mot de passe doit contenir 8 caractères minimum'];
        }

        if(!preg_match('([0-9]+)', $password))
        {
            $this->errors += ['registration_numeric_error' => 'Le mot de passe doit contenir 1 chiffre minimum'];
        }

        if(!preg_match('([a-z]+)', $password))
        {
            $this->errors += ['registration_lower_error' => 'Le mot de passe doit contenir 1 minuscule minimum'];
        }

        if(!preg_match('([A-Z]+)', $password))
        {
            $this->errors += ['registration_upper_error' => 'Le mot de passe doit contenir 1 majuscule minimum'];
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