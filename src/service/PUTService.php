<?php

namespace Urabe\Service;

use Exception;
use Urabe\Service\WebServiceContent;
use Urabe\Service\HasamiRESTfulService;
use Urabe\Urabe;

/**
 * PUT Service Class
 * This class defines a restful service with a request verbose PUT. 
 * This method is often used to insert data to the database. 
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class PUTService extends HasamiRESTfulService
{
    /**
     * @var array The collection of required fields.
     * Gets the list of required names
     */
    public $required;
    /**
     *  @var array The values to insert
     */
    public $values;
    /**
     *  @var string The type of insertion 
     *  single/bulk
     */
    public $insertionType;
    /**
     *  @var array The collection of insertion fields.
     */
    public $insert_columns;
    /**
     * __construct
     *
     * Initialize a new instance of the PUT Service class.
     * A default service task is defined as a callback using the function PUTService::default_PUT_action
     * 
     * @param WebServiceContent $data The web service content
     * @param Urabe $urabe The database manager
     * @param string $primary_key The primary key name
     * @param string $sel_filter The selection filter.
     */
    public function __construct($data, $urabe, $primary_key = null)
    {
        //1: Initialize parent
        parent::__construct($data, $urabe, "default_PUT_action");
        $body = $this->get_body();
        $this->primary_key = $primary_key;
        //2: Initialize service
        $this->values = $body->content->values;
        if (!is_array($this->values)) {
            $this->insertionType = "single";
            $this->values = $this->filter((array)$this->values);
            $this->insert_columns = array_keys($this->values);
        } else {
            $this->insertionType = "bulk";
            $bulk = array();
            foreach ($this->values as &$value)
                array_push($bulk, $this->filter((array)$this->value));
            $this->values = $bulk;
            $this->insert_columns = array_keys($bulk[0]);
        }
        //3: Validate body
        $this->required = $this->get_required_columns();
        $this->validate($body);
    }
    /**
     * Wraps the insert function from urabe
     * @param string $table_name The table name.
     * @param array $values The values to insert as key value pair
     * Example: 
     * array("column1" => value1, "column2" => value2)
     * @throws Exception An Exception is raised if the connection is null or executing a bad query
     * @return UrabeResponse Returns the service response formatted as an executed response
     */
    public function insert($table_name, $values)
    {
        return $this->urabe->executor->insert($table_name, $values);
    }
    /**
     * Wraps the insert_bulk function from urabe
     *
     * @param string $table_name The table name.
     * @param array $values The values to insert as key value pair array. 
     * Example: 
     * array(
     *  array("column1" => value1),
     *  array("column2" => value2)
     * )
     * @throws Exception An Exception is raised if the connection is null or executing a bad query
     * @return UrabeResponse Returns the service response formatted as an executed response
     */
    public function insert_bulk($table_name, $values)
    {
        return $this->urabe->executor->insert_bulk($table_name, $values);
    }
    /**
     * Validates the content data before insert
     *
     * @param WebServiceBody $body The request content body
     * @throws Exception An Exception is thrown if the request content is not valid
     */
    public function validate($body)
    {
        //Validate column data
        $this->validate_columns($this->insert_columns, $this->required);
        //Validate the body contains the values field
        if (!$body->exists(NODE_VAL))
            throw new Exception(sprintf(ERR_INCOMPLETE_DATA, NODE_VAL));
    }

    /**
     * Select the max id from a given table
     *
     * @param Urabe $urabe The database manager
     * @param string $table_name The table name 
     * @param string $col_id_name The column name for the id
     * @return int The maxe id vaue
     */
    public function get_max_id($urabe, $table_name, $col_id_name)
    {
        $sql = "SELECT MAX($col_id_name) $col_id_name FROM $table_name";
        $id = $urabe->selector->select_one($sql);
        return $id;
    }

    /**
     * Defines the default PUT action, by default execute an insertion query with the given data passed
     * in the body properties insert_values
     * @param HasamiRESTfulService $service The web service that executes the action
     * @throws Exception An Exception is thrown if the response can be processed correctly
     * @return UrabeResponse The server response
     */
    public function default_PUT_action($service)
    {
        try {
            $table = $service->get_table();
            $table_name = $table->table_name;
            //Insert 
            if ($service->insertionType == "bulk")
                $response = $service->urabe->executor->insert_bulk($table_name, $service->values);
            else if ($service->insertionType == "single") {
                $response = $service->urabe->executor->insert($table_name, $service->values);
                if ($response->affected_rows > 0 && isset($this->primary_key)) {
                    $column_name = $this->primary_key;
                    $insert_id = $this->get_max_id($service->urabe, $table_name, $this->primary_key);
                    $result = $response->result;
                    $result[$column_name] = $insert_id;
                    $response->result = $result;
                }
            }
            return $response;
        } catch (Exception $e) {
            throw new Exception("Error Processing Request, " . $e->getMessage(), $e->getCode());
        }
    }
}
