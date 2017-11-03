<?php
class ModelEvents extends ModelDB
{
    public function getEvents($param)
    {

        if ($this->checkData($param) == 'admin' || $this->checkData($param) == 'user')
        {
            unset($param['hash'], $param['id_user']);
            $sql = 'SELECT '
                . ' e.id,'
                . ' e.id_user,'
                . ' u.username as user_name,'
                . ' e.id_room,'
                . ' r.name as room_name,'
                . ' e.description,'
                . ' e.time_start,'
                . ' e.time_end,'
                . ' e.id_parent,'
                . ' e.create_time'
                . ' FROM events e'
                . ' LEFT JOIN users u'
                . ' ON e.id_user=u.id'
                . ' LEFT JOIN rooms r'
                . ' ON e.id_room=r.id';
            //get request LIKE
            if (isset($param['flag']) && $param['flag'] === 'like')
            {
                unset($param['flag']);
                $sql .= " WHERE ";
                foreach ($param as $key => $val)
                {
                    $sql .= $key.' LIKE '.$this->pdo->quote($val.'-%').' AND ';
                }
                $sql = substr($sql, 0, -5);
                $sql .= ' ORDER BY e.id';
                $data = $this->selectQuery($sql);
                return $data;
            }
            //get request by params
            if (!empty($param)) {
                if (is_array($param)) {
                    $sql .= " WHERE ";
                    foreach ($param as $key => $val) {
                        $sql .= 'e.' . $key . '=' . $this->pdo->quote($val) . ' AND ';
                    }
                    $sql = substr($sql, 0, -5);
                }
                $sql .= ' ORDER BY e.id';
            } else {
                $sql .= ' ORDER BY e.id';
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