<?php
include '../../app/lib/function.php';
class Events extends RestServer
{
    private $model;
    private $response;

    /**
     * create obj - model & response
     * parent run method
     * Events constructor.
     */
    public function __construct()
    {
        $this->model = new ModelEvents();
        $this->response = new Response();
        $this->run();
    }

    public function getEvents($param)
    {
        try
        {
            $result = $this->model->getEvents($param);
            $result = $this->encodedData($result);
            return $this->response->serverSuccess(200, $result);
        }
        catch (Exception $exception)
        {
            return $this->response->serverError(500, $exception->getMessage());
        }
    }
}
$events = new Events();
