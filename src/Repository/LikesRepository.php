<?php


namespace App\Repository;


use App\Entity\Post;
use App\Entity\User;
use Lib\AbstractRepository;

class LikesRepository extends AbstractRepository
{
    /**
     * Add Like
     *
     * @param User $user
     * @param Post $post
     */
    public function create(User $user, Post $post) : void
    {
        $query = $this->getPDO()->prepare('
            INSERT INTO likes(user_id,  post_id)
            VALUES(:user, :post)
        ');

        $query->execute([
            'user' => $user->getId(),
            'post' => $post->getId()
        ]);
    }


    /**
     * Return one like
     *
     * @param User $user
     * @param Post $post
     * @return array|null
     */
    public function findOne(User $user, Post $post)
    {
        $query = $this->getPDO()->prepare('
            SELECT *
            FROM likes
            WHERE user_id = :user
            AND post_id = :post
        ');

        $query->execute([
            'user' => $user->getId(),
            'post' => $post->getId()
        ]);

        $like = $query->fetch();

        if(!$like)
        {
            return null;
        }

        return $like;
    }


    /**
     * Count likes by post
     *
     * @param Post $post
     * @return int
     */
    public function countByPost(Post $post) : int
    {
        $query = $this->getPDO()->prepare('
            SELECT COUNT(id)
            FROM likes
            WHERE post_id = :post
        ');

        $query->execute(['post' => $post->getId()]);

        $likes = $query->fetch();

        return $likes['COUNT(id)'];
    }


    /**
     * Delete like
     *
     * @param User $user
     * @param Post $post
     */
    public function delete(User $user, Post $post)
    {
        $query = $this->getPDO()->prepare('
            DELETE FROM likes
            WHERE user_id = :user
            AND post_id = :post
        ');

        $query->execute([
            'user' => $user->getId(),
            'post' => $post->getId()
        ]);
    }
}