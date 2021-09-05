<?php

namespace Urabe\Service;

use Exception;
use Urabe\DB\TableDefinition;
use Urabe\Service\WebServiceContent;
use Urabe\Service\HasamiRESTfulService;
use Urabe\Urabe;

/**
 * POST Service Class
 * This class defines a restful service with a request verbose POST. 
 * This method is often used to update or access protected data from the database. 
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class POSTService extends HasamiRESTfulService
{
    /**
     * @var string The update condition
     */
    public $condition;
    /**
     *  @var array The values to update
     */
    public $values;
    /**
     * __construct
     *
     * Initialize a new instance of the POST Service class.
     * A default service task is defined as a callback using the function POSTService::default_POST_action
     * 
     * @param WebServiceContent $data The web service content
     * @param Urabe $urabe The database manager
     * @param string $primary_key The primary key column name
     * @param string $sel_filter The selection filter.
     */
    public function __construct($data, $urabe, $primary_key)
    {
        //1: Initialize parent
        parent::__construct($data, $urabe, "default_POST_action", $primary_key);
        $body = $this->get_body();
        $table = $this->get_table();
        //2: Validate body
        $this->validate($table, $body);
        //Initialize service
        $this->values = $body->content->values;
        $this->values = $this->filter((array)$this->values);
        //3: Actualizando la condición
        $conditionValue = $body->content->condition;
        $field = $table->get_field_definition($primary_key);
        if (in_array($field->data_type, array("Integer", "Long", "Number")))
            $this->condition = "$primary_key = $conditionValue";
        else
            $this->condition = "$primary_key = '$conditionValue'";
    }
    /**
     * Wraps the update function from urabe. The condition is obtained directly from
     * $condition property
     * @param string $table_name The table name.
     * @param object $values The values to update as column key value paired
     * Column names as keys and updates values as associated value, place holders can not be identifiers only values.
     * @throws Exception An Exception is raised if the connection is null or executing a bad query
     * @return UrabeResponse Returns the service response formatted as an executed response
     */
    public function update($table_name, $values)
    {
        return $this->urabe->executor->update($table_name, $values, $this->condition);
    }
    /**
     * Validates the content data before insert
     *
     * @param TableDefinition $table The table definition
     * @param WebServiceBody $body The request content body
     * @throws Exception An Exception is thrown if the request content is not valid
     */
    public function validate($table, $body)
    {
        //Validate the primary key exists in the table
        if (!$table->exists($this->primary_key))
            throw new Exception(sprintf(ERR_MISSING_COLUMN, $this->primary_key, $table->table_name));
        //Validate the body contains the field "condition"
        if (!$body->exists(NODE_CONDITION))
            throw new Exception(sprintf(ERR_INCOMPLETE_DATA, NODE_CONDITION));
        //Validate the body contains the field "values"
        if (!$body->exists(NODE_VAL))
            throw new Exception(sprintf(ERR_INCOMPLETE_DATA, NODE_VAL));
        //Validate columns to update
        $columns = array_keys(get_object_vars($body->content->values));
        $unknown_columns = array();
        foreach ($columns as &$column_name) 
            if (!in_array($column_name, $table->get_column_names()))
                array_push($unknown_columns, $column_name);
        if (sizeof($unknown_columns)>0)
        {
            $columns = implode(', ', $unknown_columns);
            throw new Exception(sprintf(ERR_MISSING_COLUMN, $columns, $table->table_name));
        }
    }

    /**
     * Defines the default POST action, by default updates the given values.
     * A condition is needed to update values.
     * @param HasamiRESTfulService $service The web service that executes the action
     * @throws Exception An Exception is thrown if the response can be processed correctly
     * @return UrabeResponse The server response
     */
    public function default_POST_action($service)
    {
        try {
            $table = $service->get_table();
            $table_name = $table->table_name;
            $response = $service->urabe->executor->update($table_name, $this->values, $this->condition);
            return $response;
        } catch (Exception $e) {
            throw new Exception("Error Processing Request, " . $e->getMessage(), $e->getCode());
        }
    }
}
