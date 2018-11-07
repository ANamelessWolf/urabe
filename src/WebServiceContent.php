<?php

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
     * @var array The web service get variables
     */
    public $get_variables;
    /**
     * @var array The web service url parameters, the
     * parameters can be associated in pairs or by given index. The parameters are
     * specified in the UrabeSettings
     */
    public $url_params;
    /**
     * @var object The web service body is expected to be in a JSON
     * When the service is GET the body is NULL
     */
    public $body;
    /**
     * @var object Use this variable to insert extra data needed when executing a service operation
     */
    public $extra;
    /**
     * @var string The web service request method
     */
    public $method;

    /**
     * __construct
     *
     * Initialize a new instance of the web service content
     */
    public function __construct()
    {
        $this->get_variables = array();
        //GET Variables
        foreach ($_GET as $key => $value)
            $this->get_variables[$key] = $value;
        //URL parameters
        if (isset($_SERVER['PATH_INFO']))
            $this->url_params = explode('/', trim($_SERVER['PATH_INFO'], '/'));
        else
            $this->url_params = array();
        //POST content, must be a JSON string
        $this->body = file_get_contents('php://input');
        $this->body = json_decode($this->body);
        //The Request method
        $this->method = $_SERVER['REQUEST_METHOD'];
        //Initialize $extra as an empty object
        $this->extra = (object)array();
    }
    /**
     * This function check if the given variable name is defined in the current
     * web service GET variables
     * 
     * @param string $var_name The variable name
     * @return true Returns true when the variable is defined
     */
    public function in_GET_variables($var_name)
    {
        return in_array($var_name, array_keys($this->get_variables));
    }
    /**
     * This function check if the given variable name is defined in the current
     * web service GET variables and if the variable value is equals to the given value
     * 
     * @param string $var_name The variable name
     * @param mixed $value The value to compare
     * @return true Returns true when the variable is defined
     */
    public function GET_variable_equals($var_name, $value)
    {
        return $this->in_GET_variables($var_name) && $this->get_variables[$var_name] == $value;
    }
    /**
     * This function picks the GET variables values by name and returns them in an array
     * if the value to pick is not in the GET variables it throws and exception
     * 
     * @param string $var_names The variables name to pick its values
     * @return array The picked values in the given variable names order
     */
    public function pick_GET_variable(...$var_names)
    {
        $values = array();
        $keys = array_keys($this->get_variables);
        foreach ($var_names as $var_name) {
            if (in_array($var_name, $keys))
                array_push($values, $this->get_variables[$var_name]);
            else
                throw new Exception(sprintf(ERR_INCOMPLETE_DATA, CAP_GET_VARS, "'" . implode("', '", $var_names) . "'"));
        }
        return $values;
    }
    /**
     * If the request method is GET the filter is extracted from the GET Variables
     * otherwise is searched in the body
     *
     * @return mixed filter value
     */
    public function get_filter()
    {
        if ($this->method == 'GET' && $this->in_GET_variables('filter'))
            return $this->get_variables['filter'];
        else if (isset($this->body) && property_exists($this->body, 'filter'))
            return $this->body->filter;
        else
            return null;
    }
    /**
     * Gets the GET variables names as a string array
     *
     * @return array The array of GET variables names
     */
    public function get_variables_names()
    {
        return array_keys($this->get_variables);
    }
    /**
     * Builds a condition using the primary key that match a column name
     *
     * @param string $column_name The primary key column name
     * @return string The condition
     */
    public function build_primary_key_condition($column_name)
    {
        $primary_key = null;
        if ($this->method == 'GET' && $this->in_GET_variables($column_name))
            $primary_key = $this->get_variables[$column_name];
        else if (isset($this->body) && property_exists($this->body, $column_name))
            $primary_key = $this->body->{$column_name};
        return isset($primary_key) ? "$column_name = " . $primary_key : null;
    }
    /**
     * Validates if the passed variables names are contained in the web service content.
     * As the fields are considered obligatory, they must appear in the GET variables 
     * otherwise an exception will be thrown.
     * @param array $variables The primary key column name
     * @throws Exception Throws an Exception if any of the variables are not presented in the GET variables
     * @return boolean True if all variables names are defined in GET variables
     */
    public function validate_obligatory_GET_variables(...$variables)
    {
        $obl_variables_count = 0;
        for ($i = 0; $i < count($variables); $i++)
            if ($this->in_GET_variables($variables[$i]))
            $obl_variables_count++;
        if (count($variables) == $obl_variables_count)
            return true;
        else
            throw new Exception(sprintf(ERR_INCOMPLETE_DATA, CAP_GET_VARS, "'" . implode("', '", $variables) . "'"));
    }
}
?>