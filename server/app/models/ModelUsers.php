<?php
class ModelUsers extends ModelDB
{
    public function checkUser($param)
    {
        if (empty($param['id']))
        {
            return ERR_DATA;
        }
        $id = $this->pdo->quote($param['id']);
        $sql = 'SELECT u.hash,'
            .' u.login,'
            .' r.name as role'
            .' FROM users u'
            .' LEFT JOIN roles r'
            .' ON u.id_role=r.id'
            .' WHERE u.id='.$id;
        $data = $this->selectQuery($sql);
        return $data;
    }

    public function loginUser($param)
    {
        if (!empty($param['login']) && !empty($param['pass']))
        {
            $pass = md5(md5(trim($param['pass'])));
            $login = $this->pdo->quote($param['login']);
            $id = '';
            $role = '';
            $sql = 'SELECT u.id,'
                .' r.name as role,'
                .' u.pass'
                .' FROM users u'
                .' LEFT JOIN roles r'
                .' ON u.id_role=r.id'
                .' WHERE login='.$login;
            $data = $this->selectQuery($sql);
            if (is_array($data))
            {
                foreach ($data as $val)
                {
                    if ($pass !== $val['pass'])
                    {
                        return ERR_AUTH;
                    }
                    else
                    {
                        $id = $this->pdo->quote($val['id']);
                        $role = $val['role'];
                    }
                }
            }
            else
            {
                return ERR_SEARCH;
            }
            $hash = $this->pdo->quote(md5($this->generateHash(10)));
            $sql = 'UPDATE users SET hash='.$hash.' WHERE id='.$id;
            $count = $this->execQuery($sql);
            if ($count === false)
            {
                return ERR_QUERY;
            }
            $id = trim($id, "'");
            $hash = trim($hash, "'");
            $login = trim($login, "'");
            $arrRes = ['id'=>$id, 'login'=>$login, 'hash'=>$hash, 'role'=>$role];
            return $arrRes;
        }
        else
        {
            return ERR_FIELDS;
        }
    }

    /**
     * random hash generate for user
     * @param int $length
     * @return string
     */
    public function generateHash($length=6)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
        $code = "";
        $clen = strlen($chars) - 1;
        while (strlen($code) < $length)
        {
            $code .= $chars[mt_rand(0,$clen)];
        }
        return $code;
    }
}