<?php
class ModelDB
{
    protected $pdo;
    protected $validator;

    public function __construct()
    {
        $this->validator = new Validator();
        $this->pdo = new PDO(DSN_MY, USER_NAME, PASS);
        if(!$this->pdo)
        {
            throw new Exception(ERR_DB);
        }
    }

    protected function selectQuery($sql)
    {
        $sth = $this->pdo->prepare($sql);
        $result = $sth->execute();
        if (false === $result)
        {
            throw new Exception(ERR_QUERY);
        }
        $data = $sth->fetchAll(PDO::FETCH_ASSOC);
        if (empty($data))
        {
            return ERR_SEARCH;
        }
        return $data;
    }

    protected function execQuery($sql)
    {
        $count = $this->pdo->exec($sql);
        if ($count === false)
        {
            return false;
        }
        return $count;
    }

    protected function checkData($param)
    {
        if (isset($param['hash']) && isset($param['id_user']))
        {
            $hash = $this->pdo->quote($param['hash']);
            $id = $this->pdo->quote($param['id_user']);
            $sql = 'SELECT r.name as role FROM users u LEFT JOIN roles r ON u.id_role=r.id WHERE u.id='.$id.' AND u.hash='.$hash;
            //$sql = "SELECT role FROM clients WHERE id=".$id." AND hash=".$hash;
            $data = $this->selectQuery($sql);
            if (is_array($data))
            {
                return $data[0]['role'];
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
}
