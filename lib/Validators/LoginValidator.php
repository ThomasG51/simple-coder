<?php


namespace Lib\Validators;


use App\Repository\UserRepository;
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
        if(empty($request->get('login_email')))
        {
            $this->errors += ['login_email_error' => 'L\'email ne peut pas être vide'];
        }

        if(empty($request->get('login_password')))
        {
            $this->errors += ['login_password_error' => 'Le mot de passe ne peut pas être vide'];
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