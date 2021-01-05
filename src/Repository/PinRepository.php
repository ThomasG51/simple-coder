<?php


namespace App\Repository;


use App\Entity\Post;
use App\Entity\User;
use Lib\AbstractRepository;

class PinRepository extends AbstractRepository
{
    private PostRepository $postManager;

    private UserRepository $userManager;

    private CategoryRepository $categoryManager;

    private TagsLineRepository $tagsLineManager;


    public function __construct()
    {
        $this->postManager = new PostRepository();
        $this->userManager = new UserRepository();
        $this->categoryManager = new CategoryRepository();
        $this->tagsLineManager = new TagsLineRepository();
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
     * Find pinned post by user
     *
     * @param User $user
     * @return array
     */
    public function findPinByUser(User $user) : array
    {
        $query = $this->getPDO()->prepare('
            SELECT post.*, user.email AS user, category.name AS category
            FROM pin
            INNER JOIN post ON pin.post_id = post.id
            INNER JOIN user ON post.user_id = user.id
            INNER JOIN category ON post.category_id = category.id
            WHERE pin.user_id = :user
        ');

        $query->execute([
            'user' => $user->getId()
        ]);

        $posts = [];

        foreach($query->fetchAll() as $post)
        {
            $instance = new Post();
            $instance->setId($post['id']);
            $instance->setTitle($post['title']);
            $instance->setCover($post['cover']);
            $instance->setCreatedAt($post['date']);
            $instance->setText($post['text']);
            $instance->setSlug($post['slug']);
            $instance->setStatus($post['status']);
            $instance->setUser($this->userManager->findOne($post['user']));
            $instance->setCategory($this->categoryManager->findOne($post['category']));
            $instance->setTags($this->tagsLineManager->findTagsByPost($post['slug']));

            $posts[] = $instance;
        }

        return $posts;
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