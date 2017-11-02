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