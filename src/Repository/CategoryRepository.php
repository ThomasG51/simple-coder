<?php


namespace App\Repository;


use App\Entity\Category;
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

        $categories = [];

        foreach ($query->fetchAll() as $category)
        {
            $instance = new Category($category['id'], $category['name']);

            $categories[] = $instance;
        }

        return $categories;
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

        $category = $query->fetch();

        if(!$category)
        {
            return null;
        }

        return new Category($category['id'], $category['name']);
    }


    /**
     * Return last category
     *
     * @return mixed
     */
    public function findLast()
    {
        $query = $this->getPDO()->prepare('
            SELECT *
            FROM category
            WHERE id = :lastId
        ');

        $query->execute(['lastId' => $this->getLastId('category')]);

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