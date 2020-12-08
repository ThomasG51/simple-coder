<?php


namespace App\Repository;


use App\Entity\User;
use Lib\AbstractRepository;

class UserRepository extends AbstractRepository
{
    /**
     * Create new user
     *
     * @param User $user
     */
    public function create(User $user)
    {
        $query = $this->getPDO()->prepare('
            INSERT INTO user(firstname, lastname, email, password, role)
            VALUES(:firstname, :lastname, :email, :password, :role)
        ');

        $query->execute([
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'role' => $user->getRole()
        ]);
    }


    /**
     * Find all users
     *
     * @return User|array
     */
    public function findAll()
    {
        $query = $this->getPDO()->prepare('
            SELECT *
            FROM user
            ORDER BY email
        ');

        $query->execute([]);

        $users = [];

        foreach($query->fetchAll() as $user)
        {
            $instance = new User();
            $instance->setId($user['id']);
            $instance->setFirstname($user['firstname']);
            $instance->setLastname($user['lastname']);
            $instance->setEmail($user['email']);
            $instance->setPassword($user['password']);
            $instance->setRole($user['role']);

            $users[] = $instance;
        }

        return $users;
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

        $user = $query->fetch();

        if($user)
        {
            $instance = new User();
            $instance->setId($user['id']);
            $instance->setFirstname($user['firstname']);
            $instance->setLastname($user['lastname']);
            $instance->setEmail($user['email']);
            $instance->setPassword($user['password']);
            $instance->setRole($user['role']);

            return $instance;
        }
        else
        {
            return false;
        }
    }


    /**
     * @param string $email
     * @param string $password
     */
    public function updatePassword(string $email, string $password)
    {
        $query = $this->getPDO()->prepare('
            UPDATE user
            SET password = :password
            WHERE email = :email
        ');

        $query->execute([
            'password' => $password,
            'email' => $email
        ]);
    }


    public function delete(int $id)
    {
        $query = $this->getPDO()->prepare('
            DELETE FROM user
            WHERE id = :id
        ');

        $query->execute(['id' => $id]);
    }
}