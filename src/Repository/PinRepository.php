<?php


namespace App\Repository;


use App\Entity\Post;
use App\Entity\User;
use Lib\AbstractRepository;

class PinRepository extends AbstractRepository
{
    private PostRepository $postManager;

    private UserRepository $userManager;


    public function __contruct()
    {
        $this->postManager = new PostRepository();
        $this->userManager = new UserRepository();
    }


    /**
     * Create new pin
     *
     * @param User $user
     * @param Post $post
     */
    public function create(User $user, Post $post) : void
    {
        $query = $this->getPDO()->prepare('
            INSERT INTO pin (user_id, post_id)
            VALUES (:user, :post)
        ');

        $query->execute([
            'user' => $user->getId(),
            'post' => $post->getId()
        ]);
    }


    /**
     * Find one pin
     *
     * @param User $user
     * @param Post $post
     * @return mixed
     */
    public function findOne(User $user, Post $post)
    {
        $query = $this->getPDO()->prepare('
            SELECT *
            FROM pin
            WHERE user_id = :user 
            AND post_id = :post
        ');

        $query->execute([
            'user' => $user->getId(),
            'post' => $post->getId()
        ]);

        return $query->fetch();
    }


    /**
     * Delete pin
     *
     * @param User $user
     * @param Post $post
     */
    public function delete(User $user, Post $post) : void
    {
        $query = $this->getPDO()->prepare('
            DELETE FROM pin
            WHERE user_id = :user 
            AND post_id = :post
        ');

        $query->execute([
            'user' => $user->getId(),
            'post' => $post->getId()
        ]);
    }
}