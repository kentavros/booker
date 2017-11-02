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
            if (isset($param['hash']) && isset($param['id_user']))
            {
                $result = $this->model->getUsers($param);
                $result = $this->encodedData($result);
                return $this->response->serverSuccess(200, $result);
            }
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
            $result = $this->model->addUser($param);
            return $this->response->serverSuccess(200, $result);

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
            if (isset($param['hash']) && isset($param['id_client']))
            {
                $result = $this->model->editUser($param);
                $result = $this->encodedData($result);
                return $this->response->serverSuccess(200, $result);
            }
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
            $result = $this->model->deleteUser($param);
            return $this->response->serverSuccess(200, $result);

        }
        catch (Exception $exception)
        {
            return $this->response->serverError(500, $exception->getMessage());
        }
    }
}
$users = new Users();
