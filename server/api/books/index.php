<?php
include '../../app/lib/function.php';
class Books extends RestServer
{
    private $model;
    private $response;

    /**
     * create obj - model & response
     * parent run method
     * Books constructor.
     */
    public function __construct()
    {
        $this->model = new ModelBooks();
        $this->response = new Response();
        $this->run();
    }

    public function getBooks($param=false)
    {
        try
        {
            if ($param !== false)
            {
                $result = $this->model->getBooks($param);
                $result = $this->encodedData($result);
                return $this->response->serverSuccess(200, $result);
            }
            $result = $this->model->getBooks();
            $result = $this->encodedData($result);
            return $this->response->serverSuccess(200, $result);
        }
        catch(Exception $exception)
        {
            return $this->response->serverError(500, $exception->getMessage());
        }
    }

    public function postBooks($param)
    {
        try
        {
            $result = $this->model->addBook($param);
            $result = $this->encodedData($result);
            return $this->response->serverSuccess(200, $result);
        }
        catch (Exception $exception)
        {
            return $this->response->serverError(500, $exception->getMessage());
        }
    }

    public function putBooks($param)
    {
        try
        {
            $result = $this->model->editBook($param);
            return $this->response->serverSuccess(200, $result);
        }
        catch (Exception $exception)
        {
            return $this->response->serverError(500, $exception->getMessage());
        }
    }
}
$books = new Books();