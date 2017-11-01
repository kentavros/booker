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
            $result = $this->model->checkUser($param);
            $result = $this->encodedData($result);
            return $this->response->serverSuccess(200, $result);
        }
        catch (Exception $exception)
        {
            return $this->response->serverError(500, $exception->getMessage());
        }
    }

    public function postUsers($param)
    {
        try
        {
            dump($param);

        }
        catch (Exception $exception)
        {
            return $this->response->serverError(500, $exception->getMessage());
        }
    }

    public function putUsers($param)
    {
        try
        {
            $result = $this->model->loginUser($param);
            $result = $this->encodedData($result);
            return $this->response->serverSuccess(200, $result);
        }
        catch (Exception $exception)
        {
            return $this->response->serverError(500, $exception->getMessage());
        }
    }

    public function deleteUsers($param)
    {
        try
        {
            dump($param);

        }
        catch (Exception $exception)
        {
            return $this->response->serverError(500, $exception->getMessage());
        }
    }
}
$users = new Users();