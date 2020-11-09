<?php


namespace Lib;


class AbstractRepository
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
}