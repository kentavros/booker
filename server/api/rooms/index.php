<?php
/**
 * The REST Controller - Rooms, provides access through requests:
 * GET
 * Accepts data, sends to the model, after processing returns
 */
include '../../app/lib/function.php';
class Rooms extends RestServer
{
    private $model;
    private $response;

    /**
     * create obj - model & response
     * parent run method
     * Rooms constructor.
     */
    public function __construct()
    {
        $this->model = new ModelRooms();
        $this->response = new Response();
        $this->run();
    }

    /**
     * Get the requested rooms, method - GET
     * @param $param | array
     * @return $result | array | string OR error
     */
    public function getRooms($param)
    {
        try
        {
            $result = $this->model->getRooms($param);
            $result = $this->encodedData($result);
            return $this->response->serverSuccess(200, $result);
        }
        catch (Exception $exception)
        {
            return $this->response->serverError(500, $exception->getMessage());
        }
    }
}
$rooms = new Rooms();
