<?php


namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use Lib\AbstractController;
use App\Validators\LoginValidator;
use App\Validators\RegistrationValidator;
use App\Validators\ResetPasswordValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
            $registrationValidator = new RegistrationValidator();
            $registrationValidator->validate($this->request);

            if(empty($registrationValidator->getErrors()))
            {
                $user = new User();
                $user->setFirstname($this->request->request->get('sign_up_firstname'));
                $user->setLastname($this->request->request->get('sign_up_lastname'));
                $user->setEmail($this->request->request->get('sign_up_email'));
                $user->setPassword(password_hash($this->request->request->get('sign_up_password'), PASSWORD_DEFAULT));
                $user->setRole('USER');

                $this->userManager->create($user);

                $this->session->set('user', $this->userManager->findOne($this->request->request->get('sign_up_email')));

                return new JsonResponse(['registration_done' => 'Enregistrement éffectué !']);
            }

            return new JsonResponse($registrationValidator->getErrors());
        }

        $this->session->getFlashBag()->add('alert', ['danger' => 'Erreur d\'enreistrement, le formulaire n\'a pas été soumis']);

        return $this->redirectToRoute('/');
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
            $loginValidator = new LoginValidator();
            $loginValidator->validate($this->request);

            if(empty($loginValidator->getErrors()))
            {
                $this->session->set('user', $this->userManager->findOne($this->request->request->get('login_email')));

                return new JsonResponse(['login_succeeds' => 'Connexion reussi.']);
            }

            return new JsonResponse($loginValidator->getErrors());
        }

        $this->session->getFlashBag()->add('alert', ['danger' => 'Erreur de connexion, le formulaire n\'a pas été soumis']);

        return $this->redirectToRoute('/');
    }


    /**
     * User Logout
     *
     * @return Response
     */
    public function logout() : Response
    {
        $this->checkIfConnected();

        $this->session->remove('user');

        $this->session->remove('csrf_token');

        return $this->redirectToRoute('/');
    }


    /**
     * Reset Password
     *
     * @return JsonResponse
     */
    public function resetPassword() : JsonResponse
    {
        $this->checkIfConnected();

        if($this->request->getMethod() == 'POST')
        {
            $resetPasswordValidator = new ResetPasswordValidator();
            $resetPasswordValidator->validate($this->request);

            if(empty($resetPasswordValidator->getErrors()))
            {
                $email = $this->session->get('user')->getEmail();
                $password = password_hash($this->request->get('new_password'), PASSWORD_DEFAULT);

                $this->userManager->updatePassword($email, $password);

                $this->session->set('user', $this->userManager->findOne($this->session->get('user')->getEmail()));

                return new JsonResponse(['reset_succeeds' => 'Modification effectuée !']);
            }

            return new JsonResponse($resetPasswordValidator->getErrors());
        }

        $this->session->getFlashBag()->add('alert', ['danger' => 'Erreur de reinitialisation du mot de passe, le formulaire n\'a pas été soumis']);

        return $this->redirectToRoute('/dashboard/user');
    }


    /**
     * Delete User
     *
     * @param int $id
     * @return Response
     */
    public function delete(int $id) : Response
    {
        $this->checkIfConnected();
        $this->checkIfAdmin();

        if($this->request->getMethod() === 'POST')
        {
            if($this->request->request->get('csrf_token') === $this->session->get('csrf_token'))
            {
                $this->userManager->delete($id);

                $this->session->getFlashBag()->add('alert', ['success' => 'Utilisateur supprimé']);

                return $this->redirectToRoute('/dashboard/user');
            }

            $this->session->getFlashBag()->add('alert', ['danger' => 'Token expiré !']);

            return $this->redirectToRoute('/dashboard/user');
        }

        $this->session->getFlashBag()->add('alert', ['danger' => 'Suppression de l\'utilisateur impossible, le formulaire n\'a pas été soumis !']);

        return $this->redirectToRoute('/dashboard/user');
    }
}