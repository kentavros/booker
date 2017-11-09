<?php
/**
 * Class RestServer - Accepts requests
 * with data from client applications.
 * Handles them. Checks the existence
 * of a class, a method, depending on the
 * existing method of sending the request,
 * gets the passed parameters that passes
 * to a certain class, the method for
 * later processing - sets the class method.
 * It is an inheritable class of all REST
 * application controllers.
 */
class RestServer
{
    protected $reqMethod;
    protected $url;
    protected $class;
    protected $data;
    protected $encode = ENCODE_DEFAULT;

    /**
     * Run - application entry point
     * Send headers, set Method of the classes,
     * request method ang getData sents
     */
    protected function run()
    {    
        $this->url = $_SERVER['REQUEST_URI'];
        $this->reqMethod = $_SERVER['REQUEST_METHOD'];
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: PUT, POST, GET, DELETE');
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        switch ($this->reqMethod)
        {
            case 'GET':
                $this->setMethod('get'.ucfirst($this->getClass()), $this->getData());
                break;
            case 'POST':
                $this->setMethod('post'.ucfirst($this->getClass()), $this->getData());
                break;
            case 'PUT':
                $this->setMethod('put'.ucfirst($this->getClass()), $this->getData());
                break;
            case 'DELETE':
                $this->setMethod('delete'.ucfirst($this->getClass()), $this->getData());
                break;
            case 'OPTIONS':
                exit();
                break;
        }
    }

    /**
     * Checks the existence of a method that calls it
     * @param $classMethod
     * @param bool $data
     */
    protected function setMethod($classMethod, $data=false)
    {
        if(method_exists($this, $classMethod))
        {
            echo $this->$classMethod($data);
        }
        else
        {
            header("HTTP/1.0 405 Method Not Allowed");
            echo $this->class.'ERROR';
        }
    }

    /**
     * Get class from url after /api/
     * @return mixed
     */
    protected function getClass()
    {
        //Cut for /api/
        $clearUrl = explode('/api/', $this->url);
        //Get class
        $clearUrl = explode('/', $clearUrl[count($clearUrl)-1]);
        $this->class = $clearUrl[0];
        return $this->class;
    }

    /**
     * Get data from request methods (GET, POST, PUT, DELETE)
     * @return array|bool|mixed
     */
    protected function getData()
    {
        if (($this->reqMethod == 'GET') || ($this->reqMethod == 'DELETE'))
        {
            //Cut for /api/
            $clearUrl = explode('/api/', $this->url);
            $clearUrl = explode('/', $clearUrl[count($clearUrl) - 1], 2);
            //Get data
            $data = $clearUrl[count($clearUrl) - 1];
            //Get encode type
            preg_match('#(\.[a-z]+)#', $data, $match);
            if (!empty($match[0]))
            {
                $this->encode = $match[0];
                $data = trim($data, $this->encode);
            }
            //Cut extension
            $data = explode('/', $data);
            if (count($data) % 2) {
                //Odd values
                $id = (int)$data[count($data) - 1];
                $data = [];
                $data['id'] = $id;
                if ($data['id'] === 0)
                {
                    $data = false;
                }
            } else {
                //Even value
                $arrEven = [];
                $arrOdd = [];
                foreach ($data as $key => $val) {
                    if ($key % 2) {
                        $arrOdd[] = urldecode($val);
                    } else {
                        $arrEven[] = $val;
                    }
                }
                $data = array_combine($arrEven, $arrOdd);
            }
            $this->data = $data;
            return $this->data;
        }
        elseif ($this->reqMethod == 'POST') 
        {
            $this->data = $_POST;
            return $this->data;
        }
        elseif ($this->reqMethod == 'PUT')
        {
//            parse_str(file_get_contents("php://input"), $putParams);
//            $this->data = $putParams;
            $this->data = json_decode(file_get_contents("php://input"), true);
            return $this->data;
        }
    }

    /**
     * Method Converting data into extensions
     * .json, .txt, .xhtml, .xml
     * ENCODE_DEFAULT - constant in config file (config.php)
     * @param $data
     * @return mixed|string
     */
    protected function encodedData($data)
    {
        switch ($this->encode)
        {
            case '.json':
                header('Content-Type: application/json');
                return json_encode($data);
                break;
            case '.txt':
                header("Content-type: text/javascript");
                return print_r($data, true);
                break;
            case '.xhtml':
                header('Content-Type: text/html; charset=utf-8');
                $str = '<head></head><body><pre>';
                $str .= print_r($data, true);
                $str .= '</pre></body>';
                return $str;
                break;
            case '.xml':
                header("Content-type: text/xml");
                $xml = new SimpleXMLElement('<?xml version="1.0"?><data></data>');
                $this->toXml($data, $xml);
                return $xml->asXML();
                break;
        }
    }

    /**
     * Auxiliary method for data conversion to xml format
     * @param $data
     * @param $xml
     */
    private function toXml($data, $xml)
    {
        foreach($data as $key=>$val)
        {
        if(is_numeric($key))
        {
            $key = 'book'.$key;
        }
        if(is_array($val))
        {
            $subnode = $xml->addChild($key);
            $this->toXml($val, $subnode);
        }
        else
        {
            $xml->addChild("$key",htmlspecialchars("$val"));
        }
        }
    }
}
