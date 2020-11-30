<?php


namespace App\Repository;


use Lib\AbstractRepository;

class UserRepository extends AbstractRepository
{
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
}