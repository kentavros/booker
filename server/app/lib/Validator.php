<?php

/**
 * Class Validator - Validates data sent
 * by client applications to the  REST
 * application
 */
class Validator
{
    /**
     * Validation of registration form fields when adding a user
     * @param $param
     * @return bool|string
     */
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

    /**
     * Validate fields when editing user data
     * @param $param
     * @return bool|string
     */
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

    /**
     * Validation of adding events
     * @param $param
     * @return bool|string
     */
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

    /**
     * Validation of the recursive method and the amount of recursion
     * @param $param
     * @return bool
     */
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

    /**
     * Validation time of the beginning and end of the event
     * within the constants of the beginning and the end
     * @param $start
     * @param $end
     * @return bool
     */
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

    /**
     * check the date $start of the day off or weekday
     * @param $start
     * @return bool
     */
    public function isNoWeekend($start)
    {
        $start = date("w", $start/1000);
        if ($start == WEEKEND1 || $start == WEEKEND2)
        {
            return false;
        }
        return true;
    }

    /**
     * Validation start time is no longer than the end time of an event
     * @param $start
     * @param $end
     * @return bool
     */
    private function isTStartNoMoreTEnd($start, $end)
    {
        if ($start < $end && $start != $end)
        {
            return true;
        }
        return false;
    }

    /**
     * Validate the number of characters in the field description
     * @param $string
     * @return bool
     */
    private function isLengthDescr($string)
    {
        if (strlen($string) > 6)
        {
            return true;
        }
        return false;
    }

    /**
     * Validate the number of characters in the field
     * @param $userName
     * @return bool
     */
    private function isUserName($userName)
    {
        if (strlen($userName) > 3 && strlen($userName) < 35)
        {
            return true;
        }
        return false;
    }

    /**
     * Validate the number of characters in the field and characters
     * @param $login
     * @return bool
     */
    private function isLogin($login)
    {
        if (preg_match("/^[a-zA-Z0-9]{3,30}+$/",$login))
        {
            return true;
        }
        return false;
    }

    /**
     * Validate email format
     * @param $email
     * @return bool
     */
    private function isEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            return true;
        }
        return false;
    }

    /**
     * Validate the number of characters in the field and characters
     * @param $pass
     * @return bool
     */
    private function isPass($pass)
    {
        if (preg_match("/^[a-zA-Z0-9]{4,20}+$/",$pass))
        {
            return true;
        }
        return false;
    }
}
