<?php
class Db
{
    protected $pdo;

    /**
     * Db constructor.
     * Create PDO connection
     */
    public function __construct()
    {
        $this->pdo = new PDO(DSN_MY, USER_NAME, PASS);
        if(!$this->pdo)
        {
            return false;
        }
    }

    /**
     * get private prop $pdo
     * @return PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * Make query
     * @param $sql
     * @return bool|int
     */
    public function execQuery($sql)
    {
        $count = $this->pdo->exec($sql);
        if ($count === false)
        {
            return false;
        }
        return $count;
    }
}