<?php
/**
 * The REST Controller - Events, provides access through requests:
 * GET, POST, PUT, DELETE
 * Accepts data, sends to the model, after processing returns
 */
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

    /**
     * Get the requested events, method - GET
     * @param $param | array
     * @return $result | array | string OR error
     */
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

    /**
     * Create (add) new events in DB, method - POST
     * @param $param | array
     * @return $result | array | string OR error
     */
    public function postEvents($param)
    {
        try
        {
            $result = $this->model->addEvents($param);
            $result = $this->encodedData($result);
            return $this->response->serverSuccess(200, $result);
        }
        catch (Exception $exception)
        {
            return $this->response->serverError(500, $exception->getMessage());
        }
    }

    /**
     * Edit/Update events in DB, method - PUT
     * @param $param | array
     * @return $result | array | string OR error
     */
    public function putEvents($param)
    {
        try
        {
            $result = $this->model->editEvent($param);
            $result = $this->encodedData($result);
            return $this->response->serverSuccess(200, $result);
        }
        catch (Exception $exception)
        {
            return $this->response->serverError(500, $exception->getMessage());
        }
    }

    /**
     * Delete events in DB, method - DELETE
     * @param $param | array
     * @return $result | string OR error
     */
    public function deleteEvents($param)
    {
        try
        {
            $result = $this->model->deleteEvent($param);
            return $this->response->serverSuccess(200, $result);

        }
        catch (Exception $exception)
        {
            return $this->response->serverError(500, $exception->getMessage());
        }
    }
}
$events = new Events();