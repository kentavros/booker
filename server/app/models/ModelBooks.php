<?php
class ModelBooks extends ModelDB
{
    /**
     * Get All books or book by param - id, price etc/
     * @param bool $param
     * @param $param['hash'] & $param['id_client'] & $param['id'] -book
     * @return array|string
     */
    public function getBooks($param=false)
    {
        //check admin request or user request
        if (!isset($param['hash']) && !isset($param['id_client']))
        {
            //User request
            $sql = 'SELECT b.id as id,'
                .' b.title,'
                .' b.price,'
                .' b.description,'
                .' b.discount,'
                .' b.img,'
                .' a.id as a_id,'
                .' a.name as a_name,'
                .' g.id as g_id,'
                .' g.name as g_name'
                .' FROM books b '
                .' LEFT JOIN book_to_author ba'
                .' ON b.id=ba.id_book'
                .' LEFT JOIN authors a'
                .' ON a.id=ba.id_author'
                .' LEFT JOIN book_to_genre bg'
                .' ON b.id=bg.id_book'
                .' LEFT JOIN genres g'
                .' ON bg.id_genre=g.id'
                .' WHERE active="yes"';
            if ($param !== false)
            {
                if (is_array($param))
                {
                    $sql .= " AND ";
                    foreach ($param as $key => $val)
                    {
                        $sql .= 'b.'.$key.'='.$this->pdo->quote($val).' AND ';
                    }
                    $sql = substr($sql, 0, -5);
                }
                $sql .= ' ORDER BY b.id';
            }
            else
            {
                $sql .= ' ORDER BY b.id';
            }
            $data = $this->selectQuery($sql);
            $result = $this->filteredBooks($data);
            return $result;
        }
        else
        {
            //Admin request
            if ($this->checkData($param) == 'admin')
            {
                unset($param['hash'], $param['id_client']);
                $sql = 'SELECT b.id as id,'
                    .' b.title,'
                    .' b.price,'
                    .' b.description,'
                    .' b.discount,'
                    .' b.active,'
                    .' b.img,'
                    .' a.id as a_id,'
                    .' a.name as a_name,'
                    .' g.id as g_id,'
                    .' g.name as g_name'
                    .' FROM books b '
                    .' LEFT JOIN book_to_author ba'
                    .' ON b.id=ba.id_book'
                    .' LEFT JOIN authors a'
                    .' ON a.id=ba.id_author'
                    .' LEFT JOIN book_to_genre bg'
                    .' ON b.id=bg.id_book'
                    .' LEFT JOIN genres g'
                    .' ON bg.id_genre=g.id';
                if (!empty($param))
                {
                    if (is_array($param))
                    {
                        $sql .= " WHERE ";
                        foreach ($param as $key => $val)
                        {
                            $sql .= 'b.'.$key.'='.$this->pdo->quote($val).' AND ';
                        }
                        $sql = substr($sql, 0, -5);
                    }
                    $sql .= ' ORDER BY b.id';
                }
                else
                {
                    $sql .= ' ORDER BY b.id';
                }
                $data = $this->selectQuery($sql);
                $result = $this->filteredBooks($data);
                return $result;
            }
            else
            {
                return ERR_ACCESS;
            }

        }

    }

    /**
     * Cleans and copies received after the request, the book - author - genre
     * @param $data
     * @return array
     */
    public function filteredBooks($data)
    {
        $arr = [];
        foreach ($data as $val)
        {
            if (!isset($arr[$val['id']]))
            {
                $arr[$val['id']] = $val;
            }
            if ($arr[$val['id']]['id'] == $val['id'])
            {
                $arr[$val['id']]['authors'][] = ['id'=>$val['a_id'], 'name'=>$val['a_name']];
                $arr[$val['id']]['genres'][] = ['id'=>$val['g_id'], 'name'=>$val['g_name']];
                unset($arr[$val['id']]['a_id'], $arr[$val['id']]['a_name'], $arr[$val['id']]['g_id'], $arr[$val['id']]['g_name']);
            }
            //Remove duplicate elements of a multidimensional array
            $arr[$val['id']]['authors'] = array_map("unserialize", array_unique(array_map("serialize", $arr[$val['id']]['authors'])));
            $arr[$val['id']]['genres'] = array_map("unserialize", array_unique(array_map("serialize", $arr[$val['id']]['genres'])));
        }
        //Reindex arr
        $arr = array_values($arr);
        return $arr;
    }

    public function addBook($param)
    {
        if (isset($param['hash']) && isset($param['id_client']))
        {
            if ($this->checkData($param) == 'admin')
            {
                //Validate all fields - not empty
                if (empty($param['title']) || empty($param['price']) || empty($param['description'])
                    || empty($param['active']) || empty($param['img']))
                {
                    return ERR_FIELDS;
                }
                //Discount
                if (filter_var($param['discount'], FILTER_VALIDATE_INT) || filter_var($param['discount'],FILTER_VALIDATE_FLOAT)
                    || $param['discount'] == '0' || $param['discount'] == '0.00')
                {
                    if ((int)$param['discount'] < 0 || (int)$param['discount'] > 99)
                    {
                        return ERR_DISC_INC;
                    }
                    $discount = $this->pdo->quote($param['discount']);
                }
                else
                {
                    return ERR_DISC;
                }
                //Price
                if (filter_var($param['price'], FILTER_VALIDATE_INT) || filter_var($param['price'],FILTER_VALIDATE_FLOAT)
                    || $param['price'] === '0' || $param['price'] === '0.00')
                {
                    if ((int)$param['price'] < 0)
                    {
                        return ERR_PRICE;
                    }
                    $price = $this->pdo->quote($param['price']);
                }
                else
                {
                    return ERR_PRICE;
                }
                $title = $this->pdo->quote($param['title']);
                $description = $this->pdo->quote($param['description']);
                $active = $this->pdo->quote($param['active']);
                $img = $this->pdo->quote($param['img']);
                $sql = 'INSERT INTO books'
                    .' ('
                    .'title,'
                    .' price,'
                    .' description,'
                    .' discount,'
                    .' active,'
                    .' img'
                    .') VALUES ('
                    .$title.', '
                    .$price.', '
                    .$description.', '
                    .$discount.', '
                    .$active.', '
                    .$img.')';
                $this->execQuery($sql);
                $result['id_book'] = $this->pdo->lastInsertId();
                return $result;
            }
            else
            {
                return ERR_ACCESS;
            }
        }
        else
        {
            return ERR_ACCESS;
        }
    }

    public function editBook($param)
    {
        if (isset($param['hash']) && isset($param['id_client']))
        {
            if ($this->checkData($param) == 'admin')
            {
                //Validate all fields - not empty
                if (empty($param['title']) || empty($param['price']) || empty($param['description'])
                    || empty($param['active']) || empty($param['img']))
                {
                    return ERR_FIELDS;
                }
                //Discount
                if (filter_var($param['discount'], FILTER_VALIDATE_INT) || filter_var($param['discount'],FILTER_VALIDATE_FLOAT)
                    || $param['discount'] == '0' || $param['discount'] == '0.00')
                {
                    if ((int)$param['discount'] < 0 || (int)$param['discount'] > 99)
                    {
                        return ERR_DISC_INC;
                    }
                    $discount = $this->pdo->quote($param['discount']);
                }
                else
                {
                    return ERR_DISC;
                }
                //Price
                if (filter_var($param['price'], FILTER_VALIDATE_INT) || filter_var($param['price'],FILTER_VALIDATE_FLOAT)
                    || $param['price'] === '0' || $param['price'] === '0.00')
                {
                    if ((int)$param['price'] < 0)
                    {
                        return ERR_PRICE;
                    }
                    $price = $this->pdo->quote($param['price']);
                }
                else
                {
                    return ERR_PRICE;
                }
                $id = $this->pdo->quote($param['id']);
                $title = $this->pdo->quote($param['title']);
                $description = $this->pdo->quote($param['description']);
                $active = $this->pdo->quote($param['active']);
                $img = $this->pdo->quote($param['img']);
                $sql = 'UPDATE books SET'
                    .' title='.$title
                    .', price='.$price
                    .', description='.$description
                    .', discount='.$discount
                    .', active='.$active
                    .', img='.$img
                    .' WHERE id='.$id;
                $result = $this->execQuery($sql);
                return $result;
            }
            else
            {
                return ERR_ACCESS;
            }
        }
        else
        {
            return ERR_ACCESS;
        }
    }
}