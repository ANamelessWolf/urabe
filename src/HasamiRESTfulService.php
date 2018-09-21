<?php
include_once "Urabe.php";
/**
 * Hasami Restful Service Class
 * This class creates and manage a simple REST service that makes a transaction to supported database or
 * execute a defined action
 * 
 * @version 1.0.0
 * @api Makoto Urabe
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class HasamiRestfulService
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
     * @var HasamiWrapper The Web service manager
     */
    public $wrapper;
    /**
     * @var callback|string Defines the service task, when the service is a callback the method
     * has to be defined as follows.
     * function UrabeResponse (WebServiceContent $data, Urabe $urabe);
     * When the service task is given as a string the action is directly called from the defined wrapper
     */
    public $service_task;
    /**
     * __construct
     *
     * Initialize a new instance of the Hasami Restful service class.
     * 
     * @param WebServiceContent $data The web service content
     * @param Urabe $urabe The database manager
     */
    public function __construct($data = null, $urabe = null)
    {
        $this->data = $data;
        $this->urabe = $urabe;
    }
    /**
     * This function validates that the body contains all the given fields.
     * The fields may refer to the column names and must match name and case
     * @param array $fields The fields that must be contained in the body, as an array of strings
     * @throws Exception An Exception is thrown if the body is null or the body does not contains all fields
     * @return void
     */
    public function validate_body($fields)
    {
        if (is_null($body))
            throw new Exception(ERR_BODY_IS_NULL);
        $values = array();
        foreach ($fields as &$field_name) {
            if (property_exists($body, $field_name))
                array_push($values, $body->$field_name);
        }
        if (count($fields) != count($values))
            throw new Exception(sprintf(ERR_INCOMPLETE_BODY, CAP_EXTRACT, implode(', ', $fields)));
    }
    /**
     * This method validates the columns contained in the given node.
     * It's expected that the body contains the passed node and the node contain a columns property
     * @param string $property_name The name of the property that contains the columns property in the body
     * @param array $obligatory_columns An array of column names that must exists in the columns property
     * @throws Exception An Exception is thrown if the body is null or the body does not contains all fields
     * @return void 
     */
    public function validate_columns($property_name, $obligatory_columns)
    {
        if (is_null($this->data->body))
            throw new Exception(ERR_BODY_IS_NULL);
        if (property_exists($this->data->body, $property_name))
            throw new Exception(sprintf(ERR_INCOMPLETE_BODY, $property_name));
        if (property_exists($this->data->body->{$property_name}, NODE_COLS))
            throw new Exception(sprintf(ERR_INCOMPLETE_DATA, $property_name, NODE_COLS));
        $columns = $this->data->body->{$property_name}->{NODE_COLS};
        //Columns must contain all obligatory columns
        foreach ($obligatory_columns as &$column)
            if (!in_array($column, $columns))
            throw new Exception(sprintf(ERR_INCOMPLETE_DATA, NODE_COLS, implode(', ', $obligatory_columns)));
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
        else if (is_string($this->service_task))
            $result = $this->wrapper->{$this->service_task}($data, $urabe);
        else if (!is_null($this->service_task))
            $result = call_user_func_array($this->service_task, array($data, $urabe));
        else
            throw new Exception(ERR_BAD_RESPONSE);
        return $result;
    }
}