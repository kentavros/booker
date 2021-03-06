<?php

/**
 * Class ModelRooms - model Rooms,
 * works with controller Rooms.
 * Receives data from the controller,
 * works with its own table and returns
 * data to its controller
 */
class ModelRooms extends ModelDB
{
    /**
     * Get list All Rooms or By Param
     * @param $param
     * @return array|string
     */
    public function getRooms($param)
    {
        if ($this->checkData($param) == 'admin' || $this->checkData($param) == 'user' )
        {
            unset($param['hash'], $param['id_user']);
            $sql = 'SELECT id, name FROM rooms';
            if ($param != false) {
                if (is_array($param)) {
                    $sql .= " WHERE ";
                    foreach ($param as $key => $val) {
                        $sql .= $key . '=' . $this->pdo->quote($val) . ' AND ';
                    }
                    $sql = substr($sql, 0, -5);
                }
                $sql .= ' ORDER BY id';
            } else {
                $sql .= ' ORDER BY id';
            }
            $data = $this->selectQuery($sql);
            return $data;
        }
        else
        {
            return ERR_ACCESS;
        }
    }
}