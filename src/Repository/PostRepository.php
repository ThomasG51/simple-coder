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
     * @return array
     */
    public function findAll() : array
    {
        $query = $this->getPDO()->prepare('
            SELECT post.*, category.*, user.* 
            FROM post 
            LEFT JOIN category ON post.category_id = category.id 
            LEFT JOIN user ON post.user_id = user.id 
            ORDER BY date DESC
        ');

        $query->execute();

        $posts = [];

        foreach($query->fetchAll() as $post)
        {
            $user = $this->userManager->findOne($post['email']);
            $category = $this->categoryManager->findOne($post['name']);
            $tags = $this->tagsLineManager->findTagsByPost($post['slug']);

            $instance = new Post($post['id'], $post['title'], $post['cover'], $post['date'], $post['text'], $post['slug'], $user, $category, $tags);

            $posts[] = $instance;
        }

        return $posts;
    }


    /**
     * Return one post
     *
     * @param string $slug
     * @return mixed
     */
    public function findOne(string $slug)
    {
        $query = $this->getPDO()->prepare('
            SELECT post.*, category.*, user.* 
            FROM post 
            LEFT JOIN category ON post.category_id = category.id 
            LEFT JOIN user ON post.user_id = user.id
            WHERE slug = :slug
            ORDER BY date DESC
        ');

        $query->execute(['slug' => $slug]);
        $post = $query->fetch();

        $user = $this->userManager->findOne($post['email']);
        $category = $this->categoryManager->findOne($post['name']);
        $tags = $this->tagsLineManager->findTagsByPost($post['slug']);

        if($post)
        {
            return new Post($post['id'], $post['title'], $post['cover'], $post['date'], $post['text'], $post['slug'], $user, $category, $tags);
        }
        else
        {
            return false;
        }
    }


    /**q
     * Create new post
     *
     * @param string $title
     * @param string $cover
     * @param string $text
     * @param string $slug
     * @param int $user_id
     * @param int $category_id
     * @return void
     */
    public function create(string $title, string $cover, string $text, string $slug, int $user_id, int $category_id) : void
    {
        $query = $this->getPDO()->prepare('
            INSERT INTO post(id, title, cover, date, text, slug, user_id, category_id) 
            VALUES (:id, :title, :cover, NOW(), :text, CONCAT(:slug, :next_id), :user_id, :category_id)
        ');

        $query->execute([
            'id' => $this->getNextId('post'),
            'title' => $title,
            'cover' => $cover,
            'text' => $text,
            'slug' => $slug,
            'next_id' => $this->getNextId('post'),
            'user_id' => $user_id,
            'category_id' => $category_id
        ]);
    }
}