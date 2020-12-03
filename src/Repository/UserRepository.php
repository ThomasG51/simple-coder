<?php


namespace App\Repository;


use App\Entity\User;
use Lib\AbstractRepository;

class UserRepository extends AbstractRepository
{
    /**
     * Create new user
     *
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @param string $password
     */
    public function create(string $firstname, string $lastname, string $email, string $password)
    {
        $query = $this->getPDO()->prepare('
            INSERT INTO user(firstname, lastname, email, password)
            VALUES(:firstname, :lastname, :email, :password)
        ');

        $query->execute([
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'password' => $password
        ]);
    }


    /**
     * Find one user by email
     *
     * @param string $email
     * @return User|false
     */
    public function findOne(string $email)
    {
        $query = $this->getPDO()->prepare('
            SELECT *
            FROM user
            WHERE email = :email
        ');

        $query->execute(['email' => $email]);

        $instance = $query->fetch();

        if($instance)
        {
            return new User($instance['id'], $instance['firstname'], $instance['lastname'], $instance['email'], $instance['password']);
        }
        else
        {
            return false;
        }
    }
}