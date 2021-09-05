<?php

namespace Urabe\Service;

use Exception;

/**
 * This class obtains the web service content from the POST method.
 * The content must be in JSON format
 * @version 1.0.0
 * @api Makoto Urabe DB Manager
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class WebServiceBody
{
    /**
     * @var object The web service body is expected to be in a JSON
     * When the service is GET the body is NULL
     */
    public $content;
    /**
     * @var array The body property names
     */
    public $property_names;
    /**
     * __construct
     *
     * Initialize a new instance of the JSON Body
     * @param string $method The Request method
     */
    public function __construct($method)
    {
        if ($method != "GET") {
            //POST content, must be a JSON string
            $this->content = file_get_contents('php://input');
            $this->content = json_decode($this->content);
            //Extracting property names
            if (isset($this->content))
                $this->property_names = array_keys(get_object_vars($this->content));
            else
                $this->property_names = array();
        } else
            $this->content = null;
    }

    /**
     * Builds a simple condition using the column name to be equals to the
     * body condition property value
     *
     * @param string $column_name The column name
     * @return array One record array with key value pair value, column_name => condition_value
     */
    public function build_simple_condition($column_name)
    {
        $result = array();
        if ($column_name != null && $this->exists($column_name))
            return $result[$column_name] = $this->content->{NODE_CONDITION};
        else
            return null;
    }

    /**
     * This function check if the given property name is defined in the current
     * body
     * 
     * @param string $prop_name The property name
     * @return bool Returns true when the variable is defined
     */
    public function exists($prop_name)
    {
        if (is_null($this->content))
            return false;
        else
            return  in_array($prop_name, $this->property_names);
    }
    /**
     * Validates if the passed properties names contains all the obligatory names.
     * As the names are considered obligatory, if one not found an
     * exception is thrown.
     * @param array $prop_names The property names
     * @throws Exception Throws an Exception if any of the variables are missing
     * @return boolean True if all variables are defined
     */
    public function validate_obligatory(...$prop_names)
    {
        $vCount = 0;
        foreach ($prop_names as $var_name)
            $vCount += $this->exists($var_name) ? 1 : 0;
        if (count($prop_names) == $vCount)
            return true;
        else {
            $names = implode("', '", $prop_names);
            throw new Exception(sprintf(ERR_INCOMPLETE_BODY, "'" . $names . "'"));
        }
    }
}
