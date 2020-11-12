<?php


namespace App\Repository;


use Lib\AbstractRepository;

class CategoryRepository extends AbstractRepository
{
    /**
     * Return all categories
     *
     * @return array
     */
    public function findAll() : array
    {
        $query = $this->getPDO()->prepare('
            SELECT * 
            FROM category 
            ORDER BY name
        ');

        $query->execute();

        return $query->fetchAll();
    }


    /**
     * Retrun one category
     *
     * @param string $name
     * @return mixed
     */
    public function findOne(string $name)
    {
        $query = $this->getPDO()->prepare('
            SELECT * 
            FROM category
            WHERE name = :name
        ');

        $query->execute(['name' => $name]);

        return $query->fetch();
    }


    /**
     * Create new category
     *
     * @param string $name
     */
    public function create(string $name) : void
    {
        $query = $this->getPDO()->prepare('
            INSERT INTO category(name) 
            VALUES (:name)
        ');

        $query->execute(['name' => $name]);
    }
}