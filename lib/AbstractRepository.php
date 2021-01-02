<?php


namespace Lib;


abstract class AbstractRepository
{
    /**
     * PDO instance
     */
    private $pdo = null;


    /**
     * Get database connection
     *
     * @return \PDO
     */
    public function getPDO(): \PDO
    {
        try {
            if($this->pdo === null)
            {
                $this->pdo = new \PDO(
                    'mysql:host='.$_SERVER['DB_HOST'].':'.$_SERVER['DB_PORT'].';dbname='.$_SERVER['DB_NAME'],
                    $_SERVER['DB_USERNAME'],
                    $_SERVER['DB_PASSWORD'],
                    [
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
                    ]
                );
            }
            return $this->pdo;
        }
        catch(\Exception $e){
            echo 'Exception reçue : ',  $e->getMessage(), "\n";
        }
    }


    /**
     * Return next id from specific table
     *
     * @param string $table
     * @return int
     */
    public function getNextId(string $table) : int
    {
        $query = $this->getPDO()->prepare('SELECT MAX(id) FROM ' . $table);
        $query->execute();

        $id = $query->fetch();

        return $id['MAX(id)'] + 1;
    }


    /**
     * Return last id from specific table
     *
     * @param string $table
     * @return int
     */
    public function getLastId(string $table) : int
    {
        $query = $this->getPDO()->prepare('SELECT MAX(id) FROM ' . $table);
        $query->execute();

        $id = $query->fetch();

        return $id['MAX(id)'];
    }


    /**
     * Return count of specific item
     *
     * @param string $table
     * @return int
     */
    public function countItems(string $table) : int
    {
        $query = $this->getPDO()->prepare('
            SELECT COUNT(id) AS items FROM '.$table.'
        ');

        $query->execute();

        $count = $query->fetch();

        return $count['items'];
    }
}