<?php


namespace App\Repository;


use App\Entity\Tags;
use Lib\AbstractRepository;

class TagsRepository extends AbstractRepository
{
    /**
     * Return all tags
     *
     * @return array
     */
    public function findAll() : array
    {
        $query = $this->getPDO()->prepare('
            SELECT * 
            FROM tags 
            ORDER BY name
        ');

        $query->execute();

        $tags = [];

        foreach ($query->fetchAll() as $tag)
        {
            $instance = new Tags($tag['id'], $tag['name']);

            $tags[] = $instance;
        }

        return $tags;
    }


    /**
     * Return one tag
     *
     * @param string $name
     * @return mixed
     */
    public function findOne(string $name)
    {
        $query = $this->getPDO()->prepare('
            SELECT *
            FROM tags
            WHERE name = :name
        ');

        $query->execute(['name' => $name]);

        $tag = $query->fetch();

        if(!$tag)
        {
            return null;
        }

        return new Tags($tag['id'], $tag['name']);
    }


    /**
     * Return last tag
     *
     * @return mixed
     */
    public function findLast()
    {
        $query = $this->getPDO()->prepare('
            SELECT *
            FROM tags
            WHERE id = :lastId
        ');

        $query->execute(['lastId' => $this->getLastId('tags')]);

        return $query->fetch();
    }


    /**
     * Create new tag
     *
     * @param string $name
     */
    public function create(string $name) : void
    {
        $query = $this->getPDO()->prepare('
            INSERT INTO tags(name) 
            VALUES (:name)
        ');

        $query->execute(['name' => $name]);
    }


    /**
     * Delete tags
     *
     * @param Tags $tags
     */
    public function delete(Tags $tags) : void
    {
        $query = $this->getPDO()->prepare('
            DELETE FROM tags
            WHERE name = :name
        ');

        $query->execute([
            'name' => $tags->getname()
        ]);
    }
}