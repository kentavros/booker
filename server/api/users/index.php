<?php
include '../../app/lib/function.php';
class Users extends RestServer
{
    private $model;
    private $response;

    /**
     * create obj - model, validator & response
     * parent run method
     * Users constructor.
     */
    public function __construct()
    {
        $this->model = new ModelUsers();
        $this->response = new Response();
        $this->run();
    }

    public function getUsers($param = false)
    {
        try
        {
            dump($param);
            exit();
        }
        catch (Exception $exception)
        {
            return $this->response->serverError(500, $exception->getMessage());
        }
    }

//    public function getBooks($param=false)
//    {
//        try
//        {
//            if ($param !== false)
//            {
//                $result = $this->model->getBooks($param);
//                $result = $this->encodedData($result);
//                return $this->response->serverSuccess(200, $result);
//            }
//            $result = $this->model->getBooks();
//            $result = $this->encodedData($result);
//            return $this->response->serverSuccess(200, $result);
//        }
//        catch(Exception $exception)
//        {
//            return $this->response->serverError(500, $exception->getMessage());
//        }
//    }
//
//    public function postBooks($param)
//    {
//        try
//        {
//            $result = $this->model->addBook($param);
//            $result = $this->encodedData($result);
//            return $this->response->serverSuccess(200, $result);
//        }
//        catch (Exception $exception)
//        {
//            return $this->response->serverError(500, $exception->getMessage());
//        }
//    }
//
//    public function putBooks($param)
//    {
//        try
//        {
//            $result = $this->model->editBook($param);
//            return $this->response->serverSuccess(200, $result);
//        }
//        catch (Exception $exception)
//        {
//            return $this->response->serverError(500, $exception->getMessage());
//        }
//    }
}
$users = new Users();