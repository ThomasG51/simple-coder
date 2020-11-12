<?php


namespace App\Repository;


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

        return $query->fetchAll();
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
}