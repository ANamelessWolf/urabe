<?php

namespace Urabe\Service;

use Exception;
use stdClass;
use Urabe\DB\TableDefinition;
use Urabe\Service\WebServiceContent;
use Urabe\Urabe;

/**
 * Hasami Restful Service Class
 * This class creates and manage a simple REST service that makes a transaction to supported database or
 * execute a defined action
 * 
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class HasamiRESTfulService
{
    /**
     * The web service request content
     *
     * @var WebServiceContent The web service content
     */
    public $data;
    /**
     * @var Urabe The database manager
     */
    public $urabe;
    /**
     * @var string Sets or gets the table primary key column name
     * This field is neccesary to create a condition for UPDATE or DELETE Services
     */
    protected $primary_key;
    /**
     * @var string Defines the service task, to use to get the response. Must be a funtion name inside the caller.
     * Function format:
     * function UrabeResponse (HasamiRESTfulService $service);
     */
    public $service_task;
    /**
     * @var object The class is the one that calls the function defined in the property service_task
     */
    public $caller;
    /**
     * __construct
     *
     * Initialize a new instance of the Hasami Restful service class.
     * 
     * @param WebServiceContent $data The web service content
     * @param Urabe $urabe The database manager
     * @param string|NULL $primary_key The name of the primary key.
     * @param string $service_task The name of the function tu be used as the service task
     */
    public function __construct($data, $urabe, $service_task = null,  $primary_key = null)
    {
        $this->data = $data;
        $this->urabe = $urabe;
        $this->primary_key = $primary_key;
        $this->caller = $this;
        $this->service_task = $service_task;
    }
    /**
     * Gets the table definition
     *
     * @return TableDefinition The table definition
     */
    public function get_table()
    {
        return $this->urabe->parser->table_definition;
    }

    /**
     * Gets the table columns
     *
     * @return array The table columns
     */
    public function get_columns()
    {
        return $this->urabe->parser->table_definition->get_column_names();
    }

    /**
     * Gets the body object from the request data
     *
     * @return WebServiceBody The request body
     */
    public function get_body()
    {
        return $this->data->body;
    }

    /**
     * Gets the table required fields
     *
     * @return array The table required fields
     */
    public function get_required_columns()
    {
        return $this->urabe->parser->table_definition->get_required_column_names();
    }

    /**
     * This method validates the columns names are defined in the obligatory names
     * @param array $column_names The list of column names to check if they are contained in the obligatory columns
     * @param array $obligatory_column_names The names of obligatory columns
     * @throws Exception An Exception is thrown if the body is null or the body does not contains all fields
     * @return void 
     */
    public function validate_columns($column_names, $obligatory_column_names)
    {
        $diff = array_diff($obligatory_column_names, $column_names);
        //1: One or mor obligatory column is missing
        if (sizeof($diff) > 0) {
            $msg =  sprintf(ERR_INCOMPLETE_DATA, NODE_COLS, implode(', ', $obligatory_column_names));
            throw new Exception($msg);
        }
    }

    /**
     * Formats a value using a field definition
     *
     * @param TableDefinition $table The Table definition
     * @param string $column_name The column name
     * @param mixed $value The value to format
     * @return mixed The value formatted
     */
    public function format_value($table, $column_name, $value)
    {
        $field_definition = $table->get_field_definition($column_name);
        return $field_definition->format_value($this->urabe->get_driver(), $value);
    }
    /**
     * Filter the values that are not defined in the table definition
     *
     * @param array $values The values to be filtered
     * @return array The filtered values
     */
    public function filter($values)
    {
        $filter_result = array();
        $table = $this->get_table();
        foreach ($values as $key => $value) {
            if ($table->exists($key))
                $filter_result[$key] = $value;
        }
        return $filter_result;
    }

    /**
     * Gets the service response
     * @throws Exception An Exception is thrown when the service task is not defined or an error occurs 
     * during the callback
     * @return UrabeResponse The web service response
     */
    public function get_response()
    {
        if (is_null($this->service_task))
            throw new Exception(ERR_INVALID_SERVICE_TASK);
        else if (!is_null($this->caller) && is_string($this->service_task))
            $result = $this->caller->{$this->service_task}($this->data, $this->urabe);
        else
            throw new Exception(ERR_BAD_RESPONSE);
        return $result;
    }
}
