<?php
class ModelUsers extends ModelDB
{
    public function getUsers($param)
    {
        if ($this->checkData($param) == 'admin')
        {
            unset($param['hash'], $param['id_user']);
            $sql = 'SELECT'
                .' u.id,'
                .' r.name as role,'
                .' u.id_role,'
                .' u.login,'
                .' u.email,'
                .' u.username'
                .' FROM users u'
                .' LEFT JOIN roles r'
                .' ON u.id_role=r.id';
            if (!empty($param))
            {
                if (is_array($param))
                {
                    $sql .= " WHERE ";
                    foreach ($param as $key => $val)
                    {
                        $sql .= 'u.'.$key.'='.$this->pdo->quote($val).' AND ';
                    }
                    $sql = substr($sql, 0, -5);
                }
                $sql .= ' ORDER BY u.id';
            }
            else
            {
                $sql .= ' ORDER BY u.id';
            }
            $data = $this->selectQuery($sql);
            return $data;
        }
        else
        {
            return ERR_ACCESS;
        } 
    }


    public function checkUser($param)
    {
        if (empty($param['id']))
        {
            return ERR_DATA;
        }

        $id = $this->pdo->quote($param['id']);
        $sql = 'SELECT u.hash,'
            .' u.login,'
            .' u.username,'
            .' r.name as role'
            .' FROM users u'
            .' LEFT JOIN roles r'
            .' ON u.id_role=r.id'
            .' WHERE u.id='.$id;
        $data = $this->selectQuery($sql);
        return $data;
    }

    public function addUser($param)
    {
        if ($this->checkData($param) == 'admin')
        {
            $validate = $this->validator->isValidateRegistration($param);
            if ($validate === true)
            {
                $userName = $this->pdo->quote($param['username']);
                $id_role = $this->pdo->quote($param['id_role']);
                $login = $this->pdo->quote($param['login']);
                $pass = md5(md5(trim($param['pass'])));
                $pass = $this->pdo->quote($pass);
                $email = $this->pdo->quote($param['email']);
                $sql = 'INSERT INTO users (id_role, login, pass, username, email) VALUES ('.$id_role.', '.$login.', '.$pass.', '.$userName.', '.$email.')';
                $result = $this->execQuery($sql);
                if ($result === false)
                {
                    return ERR_LOGIN;
                }
                return $result;
            }
            return $validate;
        }
        return ERR_ACCESS;
    }

    public function editUser($param)
    { 
        if ($this->checkData($param) == 'admin')
        {
            //dump($param); 
            $validate = $this->validator->isValidateEdit($param);
            if ($validate === true)
            {
                $id = $this->pdo->quote($param['id']);
                $userName = $this->pdo->quote($param['username']);
                $role = $this->pdo->quote($param['role']);
                $email = $this->pdo->quote($param['email']);
                $sql = 'UPDATE users SET'
                    .' username='.$userName.','
                    .' id_role='.$role.','
                    .' email='.$email;
                if(isset($param['pass']))
                {
                    $pass = md5(md5(trim($param['pass'])));
                    $pass = $this->pdo->quote($pass);
                    $sql .=', pass='.$pass; 
                }
                $sql .=' WHERE id='.$id;
                $data = $this->execQuery($sql);
                return $data;
            }
        }
        return ERR_ACCESS;
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

    public function deleteUser($param)
    {
        if ($this->checkData($param) == 'admin')
        {
            //check with what role the user being deleted
            if ($this->getRole($param['id']) == 'user')
            {
                $id = $this->pdo->quote($param['id']);
                //Delete future events
                $sql = 'DELETE FROM events WHERE id_user='.$id.' AND time_start > NOW()';
                $this->execQuery($sql);
                //Delete User
                $sql = 'DELETE FROM users WHERE id='.$id;
                $result = $this->execQuery($sql);
                return $result;
            }
            else
            {
                //Check that the admin is not alone
                $sql = 'SELECT count(id_role) as sum FROM users WHERE id_role=2';
                $data = $this->selectQuery($sql);
                if ($data[0]['sum'] > 1)
                {
                    // not alone - delete!
                    $id = $this->pdo->quote($param['id']);
                    $sql = 'DELETE FROM users WHERE id='.$id;
                    $result = $this->execQuery($sql);
                    return $result;
                }
                //alone - no delete
                return ERR_A_DEL;
            }

        }
        return ERR_ACCESS;
    }

    private function getRole($id)
    {
        $id = $this->pdo->quote($id);
        $sql = 'SELECT r.name as role FROM users u LEFT JOIN roles r ON u.id_role=r.id WHERE u.id='.$id;
        $data = $this->selectQuery($sql);
        if (is_array($data))
        {
            return $data[0]['role'];
        }
        return false;
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
