<?php
class Validator
{
    public function isValidateRegistration($param)
    {
        if (!empty($param['username']) && !empty($param['login']) && !empty($param['email']) && !empty($param['pass']) && !empty($param['id_role']))
        {
            if ($this->isUserName($param['username']))
            {
                if ($this->isLogin($param['login']))
                {
                    if ($this->isEmail($param['email']))
                    {
                        if ($this->isPass($param['pass']))
                        {
                            return true;
                        }
                        return INVAL_PASS;
                    }
                    return INVAL_EMAIL;
                }
                return INVAL_LOGIN;
            }
            return INVAL_USERNAME;
        }
        return ERR_FIELDS;
    }

    public function isValidateEdit($param)
    {
        if (!empty($param['username']) && !empty($param['email']) && !empty($param['role']))
        {
            if ($this->isUserName($param['username']))
            {
                    if ($this->isEmail($param['email']))
                    {
                        if (isset($param['pass']))
                        {
                            if ($this->isPass($param['pass']))
                            {
                                return true;
                            }
                            return INVAL_PASS;
                        }
                        return true;
                    }
                    return INVAL_EMAIL;
            }
            return INVAL_USERNAME;
        }
        return ERR_FIELDS;
    }

    public function isValidEventAdd($param)
    {
        if (!empty($param['booked_for']) && !empty($param['dateTimeStart']) && !empty($param['dateTimeEnd']) && !empty($param['description']))
        {
            if ($this->isLengthDescr($param['description']))
            {
                if ($this->isTStartNoMoreTEnd($param['dateTimeStart'], $param['dateTimeEnd']))
                {
                    if ($this->isNoWeekend($param['dateTimeStart']))
                    {
                        if ($this->isValidTimeStEn($param['dateTimeStart'], $param['dateTimeEnd']))
                        {
                            if (!isset($param['recurringMethod']))
                            {
                                return true;
                            }
                            else
                            {
                                if ($this->isValidRecurring($param))
                                {
                                    return true;
                                }
                                return INVAL_RECURR;
                            }
                        }
                        return INVAL_TIME_S_E;
                    }
                    return INVAL_WEEKEND;
                }
                return INVAL_TIMEMORE;
            }
            return INVAL_DESCR;
        }
        return ERR_FIELDS;
    }

    private function isValidRecurring($param)
    {
        if ($param['recurringMethod'] == 'weekly' || $param['recurringMethod'] == 'bi-weekly' || $param['recurringMethod'] == 'monthly')
        {
            if (!empty($param['duration']))
            {
                $duration = (int)$param['duration'];
                switch ($param['recurringMethod'])
                {
                    case 'weekly':
                        if ($duration >= 1 && $duration < 5)
                        {
                            return true;
                        }
                        break;
                    case 'bi-weekly':
                        if ($duration >= 1 && $duration < 3)
                        {
                            return true;
                        }
                        break;
                    case 'monthly':
                        if ($duration == 1)
                        {
                            return true;
                        }
                        break;
                }
                return false;
            }
            return false;
        }
        return false;
    }


    private function isValidTimeStEn($start, $end)
    {
        $start = date("G", $start/1000);
        $end = date("G", $end/1000);
        if ($start >= TIME_START && $start < TIME_END)
        {
            if ($end >= TIME_START && $end <= TIME_END)
            {
                return true;
            }
        }
        return false;

    }

    public function isNoWeekend($start)
    {
        $start = date("w", $start/1000);
        if ($start == WEEKEND1 || $start == WEEKEND2)
        {
            return false;
        }
        return true;
    }

    private function isTStartNoMoreTEnd($start, $end)
    {
        if ($start < $end && $start != $end)
        {
            return true;
        }
        return false;
    }

    private function isLengthDescr($string)
    {
        if (!strlen($string) < 6)
        {
            return true;
        }
        return false;
    }

    private function isUserName($userName)
    {
        if (strlen($userName) > 3 || strlen($userName) < 35)
        {
            return true;
        }
        return false;
    }

    private function isLogin($login)
    {
        if (preg_match("/^[a-zA-Z0-9]{3,30}+$/",$login))
        {
            return true;
        }
        return false;
    }

    private function isEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            return true;
        }
        return false;
    }

    private function isPass($pass)
    {
        if (preg_match("/^[a-zA-Z0-9]{4,20}+$/",$pass))
        {
            return true;
        }
        return false;
    }
}
