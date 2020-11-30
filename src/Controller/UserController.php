<?php


namespace App\Controller;


use App\Repository\UserRepository;
use Lib\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    private UserRepository $userManager;


    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->userManager = new UserRepository();
    }

    public function create()
    {
        if($this->request->getMethod() == 'POST')
        {
            $firstname = htmlspecialchars($this->request->request->get('sign_up_firstname'));
            $lastname = htmlspecialchars($this->request->request->get('sign_up_lastname'));
            $email = htmlspecialchars($this->request->request->get('sign_up_email'));
            $password = htmlspecialchars($this->request->request->get('sign_up_password'));
            $confirm_password = htmlspecialchars($this->request->request->get('sign_up_confirm_password'));

            $registration_errors = [];

            if(empty($firstname))
            {
                $registration_errors += ['registration_firstname_error' => 'Veuillez remplir votre prénom'];
            }

            if(empty($lastname))
            {
                $registration_errors += ['registration_lastname_error' => 'Veuillez remplir votre nom'];
            }

            if(empty($email))
            {
                $registration_errors += ['registration_email_error' => 'Veuillez remplir votre e-mail'];
            }

            if(empty($password))
            {
                $registration_errors += ['registration_password_error' => 'Veuillez remplir votre mot de passe'];
            }

            if(empty($confirm_password))
            {
                $registration_errors += ['registration_confirm_error' => 'Veuillez confirmer votre mot de passe'];
            }

//            if($this->userManager->finByEmail($email))
//            {
//                $registration_errors += ['registration_exist_error' => 'L\'utilisateur existe déjà'];
//            }

            if(!empty($password) && !empty($confirm_password) && $password === $confirm_password)
            {
                if(strlen($password) < 8)
                {
                    $registration_errors += ['registration_length_error' => 'Le mot de passe doit contenir 8 caractères minimum'];
                }

                if(!preg_match('([0-9]+)', $password))
                {
                    $registration_errors += ['registration_numeric_error' => 'Le mot de passe doit contenir 1 chiffre minimum'];
                }

                if(!preg_match('([a-z]+)', $password))
                {
                    $registration_errors += ['registration_lower_error' => 'Le mot de passe doit contenir 1 minuscule minimum'];
                }

                if(!preg_match('([A-Z]+)', $password))
                {
                    $registration_errors += ['registration_upper_error' => 'Le mot de passe doit contenir 1 majuscule minimum'];
                }

                $password = password_hash($this->request->request->get('password'), PASSWORD_DEFAULT);
            }
            else
            {
                $registration_errors += ['registration_match_error' => 'Les mots de passe ne sont pas identiques'];
            }

            if(empty($registration_errors))
            {
                $this->userManager->create($firstname, $lastname, $email, $password);

                return new JsonResponse(['registration_done' => 'Enregistrement éffectué !']);
            }

            return new JsonResponse($registration_errors);
        }
    }
}