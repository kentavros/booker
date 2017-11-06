<?php
class ModelEvents extends ModelDB
{
    public function getEvents($param)
    {
        //SELECT * FROM `events` WHERE id=81 OR id_parent=81 AND time_start > NOW()
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
                $sql .= ' ORDER BY e.time_start';
                $data = $this->selectQuery($sql);
                return $data;
            }
            //get paren event by future time
            else if (isset($param['flag']) && $param['flag'] === 'parent')
            {
                unset($param['flag']);
                $idEvent = $this->pdo->quote($param['id']);
                $sql .= ' WHERE (e.id='.$idEvent.' OR e.id_parent='.$idEvent.') AND e.time_start > NOW()';
               // dump($sql);
                $data = $this->selectQuery($sql);
                return $data;
            }
          


            //get request by params
//            if (!empty($param)) {
//                if (is_array($param)) {
//                    $sql .= " WHERE ";
//                    foreach ($param as $key => $val) {
//                        $sql .= 'e.' . $key . '=' . $this->pdo->quote($val) . ' AND ';
//                    }
//                    $sql = substr($sql, 0, -5);
//                }
//                $sql .= ' ORDER BY e.id';
//            } else {
//                $sql .= ' ORDER BY e.id';
//            }
//            $data = $this->selectQuery($sql);
//            return $data;
        }
        else
        {
            return ERR_ACCESS;
        }
    }

    public function addEvents($param)
    {
        if ($this->checkData($param) == 'admin' || $this->checkData($param) == 'user')
        {
            $validate = $this->validator->isValidEventAdd($param);
            if ($validate === true)
            {
                $bookedFor = $this->pdo->quote($param['booked_for']);
                $idRoom = $this->pdo->quote($param['id_room']);
                $dateStart = new DateTime();
                $dateStart->setTimestamp($param['dateTimeStart']/1000);
                $dateS = $this->pdo->quote($dateStart->format(TIME_FORMAT));
                $dateEnd = new DateTime();
                $dateEnd->setTimestamp($param['dateTimeEnd']/1000);
                $dateE = $this->pdo->quote($dateEnd->format(TIME_FORMAT));
                $description = $this->pdo->quote($param['description']);
                    if($this->checkEvent($param) === true)
                    {
                        $sql = 'INSERT INTO events (id_user, id_room, description, time_start, time_end)'
                            .' VALUES ('.$bookedFor.', '.$idRoom.', '.$description.', '.$dateS.', '.$dateE.')';
                        $result = $this->execQuery($sql);
                        if (!isset($param['recurringMethod']))
                        {
                            return $result;
                        }
                        else
                        {
                            $param['id_parent'] = $this->pdo->lastInsertId();
                            $param['duration'] = (int)$param['duration'];
                            $result = $this->addRecurringEvent($param, $dateStart, $dateEnd);
                            return $result;
                        }
                    }
                    return ERR_ADDEVENT;
            }
           return $validate;
        }
        else
        {
            return ERR_ACCESS;
        }
    }

    private function addRecurringEvent($param, $dateStart, $dateEnd)
    {
        $arrErrors = [];
        for ($i=0; $i< $param['duration']; $i++)
        {
            $dateStart->modify($this->getEventPeriod($param['recurringMethod']));
            $dateEnd->modify($this->getEventPeriod($param['recurringMethod']));
            $dateS = $this->pdo->quote($dateStart->format(TIME_FORMAT));
            $dateE = $this->pdo->quote($dateEnd->format(TIME_FORMAT));
            $param['dateTimeStart'] = $dateStart->getTimestamp()*1000;
            $param['dateTimeEnd'] = $dateEnd->getTimestamp()*1000;
            $bookedFor = $this->pdo->quote($param['booked_for']);
            $description = $this->pdo->quote($param['description']);
            $idRoom = $this->pdo->quote($param['id_room']);
            if ($this->validator->isNoWeekend($param['dateTimeStart']))
            {
                if($this->checkEvent($param) === true)
                {
                    $sql = 'INSERT INTO events (id_user, id_room, description, time_start, time_end, id_parent)'
                        .' VALUES ('.$bookedFor.', '.$idRoom.', '.$description.', '.$dateS.', '.$dateE.', '.$param['id_parent'].')';
                    $result = $this->execQuery($sql);
                }
                else
                {
                    $arrErrors[]='Date and time is reserved by another user: '.$dateS.' - '.$dateE;
                }
            }
            else
            {
                $arrErrors[]= $dateS.' is a weekend. '. INVAL_WEEKEND;
            }
        }
        if (count($arrErrors) == 0)
        {
            return true;
        }
        return $arrErrors;
    }

    private function getEventPeriod($recurring)
    {
        $period = '';
        switch ($recurring)
        {
            case 'weekly':
                $period = '+1 week';
                break;
            case 'bi-weekly':
                $period = '+2 week';
                break;
            case 'monthly':
                $period = '+1 month';
                break;
        }
        return $period;
    }
}
