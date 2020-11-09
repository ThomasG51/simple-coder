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
        $query = $this->getPDO()->prepare('SELECT * FROM post ORDER BY date DESC');
        $query->execute();

        return $query->fetchAll();
    }


    /**
     * Return one post
     *
     * @param $slug
     * @return mixed
     */
    public function findOne($slug)
    {
        $query = $this->getPDO()->prepare('SELECT * FROM post WHERE slug = :slug');
        $query->execute(['slug' => $slug]);

        return $query->fetch();
    }
}