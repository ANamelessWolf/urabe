<?php
include_once "Warai.php";
include_once "HasamiUtils.php";
include_once "HasamiRESTfulService.php";
/**
 * GET Service Class
 * Defines a RESTful service with the GET verbose that is often used to **select** data 
 * from the database. 
 * This service contains a collection of methods to do a selection to an ORACLE table.
 * @version 1.0.0
 * @api Makoto Urabe Oracle
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class GETService extends HasamiRESTfulService
{
    /**
     * __construct
     *
     * Initialize a new instance of the GET Service class.
     * 
     * @param HAsamiWrapper The web service wrapper
     */
    public function __construct($service)
    {
        parent::__construct($service);
        $this->service_task = function ($sender) {
            return $this->default_GET_action();
        };
    }
    /**
     * Defines the default GET action. The action selects all fields from the current table 
     * without using the parameters
     *
     * @return QueryResult|string The server response
     */
    public function default_GET_action()
    {
        try {
            if (!is_null($this->parameters) && !is_null($this->service->primary_key) &&
                $this->parameters->exists($this->service->primary_key)) {
                $id = $this->parameters->get_value($this->service->primary_key);
                return select_by_primary_key($service, $id, $this->response_is_encoded);
            } else {
                return select_all($this->service, $this->response_is_encoded);
            }
        } catch (Exception $e) {
            return error_response($e->getMessage());
        }
    }
}
?>