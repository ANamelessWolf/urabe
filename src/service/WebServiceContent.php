<?php

namespace Urabe\Service;

use Urabe\Service\WebServiceBody;
use Urabe\Service\GETVariables;
use Exception;

/**
 * This class obtains the web service content from GET variables, URL parameters, POST body
 * and the request method
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class WebServiceContent
{
    /**
     * @var GETVariables The web service get variables
     */
    public $get_vars;
    /**
     * @var array The web service url parameters
     */
    public $url_params;
    /**
     * @var WebServiceBody The web service body 
     */
    public $body;
    /**
     * @var object Use this variable save extra data
     */
    public $extra;
    /**
     * @var string The request method. 
     * Supported Method GET POST PUT DELETE
     */
    public $method;
    /**
     * __construct
     *
     * Initialize a new instance of the web service content
     */
    public function __construct()
    {
        //The Request method
        $this->method = $_SERVER['REQUEST_METHOD'];
        //GET Variables
        $this->get_vars = new GETVariables();
        //URL parameters
        if (isset($_SERVER['PATH_INFO']))
            $this->url_params = explode('/', trim($_SERVER['PATH_INFO'], '/'));
        else
            $this->url_params = array();
        //POST content, must be a JSON string
        $this->body = new WebServiceBody($this->method);
        //Initialize $extra as an empty object
        $this->extra = (object)array();
    }
    /**
     * If the request method is GET the filter is extracted from the GET Variables
     * otherwise is searched in the body
     *
     * @return mixed filter value
     */
    public function get_filter()
    {
        if ($this->method == 'GET')
            return $this->get_vars->get('filter');
        else if (isset($this->body) && property_exists($this->body, 'filter'))
            return $this->body->filter;
        else
            return null;
    }
    /**
     * This method throws an exception when the action is 
     * access by a not valid allowed method.
     *
     * @param array ...$allowed_methods A string array containing the allowed methods
     * @return void
     */
    public function check_method_restriction(...$allowed_methods)
    {
        if (!in_array($this->method, $allowed_methods))
            throw new Exception(sprintf(ERR_SERVICE_RESTRICTED, $this->method));
    }
    /**
     * Builds a simple condition using a column name to be equals to the content
     * store value.
     *
     * @param string $column_name The column name
     * @return array One record array with key value pair value, column_name => condition_value
     */
    public function build_simple_condition($column_name)
    {
        if ($this->method == 'GET')
            $result = $this->get_vars->build_simple_condition($column_name);
        else 
            $result = $this->body->build_simple_condition($column_name);
        return $result;
    }
}
