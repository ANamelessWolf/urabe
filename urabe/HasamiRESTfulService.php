<?php
include_once "Warai.php";
include_once "Urabe.php";
/**
 * Hasami RESTful Service Class
 *
 * Creates and manage a simple REST service that makes a transaction to an Oracle database.
 * @version 1.0.0
 * @api Makoto Urabe Oracle
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class HasamiRESTfulService
{
    /**
     * @var HasamiWrapper Access the Oracle web service wrapper
     */
    public $service;
    /**
     * @var ParameterCollection The web service parameter collection
     */
    public $parameters;
    /**
     * The request body as a a JSON object
     *
     * @var stdclass The body as JSON object
     */
    public $body;
    /**
     * @var string Saves the last error executed on a query..
     */
    protected $query_error;
    /**
     * @var callback Defines the service task
     * (HasamiRESTfulService $service): string
     */
    public $service_task;
    /**
     * __construct
     *
     * Initialize a new instance of the Hasami RESTful service class.
     * 
     * @param HasamiWrapper $service The web service wrapper
     * @param stdClass $body The JSON body
     */
    public function __construct($service, $body = null)
    {
        $this->service = $service;
        $this->body = $body;
    }
    /**
     * Extract the values from the body in the same order that the $table_fields are defined.
     * @param string[] $table_fields The name of the values keys to extract.
     * @return string[] The stored values.
     */
    public function extract_values($table_fields)
    {
        if (is_null($body))
            throw new Exception(ERR_BODY_IS_NULL);
        $values = array();
        foreach ($table_fields as &$field_name) {
            if (property_exists($body, $field_name))
                array_push($values, $body->$field_name);
        }
        if (count($table_fields) != count($values))
            throw new Exception(sprintf(ERR_INCOMPLETE_BODY, CAP_EXTRACT, $this->concat_fields($table_fields)));
        return $values;
    }
    /**
     * Gets the service response
     *
     * @return stdClass The web server response
     */
    public function get_response_result()
    {
        $this->parameters = new ParameterCollection();
        if (PARAM_TYPE == URL_PARAM)
            $this->parameters->get_url_parameters();
        else
            $this->parameters->get_variables();
        if (!is_null($this->service_task))
            $result = call_user_func_array($this->service_task, array($this));
        else
            $result = new QueryResult();
        return $result;
    }
    /**
     * Concatenate a collection of fields with comas
     *
     * @param string[]|int[] $fields The fields to concatenate
     * @return string The fields concatenated
     */
    protected function concat_fields($fields)
    {
        $str = "";
        foreach ($fields as &$value)
            $str .= $value . ", ";
        return substr($str, 0, strlen($str) - 2);
    }
}