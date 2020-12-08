<?php


namespace App\Controller;


use App\Repository\UserRepository;
use Lib\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    private UserRepository $userManager;


    /**
     * UserController constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->userManager = new UserRepository();
    }


    /**
     * Create new user
     *
     * @return JsonResponse
     */
    public function create() : JsonResponse
    {
        if($this->request->getMethod() == 'POST')
        {
            $firstname = $this->request->request->get('sign_up_firstname');
            $lastname = $this->request->request->get('sign_up_lastname');
            $email = $this->request->request->get('sign_up_email');
            $password = $this->request->request->get('sign_up_password');
            $confirm_password = $this->request->request->get('sign_up_confirm_password');

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

            if($this->userManager->findOne($email))
            {
                $registration_errors += ['registration_exist_error' => 'L\'utilisateur existe déjà'];
            }

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
            }
            else
            {
                $registration_errors += ['registration_match_error' => 'Les mots de passes ne sont pas identiques'];
            }

            if(empty($registration_errors))
            {
                $password = password_hash($this->request->request->get('sign_up_password'), PASSWORD_DEFAULT);
                $role = 'ROLE_USER';

                $this->userManager->create($firstname, $lastname, $email, $password, $role);

                return new JsonResponse(['registration_done' => 'Enregistrement éffectué !']);
            }

            return new JsonResponse($registration_errors);
        }
    }


    /**
     * User Login
     *
     * @return JsonResponse
     */
    public function login() : JsonResponse
    {
        if($this->request->getMethod() == 'POST')
        {
            if(!empty($this->request->request->get('login_email')))
            {
                $user = $this->userManager->findOne($this->request->request->get('login_email'));

                if($user)
                {
                    if(!empty($this->request->request->get('login_password')))
                    {
                        if(password_verify($this->request->request->get('login_password'), $user->getPassword()))
                        {
                            $this->session->set('user', $user);

                            return new JsonResponse(['login_succeeds' => 'Connexion reussi.']);
                        }
                        else
                        {
                            return new JsonResponse(['login_password_error' => 'Le mot de passe est incorrect']);
                        }
                    }
                    else
                    {
                        return new JsonResponse(['login_password_error' => 'Le mot de passe ne peut pas être vide']);
                    }
                }
                else
                {
                    return new JsonResponse(['login_email_error' => 'Le compte n\'existe pas']);
                }
            }
            else
            {
                return new JsonResponse(['login_email_error' => 'L\'email ne peut pas être vide']);
            }
        }
    }


    /**
     * User Logout
     */
    public function logout()
    {
        $this->session->remove('user');

        return $this->redirectToRoute('/');
    }


    /**
     * Reset Password
     *
     * @return JsonResponse
     */
    public function resetPassword() : JsonResponse
    {
        if($this->request->getMethod() == 'POST')
        {
            $errors = [];

            if(empty($this->request->get('old_password')))
            {
                $errors += ['old_password_error' => 'Veuillez remplir votre ancien mot de passe !'];
            }

            if(empty($this->request->get('new_password')))
            {
                $errors += ['new_password_error' => 'Veuillez remplir votre nouveau mot de passe !'];
            }

            if(empty($this->request->get('confirm_password')))
            {
                $errors += ['confirm_password_error' => 'Veuillez confirmez votre nouveau mot de passe !'];
            }

            if(password_verify($this->request->get('old_password'), $this->session->get('user')->getPassword()) == false)
            {
                $errors += ['old_password_error' => 'Votre ancien mot de passe n\'est pas valide'];
            }

            if(password_verify($this->request->get('old_password'), $this->session->get('user')->getPassword()) && $this->request->get('old_password') == $this->request->get('new_password'))
            {
                $errors += ['new_password_error' => 'Votre mot de passe ne peut pas être identique à l\'ancien !'];
            }

            if(!empty($this->request->get('new_password')) && !empty($this->request->get('confirm_password')) && $this->request->get('new_password') === $this->request->get('confirm_password'))
            {
                if(strlen($this->request->get('new_password')) < 8)
                {
                    $errors += ['new_password_error' => 'Le mot de passe doit contenir 8 caractères minimum'];
                }

                if(!preg_match('([0-9]+)', $this->request->get('new_password')))
                {
                    $errors += ['new_password_error' => 'Le mot de passe doit contenir 1 chiffre minimum'];
                }

                if(!preg_match('([a-z]+)', $this->request->get('new_password')))
                {
                    $errors += ['new_password_error' => 'Le mot de passe doit contenir 1 minuscule minimum'];
                }

                if(!preg_match('([A-Z]+)', $this->request->get('new_password')))
                {
                    $errors += ['new_password_error' => 'Le mot de passe doit contenir 1 majuscule minimum'];
                }
            }
            else
            {
                $errors += ['confirm_password_error' => 'Les deux nouveaux mots de passe doivent être identiques !'];
            }

            if(empty($errors))
            {
                $email = $this->session->get('user')->getEmail();
                $password = password_hash($this->request->get('new_password'), PASSWORD_DEFAULT);

                $this->userManager->updatePassword($email, $password);

                return new JsonResponse(['reset_succeeds' => 'Modification effectuée !']);
            }

            return new JsonResponse($errors);
        }
    }
}