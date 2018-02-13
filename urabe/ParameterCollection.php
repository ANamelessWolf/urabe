<?php
include_once "Warai.php";
/**
 * This class obtains the web service query parameters from the URL path or from the GET variables
 * each parameter is associated with a key and saved on the $parameters property.
 * The url parameters must be in pairs.
 * @version 1.0.0
 * @api Makoto Urabe Oracle
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class ParameterCollection
{
    /**
     * @var string[] The parameter collection
     */
    public $parameters;
    /**
     * __construct
     *
     * Initialize a new instance of the query parameters.collections
     */
    public function __construct()
    {
        $this->parameters = array();
    }
    /**
     * Get the available parameters from the URL
     * The parameters are added to the paramaters property
     *
     * @return void
     */
    public function get_url_parameters()
    {
        if (isset($_SERVER['PATH_INFO']))
            $request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
        else
            $request = array();
        $values_count = count($request);
        //Los parámetros deben ir en parejas
        if (($values_count % 2) == 0) {
            for ($i = 1; $i < $values_count; $i += 2)
                if ($i < $values_count) {
                $this->parameters[$request[$i - 1]] = $request[$i];
            }
        } else
            throw new Exception(ERR_URL_PARAM_FORMAT, 1);
    }
    /**
     * Get the available GET variables as paramaters.
     * The parameters are added to the paramaters property
     *
     * @return void
     */
    public function get_variables()
    {
        $this->parameters = array_merge($this->parameters, $_GET);
    }
    /**
     * Check if a parameter key exists in the parameter collection
     * and is not null
     * @param string $parameter_key The paramter to validate
     * @return True if the parameter exists on the paramater key
     */
    public function exists($parameter_key)
    {
        return array_key_exists($parameter_key, $this->parameters) && !is_null($this->parameters[$parameter_key]);
    }
    /**
     * Check if a parameter is equals to a given value
     *
     * @param string $parameter_key The parameter key
     * @param mixed $parameter_value The parameter value to be equals
     * @return True if the parameter exists and its equals to a given value
     */
    public function equals_to($parameter_key, $parameter_value)
    {
        return exists($parameter_key) && $this->parameters[$parameter_key] == $parameter_value;
    }
}
?>