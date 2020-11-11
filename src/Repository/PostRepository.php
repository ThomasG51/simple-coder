<?php


namespace App\Repository;


use Lib\AbstractRepository;

class PostRepository extends AbstractRepository
{
    /**
     * Return all posts
     *
     * @return array
     */
    public function findAll() : array
    {
        $query = $this->getPDO()->prepare('
            SELECT post.*, category.name, user.firstname, user.lastname 
            FROM post 
            LEFT JOIN category ON post.category_id = category.id 
            LEFT JOIN user ON post.user_id = user.id 
            ORDER BY date DESC
        ');

        $query->execute();

        return $query->fetchAll();
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
            SELECT post.*, category.name, user.firstname, user.lastname 
            FROM post 
            LEFT JOIN category ON post.category_id = category.id 
            LEFT JOIN user ON post.user_id = user.id
            WHERE slug = :slug
            ORDER BY date DESC
        ');

        $query->execute(['slug' => $slug]);

        return $query->fetch();
    }


    /**
     * Return next id
     *
     * @return int
     */
    public function findNextId() : int
    {
        $query = $this->getPDO()->prepare('SELECT MAX(id) FROM post');
        $query->execute();

        $id = $query->fetch();

        return $id['MAX(id)'] + 1;
    }


    /**
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
            INSERT INTO post(title, cover, date, text, slug, user_id, category_id) 
            VALUES (:title, :cover, NOW(), :text, CONCAT(:slug, :next_id), :user_id, :category_id)
        ');

        $query->execute([
            'title' => $title,
            'cover' => $cover,
            'text' => $text,
            'slug' => $slug,
            'next_id' => $this->findNextId(),
            'user_id' => $user_id,
            'category_id' => $category_id
        ]);
    }
}