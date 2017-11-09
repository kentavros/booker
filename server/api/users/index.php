<?php
/**
 * The REST Controller - Users, provides access through requests:
 * GET, POST, PUT, DELETE
 * Accepts data, sends to the model, after processing returns
 */
include '../../app/lib/function.php';
class Users extends RestServer
{
    private $model;
    private $response;

    /**
     * create obj - model & response
     * parent run method
     * Users constructor.
     */
    public function __construct()
    {
        $this->model = new ModelUsers();
        $this->response = new Response();
        $this->run();
    }

    /**
     * Get the requested users, method - GET
     * @param $param | array
     * @return $result | array | string OR error
     */
    public function getUsers($param)
    {
        try
        {
                $result = $this->model->getUsers($param);
                $result = $this->encodedData($result);
                return $this->response->serverSuccess(200, $result);
        }
        catch (Exception $exception)
        {
            return $this->response->serverError(500, $exception->getMessage());
        }
    }

    /**
     * Create (add) new users in DB, method - POST
     * @param $param | array
     * @return $result | array | string OR error
     */
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

    /**
     * Edit/Update events in DB,
     * Get login users in app
     * method - PUT
     * @param $param | array
     * @return $result | array - hash | string OR error
     */
    public function putUsers($param)
    {
        try
        {
            if (isset($param['hash']) && isset($param['id_user']))
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

    /**
     * Delete users in DB, method - DELETE
     * @param $param | array
     * @return $result | string OR error
     */
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
