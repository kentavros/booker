<?php

/**
 * Class ModelDB - Model Data Base,
 * works with database tables MySQL.
 * Create PDO, Validator object construct.
 * It is an inheritable class of all REST application models.
 */
class ModelDB
{
    protected $pdo;
    protected $validator;

    /**
     * Create sql connection with PDO & set proporty validator
     * ModelDB constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->validator = new Validator();
        $this->pdo = new PDO(DSN_MY, USER_NAME, PASS);
        if(!$this->pdo)
        {
            throw new Exception(ERR_DB);
        }
    }

    /**
     * Get select from DB
     * @param $sql
     * @return array|string
     * @throws Exception
     */
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

    /**
     * Get insert, update, delete from DB
     * @param $sql
     * @return bool|int
     */
    protected function execQuery($sql)
    {
        $count = $this->pdo->exec($sql);
        if ($count === false)
        {
            return false;
        }
        return $count;
    }

    /**
     * Checks in data sent hash and id_user
     * for granting access
     * @param $param
     * @return bool
     */
    protected function checkData($param)
    {
        if (isset($param['hash']) && isset($param['id_user']))
        {
            $hash = $this->pdo->quote($param['hash']);
            $id = $this->pdo->quote($param['id_user']);
            $sql = 'SELECT r.name as role FROM users u LEFT JOIN roles r ON u.id_role=r.id WHERE u.id='.$id.' AND u.hash='.$hash;
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

    /**
     * Checks the existence of events in the given time frame
     * @param $param
     * @return bool
     */
    protected function checkEvent($param)
    {
        //IF Add
        $dateStart = new DateTime();
        $dateEnd = new DateTime();
        $dateStart->setTimestamp($param['dateTimeStart']/1000);
        $dateEnd->setTimestamp($param['dateTimeEnd']/1000);
        $day = $dateStart->format('Y-m-d');
        $day = $this->pdo->quote($day.'%');
        $idRoom = $this->pdo->quote($param['id_room']);
        $sql = 'SELECT '
            .' time_start,'
            .' time_end'
            .' FROM events'
            .' WHERE'
            .' time_start'
            .' LIKE'
            .' '.$day
            .' AND id_room ='.$idRoom;
        //IF update
        if (!empty($param['event_id']))
        {
            $idEvent = $this->pdo->quote($param['event_id']);
            $sql .= ' AND id !='.$idEvent;
        }

        $data = $this->selectQuery($sql);
        if (!is_array($data))
        {
            return true;
        }
        foreach ($data as $val)
        {
            if (!((new DateTime($val['time_start']) < $dateStart && new DateTime($val['time_end']) <= $dateStart)
                || ($dateEnd <= new DateTime($val['time_start']) && $dateEnd < new DateTime($val['time_end']))))
            {
                return false;
            }
        }
        return true;
    }
}