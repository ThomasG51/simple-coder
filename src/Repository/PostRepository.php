<?php


namespace App\Repository;


use App\Entity\Post;
use Lib\AbstractRepository;

class PostRepository extends AbstractRepository
{
    private UserRepository $userManager;

    private CategoryRepository $categoryManager;

    private TagsLineRepository $tagsLineManager;


    /**
     * PostRepository constructor.
     */
    public function __construct()
    {
        $this->userManager = new UserRepository();
        $this->categoryManager = new CategoryRepository();
        $this->tagsLineManager = new TagsLineRepository();
    }


    /**
     * Return all posts
     *
     * @return array[POST]
     */
    public function findAll() : array
    {
        $query = $this->getPDO()->prepare('
            SELECT post.id AS id_post, post.*, category.*, user.* 
            FROM post
            LEFT JOIN category ON post.category_id = category.id 
            LEFT JOIN user ON post.user_id = user.id 
            ORDER BY date DESC
        ');

        $query->execute();

        $posts = [];

        foreach($query->fetchAll() as $post)
        {
            $instance = new Post();
            $instance->setId($post['id_post']);
            $instance->setTitle($post['title']);
            $instance->setCover($post['cover']);
            $instance->setDate($post['date']);
            $instance->setText($post['text']);
            $instance->setSlug($post['slug']);
            $instance->setStatus($post['status']);
            $instance->setUser($this->userManager->findOne($post['email']));
            $instance->setCategory($this->categoryManager->findOne($post['name']));
            $instance->setTags($this->tagsLineManager->findTagsByPost($post['slug']));

            $posts[] = $instance;
        }

        return $posts;
    }


    /**
     * Return one post
     *
     * @param string $slug
     * @return Post|false
     */
    public function findOne(string $slug)
    {
        $query = $this->getPDO()->prepare('
            SELECT post.id AS id_post, post.*, category.*, user.*
            FROM post 
            INNER JOIN category ON post.category_id = category.id 
            INNER JOIN user ON post.user_id = user.id
            WHERE slug = :slug
            ORDER BY date DESC
        ');

        $query->execute(['slug' => $slug]);
        $post = $query->fetch();

        if(!$post)
        {
            return null;
        }

        $instance = new Post();
        $instance->setId($post['id_post']);
        $instance->setTitle($post['title']);
        $instance->setCover($post['cover']);
        $instance->setDate($post['date']);
        $instance->setText($post['text']);
        $instance->setSlug($post['slug']);
        $instance->setStatus($post['status']);
        $instance->setUser($this->userManager->findOne($post['email']));
        $instance->setCategory($this->categoryManager->findOne($post['name']));
        $instance->setTags($this->tagsLineManager->findTagsByPost($post['slug']));

        return $instance;
    }


    /**
     * Create new post
     *
     * @param Post $post
     * @return void
     */
    public function create(Post $post) : void
    {
        $query = $this->getPDO()->prepare('
            INSERT INTO post(id, title, cover, date, text, slug, status, user_id, category_id) 
            VALUES (:id, :title, :cover, NOW(), :text, CONCAT(:slug, :next_id), :status, :user_id, :category_id)
        ');

        $query->execute([
            'id' => $this->getNextId('post'),
            'title' => $post->getTitle(),
            'cover' => $post->getCover(),
            'text' => $post->getText(),
            'slug' => $post->getSlug(),
            'next_id' => $this->getNextId('post'),
            'status' => $post->getStatus(),
            'user_id' => $post->getUser()->getId(),
            'category_id' => $post->getCategory()->getId()
        ]);
    }


    /**
     * Post archiving
     *
     * @param Post $post
     * @return void
     */
    public function archiving(Post $post) : void
    {
        $query = $this->getPDO()->prepare('
            UPDATE post
            SET status = :status
            WHERE slug = :slug
        ');

        $query->execute([
            'slug' => $post->getSlug(),
            'status' => $post->getStatus()
        ]);
    }


    /**
     * Update Post
     *
     * @param Post $post
     * @return void
     */
    public function update(Post $post) : void
    {
        $query = $this->getPDO()->prepare('
            UPDATE post
            SET title = :title, text = :text, cover = :cover, category_id = :category
            WHERE slug = :slug
        ');

        $query->execute([
            'slug' => $post->getSlug(),
            'title' => $post->getTitle(),
            'text' => $post->getText(),
            'cover' => $post->getCover(),
            'category' => $post->getCategory()->getId()
        ]);
    }


    /**
     * Delete Post
     *
     * @param string $slug
     * @return void
     */
    public function delete(string $slug) : void
    {
        $query = $this->getPDO()->prepare('
            DELETE FROM post
            WHERE slug = :slug
        ');

        $query->execute(['slug' => $slug]);
    }
}